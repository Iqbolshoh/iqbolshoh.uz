<?php

namespace App\Services;

use App\Enums\ActivitySource;
use App\Models\ActivityCategory;
use App\Models\ActivityEntry;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * The time half of the bot.
 *
 * Built to answer one question the other two halves cannot: where did the day
 * actually go. A plan says what was intended, money says what it cost, and
 * neither of them knows that eight of the twenty-four hours were spent asleep.
 *
 * Deliberately the same shape as FinanceBot — one line of text is one entry,
 * buttons exist for the cases where that was not enough — because the two are
 * read one after the other and a person should not have to learn the bot
 * twice.
 */
class ActivityBot
{
    /**
     * How many activities a picker shows before offering the rest.
     *
     * Same reasoning as the money picker: fourteen buttons is a wall to scroll
     * past, and the ones actually used are almost always the answer.
     */
    private const PICKER_SHORTLIST = 8;

    public function __construct(
        private readonly TelegramClient $client,
        private readonly ActivityService $activities,
        private readonly DurationTextParser $parser,
    ) {}

    /**
     * Try to read a free-text line as a stretch of time.
     *
     * Returns false when the text carries no duration, so the caller can fall
     * back to its own handling. Money is tried first by the caller, and the
     * two can never collide: a duration always names its unit, and an amount
     * never does.
     */
    public function handleText(int $chatId, User $user, string $text): bool
    {
        $categories = $this->activities->categories($user);

        $parsed = $this->parser->parse($text, $categories);
        $pending = $this->pendingCategory($chatId, $user);

        // The bot has just asked "how long?" about an activity that was
        // tapped, so a bare number is an answer to that question.
        if ($pending !== null && $parsed === null && ($minutes = $this->parser->durationOnly($text)) !== null) {
            $parsed = ['minutes' => $minutes, 'category' => $pending, 'note' => null];
        }

        if ($parsed === null) {
            return false;
        }

        if ($pending !== null) {
            $parsed['category'] ??= $pending;

            $this->forgetPending($chatId);
        }

        $entry = $this->activities->record(
            user: $user,
            minutes: $parsed['minutes'],
            category: $parsed['category'],
            note: $parsed['note'],
            source: ActivitySource::Telegram,
        );

        $lines = [$this->confirmation($entry), '', $this->todayLine($user)];

        $keyboard = $parsed['category'] === null
            ? $this->categoryPicker($entry, $user)
            : $this->afterSaveKeyboard($entry);

        if ($parsed['category'] === null) {
            $lines[] = '';
            $lines[] = __('bot.act.ask_category');
        }

        $this->client->sendMessage($chatId, implode("\n", $lines), $keyboard);

        return true;
    }

    /**
     * A button press on the time side.
     *
     * @param  list<string>  $parts  "t", action, and whatever the action needs
     */
    public function handleCallback(int $chatId, int $messageId, User $user, array $parts): void
    {
        if (! in_array($parts[1] ?? '', ['add', 'new'], true)) {
            $this->forgetPending($chatId);
        }

        match ($parts[1] ?? '') {
            'menu' => $this->sendMenu($chatId, $user, $messageId),
            'day' => $this->sendPeriod($chatId, $user, 'today', $messageId),
            'week' => $this->sendPeriod($chatId, $user, 'week', $messageId),
            'month' => $this->sendPeriod($chatId, $user, 'month', $messageId),
            'recent' => $this->sendRecent($chatId, $user, $messageId),
            'add' => $this->startEntry($chatId, $messageId, $user, ($parts[2] ?? '') === 'all'),
            'new' => $this->askDuration($chatId, $messageId, $user, (int) ($parts[2] ?? 0)),
            'cat' => $this->assignCategory($chatId, $messageId, $user, (int) ($parts[2] ?? 0), (int) ($parts[3] ?? 0)),
            'pick' => $this->offerCategories($chatId, $messageId, $user, (int) ($parts[2] ?? 0), ($parts[3] ?? '') === 'all'),
            'skip' => $this->skipCategory($chatId, $messageId, $user, (int) ($parts[2] ?? 0)),
            'undo' => $this->undo($chatId, $messageId, $user, (int) ($parts[2] ?? 0)),
            default => null,
        };
    }

    /** The time screen: today, the week, the month, and how much of it is known. */
    public function sendMenu(int $chatId, User $user, ?int $editMessageId = null): void
    {
        $stats = new ActivityStats($user->id, $user->timezone);
        $today = $stats->today();

        $day = $stats->summary($today, $today);
        $week = $stats->summary($today->startOfWeek(), $today->endOfWeek());
        $month = $stats->summary($today->startOfMonth(), $today->endOfMonth());

        $lines = [
            __('bot.act.title'),
            '',
            __('bot.act.today', ['duration' => ActivityEntry::duration($day['minutes'])]),
            __('bot.act.week_so_far', ['duration' => ActivityEntry::duration($week['minutes'])]),
            __('bot.act.month_so_far', ['duration' => ActivityEntry::duration($month['minutes'])]),
            '',
            __('bot.act.covered', ['percent' => $day['covered']]),
            '',
            __('bot.act.how_to_add'),
        ];

        $rows = [
            [TelegramClient::button(__('bot.btn.add_time'), 't:add')],
            [
                TelegramClient::button(__('bot.btn.act_today'), 't:day'),
                TelegramClient::button(__('bot.btn.week'), 't:week'),
                TelegramClient::button(__('bot.btn.month'), 't:month'),
            ],
        ];

        $second = [];

        if ($this->hasAny($user)) {
            $second[] = TelegramClient::button(__('bot.btn.recent'), 't:recent');
        }

        if (($last = $this->activities->lastFrom($user, ActivitySource::Telegram)) !== null) {
            $second[] = TelegramClient::button(__('bot.btn.undo'), "t:undo:{$last->id}");
        }

        if ($second !== []) {
            $rows[] = $second;
        }

        $rows[] = [
            TelegramClient::button(__('bot.btn.money'), 'f:menu'),
            TelegramClient::button(__('bot.btn.home'), 'nav:menu'),
        ];

        $this->deliver($chatId, $editMessageId, implode("\n", $lines), TelegramClient::keyboard($rows));
    }

    /**
     * A period in detail: where the time went, biggest first.
     *
     * Coverage is printed next to the breakdown rather than instead of it,
     * because the two answer different questions and the list is misleading
     * without the denominator — twelve hours of work reads very differently
     * against fourteen logged hours than against a fully accounted week.
     */
    public function sendPeriod(int $chatId, User $user, string $period, ?int $editMessageId = null): void
    {
        $stats = new ActivityStats($user->id, $user->timezone);
        $today = $stats->today();

        [$start, $end, $title, $empty] = match ($period) {
            'today' => [$today, $today, __('bot.act.day_title'), __('bot.act.empty_day')],
            'week' => [$today->startOfWeek(), $today->endOfWeek(), __('bot.act.week_title'), __('bot.act.empty_week')],
            default => [
                $today->startOfMonth(),
                $today->endOfMonth(),
                __('bot.act.month_title', ['month' => $today->translatedFormat('F Y')]),
                __('bot.act.empty_month'),
            ],
        };

        $others = array_values(array_diff(['today', 'week', 'month'], [$period]));
        $summary = $stats->summary($start, $end);

        if ($summary['count'] === 0) {
            $this->deliver($chatId, $editMessageId, $title . "\n\n" . $empty, TelegramClient::keyboard([
                [TelegramClient::button(__('bot.btn.add_time'), 't:add')],
                $this->periodButtons($others),
                [TelegramClient::button(__('bot.btn.back'), 't:menu')],
            ]));

            return;
        }

        $lines = [
            $title,
            '',
            __('bot.act.today', ['duration' => ActivityEntry::duration($summary['minutes'])]),
        ];

        if ($period !== 'today') {
            $lines[] = __('bot.act.average', ['duration' => ActivityEntry::duration($summary['average'])]);
        }

        $lines[] = __($period === 'today' ? 'bot.act.covered' : 'bot.act.covered_period', ['percent' => $summary['covered']]);
        $lines[] = '<i>' . __('bot.act.entries', ['count' => $summary['count']]) . '</i>';

        $rows = $stats->byCategory($start, $end);

        $lines[] = '';
        $lines[] = __('bot.act.by_category');

        foreach ($rows->take(8) as $row) {
            $lines[] = sprintf(
                '<code>%s</code> %s · <b>%s</b>',
                $this->bar((float) $row['share']),
                $this->categoryLabel($row['category']),
                ActivityEntry::duration($row['minutes'])
            );
        }

        // The one judgement the report makes, and only because the categories
        // carry it: how much of the logged time was time the owner would want
        // back. Printed as two totals rather than a score, because the useful
        // number is the one you can act on.
        $good = $rows->filter(fn (array $row): bool => $row['category']?->is_good ?? true)->sum('minutes');
        $bad = $rows->sum('minutes') - $good;

        if ($bad > 0) {
            $lines[] = '';
            $lines[] = __('bot.act.balance', [
                'good' => ActivityEntry::duration((int) $good),
                'bad' => ActivityEntry::duration((int) $bad),
            ]);
        }

        $targets = $stats->againstTargets($start, $end)->filter(fn (array $row): bool => $row['minutes'] > 0);

        if ($targets->isNotEmpty()) {
            $lines[] = '';
            $lines[] = __('bot.act.targets');

            foreach ($targets as $row) {
                $lines[] = __('bot.act.target_line', [
                    'category' => $row['category']->label(),
                    'spent' => ActivityEntry::duration($row['minutes']),
                    'target' => ActivityEntry::duration($row['target']),
                ]);
            }
        }

        $this->deliver($chatId, $editMessageId, implode("\n", $lines), TelegramClient::keyboard([
            $this->periodButtons($others),
            [
                TelegramClient::button(__('bot.btn.add_time'), 't:add'),
                TelegramClient::button(__('bot.btn.recent'), 't:recent'),
            ],
            [
                TelegramClient::button(__('bot.btn.back'), 't:menu'),
                TelegramClient::button(__('bot.btn.home'), 'nav:menu'),
            ],
        ]));
    }

    /** The last handful of entries, newest first. */
    public function sendRecent(int $chatId, User $user, ?int $editMessageId = null): void
    {
        $entries = ActivityEntry::query()
            ->where('user_id', $user->id)
            ->with('category')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $lines = [__('bot.act.recent_title'), ''];

        if ($entries->isEmpty()) {
            $lines[] = __('bot.act.empty_recent');
        }

        foreach ($entries as $entry) {
            $lines[] = sprintf(
                '<code>%s</code>  %s · %s%s',
                $entry->date->format('d.m'),
                $entry->formattedDuration(),
                $this->categoryLabel($entry->category),
                $entry->source === ActivitySource::Status ? ' <i>· ' . __('bot.act.from_status') . '</i>' : ''
            );

            if ($entry->note !== null) {
                $lines[] = '        <i>' . e($entry->note) . '</i>';
            }
        }

        $buttons = [];

        if (($last = $this->activities->lastFrom($user, ActivitySource::Telegram)) !== null) {
            $buttons[] = TelegramClient::button(__('bot.btn.undo'), "t:undo:{$last->id}");
        }

        $buttons[] = TelegramClient::button(__('bot.btn.back'), 't:menu');

        $this->deliver($chatId, $editMessageId, implode("\n", $lines), TelegramClient::keyboard([
            $buttons,
            [TelegramClient::button(__('bot.btn.home'), 'nav:menu')],
        ]));
    }

    /** Step one of logging time with buttons: which activity. */
    private function startEntry(int $chatId, int $messageId, User $user, bool $all): void
    {
        $categories = $this->activities->categoriesByUse($user);
        $shorten = ! $all && $categories->count() > self::PICKER_SHORTLIST + 2;

        $rows = $this->buttonRows(
            $shorten ? $categories->take(self::PICKER_SHORTLIST) : $categories,
            fn (ActivityCategory $category): string => "t:new:{$category->id}"
        );

        if ($shorten) {
            $rows[] = [TelegramClient::button(__('bot.btn.all_categories'), 't:add:all')];
        }

        $rows[] = [TelegramClient::button(__('bot.btn.back'), 't:menu')];

        $this->deliver($chatId, $messageId, __('bot.act.pick_for_new'), TelegramClient::keyboard($rows));
    }

    /** Step two: the activity is settled, so all that is left is a length. */
    private function askDuration(int $chatId, int $messageId, User $user, int $categoryId): void
    {
        $category = ActivityCategory::query()
            ->where('user_id', $user->id)
            ->find($categoryId);

        if ($category === null) {
            $this->sendMenu($chatId, $user, $messageId);

            return;
        }

        Cache::put($this->pendingKey($chatId), $category->id, now()->addMinutes(15));

        $this->deliver(
            $chatId,
            $messageId,
            __('bot.act.ask_duration', ['category' => $category->label()]),
            TelegramClient::keyboard([[
                TelegramClient::button(__('bot.btn.back'), 't:add'),
                TelegramClient::button(__('bot.btn.home'), 'nav:menu'),
            ]])
        );
    }

    /** The activity buttons for one entry, ordered by how often each is used. */
    private function categoryPicker(ActivityEntry $entry, User $user, bool $all = false): array
    {
        $categories = $this->activities->categoriesByUse($user);
        $shorten = ! $all && $categories->count() > self::PICKER_SHORTLIST + 2;

        $rows = $this->buttonRows(
            $shorten ? $categories->take(self::PICKER_SHORTLIST) : $categories,
            fn (ActivityCategory $category): string => "t:cat:{$entry->id}:{$category->id}"
        );

        if ($shorten) {
            $rows[] = [TelegramClient::button(__('bot.btn.all_categories'), "t:pick:{$entry->id}:all")];
        }

        $rows[] = [
            TelegramClient::button(__('bot.btn.skip'), "t:skip:{$entry->id}"),
            TelegramClient::button(__('bot.btn.undo'), "t:undo:{$entry->id}"),
        ];

        return TelegramClient::keyboard($rows);
    }

    /** Reopen the picker on an entry that already has an answer. */
    private function offerCategories(int $chatId, int $messageId, User $user, int $entryId, bool $all): void
    {
        $entry = ActivityEntry::query()
            ->where('user_id', $user->id)
            ->with('category')
            ->find($entryId);

        if ($entry === null) {
            $this->client->editMessage($chatId, $messageId, __('bot.act.nothing_to_undo'));

            return;
        }

        $this->client->editMessage($chatId, $messageId, implode("\n", [
            $this->confirmation($entry),
            '',
            __('bot.act.pick_category'),
        ]), $this->categoryPicker($entry, $user, $all));
    }

    /** File an entry the parser could not place, and remember the word. */
    private function assignCategory(int $chatId, int $messageId, User $user, int $entryId, int $categoryId): void
    {
        $entry = ActivityEntry::query()->where('user_id', $user->id)->find($entryId);
        $category = ActivityCategory::query()->where('user_id', $user->id)->find($categoryId);

        if ($entry === null || $category === null) {
            $this->client->editMessage($chatId, $messageId, __('bot.act.nothing_to_undo'));

            return;
        }

        $entry->update(['category_id' => $category->id]);

        $learned = $this->learn($category, $entry->note);

        $lines = [$this->confirmation($entry->fresh('category'))];

        if ($learned !== null) {
            $lines[] = '';
            $lines[] = __('bot.act.learned', ['word' => $learned, 'category' => $category->label()]);
        }

        $lines[] = '';
        $lines[] = $this->todayLine($user);

        $this->client->editMessage($chatId, $messageId, implode("\n", $lines), $this->afterSaveKeyboard($entry));
    }

    /**
     * Teach the activity the word the owner just corrected.
     *
     * Only a single plain word, and never one already known — the same rule as
     * the money side, for the same reason: a whole phrase would match nothing
     * next time.
     */
    private function learn(ActivityCategory $category, ?string $note): ?string
    {
        $word = Str::of((string) $note)->lower()->trim()->toString();

        if ($word === '' || Str::contains($word, ' ') || mb_strlen($word) < 3 || mb_strlen($word) > 24) {
            return null;
        }

        if (in_array($word, $category->matchWords(), true)) {
            return null;
        }

        $category->update(['keywords' => trim($category->keywords . ',' . $word, ',')]);

        return $word;
    }

    /** Leave the entry where it is, without an activity. */
    private function skipCategory(int $chatId, int $messageId, User $user, int $entryId): void
    {
        $entry = ActivityEntry::query()->where('user_id', $user->id)->with('category')->find($entryId);

        if ($entry === null) {
            $this->client->editMessage($chatId, $messageId, __('bot.act.nothing_to_undo'));

            return;
        }

        $this->client->editMessage($chatId, $messageId, implode("\n", [
            $this->confirmation($entry),
            '',
            $this->todayLine($user),
        ]), $this->afterSaveKeyboard($entry));
    }

    /** Remove an entry the bot itself added. */
    private function undo(int $chatId, int $messageId, User $user, int $entryId): void
    {
        $entry = ActivityEntry::query()
            ->where('user_id', $user->id)
            ->where('source', ActivitySource::Telegram->value)
            ->with('category')
            ->find($entryId);

        if ($entry === null) {
            $this->client->editMessage($chatId, $messageId, __('bot.act.nothing_to_undo'));

            return;
        }

        $label = $this->categoryLabel($entry->category);
        $duration = $entry->formattedDuration();

        $entry->delete();

        $this->client->editMessage($chatId, $messageId, implode("\n", [
            __('bot.act.undone', ['duration' => $duration, 'category' => $label]),
            '',
            $this->todayLine($user),
        ]), TelegramClient::keyboard([[
            TelegramClient::button(__('bot.btn.time'), 't:menu'),
            TelegramClient::button(__('bot.btn.home'), 'nav:menu'),
        ]]));
    }

    private function confirmation(ActivityEntry $entry): string
    {
        $duration = $entry->formattedDuration();

        $line = $entry->category === null
            ? __('bot.act.saved_uncategorised', ['duration' => $duration])
            : __('bot.act.saved', ['duration' => $duration, 'category' => $entry->category->label()]);

        return $entry->note === null
            ? $line
            : $line . "\n" . __('bot.fin.note_line', ['note' => e($entry->note)]);
    }

    private function afterSaveKeyboard(ActivityEntry $entry): array
    {
        return TelegramClient::keyboard([
            [TelegramClient::button(__('bot.btn.change_category'), "t:pick:{$entry->id}")],
            [
                TelegramClient::button(__('bot.btn.undo'), "t:undo:{$entry->id}"),
                TelegramClient::button(__('bot.btn.recent'), 't:recent'),
            ],
            [
                TelegramClient::button(__('bot.btn.time'), 't:menu'),
                TelegramClient::button(__('bot.btn.home'), 'nav:menu'),
            ],
        ]);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, ActivityCategory>  $categories
     * @return list<list<array<string, string>>>
     */
    private function buttonRows($categories, callable $data): array
    {
        $rows = [];
        $buttons = [];

        foreach ($categories as $category) {
            $buttons[] = TelegramClient::button($category->label(), $data($category));

            if (count($buttons) === 2) {
                $rows[] = $buttons;
                $buttons = [];
            }
        }

        if ($buttons !== []) {
            $rows[] = $buttons;
        }

        return $rows;
    }

    /**
     * @param  list<string>  $periods
     * @return list<array<string, string>>
     */
    private function periodButtons(array $periods): array
    {
        return array_map(fn (string $period): array => TelegramClient::button(
            __(match ($period) {
                'today' => 'bot.btn.act_today',
                'week' => 'bot.btn.week',
                default => 'bot.btn.month',
            }),
            't:' . ($period === 'today' ? 'day' : $period)
        ), $periods);
    }

    private function todayLine(User $user): string
    {
        $stats = new ActivityStats($user->id, $user->timezone);
        $today = $stats->today();

        return __('bot.act.today', [
            'duration' => ActivityEntry::duration($stats->summary($today, $today)['minutes']),
        ]);
    }

    private function categoryLabel(?ActivityCategory $category): string
    {
        return $category?->label() ?? __('bot.act.uncategorised');
    }

    /** A share as a bar, so eight rows read as shapes rather than as numbers. */
    private function bar(float $share): string
    {
        $filled = max(1, min(8, (int) round($share / 100 * 8)));

        return str_repeat('▓', $filled) . str_repeat('░', 8 - $filled);
    }

    private function hasAny(User $user): bool
    {
        return ActivityEntry::query()->where('user_id', $user->id)->exists();
    }

    private function pendingCategory(int $chatId, User $user): ?ActivityCategory
    {
        $id = Cache::get($this->pendingKey($chatId));

        return $id === null
            ? null
            : ActivityCategory::query()->where('user_id', $user->id)->find($id);
    }

    private function forgetPending(int $chatId): void
    {
        Cache::forget($this->pendingKey($chatId));
    }

    private function pendingKey(int $chatId): string
    {
        return "activity:pending-category:{$chatId}";
    }

    /** @param  array<string, mixed>  $keyboard */
    private function deliver(int $chatId, ?int $messageId, string $text, array $keyboard): void
    {
        $messageId === null
            ? $this->client->sendMessage($chatId, $text, $keyboard)
            : $this->client->editMessage($chatId, $messageId, $text, $keyboard);
    }
}
