<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewTicketController extends Controller
{
    public function index()
    {
        return view('ticket/new');
    }
    public function store(Request $request)
    {
        $ticket = new Ticket;
        $ticket->submitter_id = Auth::user()->id;
        $ticket->for_id = Auth::user()->id;
        $ticket->subject = $request->subject;
        $ticket->description = $request->description;
        $ticket->urgency = $request->urgency;
        $ticket->status = "Opened";
        $ticket->category_id = $request->category;
        $ticket->save();
        return redirect('dashboard')->with('status', 'Your request has been submitted and is being reviewed by the IT Team.');
    }
}
