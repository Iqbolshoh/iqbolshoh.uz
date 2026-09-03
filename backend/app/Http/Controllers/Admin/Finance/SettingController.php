<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinanceSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * The owner's rules for their own money: the monthly ceiling, when to be
 * warned, and whether the bot should ask about the day in the evening.
 */
class SettingController extends Controller
{
    public function index(): View
    {
        abort_unless(Auth::user()?->can('finance-settings.view'), 403);

        return view('admin.finance.settings', [
            'settings' => FinanceSetting::forUser(Auth::id()),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()?->can('finance-settings.edit'), 403);

        $data = $request->validate([
            'monthly_budget' => ['nullable', 'integer', 'min:0', 'max:999999999999'],
            'warn_at_percent' => ['required', 'integer', 'min:1', 'max:200'],
            'prompt_time' => ['required', 'date_format:H:i'],
        ]);

        FinanceSetting::forUser(Auth::id())->update($data + [
            'daily_prompt' => $request->boolean('daily_prompt'),
            'weekly_report' => $request->boolean('weekly_report'),
            'monthly_report' => $request->boolean('monthly_report'),
        ]);

        return redirect()->route('admin.finance-settings.index')->with('success', 'Finance settings saved.');
    }
}
