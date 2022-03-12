<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Comment;
use App\Models\File;
use App\Models\AssignTicket;
use App\Mail\NewTicket;
use App\Mail\NewComment;
use App\Mail\CloseTicket;
use App\Models\TicketCategory;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;


class TicketController extends Controller
{
    public function index()
    {
        return view('ticket/new');
    }
    public function store(Request $request)
    {
        $settings = Setting::all();
        foreach ($settings as $setting){
            $setup = $setting;
        }

        $request->validate([
            'subject' => 'required|max:255',
            'description' => 'required',
            'urgency' => 'required|numeric',
            'category' => 'required|numeric',
            'for' => 'required|numeric',
        ]);

        $ticket = new Ticket;
        $ticket->submitter_id = Auth::user()->id;
        $ticket->subject = $request->subject;
        $ticket->description = $request->description;
        $ticket->urgency = $request->urgency;
        $ticket->status = "Opened";
        $ticket->category_id = $request->category;
        $ticket->for_id = $request->for;
        $ticket->save();
        if($setup->default_assignee != NULL){
            $assignee = new AssignTicket;
            $assignee->ticket_id = $ticket->id;
            $assignee->user_id = $setup->default_assignee;
            $assignee->save();
        }

        if($request->hasFile('files'))
        {
            $allowedfileExtension=['pdf','docx', 'doc', 'txt', 'jpg', 'jpeg', 'png', 'bmp', 'gif', 'svg', 'webp'];
            $files = $request->file('files');
            foreach($files as $file){
                $originalName= $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $check=in_array($extension,$allowedfileExtension);
                //dd($check);
                if($check)
                {
                    //$filename = Storage::disk('local')->put('tickets/', $request->ticket_id . "-" . $originalName . $extension);
                    $name = $ticket->id . "-" . Carbon::now() . "-" . $originalName;
                    //dd($file);
                    $file->storeAs('tickets/', $name);

                    $newfile = new File;
                    $newfile->user_id = Auth::user()->id;
                    $newfile->ticket_id = $ticket->id;
                    $newfile->file_name= $name;
                    $newfile->save();
                }
            }
        }

        $tickets = Ticket::join('users as ts', 'submitter_id', '=', 'ts.id')
        ->join('users as tf', 'for_id', '=', 'tf.id')
        ->join('ticket_categories as tc', 'tickets.category_id', '=', 'tc.id')
        ->select('tickets.id', 'tf.email', 'tc.category_name', 'tickets.urgency', 'tickets.subject', 'tickets.description', 'ts.name')
        ->where('tickets.id', '=', $ticket->id)
        ->get();
        foreach($tickets as $t)
            Mail::to($setup->default_email)->send(new NewTicket($t));



        return redirect('dashboard')->with('status', 'Your request has been submitted and is being reviewed by the IT Team.');
    }
    public function list()
    {

        $tickets = Ticket::join('users as ts', 'submitter_id', '=', 'ts.id')
        ->join('users as tf', 'for_id', '=', 'tf.id')
        ->join('ticket_categories as tc', 'tickets.category_id', '=', 'tc.id')
        ->select('tickets.id','tf.name as ticket_for', 'ts.name as ticket_submitter', 'tc.category_name', 'tickets.urgency', 'tickets.updated_at', 'tickets.status', 'tickets.subject')
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
            'comment' => 'required',
        ]);

        $comment = new Comment;
        $comment->ticket_id = $request->ticket_id;
        $comment->user_id = Auth::user()->id;
        $comment->comment = $request->comment;
        $comment->save();

        if($request->hasFile('files'))
        {
            $allowedfileExtension=['pdf','docx', 'doc', 'txt', 'jpg', 'jpeg', 'png', 'bmp', 'gif', 'svg', 'webp'];
            $files = $request->file('files');
            foreach($files as $file){
                $originalName= $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $check=in_array($extension,$allowedfileExtension);
                //dd($check);
                if($check)
                {
                    //$filename = Storage::disk('local')->put('tickets/', $request->ticket_id . "-" . $originalName . $extension);
                    $name = $request->ticket_id . "-" . Carbon::now() . "-" . $originalName;
                    //dd($file);
                    $file->storeAs('tickets/', $name);

                    $newfile = new File;
                    $newfile->user_id = Auth::user()->id;
                    $newfile->comment_id = $comment->id;
                    $newfile->file_name= $name;
                    $newfile->save();
                }
            }
        }



        if(Auth::user()->role == "IT"){
            Ticket::where('id', $request->ticket_id)->update(['updated_at' => Carbon::now(), 'status' => "Replied"]);
        } else{
            Ticket::where('id', $request->ticket_id)->update(['updated_at' => Carbon::now(), 'status' => "Pending Response"]);
        }

        $comments = Comment::join('users', 'user_id', '=', 'users.id')
        ->join('tickets', 'ticket_id', '=', 'tickets.id')
        ->join('users as tf', 'tickets.for_id', '=', 'tf.id')
        ->join('assign_tickets', 'comments.ticket_id', '=', 'assign_tickets.ticket_id')
        ->join('users as au', 'assign_tickets.user_id', '=', 'au.id')
        ->select('tickets.id', 'comments.comment', 'users.name', 'tickets.urgency', 'tickets.subject', 'tf.email', 'au.email as auemail')
        ->where('comments.id', $comment->id)
        ->get();
        foreach($comments as $c){
            foreach([$c->email, $c->auemail] as $receiver)
                Mail::to($receiver)->send(new NewComment($c));
        }



        return redirect('ticket/view/' . $request->ticket_id);
    }
    public function close_ticket(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|numeric'
        ]);
        Ticket::where('id', $request->ticket_id)->update(['updated_at' => Carbon::now(), 'status' => "Closed"]);

        $tickets = Ticket::join('users as ts', 'submitter_id', '=', 'ts.id')
        ->join('users as tf', 'for_id', '=', 'tf.id')
        ->join('ticket_categories as tc', 'tickets.category_id', '=', 'tc.id')
        ->select('tickets.id', 'tf.name', 'tf.email', 'tickets.subject')
        ->where('tickets.id', '=', $request->ticket_id)
        ->get();
        foreach($tickets as $t)
            Mail::to($t->email)->send(new CloseTicket($t, Auth::user()->name));
        return redirect('dashboard');
    }
    public function download(Request $request)
    {
        $file = File::select('file_name', 'user_id', 'ticket_id', 'comment_id')->where('id', '=', $request->id)->get();
        foreach($file as $file){
            if($file->ticket_id != NULL)
                $ticket = Ticket::select('id', 'submitter_id', 'for_id')->where('id', '=', $file->ticket_id)->get();
            else
            {
                $ticket = DB::table('comments')
                ->join('tickets', 'ticket_id', 'tickets.id')
                ->where('comments.id', '=', $file->comment_id)
                ->select('tickets.submitter_id as submitter_id', 'tickets.for_id as for_id')->get();
            }

            foreach($ticket as $ticket){
                echo $ticket->for_id;
                if(Auth::user()->role == "IT" || Auth::user()->id == $file->user_id || $ticket->submitter_id == Auth::user()->id ||
                $ticket->for_id == Auth::user()->id)
                    return Storage::download('tickets/' . $file->file_name);
                else
                    return redirect('dashboard')->with('status', 'No access');
            }

        }
    }
    public function categories()
    {
        $categories = TicketCategory::select('category_name')->get();
        return view('ticket.categories', $categories);
    }

    public function category_store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|unique:ticket_categories|max:75',
        ]);
        $category = new TicketCategory;
        $category->category_name = $request->category_name;
        $category->save();

        return redirect('/tickets/categories');
    }
}
