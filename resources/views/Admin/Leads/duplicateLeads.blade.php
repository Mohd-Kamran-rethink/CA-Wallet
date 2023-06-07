@extends('Admin.index')
@section('content')
    <style>
        .custom-modal {
            max-width: 70%;
            width: 70%;
            max-height: 70%;
            height: auto;
        }

        @media (max-width: 580px) {
            .custom-modal {
                max-width: 100%;
                width: 100%;
            }
        }
    </style>
    @php
        function serialToDate($serialNumber)
        {
            $unixTimestamp = ($serialNumber - 25569) * 86400; // adjust for Unix epoch and convert to seconds
            $date = \Carbon\Carbon::createFromTimestamp($unixTimestamp);
            return $date->format('d-m-Y');
        }
    @endphp

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Duplicate Leads</h1>
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
           
        </div>
    </section>



    <section class="content">
        <div class="card">
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-between align-items-centers row">
                    <form action="{{ url('leads/follow-up') }}" method="GET" id="search-form"
                        class="filters d-flex flex-row col-8">
                        <div class="input-group input-group-md col-4 " style="width: 150px;">
                            <input type="text" value="{{ isset($searchTerm) ? $searchTerm : '' }}" name="table_search"
                                class="form-control float-right" placeholder="Search" id="searchInput">
                        </div>
                        
                        @if (session('user')->role == 'manager')
                            <div class="input-group col-4">
                                <select name="agent_id" id="agent_id" class="form-control">
                                    <option value="">--Filter By Agent--</option>
                                    @foreach ($agents as $item)
                                        <option {{ isset($FilterAgent) && $FilterAgent == $item->id ? 'selected' : '' }}
                                            value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="input-group col-4">
                            <button class="btn btn-success" onclick="searchData()">Filter</button>
                        </div>
                    </form>
                  
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
                                            <th>State</th>
                                            <th>Agent</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($leads as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->source_name }}</td>
                                                <td>{{ $item->date }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->number }}</td>
                                                <td>{{ $item->language }}</td>
                                                <td>{{ $item->state??'' }}</td>
                                                <td> {{ $item->agent_name }}</td>
                                                
                                                
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">No data</td>
                                        </tr>
                                    @endforelse
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
{{-- status change modal --}}
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
                        <select onchange="handleStatusValues(this)" name="status" class="form-control" id="">
                            <option value="0" data-second-value="null">--Choose--</option>
                            @foreach ($statuses as $item)
                                <option value="{{ $item->id }}" data-second-value="{{ $item->id }}">
                                    {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- for deposited options --}}
                    <div class="form-group  for-deposited" style="display: none">
                        <label for="">Amout <span class="text-danger">*</span></label>
                        <input name="amount" id="amount" type="text" class="form-control">
                        <span class="text-danger error-amount"></span>
                    </div>
                    <div class="form-group  for-deposited" style="display: none">
                        <label for="">ID Name<span class="text-danger">*</span></label>
                        <input name="IdName" id="idName" type="text" class="form-control">
                        <span class="text-danger error-idName"></span>
                    </div>
                    {{-- for followup and busy options --}}
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
</div>
{{-- hsitory modal --}}
<div class="modal fade show" id="modal-history" style=" padding-right: 17px;" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered custom-modal" style="">
        <div class="modal-content" style="height: 100%;">
            <div class="modal-header">
                <h4 class="modal-title">Status History</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body  p-0" id="historytable" style="width: auto">
            </div>
            <div class="modal-footer ">
                <button type="button" data-dismiss="modal" aria-label="Close"
                    class="btn btn-default">Cancel</button>
            </div>
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
        const filter_agent = $('#agent_id').val();
        url.searchParams.set('search', searchValue);
        url.searchParams.set('agent', filter_agent ?? '');
        $('#search-form').attr('action', url.toString()).submit();
    }
    const openLeadModal = (leadId,idName) => {
       
        $(`#modal-default`).modal("show");
        $(`#lead_id`).val(leadId);
        $(`#idName`).val(idName);
    }
    const handleStatusValues = (option) => {
        status = $(option).find(':selected').data('second-value');
        let submitButton = $('#status-submit-button')
        let conditionalInput = $('.conditional-input')
        let forDepostied = $('.for-deposited')
        conditionalInput.hide()
        if (status == "0") {
            submitButton.attr('disabled', true);
        } else {
            if (status == 6||status == 7||status == 8 || status == 11) {

                conditionalInput.show()
            } else {
                conditionalInput.hide()
            }
            submitButton.removeAttr('disabled');
        }
        // if (status == 1) {
        //     forDepostied.show()
        // } else {
        //     forDepostied.hide()
        // }

    }
    const submitStatusChange = () => {
        let submitButton = $('#status-submit-button')
        event.preventDefault();
        let datePicker = $('#datePicker').val()
        let amount = $('#amount').val()
        let IdName = $('#idName').val()
        if ((status == 6||status == 7||status == 8 || status == 11) && !datePicker) {
            $('.error-date').html('Please enter valid date')

        } 
        // else if ((status == 1) && !amount) {
        //     $('.error-amount').html('Please enter amount')
        // }
        // else if ((status == 1) && !IdName) {
        //     $('.error-idName').html('Please enter IdName')
        // }  
        else {
            $('#status-form').submit();
        }
    }
    // history modal
    const openHistoryModal = (leadId) => {
        $(`#modal-history`).modal("show");
        $(`#lead_id`).val(leadId);
        searchLeadsStatus(leadId)
    }

    function searchLeadsStatus(lead_id) {
        const leadsStatusData = {!! json_encode($leads_status_history) !!};
        const filteredData = leadsStatusData.filter(data => data.lead_id == lead_id);

        const table = createTable(filteredData);
        const loadingSpinner = "<div class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</div>";
        const noData = "<div class='text-center'>No Data Found</div>";
        $("#historytable").html(loadingSpinner);
        setTimeout(() => {
            const filteredData = leadsStatusData.filter(data => data.lead_id == lead_id);
            if (filteredData.length == 0) {
                $("#historytable").html(noData);
            } else {
                $("#historytable").html(table);
            }
        }, 500);
    }

    function createTable(data) {
        let table = "<table class='table '>";
        table +=
            "<thead><tr><th>Sr.No</th><th style='width:10%;text-align:center'>Status</th><th style='width:30%;text-align:center'>FollowUp Date</th><th style='width:10%;text-align:center'>Amount</th><th>Created at</th><th>Remark</th></tr></thead>";
        table += "<tbody>";
        data.forEach((item, index) => {
            table +=
                `<tr><td>${index+1}</td><td style='width:10%;text-align:center'>${item.status_name}</td><td style='width:30%;text-align:center'>${item.followup_date??'--'}</td><th style='width:10%;text-align:center'>${item.amount??'--'}</th><td style="word-wrap">${moment(item.created_at).format('DD-MM-YYYY') ??'--'}</td><td style="word-wrap">${item.remark??'--'}</td></tr>`;
        });
        table += "</tbody></table>";
        return table;
    }
</script>




@endsection
