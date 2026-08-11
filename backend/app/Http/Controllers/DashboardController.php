<?php

namespace App\Http\Controllers;

use App\Models\Beyond;
use App\Models\ContactMessage;
use App\Models\Highlight;
use App\Models\Journey;
use App\Models\ProcessStep;
use App\Models\Project;
use App\Models\Service;
use App\Models\ServiceOrder;
use App\Models\Stat;
use App\Models\TechStack;
use App\Models\Plan;
use App\Services\PlanStats;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Plan's own numbers sit above the content counts: the panel is opened
        // in the morning to see the day, not to count portfolio entries.
        $stats = new PlanStats((int) $user->id);
        $today = CarbonImmutable::today($user->timezone ?? config('app.timezone'));

        $plan = [
            'today' => $stats->summary($today, $today),
            'month' => $stats->summary($today->startOfMonth(), $today->endOfMonth()),
            'trend' => $stats->dailyTrend(14),
            'goals' => $stats->byGoal($today),
            'upcoming' => Plan::query()
                ->where('user_id', $user->id)
                ->open()
                ->where('date', '>=', $today->toDateString())
                ->with('goal:id,title,color')
                ->orderBy('date')
                ->orderBy('start_time')
                ->take(5)
                ->get(),
        ];

        $counts = [
            'projects'      => Project::count(),
            'services'      => Service::count(),
            'tech-stacks'   => TechStack::count(),
            'stats'         => Stat::count(),
            'highlights'    => Highlight::count(),
            'journeys'      => Journey::count(),
            'beyonds'       => Beyond::count(),
            'process-steps' => ProcessStep::count(),
        ];

        $inbox = [
            'contact' => [
                'total'  => ContactMessage::count(),
                'unread' => ContactMessage::whereNull('read_at')->count(),
            ],
            'orders' => [
                'total'  => ServiceOrder::count(),
                'unread' => ServiceOrder::whereNull('read_at')->count(),
            ],
        ];

        // A single mixed "latest activity" list: the two form types share the
        // fields that matter here, and the editor cares about what arrived
        // last, not which table it landed in.
        $recent = ContactMessage::latest()->take(5)->get()
            ->map(fn(ContactMessage $message) => [
                'type'    => 'contact',
                'id'      => $message->id,
                'name'    => $message->name,
                'email'   => $message->email,
                'summary' => $message->subject ?: $message->message,
                'unread'  => $message->read_at === null,
                'date'    => $message->created_at,
            ])
            ->concat(
                ServiceOrder::latest()->take(5)->get()
                    ->map(fn(ServiceOrder $order) => [
                        'type'    => 'orders',
                        'id'      => $order->id,
                        'name'    => $order->name,
                        'email'   => $order->email,
                        'summary' => $order->service_name ?: $order->message,
                        'unread'  => $order->read_at === null,
                        'date'    => $order->created_at,
                    ])
            )
            ->sortByDesc('date')
            ->take(6)
            ->values();

        return view('dashboard.index', compact('user', 'counts', 'inbox', 'recent', 'plan'));
    }
}
