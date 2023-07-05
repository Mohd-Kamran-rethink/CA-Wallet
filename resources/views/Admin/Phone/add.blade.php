@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($source) ? 'Edit Phone Number' : 'Add Phone Number' }}</h1>
                    <h6 class="text-danger">* Items marked with an asterisk are required fields and must be completed</h6>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="card">

            <div class="card-body">
                <form action="{{ isset($number) ? url('phone-numbers/edit') : url('phone-numbers/add') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="hiddenId" value="{{ isset($number) ? $number->id : '' }}">
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Phone Number<span style="color:red">*</span></label>
                                <input type="phone" name="number" class="form-control" data-validation="required"
                                    value="{{ isset($number) ? $number->number : old('number') }}">
                                @error('number')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>


                    </div>
                    <div class="row">
                        <input type="hidden" name="hiddenId" value="{{ isset($number) ? $number->id : '' }}">
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Device Name<span style="color:red">*</span></label>
                                <select type="platform" name="device_name" class="form-control">
                                    <option value="">Select</option>
                                    <option {{ isset($number) && $number->device_name == 'galaxy_a04' ? 'selected' : '' }}
                                        value="galaxy_a04">Galaxy A04</option>
                                    <option {{ isset($number) && $number->device_name == 'galaxy_a03_core' ? 'selected' : '' }}
                                        value="galaxy_a03_core">Galaxy A03 Core</option>
                                    <option {{ isset($number) && $number->device_name == 'redmi_9a' ? 'selected' : '' }}
                                        value="redmi_9a">Redmi 9A</option>
                                    <option {{ isset($number) && $number->device_name == 'oppo_f1s' ? 'selected' : '' }}
                                        value="oppo_f1s">Oppo F1S</option>
                                    <option {{ isset($number) && $number->device_name == 'galaxy_a03' ? 'selected' : '' }}
                                        value="galaxy_a03">Galaxy A03</option>
                                    <option {{ isset($number) && $number->device_name == 'galaxy_s22' ? 'selected' : '' }}
                                        value="galaxy_s22">Galaxy S22</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <input type="hidden" name="hiddenId" value="{{ isset($number) ? $number->id : '' }}">
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Device Code<span style="color:red">*</span></label>
                                <input  name="device_code" class="form-control" data-validation="required"
                                    value="{{ isset($number) ? $number->device_code : old('device_code') }}">
                                @error('device_code')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>


                    </div>
                    <div class="row">
                        <input type="hidden" name="hiddenId" value="{{ isset($number) ? $number->id : '' }}">
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Whatsapp/WATI<span style="color:red">*</span></label>
                                <select  name="platform" class="form-control">
                                    <option value="">Select</option>
                                    <option {{ isset($number) && $number->platform == 'wa' ? 'selected' : '' }} value="wa">
                                        Whatsapp</option>
                                    <option {{ isset($number) && $number->platform == 'wa_b' ? 'selected' : '' }} value="wa_b">
                                        Whatsapp Business</option>
                                    <option {{ isset($number) && $number->platform == 'wa_c' ? 'selected' : '' }} value="wa_c">
                                        Whatsapp Clone</option>
                                    <option {{ isset($number) && $number->platform == 'wati' ? 'selected' : '' }} value="wati">
                                        WATI</option>
                                </select>
                            </div>
                        </div>


                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <button type="submit" class="btn btn-info">Save</button>
                            <a href="{{ url('/phone-numbers') }}" type="button" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
