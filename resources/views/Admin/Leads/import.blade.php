@extends('Admin.index')
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{isset($lead)?"Edit lead":"Add lead"}}</h1>
                <h6 class="text-danger">* Items marked with an asterisk are required fields and must be completed</h6>
            </div>
        </div>
    </div>
</section>
    <section class="content">
        <div class="card">

            <div class="card-body">
                <form action="{{url('leads/import')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label for="exampleInputFile">Import Excel</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input"
                                            id="exampleInputFile" name="excel_file">
                                        <label class="custom-file-label" for="exampleInputFile">Choose
                                            file</label>
                                    </div>
                                    <div class="input-group-append">
                                        <span class="input-group-text">Upload</span>
                                    </div>
                                </div>
                            </div>
                        </div>                       
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <button type="submit" class="btn btn-info">Save</button>
                            <a href="{{url('/leads')}}" type="button" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
