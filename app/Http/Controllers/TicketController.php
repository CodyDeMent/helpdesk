<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Comment;
use App\Models\AssignTicket;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class TicketController extends Controller
{
    public function index()
    {
        return view('ticket/new');
    }
    public function store(Request $request)
    {

        $request->validate([
            'subject' => 'required|max:255',
            'description' => 'required',
            'urgency' => 'required|numeric',
            'category' => 'required|numeric'
        ]);

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
    public function list()
    {

        $tickets = Ticket::join('users as ts', 'submitter_id', '=', 'ts.id')
        ->join('users as tf', 'for_id', '=', 'tf.id')
        ->join('ticket_categories as tc', 'tickets.category_id', '=', 'tc.id')
        ->select('tickets.id','tf.name as ticket_for', 'ts.name as ticket_submitter', 'tc.category_name', 'tickets.urgency', 'tickets.updated_at', 'tickets.status')
        ->get();

        //return $tickets;
        return view('ticket.list', ['tickets' => $tickets]);
    }
    public function view(Request $request)
    {
        $tickets = Ticket::join('users as ts', 'submitter_id', '=', 'ts.id')
        ->join('users as tf', 'for_id', '=', 'tf.id')
        ->join('ticket_categories as tc', 'tickets.category_id', '=', 'tc.id')
        ->select('tickets.id','tf.name as ticket_for', 'ts.name as ticket_submitter',
         'tc.category_name', 'tickets.urgency', 'tickets.updated_at', 'tickets.status',
         'tickets.subject', 'tickets.description', 'tickets.submitter_id', 'tickets.for_id','tickets.created_at', 'tickets.status')
        ->where('tickets.id', '=', $request->id)
        ->get();
        foreach($tickets as $ticket){
            if($ticket->submitter_id == Auth::user()->id || $ticket->for_id == Auth::user()->id || Auth::user()->role == "IT")
                return view('ticket.view', ['ticket' => $tickets]);
            else
                return redirect('dashboard')->with('status', 'You do not have access to view this ticket.');
        }

    }
    public function assign(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|numeric',
            'user' => 'required|numeric'
        ]);


        $assignUser = new AssignTicket;
        $assignUser->ticket_id = $request->ticket_id;
        $assignUser->user_id = $request->user;
        $assignUser->save();

        return redirect('ticket/view/' . $request->ticket_id);
    }
    public function update_assign(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|numeric',
            'user' => 'required|numeric'
        ]);

        AssignTicket::where('ticket_id', $request->ticket_id)->update(['user_id' => $request->user]);

        return redirect('ticket/view/' . $request->ticket_id);
    }
    public function store_comment(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|numeric',
            'comment' => 'required'
        ]);

        $comment = new Comment;
        $comment->ticket_id = $request->ticket_id;
        $comment->user_id = Auth::user()->id;
        $comment->comment = $request->comment;
        $comment->save();

        if(Auth::user()->role == "IT"){
            Ticket::where('id', $request->ticket_id)->update(['updated_at' => Carbon::now(), 'status' => "Replied"]);
        } else{
            Ticket::where('id', $request->ticket_id)->update(['updated_at' => Carbon::now(), 'status' => "Pending Response"]);
        }


        return redirect('ticket/view/' . $request->ticket_id);
    }
    public function close_ticket(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|numeric'
        ]);
        Ticket::where('id', $request->ticket_id)->update(['updated_at' => Carbon::now(), 'status' => "Closed"]);
        return redirect('dashboard');
    }
}