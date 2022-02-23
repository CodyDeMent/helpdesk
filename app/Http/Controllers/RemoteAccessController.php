<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RemoteAccess;
use Illuminate\Support\Facades\Auth;

class RemoteAccessController extends Controller
{
    public function index()
    {
        return view('ticket/new/remote-access');
    }
    public function store(Request $request)
    {
        $ticket = new RemoteAccess;
        $ticket->user_id = Auth::user()->id;
        $ticket->ip_address = $request->ip();
        $ticket->reason = $request->reason;
        $ticket->urgency = $request->urgency;
        $ticket->status = "Active";
        $ticket->save();
        return redirect('dashboard')->with('status', 'Your request has been submitted and is being reviewed by the IT Team.');
    }
}
