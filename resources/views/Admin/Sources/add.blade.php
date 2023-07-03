@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($source) ? 'Edit Source' : 'Add Source' }}</h1>
                    <h6 class="text-danger">* Items marked with an asterisk are required fields and must be completed</h6>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="card">

            <div class="card-body">
                <form action="{{ isset($source) ? url('sources/edit') : url('sources/add') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="sourceId" value="{{ isset($source) ? $source->id : '' }}">
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Source Name<span style="color:red">*</span></label>
                                <input type="text" name="name" placeholder="Demo Source" class="form-control"
                                    data-validation="required" value="{{ isset($source) ? $source->name : old('name') }}">
                                @error('name')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <h4>Mandatory Fileds in</h4>
                    <div class="row">
                        <div class="col-md-2">
                            <input {{ isset($source) && $source->agentPhone ? 'checked' : '' }} class="pt-1" type="checkbox" name="agentPhone">
                            <label for="">Agent Phone</label>
                        </div>
                        {{-- <div class="col-2">
                            <input {{ isset($source) && $source->clientPhone ? 'checked' : '' }} class="pt-1" type="checkbox" name="clientPhone">
                            <label for="">Client Phone</label>
                        </div> --}}
                        <div class="col-2">
                            <input {{ isset($source) && $source->statusID ? 'checked' : '' }} class="pt-1" type="checkbox" name="statusID">
                            <label for="">Status</label>
                        </div>
                    </div>
                    <h4>Show in mannual lead</h4>
                    <div class="row">
                        <div class="col-xs-12 col-md-4">
                            <input {{ isset($source) && $source->show_in_mannual_lead ? 'checked' : '' }} type="checkbox"
                                name="show_in_mannual">
                            <label>Yes</label>

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
