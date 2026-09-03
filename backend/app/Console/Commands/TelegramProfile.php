<?php

namespace App\Console\Commands;

use App\Services\TelegramClient;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Push the bot's public face to Telegram.
 *
 * The name on the chat header, the text on an empty chat, and the "/" menu are
 * not part of this codebase at runtime: Telegram stores them, and a fresh
 * token starts with none of them. That is why they are kept in the `lang` files
 * and shipped from here — otherwise the only record of the bot's own wording
 * would be Telegram's copy of it.
 *
 * Two rules the Bot API does not enforce but punishes:
 *
 * 1. Every list is stored per language code, and a client whose language has
 *    no list of its own reads the DEFAULT scope. Push only the four languages
 *    and anyone on a fifth stays on whatever was there before — for this bot,
 *    nothing at all.
 * 2. Telegram files Tajik under "tg". This app's translation files say "tj",
 *    so the two are mapped rather than assumed equal.
 */
class TelegramProfile extends Command
{
    protected $signature = 'telegram:profile
        {--commands : Push only the "/" menu, leaving the name and descriptions alone}
        {--dry-run : Print what would be sent and send nothing}';

    protected $description = 'Push the bot name, description and command menu to Telegram, in every language';

    /** This app's locale => the language code Telegram files it under. */
    private const LANGUAGES = ['uz' => 'uz', 'ru' => 'ru', 'en' => 'en', 'tj' => 'tg'];

    /**
     * The commands offered in the "/" menu, in the order they are shown.
     *
     * Aliases the bot also answers (/pul, /til) are deliberately absent: the
     * menu is a list to pick from, and offering the same action twice under
     * two names makes it read as two different things.
     */
    private const COMMANDS = ['menu', 'today', 'tomorrow', 'status', 'stats', 'money', 'language'];

    /** Telegram's own limits. Over any of them the whole call is rejected. */
    private const LIMITS = ['name' => 64, 'short' => 120, 'description' => 512];

    /** The avatar, kept next to the code so a fresh token can be dressed again. */
    private const AVATAR = 'telegram/avatar.png';

    public function __construct(private readonly TelegramClient $client)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! $this->client->isConfigured()) {
            $this->error('TELEGRAM_BOT_TOKEN is not set.');

            return self::FAILURE;
        }

        // The default scope first, in the app's own language: it is the one
        // that answers for every client this bot has no translation for.
        $scopes = ['' => config('app.locale')] + array_flip(self::LANGUAGES);
        $failed = false;

        foreach ($scopes as $code => $locale) {
            $label = $code === '' ? "default ({$locale})" : $code;

            $failed = ! $this->push((string) $code, (string) $locale, $label) || $failed;
        }

        // The name and the avatar are not stored per language, so they are
        // sent once rather than in every scope — five identical writes would
        // only spend the tight rate limit setMyName is given.
        if (! $this->option('commands')) {
            $failed = ! $this->pushIdentity() || $failed;
        }

        if ($this->option('dry-run')) {
            $this->newLine();
            $this->warn('Dry run: nothing was sent.');
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    /** Everything Telegram stores for one language. */
    private function push(string $code, string $locale, string $label): bool
    {
        $commands = $this->commands($locale);
        $this->line("<info>{$label}</info> — " . count($commands) . ' ' . Str::plural('command', $commands));

        if ($this->option('dry-run')) {
            foreach ($commands as $command) {
                $this->line("  /{$command['command']} — {$command['description']}");
            }

            return $this->option('commands') || $this->reportLengths($locale);
        }

        $ok = $this->send('setMyCommands', fn () => $this->client->setMyCommands($commands, $code));

        if ($this->option('commands')) {
            return $ok;
        }

        if (! $this->reportLengths($locale)) {
            return false;
        }

        $texts = $this->texts($locale);

        $ok = $this->send('setMyDescription', fn () => $this->client->setMyDescription($texts['description'], $code)) && $ok;
        $ok = $this->send('setMyShortDescription', fn () => $this->client->setMyShortDescription($texts['short'], $code)) && $ok;

        return $ok;
    }

    /** The half of the profile that is the same in every language. */
    private function pushIdentity(): bool
    {
        $name = (string) config('services.telegram.bot_name');
        $avatar = resource_path(self::AVATAR);

        $this->newLine();
        $this->line("<info>name</info> — {$name}");
        $this->line('<info>avatar</info> — ' . self::AVATAR);

        if (mb_strlen($name) > self::LIMITS['name']) {
            $this->error("  the bot name is " . mb_strlen($name) . " characters, over Telegram's " . self::LIMITS['name']);

            return false;
        }

        if (! is_file($avatar)) {
            $this->error("  {$avatar} is missing");

            return false;
        }

        if ($this->option('dry-run')) {
            return true;
        }

        // setMyName is rate limited far more tightly than the rest, so a
        // refusal here must not stop the avatar from going out.
        $named = $this->send('setMyName', fn () => $this->client->setMyName($name));
        $shown = $this->send('setMyProfilePhoto', fn () => $this->client->setMyProfilePhoto($avatar));

        return $named && $shown;
    }

    /** @return list<array{command: string, description: string}> */
    private function commands(string $locale): array
    {
        return array_map(fn (string $name) => [
            'command' => $name,
            'description' => $this->line_("bot.cmd.{$name}", $locale),
        ], self::COMMANDS);
    }

    /** @return array{short: string, description: string} */
    private function texts(string $locale): array
    {
        return [
            'short' => $this->line_('bot.profile.short', $locale),
            'description' => $this->line_('bot.profile.description', $locale),
        ];
    }

    /**
     * Refuse to send a line Telegram will reject.
     *
     * Checked here rather than left to the API because the failure is silent
     * in practice: one over-long description fails, the other languages
     * succeed, and the bot is left half-translated with a green exit code.
     */
    private function reportLengths(string $locale): bool
    {
        $ok = true;

        foreach ($this->texts($locale) as $field => $text) {
            $length = mb_strlen($text);

            if ($length > self::LIMITS[$field]) {
                $this->error("  bot.profile.{$field} in {$locale} is {$length} characters, over Telegram's " . self::LIMITS[$field]);
                $ok = false;
            }
        }

        return $ok;
    }

    /** A translation read in one language regardless of the app's own locale. */
    private function line_(string $key, string $locale): string
    {
        return (string) __($key, [], $locale);
    }

    /** @param  callable(): ?\Illuminate\Http\Client\Response  $call */
    private function send(string $method, callable $call): bool
    {
        $response = $call();

        if ($response?->json('ok') === true) {
            return true;
        }

        $this->error("  {$method} refused: " . ($response?->json('description') ?? 'no response'));

        return false;
    }
}
