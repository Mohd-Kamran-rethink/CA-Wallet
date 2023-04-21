@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($client) ? 'Edit Client' : 'Add Client' }}</h1>
                    <h6 class="text-danger">* Items marked with an asterisk are required fields and must be completed</h6>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="card">

            <div class="card-body">
                <form action="{{ isset($client) ? url('clients/edit') : url('clients/add') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="userId" value="{{ isset($client) ? $client->id : '' }}">
                        
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Client Name <span style="color:red">*</span></label>
                                <input type="text" name="name" placeholder="John" class="form-control"
                                    data-validation="required" value="{{ isset($client) ? $client->name : old('name') }}">
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
                                <input {{ isset($client)?"readonly":''}} type="number" name="number"
                                    value="{{ isset($client) ? $client->number : old('number') }}" id="number"
                                    placeholder="972873818" class="form-control" data-validation="required">
                                @error('number')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>ID Name <span style="color:red">*</span></label>
                                <input type="text" name="ca_id" {{ isset($client)?"readonly":''}}
                                    value="{{ isset($client) ? $client->ca_id : old('ca_id') }}" id="ca_id"
                                    placeholder="ID Name" class="form-control">
                                @error('ca_id')
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
                            <a href="{{ url('/clients') }}" type="button" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
