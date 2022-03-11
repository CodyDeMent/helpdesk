@php
    $categories  = DB::table('ticket_categories')->get();
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Update User Role') }}
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

                <form name="first-time-setup" id="first-time-setup" method="post" action="{{url('update/role')}}">
                    @csrf
                    <div class="form-group">
                        <label for="user">User</label>
                        <select name="user" class="form-control">
                            @foreach (DB::table('users')->where('id', '!=', Auth::user()->id)->select('id', 'name')->get() as $user)
                            <option value={{$user->id}}>{{$user->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select name="role" class="form-control">
                            <option value="User">User</option>
                            <option value="IT">IT</option>
                        </select>
                    </div>


                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
