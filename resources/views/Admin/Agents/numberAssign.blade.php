@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Assign Number</h1>
                    <h6 class="text-danger">* Items marked with an asterisk are required fields and must be completed</h6>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="card">
            <div class="card-body">
                <form action="{{url('agents/assign-numbers')}}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="userId" value="{{ isset($agent) ? $agent->id : '' }}">


                        <div class="col-3">

                            <label for="">Phone Number</label>
                            <select name="numbers" class="form-control">
                                <option value="0">--Choose---</option>
                                @foreach ($numbers as $item)
                                    <option value="{{ $item->id }}">{{ $item->number }}</option>
                                @endforeach
                            </select>
                            @error('numbers')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <div class="col-3">

                            <label for="">Platform</label>
                            <select name="platform" class="form-control">
                                    <option value="0">--Choose--</option>
                                    <option value="whatsapp">Whats App</option>
                                    <option value="wati">Wati</option>
                        </select>
                            @error('platform')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                       
                    </div>

                               


                    <div class="row mt-2">
                        <div class="col-12">
                            <button type="submit" class="btn btn-info">Save</button>
                            <a href="{{ url('/agents') }}" type="button" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
