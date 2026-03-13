<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use Illuminate\Http\Request;

class LogicController extends Controller
{
    public function addagent(Request $request)
    {
        $request->validate(['agent' => 'required|string']);

        Agent::create([
            'agent' => $request->agent,
        ]);

        return back()->with('success', 'Agent added successfully.');
    }

    public function viewagent($agent)
    {
        $agentRecord = Agent::where('agent', $agent)->firstOrFail();
        return view('admin.agents.view', compact('agentRecord'));
    }

    public function delagent($id)
    {
        Agent::findOrFail($id)->delete();
        return back()->with('success', 'Agent deleted.');
    }
}
