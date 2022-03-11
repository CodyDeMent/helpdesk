@php
    $categories  = DB::table('ticket_categories')->get();
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('First Time Setup') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form name="first-time-setup" id="first-time-setup" method="post" action="{{url('setup/store')}}">
                    @csrf
                    <div class="form-group">
                        <label for="it">Please Verify you are a member of the IT Team</label>
                        <select name="it" class="form-control" required>
                            <option value="no">No, I am not a member of the IT Team</option>
                            <option value="yes">Yes, I'm a member of the IT Team</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="email">Email to send to for new tickets</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="default_assignee">Default Assigned User</label>
                        <select name="default_assignee" class="form-control">
                            <option value=0>None</option>
                            @foreach (DB::table('users')->select('id', 'name')->get() as $user)
                            <option value={{$user->id}}>{{$user->name}}</option>
                            @endforeach
                        </select>
                    </div>


                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
