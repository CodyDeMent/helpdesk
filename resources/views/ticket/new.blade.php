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

                <form name="technical-support-form" id="technical-support-form" method="post" action="{{url('ticket/new/store')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="technicalSupport">Category</label>
                        <select name="category" class="form-control" required>
                            @foreach ($categories as $category)
                            <option value={{$category->id}}>{{$category->category_name}}</option>
                            @endforeach
                          </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Subject</label>
                        <input type="text" id="subject" name="subject" class="form-control" required>
                      </div>
                    <div class="form-group">
                        <label for="technicalSupport">Description of Issue</label>
                        <textarea name="description" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="technicalSupport">Urgency</label>
                        <select name="urgency" class="form-control" required>
                            <option value=5>Immediate</option>
                            <option value=4>High</option>
                            <option value=3 selected>Medium</option>
                            <option value=2>Low</option>
                            <option value=1>Very Low</option>
                          </select>
                    </div>
                    <div class="form-group">
                        <input type="file" multiple name="files[]" class="form-control">
                    </div>
                    @php
                        $supported = DB::table('supports')->join('users', 'supports.supported', '=', 'users.id')
                        ->where('supporter', Auth::user()->id)->select('users.id', 'users.name')->get();
                    @endphp
                    @if (count($supported) > 0)
                    <div class="form-group">
                        <label for="technicalSupport">Ticket For</label>
                        <select name="for" class="form-control" required>
                            <option value={{Auth::user()->id}}>{{Auth::user()->name}}</option>
                            @foreach ($supported as $s)
                            <option value={{$s->id}}>{{$s->name}}</option>
                            @endforeach
                          </select>
                    </div>
                    @else
                        <input value="{{Auth::user()->id}}" name="for" hidden>
                    @endif


                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
