<?php

namespace App\Http\Controllers\Admin\Plan;

use App\Enums\NotificationKind;
use App\Enums\NotificationStatus;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\TelegramSender;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Everything the system has tried to send, and what happened to it.
 *
 * A failed row is the point of the page: it keeps a delivery problem visible
 * instead of leaving it in a log file nobody opens.
 */
class NotificationController extends Controller
{
    public function __construct(private readonly TelegramSender $telegram) {}

    public function index(Request $request): View
    {
        $this->authorizeAction('view');

        $notifications = Notification::query()
            ->where('user_id', Auth::id())
            ->when($request->input('kind'), fn ($query, $kind) => $query->where('kind', $kind))
            ->when($request->input('status'), fn ($query, $status) => $query->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        return view('admin.plan.notifications', [
            'notifications' => $notifications,
            'kinds' => NotificationKind::cases(),
            'statuses' => NotificationStatus::cases(),
            'filters' => $request->only('kind', 'status'),
            'failedCount' => Notification::query()->where('user_id', Auth::id())->failed()->count(),
        ]);
    }

    public function retry(Notification $notification): RedirectResponse
    {
        $this->authorizeAction('retry');
        $this->authorizeOwnership($notification);

        $this->telegram->deliver($notification);

        return back()->with(
            'success',
            $notification->refresh()->status === NotificationStatus::Sent
                ? 'Notification sent.'
                : 'Still failing: ' . $notification->error
        );
    }

    public function destroy(Notification $notification): RedirectResponse
    {
        $this->authorizeAction('delete');
        $this->authorizeOwnership($notification);

        $notification->delete();

        return back()->with('success', 'Notification deleted.');
    }

    private function authorizeAction(string $action): void
    {
        abort_unless(Auth::user()?->can("notifications.{$action}"), 403);
    }

    private function authorizeOwnership(Notification $notification): void
    {
        abort_unless($notification->user_id === Auth::id(), 403);
    }
}
