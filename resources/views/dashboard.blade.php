@php
    if (Auth::user()->role == "IT") {
        $assignedTickets = DB::table('tickets')
        ->join('assign_tickets', 'tickets.id', '=', 'assign_tickets.ticket_id')
        ->join('users as tf', 'tickets.for_id', '=', 'tf.id')
        ->join('ticket_categories as tc', 'tickets.category_id', '=', 'tc.id')
        ->where('assign_tickets.user_id', Auth::user()->id)
        ->select('tickets.id', 'tf.name as ticket_for', 'tc.category_name as category_name', 'tickets.urgency', 'tickets.updated_at', 'tickets.status', 'tickets.subject')
        ->orderByDesc('updated_at')
        ->get();

        //dd($assignedTickets);
    }
    $tickets = DB::table('tickets')->join('users as tf', 'for_id', '=', 'tf.id')
        ->join('ticket_categories as tc', 'tickets.category_id', '=', 'tc.id')
        ->where('tickets.for_id', Auth::user()->id)
        ->select('tickets.id', 'tf.name as ticket_for', 'tc.category_name', 'tickets.urgency', 'tickets.updated_at', 'tickets.status')
        ->orderByDesc('updated_at')
        ->get();
        //dd($tickets);
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="container">
                    @if (count($tickets) < 1)
                        No Tickets Available
                    @else
                    @if(Auth::user()->role == "IT")
                    <div class="col">
                        <b>My Assigned Tickets</b>
                    </div>
                    <div class="row">
                        <div class="col">
                            Subject
                        </div>
                        <div class="col">
                            Category
                        </div>
                        <div class="col">
                            Urgency
                        </div>
                        <div class="col">
                            User
                        </div>
                        <div class="col-3">
                            Last Update
                        </div>
                        <div class="col">
                            Status
                        </div>
                        <div class="col">
                        </div>
                    </div>
                    <hr>
                    @foreach ($assignedTickets as $ticket)
                        @if ($ticket->status != "Closed")
                            <div class="row">
                                <div class="col">
                                    {{$ticket->subject}}
                                </div>
                                <div class="col">
                                    {{$ticket->category_name}}
                                </div>
                                <div class="col">
                                    {{$ticket->urgency}}
                                </div>
                                <div class="col">
                                    {{$ticket->ticket_for}}
                                </div>
                                <div class="col-3">
                                    {{$ticket->updated_at}}
                                </div>
                                <div class="col">
                                    {{$ticket->status}}
                                </div>
                                <div class="col">
                                    <p onclick="location.href='/ticket/view/{{$ticket->id}}'" class="pointer">View Ticket</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    @endif
                    <div class="col">
                        <b>My Open Tickets</b>
                    </div>
                    <div class="row">
                        <div class="col">
                            Category
                        </div>
                        <div class="col">
                            Urgency
                        </div>
                        <div class="col-3">
                            Last Update
                        </div>
                        <div class="col">
                            Status
                        </div>
                        <div class="col">
                        </div>
                    </div>
                    <hr>
                    @foreach ($tickets as $ticket)
                        @if ($ticket->status != "Closed")
                            <div class="row">
                                <div class="col">
                                    {{$ticket->category_name}}
                                </div>
                                <div class="col">
                                    {{$ticket->urgency}}
                                </div>
                                <div class="col-3">
                                    {{$ticket->updated_at}}
                                </div>
                                <div class="col">
                                    {{$ticket->status}}
                                </div>
                                <div class="col">
                                    <p onclick="location.href='/ticket/view/{{$ticket->id}}'" class="pointer">View Ticket</p>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    <div class="col">
                        <b>My Closed Tickets</b>
                    </div>
                    <div class="row">
                        <div class="col">
                            Category
                        </div>
                        <div class="col">
                            Urgency
                        </div>
                        <div class="col-3">
                            Last Update
                        </div>
                        <div class="col">
                            Status
                        </div>
                        <div class="col">
                        </div>
                    </div>
                    <hr>
                    @foreach ($tickets as $ticket)
                        @if ($ticket->status == "Closed")
                            <div class="row">
                                <div class="col">
                                    {{$ticket->category_name}}
                                </div>
                                <div class="col">
                                    {{$ticket->urgency}}
                                </div>
                                <div class="col-3">
                                    {{$ticket->updated_at}}
                                </div>
                                <div class="col">
                                    {{$ticket->status}}
                                </div>
                                <div class="col">
                                    <p onclick="location.href='/ticket/view/{{$ticket->id}}'" class="pointer">View Ticket</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    @endif




                </div>
            </div>
        </div>
    </div>
</x-app-layout>
