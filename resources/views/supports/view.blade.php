@php
    $users = DB::table('users')
    ->select('id', 'name')->get();
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
                    @foreach ($users as $user)
                    <div class="row">
                        <div class="col">
                            {{$user->name}}
                        </div>
                        <div class="col">
                            @foreach (DB::table('supports')
                            ->join('users', 'supports.supported', '=', 'users.id')
                            ->where('supports.supporter', $user->id)->select('users.name as name')->get() as $supported)
                                {{$supported->name}},&nbsp;

                            @endforeach
                        </div>
                        <div class="col">
                            <p onclick="location.href='/supports/update/{{$user->id}}'" class="pointer">Update Supported Users</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
