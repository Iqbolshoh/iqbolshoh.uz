<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Inbox for everything the site's forms submit: contact messages and service
 * orders. Both are read-only records — they can be opened, marked read and
 * deleted, but never edited, so the archive stays a faithful copy of what the
 * visitor actually sent.
 */
class MessageController extends Controller
{
    private const TYPES = [
        'contact' => [
            'model'    => ContactMessage::class,
            'singular' => 'Xabar',
            'plural'   => 'Aloqa xabarlari',
            'icon'     => 'mail',
        ],
        'orders' => [
            'model'    => ServiceOrder::class,
            'singular' => 'Buyurtma',
            'plural'   => 'Xizmat buyurtmalari',
            'icon'     => 'shopping-bag',
        ],
    ];

    public function index(Request $request, string $type)
    {
        $config = $this->config($type);
        abort_unless(Auth::user()?->can('messages.view'), 403);

        $model = $config['model'];
        $query = $model::query();

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->input('filter') === 'unread') {
            $query->whereNull('read_at');
        }

        $messages = $query->latest()->paginate(15)->withQueryString();

        return view('admin.messages.index', [
            'messages' => $messages,
            'type'     => $type,
            'config'   => $config,
            'unread'   => $model::whereNull('read_at')->count(),
        ]);
    }

    public function show(string $type, int $id)
    {
        $config = $this->config($type);
        abort_unless(Auth::user()?->can('messages.view'), 403);

        $model   = $config['model'];
        $message = $model::findOrFail($id);

        // Opening a message is what marks it read — there is no separate action.
        if (! $message->read_at) {
            $message->update(['read_at' => now()]);
        }

        return view('admin.messages.show', [
            'message' => $message,
            'type'    => $type,
            'config'  => $config,
        ]);
    }

    public function destroy(string $type, int $id)
    {
        $config = $this->config($type);
        abort_unless(Auth::user()?->can('messages.delete'), 403);

        $model = $config['model'];
        $model::findOrFail($id)->delete();

        return redirect()->route('admin.messages.index', $type)
            ->with('success', $config['singular'] . ' o\'chirildi.');
    }

    public function markAllRead(string $type)
    {
        $config = $this->config($type);
        abort_unless(Auth::user()?->can('messages.view'), 403);

        $model = $config['model'];
        $model::whereNull('read_at')->update(['read_at' => now()]);

        return redirect()->route('admin.messages.index', $type)
            ->with('success', 'Hammasi o\'qilgan deb belgilandi.');
    }

    private function config(string $type): array
    {
        abort_unless(isset(self::TYPES[$type]), 404);

        return self::TYPES[$type];
    }

    /** Unread counters for the sidebar badges. */
    public static function unreadCounts(): array
    {
        return [
            'contact' => ContactMessage::whereNull('read_at')->count(),
            'orders'  => ServiceOrder::whereNull('read_at')->count(),
        ];
    }

    /** Unread submissions for the header bell, newest first. */
    public static function latestUnread(int $limit = 5): array
    {
        $contact = ContactMessage::whereNull('read_at')->latest()->take($limit)->get()
            ->map(fn(ContactMessage $message) => [
                'type'    => 'contact',
                'id'      => $message->id,
                'title'   => $message->name,
                'summary' => $message->subject ?: $message->message,
                'date'    => $message->created_at,
            ]);

        $orders = ServiceOrder::whereNull('read_at')->latest()->take($limit)->get()
            ->map(fn(ServiceOrder $order) => [
                'type'    => 'orders',
                'id'      => $order->id,
                'title'   => $order->name,
                'summary' => $order->service_name ?: $order->message,
                'date'    => $order->created_at,
            ]);

        return $contact->concat($orders)->sortByDesc('date')->take($limit)->values()->all();
    }
}
