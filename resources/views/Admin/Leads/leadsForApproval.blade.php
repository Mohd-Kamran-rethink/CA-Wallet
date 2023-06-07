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
                        <div class="input-group col-3">
                            <select name="status" id="status_id" class="form-control">
                                <option value="">--Filter By Status--</option>
                                @foreach ($statuses as $item)
                                    <option {{ isset($Filterstatus) && $Filterstatus == $item->id ? 'selected' : '' }}
                                        value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if (session('user')->role == 'manager')
                            <div class="input-group col-3">
                                <select name="agent_id" id="agent_id" class="form-control">
                                    <option value="">--Filter By Agent--</option>
                                    @foreach ($agents as $item)
                                        <option {{ isset($FilterAgent) && $FilterAgent == $item->id ? 'selected' : '' }}
                                            value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="input-group ">
                            <button class="btn btn-success" onclick="searchData()">Filter</button>
                        </div>
                    </form>
                    <div class="col-md-12 col-lg-5 d-flex justify-content-end mt-2">
                        @if (session('user')->role === 'manager')
                            <button onclick="approveLeadsModal()" title="Approvelead"
                                class="mass-action-buttons btn btn-danger mx-2" disabled>Approve</button>
                            <button onclick="deleteLead()" title="delete this lead"
                                class="mass-action-buttons btn btn-danger " disabled>Reject</button>
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
                                            <th>Agent</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($leads as $item)
                                            @if ($item->current_status == 'Deposited' || $item->current_status == 'Not Intrested')
                                                @continue
                                            @endif

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
                                                <td> {{ $item->agent_name }}</td>
                                                <td>
                                                    @if (Request::is('leads/approval') && session('user')->id == 1)
                                                        <button onclick="approveLeadsModal({{ $item->id }})"
                                                            title="Approve this lead"
                                                            class="btn btn-success">Approve</button>
                                                        <button onclick="deleteLead({{ $item->id }})"
                                                            title="delete this lead" class="btn btn-danger">Reject</button>
                                                    @endif
                                                </td>



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


    {{-- leads aproval --}}
    <div class="modal fade show" id="approveLeads" style=" padding-right: 17px;" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Leads Approval</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('/leads/acceptapproval') }}" method="post">
                        @csrf
                        <input type="hidden" name="leadIds" class="lead_ids">
                        <h4 class="">Are you sure you want to approve lead?</h4>

                </div>

                <div class="modal-footer ">
                    <button type="submit" class="btn btn-success">Submit</button>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-default">Cancel</button>

                </div>
                </form>
            </div>
        </div>
    </div>
{{-- reject  leads --}}
<div class="modal fade show" id="delelteLeads" style=" padding-right: 17px;" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Reject Leads</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ url('/leads/delete') }}" method="post">
                    @csrf
                    <input type="hidden" name="leadIds" class="lead_ids">
                    <h4 class="">Are you sure you want to reject lead?</h4>

            </div>

            <div class="modal-footer ">
                <button type="submit" class="btn btn-success">Submit</button>
                <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-default">Cancel</button>

            </div>
            </form>
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
            const filter_agent = $('#agent_id').val();
            url.searchParams.set('search', searchValue);
            url.searchParams.set('status', status_id ?? '');
            url.searchParams.set('agent', filter_agent ?? '');
            $('#search-form').attr('action', url.toString()).submit();
        }
        const openLeadModal = (leadId, idName) => {

            $(`#modal-default`).modal("show");
            $(`#lead_id`).val(leadId);
            $(`#idName`).val(idName);
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

        function approveLeadsModal(leadId) {
            if (leadId) {
                $('.lead_ids').val(leadId)
            } else {
                $('.lead_ids').val(selectedLeads())
            }
            $('#approveLeads').modal('show')
        }
        function deleteLead(leadId) {
            if (leadId) {
                $('.lead_ids').val(leadId)
            } else {
                $('.lead_ids').val(selectedLeads())
            }
            $('#delelteLeads').modal('show')
        }
    </script>




@endsection
