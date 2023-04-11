@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($lead) ? 'Edit Lead' : 'Add Lead' }}</h1>
                    <h6 class="text-danger">* Items marked with an asterisk are required fields and must be completed</h6>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="card">

            <div class="card-body">
                <form action="{{ isset($lead) ? url('leads/edit') : url('leads/add') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="sourceId" value="{{ isset($lead) ? $lead->id : '' }}">
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Source Name <span style="color:red">*</span></label>
                                <Select name="SourceName" class="form-control" data-validation="required">
                                    <option value="0">--Choose--</option>
                                    @foreach ($sources as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </Select>
                                @error('SourceName')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Date <span style="color:red">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control float-right" id="reservation">
                                </div>
                                @error('date')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Name<span style="color:red">*</span></label>
                                <input type="text" class="form-control" name="" id="">
                                @error('date')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Number<span style="color:red">*</span></label>
                                <input type="text" class="form-control" name="" id="">
                                @error('date')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Language<span style="color:red">*</span></label>
                                <input type="text" class="form-control" name="" id="">
                                @error('date')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>ID Name<span style="color:red">*</span></label>
                                <input type="text" class="form-control" name="" id="">
                                @error('date')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Agent<span style="color:red">*</span></label>
                                <input type="text" class="form-control" name="" id="">
                                @error('date')
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
                            <a href="{{ url('/sources') }}" type="button" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
