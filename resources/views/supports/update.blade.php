@php
    $categories  = DB::table('ticket_categories')->get();
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Update User Support') }}
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

                <form name="first-time-setup" id="first-time-setup" method="post" action="{{url('supports/update')}}">
                    @csrf
                    <div class="form-group">
                        <label for="user">Add User</label>
                        <select name="add_user" class="form-control">
                            <option value=0></option>
                            @foreach (DB::table('users')->where('id', '!=', $request->id)->select('name', 'id')->get() as $user)
                            <option value={{$user->id}}>{{$user->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="user">Remove User</label>
                        <select name="remove_user" class="form-control">
                            <option value=0></option>
                            @foreach (DB::table('supports')
                            ->join('users', 'supports.supported', '=', 'users.id')
                            ->where('supports.supporter', $request->id)->select('users.name as name', 'users.id as id')->get() as $user)
                            <option value={{$user->id}}>{{$user->name}}</option>
                            @endforeach
                        </select>
                        <input hidden name="supporter" value="{{$request->id}}">
                    </div>


                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
