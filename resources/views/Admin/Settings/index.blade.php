@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>General Settings</h1>
                </div>
            </div>
        </div>
    </section>
    <div class="content-header">
        <div class="container-fluid">
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header border-0">

                                </div>
                                <div class=" d-flex justify-content-center">
                                    <div class="col-6 mt-5 mb-5">
                                    <form action="{{url('/project/settings')}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Project Name</label>
                                                <input type="" value="{{isset($isAlready) ?$isAlready->project_name: ''}}" class="form-control" id="exampleInputEmail1"
                                                    placeholder="Project name (Max length 30)" maxlength="30" name="project_name">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="exampleInputFile">Project Logo</label>
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input"
                                                            id="exampleInputFile" name="projectLogo">
                                                        <label class="custom-file-label" for="exampleInputFile">Choose
                                                            file</label>
                                                    </div>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Upload</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" name="is_dark_mode" value="Enabled" class="form-check-input" id="exampleCheck1">
                                                <label class="form-check-label"  for="exampleCheck1">Enable dark mode</label>
                                            </div>
                                        </div>

                                        <div class="px-4">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </form>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
