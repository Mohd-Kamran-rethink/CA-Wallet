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
    {{-- leads upload status tables --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Leads</h1>
                </div>
            </div>
            <div class="" id="notification-alert" role="alert">

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
            @if (
                (session()->has('errors') && count(session('errors')) > 0) ||
                    (session()->has('skipped') && count(session('skipped')) > 0))
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
                                            <th>State</th>
                                            <th>Agent</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (session('skipped') as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item['Sources'] }}</td>
                                                <td>{{ serialToDate($item['Date']) }}</td>
                                                <td>{{ $item['Name']??'' }}</td>
                                                <td>{{ $item['Number'] }}</td>
                                                <td>{{ $item['Language'] }}</td>
                                                <td>{{ $item['State'] }}</td>
                                                <td> {{ isset($item['Agent'])??'' }}</td>
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
                                            <th>State</th>
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
                                                <td>{{ isset($item['Number'])??'' }}</td>
                                                <td>{{ $item['Language'] }}</td>
                                                <td>{{ $item['State'] }}</td>
                                                <td> {{ isset($item['Agent'])??'' }}</td>
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
                    <form class="filters d-flex flex-row  col-lg-7 mt-2" action="{{ url('leads/list') }}" method="GET"
                        id="search-form">
                        <div class="input-group input-group-md col-3 " style="width: 150px;">
                            <input type="text" value="{{ isset($searchTerm) ? $searchTerm : '' }}" name="table_search"
                                class="form-control float-right" placeholder="Search" id="searchInput">
                        </div>
                        
                        
                        <div class="input-group ">
                            <button class="btn btn-success" onclick="searchData()">Filter</button>
                        </div>
                    </form>
                    <div class="col-md-12 col-lg-5 d-flex justify-content-end mt-2">
                       
                        @if (session('user')->role === 'manager')
                           
                            <button onclick="MassModals('modal-mass-agent')" disabled
                                class="mass-action-buttons btn btn-primary ">Assign To</button>
                           
                        @endif
                        
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            @if (session('user')->role === 'manager')
                                                <th><input type="checkbox" onchange="selectAll()" value="null"></th>
                                            @endif
                                            <th>S.No.</th>
                                            <th>Source</th>
                                            <th>Date</th>
                                            <th>Name</th>
                                            <th>Number</th>
                                            <th>Language</th>
                                            <th>State</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($leads as $key=> $item)
                                            <tr>
                                                @if (session('user')->role === 'manager')
                                                    <td><input class="checkbox" type="checkbox" onchange="selectedLeads()"
                                                            value="{{ $item->id }}"></td>
                                                @endif
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->source_name }}</td>
                                                <td>{{ $item->date }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->number }}</td>
                                                <td>{{ $item->language }}</td>
                                                <td>{{ $item->state }}</td>
                                                
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
                        <select onchange="handleStatusValues(this)" name="status" class="form-control"
                            id="">
                            <option value="0" data-second-value="0">--Choose--</option>
                            @foreach ($statuses as $item)
                                @if ($item->name == 'Deposited')
                                    @continue
                                @endif
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
                        <input name="date" type="date" class="form-control blockpast" id="datePicker">
                        <span class="text-danger error-date"></span>
                    </div>
                    <div class="form-group">
                        <label for="">Remark</label>
                        <textarea rows="3" type="text" class="form-control" name="remark" id="remark"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer ">
                <button onclick="submitStatusChange('status-form')" type="submit"
                    class="btn btn-success status-submit-button" id="status-submit-button" disabled>Submit</button>
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
{{-- mass status change modal --}}
<div class="modal fade show" id="modal-mass-status" style=" padding-right: 17px;" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Change Status</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ url('/leads/status/mass/submit') }}" method="post" id="mass-status-form">
                    @csrf
                    <input type="hidden" name="leadIds" class="lead_ids">
                    <div class="form-group">
                        <label for="">Status <span class="text-danger">*</span></label>
                        <select onchange="handleStatusValues(this)" name="status" class="form-control"
                            id="">
                            <option value="0" data-second-value="0">--Choose--</option>
                            @foreach ($statuses as $item)
                                @if ($item->name == 'Deposited')
                                    @continue
                                @endif
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
                        <input name="date" type="date" class="form-control blockpast" id="mass-datePicker">
                        <span class="text-danger error-date"></span>
                    </div>
                    <div class="form-group">
                        <label for="">Remark</label>
                        <textarea rows="3" type="text" class="form-control" name="remark" id="remark"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer ">
                <button onclick="submitMassStatusChange('mass-status-form')" type="submit"
                    class="btn btn-success status-submit-button" id="mass-status-change-button"
                    disabled>Submit</button>
                <button type="button" data-dismiss="modal" aria-label="Close"
                    class="btn btn-default">Cancel</button>

            </div>
        </div>
    </div>
</div>
{{-- assign to agent --}}
<div class="modal fade show" id="modal-mass-agent" style=" padding-right: 17px;" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Assign leads to agent</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ url('/leads/agent/mass/change') }}" method="post" id="mass-agent-form">
                    @csrf
                    <input type="hidden" name="leadIds" class="lead_ids">
                    <div class="form-group">
                        <label for="">Agents<span class="text-danger">*</span></label>
                        <select onchange="handleAgentChange(this)" name="agent_id" class="form-control"
                            id="agent-mass-dropdown">
                            <option value="0" data-second-value="0">--Choose--</option>
                            @foreach ($agents as $item)
                                <option value="{{ $item->id }}" data-second-value="{{ $item->name }}">
                                    {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                </form>
            </div>
            <div class="modal-footer ">
                <button onclick="changeMassAgent('mass-agent-form')" type="submit"
                    class="btn btn-success agent-modal-submit-button" id="mass-status-change-button"
                    disabled>Submit</button>
                <button type="button" data-dismiss="modal" aria-label="Close"
                    class="btn btn-default">Cancel</button>

            </div>
        </div>
    </div>
</div>
{{-- mannual lead add modal --}}
<div class="modal fade show" id="modal-add-lead" style=" padding-right: 17px;" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Lead</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ url('/leads/agent/mass/change') }}" method="post" id="mass-agent-form">
                    @csrf
                    <input type="hidden" name="leadIds" class="lead_ids">
                    <div class="form-group">
                        <label for="">Phone<span class="text-danger">*</span></label>
                        <input id="lead_number" type="number" name="lead_number" class="form-control">
                    </div>

                </form>
            </div>
            <div class="modal-footer ">
                <button onclick="submitMannualLead()" type="button"
                    class="btn btn-success agent-modal-submit-button" id="mass-status-change-button">Submit</button>
                <button type="button" data-dismiss="modal" aria-label="Close"
                    class="btn btn-default">Cancel</button>

            </div>
        </div>
    </div>
</div>
<script>
    // Get the datePicker input element
    const datePicker = document.getElementsByClassName('blockpast');
    // Get the current date
    const currentDate = new Date().toISOString().split('T')[0];
    for (let i = 0; i < datePicker.length; i++) {
        const element = datePicker[i];
        // Do something with each element
        datePicker[i]?.setAttribute('min', currentDate);
    }


    // Set the minimum date allowed for selection
    let lead_id;
    let status;
    const searchData = () => {
        event.preventDefault();
        const url = new URL(window.location.href);
        const searchValue = $('#searchInput').val().trim();
        url.searchParams.set('search', searchValue);
        $('#search-form').attr('action', url.toString()).submit();
    }
    const openLeadModal = (leadId, idName) => {

        $(`#modal-default`).modal("show");
        $(`#lead_id`).val(leadId);
        $(`#idName`).val(idName);
    }
    const handleStatusValues = (option) => {
        status = $(option).find(':selected').data('second-value');
        let submitButton = $('.status-submit-button')
        let conditionalInput = $('.conditional-input')
        let forDepostied = $('.for-deposited')
        conditionalInput.hide()
        if (status == "0") {
            submitButton.attr('disabled', true);
        } else {
            // if status id = 6 ,7, 8(follow up) or for busy also
            if (status == 6 || status == 7 || status == 8 || status == 11) {
                conditionalInput.show()
            } else {
                conditionalInput.hide()
            }
            submitButton.removeAttr('disabled');
        }
        // status id 1 is for deposit
        // if (status == 1) {
        //     forDepostied.show()
        // } else {
        //     forDepostied.hide()
        // }

    }
    const submitStatusChange = (formId) => {
        let submitButton = $('.status-submit-button')
        event.preventDefault();
        let datePicker = $('#datePicker').val();
        let massdatePicker = $('#mass-datePicker').val();
        let amount = $('#amount').val()
        let IdName = $('#idName').val()
        // if status id = 6,7,8.( follow up ) or for busy also
        // status id 1 is for deposit
        if ((status == 6 || status == 7 || status == 8 || status == 11) && !datePicker) {
            $('.error-date').html('Please enter valid date')
        }
        // else if ((status == 1) && !amount) {
        //     $('.error-amount').html('Please enter amount')
        // } else if ((status == 1) && !IdName) {
        //     $('.error-idName').html('Please enter IdName')
        // } 
        else {
            $(`#${formId}`).submit();
        }
    }
    const submitMassStatusChange = (formId) => {
        let submitButton = $('#mass-status-change-button')
        event.preventDefault();
        let massdatePicker = $('#mass-datePicker').val();
        if ((status == 6 || status == 7 || status == 8 || status == 11) && !massdatePicker) {
            $('.error-date').html('Please enter valid date')
        } else {
            $(`#${formId}`).submit();
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
            "<thead><tr><th>Sr.No</th><th style='width:10%;text-align:center'>Status</th><th style='width:30%;text-align:center'>FollowUp Date</th><th style='width:15%;text-align:center'>Changed By</th><th>Created at</th><th>Remark</th></tr></thead>";
        table += "<tbody>";
        data.forEach((item, index) => {
            table +=
                `<tr><td>${index+1}</td><td style='width:10%;text-align:center'>${item.status_name}</td><td style='width:30%;text-align:center'>${item.followup_date??'--'}</td><td style='width:15%;text-align:center'>${item.agentName??'--'}</td><td style="word-wrap">${moment(item.created_at).format('DD-MM-YYYY') ??'--'}</td><td style="word-wrap">${item.remark??'--'}</td></tr>`;
        });
        table += "</tbody></table>";
        return table;
    }
    // mass selection function 
    const selectedItems = [];

    function selectAll() {
        const checkboxes = document.querySelectorAll('input[class="checkbox"]');
        checkboxes.forEach((checkbox) => {
            checkbox.checked = !checkbox.checked;
        });
        selectedLeads()
    }

    function selectedLeads() {
        const checkboxes = document.querySelectorAll('input[class="checkbox"]');
        checkboxes.forEach((checkbox) => {
            if (checkbox.checked) { // If the checkbox is checked
                if (!selectedItems.includes(checkbox
                        .value)) { // If the value is not already in the array
                    selectedItems.push(checkbox
                        .value); // Add the checkbox value to the selected items array
                }
            } else { // If the checkbox is unchecked
                const index = selectedItems.indexOf(checkbox
                    .value); // Find the index of the checkbox value in the selected items array
                if (index !== -1) { // If the checkbox value is found in the array
                    selectedItems.splice(index,
                        1); // Remove the checkbox value from the selected items array
                }
            }
        });
        if (selectedItems.length === 0) {
            $('.mass-action-buttons').attr('disabled', true);
        } else {
            $('.mass-action-buttons').attr('disabled', false);
        }

        // Convert the selected items array to a comma-separated string
        const selectedIds = selectedItems.join(',');
        return selectedIds;

    }

    function MassModals(modalId) {
        let selectedIds = selectedLeads();
        $(`#${modalId}`).modal('show');
        $('.lead_ids').val(selectedIds);
    }
    const handleAgentChange = (option) => {
        agentId = $(option).find(':selected').data('second-value');
        let submitButton = $('.agent-modal-submit-button')
        if (agentId == "0") {
            submitButton.attr('disabled', true);
        } else {
            submitButton.attr('disabled', false);
        }
    }
    const changeMassAgent = (formId) => {
        let submitButton = $('.agent-modal-submit-button')
        event.preventDefault();
        let agentId = $('#agent-mass-dropdown').val()

        if (agentId == "0") {
            $('.error-agent').html('Please select agent first')
        } else {
            $(`#${formId}`).submit();
        }
    }

    function addLeadsModal() {
        let number = $('#lead_number').val('')
        $('#modal-add-lead').modal('show');
    }

    function submitMannualLead() {
        let number = $('#lead_number').val()
        $.ajax({
            url: BASE_URL +
                "/leads/add?lead_number=" + number,
            success: function(data) {
                if (data.hasOwnProperty('msg-success')) {
                    // Show success message
                    $('#notification-alert').addClass('alert').addClass('alert-success').text(data[
                        'msg-success']);
                } else if (data.hasOwnProperty('msg-error')) {
                    $('#notification-alert').addClass('alert').addClass('alert-danger').text(data[
                        'msg-error']);
                }
                $('#modal-add-lead').modal('hide');
            },
            error: function(xhr, status, error) {
                var response = JSON.parse(xhr.responseText);
                if (response.errors) {

                    // Loop through each error and display it on the respective input field
                    $.each(response.errors, function(key, value) {
                        var inputElement = $('#' + key);
                        inputElement.addClass('is-invalid');
                        inputElement.next('.invalid-feedback').html(value[0]);
                    });
                } else {
                    // Handle other error cases
                }
            }
        });

    }
</script>
@endsection
