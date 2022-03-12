@php
use App\Models\AssignTicket;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Technical Support Request') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="container">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                    @foreach ($ticket as $ticket)
                    <div class="row">

                        <div class="col-4">
                            Status: {{$ticket->status}}<br>
                            Ticket For: {{$ticket->ticket_for}}<br>
                            Submitted By:
                            {{$ticket->ticket_submitter}}
                            <br>
                            Category: {{$ticket->category_name}}
                            Urgency: {{$ticket->urgency}}<br>
                            Time Submitted: {{$ticket->created_at}}<br>
                            Last Updated: {{$ticket->updated_at}}<br>
                            Assigned User:

                            @php
                               $assignees = DB::table('assign_tickets')->join('users', 'user_id', 'users.id')->where('ticket_id', '=', $ticket->id)->select('users.name as name')->get();
                            @endphp
                            @if (count($assignees) < 1)
                                Unassigned<br><hr>
                                @if (Auth::user()->role == "IT")
                                    <form name="assignUser" id="assignUser" method="post" action="{{url('ticket/assign')}}">
                                        @csrf
                                        <div class="form-group">
                                            <label for="assignUser">Assign User</label>
                                            <select name="user" class="form-control" required>
                                                @foreach (DB::table('users')->where('role', '=', 'IT')->select('id', 'name')->get() as $it)
                                                <option value={{$it->id}}>{{$it->name}}</option>
                                                @endforeach
                                            </select>
                                            <input hidden name="ticket_id" value={{$ticket->id}}>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Assign</button>
                                    </form>
                                @endif
                            @else
                                @foreach ($assignees as $assignee)
                                    {{$assignee->name}} <br><hr>
                                    @if (Auth::user()->role == "IT")
                                <form name="assignUser" id="assignUser" method="post" action="{{url('ticket/assign/update')}}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="assignUser">Assign User</label>
                                        <select name="user" class="form-control" required>
                                            @foreach (DB::table('users')->where('role', '=', 'IT')->select('id', 'name')->get() as $it)
                                            <option value={{$it->id}}>{{$it->name}}</option>
                                            @endforeach
                                        </select>
                                        <input hidden name="ticket_id" value={{$ticket->id}}>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Assign</button>
                                </form>
                            @endif
                                @endforeach
                            @endif
                            @if ($ticket->status != "Closed")
                            <form name="close" id="close" method="post" action="{{url('ticket/close')}}">
                                @csrf
                                <div class="form-group">
                                    <input hidden name="ticket_id" value={{$ticket->id}}>
                                </div>
                                <button type="submit" class="btn btn-primary">Close Ticket</button>
                            </form>
                            @endif

                        </div>
                        <div class="col-8">
                            <div class="row">
                                <div class="col-2">
                                    <b>Subject:</b>
                                </div>
                                <div class="col-7">
                                    {{$ticket->subject}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-2">
                                    <b>Description:</b>
                                    @foreach (DB::table('files')
                                        ->where('ticket_id', '=', $ticket->id)
                                        ->select('file_name', 'id')
                                        ->get() as $file)
                                            <br><a href="/ticket/file/{{$file->id}}">{{$file->file_name}}</a>
                                        @endforeach
                                </div>
                                <div class="col-7">
                                    <p>
                                    {{$ticket->description}}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div><hr>
                    <div class="row">
                        <div class="col">
                            <b>Comments:</b><hr>
                            @foreach (DB::table('comments')
                            ->join('users', 'user_id', 'users.id')
                            ->where('ticket_id', '=', $ticket->id)
                            ->select('users.name as name', 'comments.comment as comment', 'comments.created_at as time', 'comments.id')
                            ->orderBy('time', 'desc')
                            ->get() as $comment)
                                <div class="row">
                                    <div class="col-2">
                                        <b>{{$comment->name}}</b>
                                    </div>
                                    <div class="col-3">
                                        {{$comment->time}}
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-10">
                                        {{$comment->comment}}
                                        @foreach (DB::table('files')
                                        ->where('comment_id', '=', $comment->id)
                                        ->select('file_name', 'id')
                                        ->get() as $file)
                                            <br><a href="/ticket/file/{{$file->id}}">{{$file->file_name}}</a>
                                        @endforeach
                                    </div>
                                </div><hr>
                            @endforeach
                        </div>
                    </div><hr>
                    <div class="row">
                        <div class="col">
                            <form name="comments" id="comment" method="post" action="{{url('ticket/comment/store')}}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="comments">New Comment</label>
                                    <textarea name="comment" class="form-control" required></textarea>
                                </div>
                                <input hidden name="ticket_id" value={{$ticket->id}}>
                                <input type="file" multiple name="files[]" class="form-control">
                                <button type="submit" class="btn btn-primary">Comment</button>
                            </form>
                        </div>
                    </div>
            @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
