@extends('Admin.index')
@section('content')
@php
function serialToDate($serialNumber) {
    $unixTimestamp = ($serialNumber - 25569) * 86400; // adjust for Unix epoch and convert to seconds
    $date = \Carbon\Carbon::createFromTimestamp($unixTimestamp);
    return $date->format('d-m-Y');
}
@endphp

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Leads</h1>
                </div>
            </div>
            @if (session()->has('msg-success'))
                <div class="alert alert-success" role="alert">
                    {{ session('msg-success') }}
                </div>
            @elseif (session()->has('msg-error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('msg-success') }}
                </div>
            @endif
            @if ((session()->has('errors') && count(session('errors')) > 0) || (session()->has('skipped')&&count(session('skipped')) > 0))
                <div class="card">
                    <div class="card-body">
                        @if (count(session('skipped')) > 0)
                            <div class="alert alert-warning" role="alert"><span class="font-weight-bold"
                                    style="color: white">Skipped Entries</span> </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Source</th>
                                            <th>Date</th>
                                            <th>Name</th>
                                            <th>Number</th>
                                            <th>Language</th>
                                            <th>ID Name</th>
                                            <th>Agent</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (session('skipped') as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item['Sources'] }}</td>
                                                <td>{{ serialToDate($item['Date']) }}</td>
                                                <td>{{ $item['Name'] }}</td>
                                                <td>{{ $item['Number'] }}</td>
                                                <td>{{ $item['Language'] }}</td>
                                                <td>{{ $item['ID NAME'] }}</td>
                                                <td> {{ $item['Agent'] }}</td>
                                            </tr>
                                        @endforeach



                                    </tbody>
                                </table>
                            </div>
                        @endif
                        @if (count(session('errors')) > 0)
                            <div class="alert alert-danger" role="alert"><span class="font-weight-bold"
                                    style="color: white">Errors Entries</span> </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Source</th>
                                            <th>Date</th>
                                            <th>Name</th>
                                            <th>Number</th>
                                            <th>Language</th>
                                            <th>ID Name</th>
                                            <th>Agent</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (session('errors') as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item['Sources'] }}</td>
                                                <td>{{ serialToDate($item['Date']) }}</td>
                                                <td>{{ $item['Name'] }}</td>
                                                <td>{{ $item['Number'] }}</td>
                                                <td>{{ $item['Language'] }}</td>
                                                <td>{{ $item['ID NAME'] }}</td>
                                                <td> {{ $item['Agent'] }}</td>
                                            </tr>
                                        @endforeach



                                    </tbody>
                                </table>
                            </div>
                        @endif

                    </div>
                </div>
            @endif
        </div>
    </section>



    <section class="content">
        <div class="card">
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-between align-items-centers row">
                    <form action="{{ url('leads/list') }}" method="GET" id="search-form"
                        class="filters d-flex flex-row col-8">
                        <div class="input-group input-group-md col-4 " style="width: 150px;">
                            <input type="text" value="{{ isset($searchTerm) ? $searchTerm : '' }}" name="table_search"
                                class="form-control float-right" placeholder="Search" id="searchInput">
                        </div>
                        <div class="input-group col-4">
                            <select name="status" id="status_id" class="form-control">
                                <option value="">--Filter By Status--</option>
                                @foreach ($statuses as $item)
                                    <option value="{{ trim($item->name) }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="input-group col-4">
                            <button class="btn btn-success" onclick="searchData()">Filter</button>
                        </div>
                    </form>
                    @if (session('user')->role === 'manager')
                        <div>
                            <a href="{{ url('leads/import') }}" class="btn btn-primary">Import Leads</a>
                        </div>
                    @endif
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Source</th>
                                            <th>Date</th>
                                            <th>Name</th>
                                            <th>Number</th>
                                            <th>Language</th>
                                            <th>ID Name</th>
                                            <th>Agent</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($leads as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->source_name }}</td>
                                                <td>{{ $item->date }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->number }}</td>
                                                <td>{{ $item->language }}</td>
                                                <td>{{ $item->idName }}</td>
                                                <td> {{ $item->agent_name }}</td>
                                                <td> {{ $item->status_name ?? '--' }}</td>
                                                <td>
                                                    <button onclick="openLeadModal({{ $item->id }})"
                                                        title="Chnage status" class="btn btn-secondary">Change
                                                        status</button>
                                                </td>
                                            </tr>
                                        @endforeach



                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer clearfix">
                                {{ $leads->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade show" id="modal-default" style=" padding-right: 17px;" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Change Status</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                {{-- <input type="hidden" name="deleteId" id="deleteInput">
                <input type="hidden" name="role" id="deleteInput" value="agent"> --}}
                <div class="modal-body">
                    <form action="{{ url('/leads/status/submit') }}" method="post" id="status-form">
                        @csrf
                        <input type="hidden" name="leadId" id="lead_id">
                        <div class="form-group">
                            <label for="">Status <span class="text-danger">*</span></label>
                            <select onchange="handleStatusValues(this.value)" name="status" class="form-control"
                                id="">
                                <option value="0">--Choose--</option>
                                @foreach ($statuses as $item)
                                    <option value="{{ $item->name }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group  conditional-input" style="display: none">
                            <label for="">Date <span class="text-danger">*</span></label>
                            <input name="date" type="date" class="form-control" id="datePicker">
                            <span class="text-danger error-date"></span>
                        </div>
                        <div class="form-group">
                            <label for="">Remark</label>
                            <textarea rows="3" type="text" class="form-control" name="remark" id="remark"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer ">
                    <button onclick="submitStatusChange()" type="submit" class="btn btn-success "
                        id="status-submit-button" disabled>Change</button>
                    <button type="button" data-dismiss="modal" aria-label="Close"
                        class="btn btn-default">Cancel</button>

                </div>
            </div>
        </div>
        <script>
            let lead_id;
            let status;
            const searchData = () => {
                event.preventDefault();
                const url = new URL(window.location.href);
                const searchValue = $('#searchInput').val().trim();
                const status_id = $('#status_id').val();
                url.searchParams.set('search', searchValue);
                url.searchParams.set('status', status_id ?? '');
                $('#search-form').attr('action', url.toString()).submit();
            }
            const openLeadModal = (leadId) => {

                $(`#modal-default`).modal("show");
                $(`#lead_id`).val(leadId);
            }
            const handleStatusValues = (value) => {
                status = value;
                let submitButton = $('#status-submit-button')
                let conditionalInput = $('.conditional-input')
                conditionalInput.hide()
                if (value == "0") {
                    submitButton.attr('disabled', true);
                } else {
                    if (value == "Follow Up" || value == "Busy") {

                        conditionalInput.show()
                    } else {
                        conditionalInput.hide()
                    }
                    submitButton.removeAttr('disabled');
                }
            }
            const submitStatusChange = () => {
                let submitButton = $('#status-submit-button')
                event.preventDefault();
                let datePicker = $('#datePicker').val()
                if ((status == "Follow Up" || status == "Busy") && !datePicker) {
                    $('.error-date').html('Please enter valid date')

                } else {
                    $('#status-form').submit();
                }
            }
        </script>
    @endsection
