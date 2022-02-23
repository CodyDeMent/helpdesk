@php
    use Illuminate\Support\Facades\DB;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tickets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="container">
                    <div class="row">
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
                    @foreach ($tickets as $ticket)
                        @if ($ticket->status != "Inactive")
                            <div class="row">
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


                </div>
            </div>
        </div>
    </div>
</x-app-layout>
