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
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

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

        return view('dashboard.index', compact('user', 'counts', 'inbox', 'recent'));
    }
}
