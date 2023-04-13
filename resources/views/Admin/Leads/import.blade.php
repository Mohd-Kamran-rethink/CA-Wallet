@extends('Admin.index')
@section('content')
<style>
.lead-instructions li{list-style: decimal;color: white}
</style>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2s">
                <div>
                    <h1>{{ isset($lead) ? 'Edit lead' : 'Import leads' }}</h1>
                    <div class="lead-instructions py-3 px-1 rounded my-2" style="background: rgb(48, 177, 48)">
                        <ul>
                            <li>Only upload files in XLSX or XLS format.</li>
                            <li>Make sure that the file you are uploading contains the headers "Sources", "Date",
                                "Name","mobile number", "Language", "ID NAME", and "Agent".</li>
                            <li>Ensure that all required fields ("Sources", "Date", "Name", and "Agent") are filled in
                                for each entry in the Excel file.</li>
                            <li>The "mobile number" field must contain exactly 12 digits, with + characters.</li>
                            <li>If an entry with the same combination of "Date", "Name", "mobile number", and "Agent"
                                already exists in the system, it will be skipped and not added to the database.</li>
                            <li>If any validation errors occur when uploading the Excel file, a list of the errors will
                                be displayed and the corresponding entries will not be added to the database. Please review
                                the errors and make any necessary changes to the Excel file before trying again.</li>
                            
                        </ul>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="card">

            <div class="card-body">
                <form action="{{ url('leads/import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label for="exampleInputFile">Import</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="exampleInputFile"
                                            name="excel_file">
                                        <label class="custom-file-label" for="exampleInputFile">Choose .xls file</label>
                                    </div>
                                </div>
                                @error('excel_file')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <button type="submit" class="btn btn-info">Save</button>
                            <a href="{{ url('/leads') }}" type="button" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
