<?php

namespace App\Http\Controllers;

use App\Models\TechnicalSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TechnicalSupportController extends Controller
{
    public function index()
    {
        return view('ticket/new/technical-support');
    }
    public function store(Request $request)
    {
        $ticket = new TechnicalSupport;
        $ticket->user_id = Auth::user()->id;
        $ticket->subject = $request->subject;
        $ticket->description = $request->description;
        $ticket->urgency = $request->urgency;
        $ticket->status = "Active";
        $ticket->save();
        return redirect('dashboard')->with('status', 'Your request has been submitted and is being reviewed by the IT Team.');
    }
}