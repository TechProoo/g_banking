<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function updatesettings(Request $request)
    {
        Settings::where('id', 1)->update($request->except(['_token', '_method']));
        return back()->with('success', 'Settings updated.');
    }

    public function updateasset(Request $request)
    {
        Settings::where('id', 1)->update($request->only([
            'btc_address', 'eth_address', 'ltc_address',
            'bank_name', 'account_name', 'account_number',
        ]));
        return back()->with('success', 'Asset settings updated.');
    }

    public function updatemarket(Request $request)
    {
        Settings::where('id', 1)->update($request->only([
            'trade_mode', 'weekend_trade', 'payment_mode',
        ]));
        return back()->with('success', 'Market settings updated.');
    }

    public function updatefee(Request $request)
    {
        Settings::where('id', 1)->update($request->only([
            'commission_type', 'commission_fee',
            'monthlyfee', 'quarterlyfee', 'yearlyfee',
        ]));
        return back()->with('success', 'Fee settings updated.');
    }
}
