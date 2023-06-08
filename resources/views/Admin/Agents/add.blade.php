@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($agent) ? 'Edit Agent' : 'Add Agent' }}</h1>
                    <h6 class="text-danger">* Items marked with an asterisk are required fields and must be completed</h6>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="card">
            <div class="card-body">
                <form action="{{ isset($agent) ? url('agents/edit') : url('agents/add') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="userId" value="{{ isset($agent) ? $agent->id : '' }}">
                        <input type="hidden" name="role" value="agent">
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Agent Name <span style="color:red">*</span></label>
                                <input type="text" name="name" placeholder="John" class="form-control"
                                    data-validation="required" value="{{ isset($agent) ? $agent->name : old('name') }}">
                                @error('name')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Phone <span style="color:red">*</span></label>
                                <input type="number" name="phone" value="{{ isset($agent) ? $agent->phone : old('phone') }}"
                                    id="phone" placeholder="972873818" data-errortext="This is dealer's username!"
                                    class="form-control" data-validation="required">
                                @error('phone')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Email <span style="color:red">*</span></label>
                                <input type="email" name="email" value="{{ isset($agent) ? $agent->email : old('email') }}"
                                    id="username" placeholder="johs@gmail.com" class="form-control">
                                @error('email')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        @if (session('user')->role=='manager')
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label>Language <span style="color:red">*</span></label>
                                    <select class="form-control" name="language" id="">
                                        <option value="0">--Choose--</option>
                                        @foreach ($languages as $item)
                                            <option  {{isset($agent)&&$item->name==$agent->language?'selected':''}} value="{{ $item->name }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('language')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label>Zones <span style="color:red">*</span></label>
                                    <select class="form-control" name="zone" id="">
                                        <option value="0">--Choose--</option>
                                        @foreach ($zones as $item)
                                            <option {{isset($agent)&&$item->name==$agent->zone?'selected':''}} value="{{ $item->name }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('zone')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label>States <span style="color:red">*</span></label>
                                    <select class="form-control" name="state" id="">
                                        <option value="0">--Choose--</option>
                                        @foreach ($states as $item)
                                            <option {{isset($agent)&&$item->name==$agent->state?'selected':''}} value="{{ $item->name }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('state')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label>Lead Type <span style="color:red">*</span></label>
                                    <select class="form-control" name="lead_type" id="">
                                        <option value="0">--Choose--</option>
                                            <option {{isset($agent)&&$agent->lead_type=="Whats App"?'selected':''}} value="Whats App">Whats App</option>
                                            <option {{isset($agent)&&$agent->lead_type=="Call"?'selected':''}} value="Call">Call</option>
                                        </select>
                                    @error('lead_type')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label>Agent Type <span style="color:red">*</span></label>
                                    <select class="form-control" name="agent_type" id="">
                                        <option value="0">--Choose--</option>
                                            <option {{isset($agent)&&$agent->agent_type=="Retention"?'selected':''}} value="Retention">Retention</option>
                                            <option {{isset($agent)&&$agent->agent_type=="Normal"?'selected':''}} value="Normal">Normal</option>
                                        </select>
                                    @error('agent_type')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        @endif
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Password <span style="color:red">*</span></label>
                                <input type="text" name="password" value="" id="password" placeholder="Password"
                                    class="form-control" data-validation="required">
                                @error('password')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Confirm Password <span style="color:red">*</span></label>
                                <input type="password" name="confirmPassword" value="" id="confirmPassword"
                                    placeholder="Confirm password" data-errortext="This is dealer's username!"
                                    class="form-control" data-validation="required">
                                @error('confirmPassword')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
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
