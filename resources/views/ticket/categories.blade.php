@php
    $categories  = DB::table('ticket_categories')->get();
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
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                Current Categories:<br>
                @foreach ($categories as $category)
                    {{$category->category_name}}<br>
                @endforeach<hr>

                <form name="technical-support-form" id="technical-support-form" method="post" action="{{url('ticket/category/store')}}">
                    @csrf
                    <div class="form-group">
                        <label for="technicalSupport">Category</label>
                        <input type="text" class="form-control" name="category_name" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
