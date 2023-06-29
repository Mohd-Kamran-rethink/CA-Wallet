@extends('Admin.index')
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{isset($source)?"Edit Phone Number":"Add Phone Number"}}</h1>
                <h6 class="text-danger">* Items marked with an asterisk are required fields and must be completed</h6>
            </div>
        </div>
    </div>
</section>
    <section class="content">
        <div class="card">

            <div class="card-body">
                <form action="{{isset($number)?url('phone-numbers/edit'):url('phone-numbers/add')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="hiddenId" value="{{isset($number)?$number->id:''}}">
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Phone Number<span style="color:red">*</span></label>
                                <input  type="phone"  name="number" 
                                    class="form-control" data-validation="required" value="{{isset($number)?$number->number:old('number')}}">
                                    @error('number')
                                    <span class="text-danger">
                                        {{$message}}
                                    </span>
                                    @enderror
                            </div>
                        </div>
                       
                       
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <button type="submit" class="btn btn-info">Save</button>
                            <a href="{{url('/phone-numbers')}}" type="button" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
