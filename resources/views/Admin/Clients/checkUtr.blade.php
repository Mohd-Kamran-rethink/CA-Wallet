@extends('Admin.index')
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Seach UTR</h1>
                <h6 class="text-danger">* Items marked with an asterisk are required fields and must be completed</h6>
            </div>
        </div>
    </div>
</section>
    <section class="content">
        <div class="card">

            <div class="card-body">
                <form action="{{url('check-utr')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Enter UTR<span style="color:red">*</span></label>
                                <input type="text" name="utr" 
                                    class="form-control"  value="{{isset($utr)?$utr:old('utr')}}">
                                    @error('utr')
                                    <span class="text-danger">
                                        {{$message}}
                                    </span>
                                    @enderror
                            </div>
                        </div>
                        
                        
                    </div>
                    @if(isset($status))
                    <div role="alert" class="alert alert-{{$warn}} col-6">
                        {{isset($status)?$status:''}}
                    </div>
                    @endif
                       
                    <div class="row mt-2">
                        <div class="col-12">
                            <button type="submit" class="btn btn-info">Search</button>
                            <a href="{{url('/check-utr')}}" type="button" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
