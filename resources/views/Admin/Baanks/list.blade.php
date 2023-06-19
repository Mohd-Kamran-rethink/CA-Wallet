@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Active Banks</h1>
                </div>
            </div>
            @if (session()->has('msg-success'))
                <div class="alert alert-success" role="alert">
                    {{ session('msg-success') }}
                </div>
            @elseif (session()->has('msg-error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('msg-error') }}
                </div>
            @endif
        </div>
    </section>
    <section class="content">
        <div class="card">
            {{-- <div class="card-body">
                <div class="card-header d-flex justify-content-between px-0 mx-0">
                    <form action="{{ url('clients') }}" method="GET" id="search-form"
                        class="filters d-flex flex-row col-11 pl-0">
                        <div class="col-3">
                            <label for="">Search</label>
                            <input id="searchInput" name="saerch-input" placeholder="Search by number" type="text"
                                class="form-control " value="{{ isset($search) ? $search : '' }}">
                        </div>
                        <div class="col-2">
                            <label for="">From</label>
                            <input name="from_date" type="date" class="form-control from_date" id="datePicker"
                                value="{{ isset($startDate) ? $startDate : '' }}">
                        </div>
                        <div class="col-2">
                            <label for="">To</label>
                            <input name="to_date" type="date" class="form-control to_date" id="datePicker"
                                value="{{ isset($endDate) ? $endDate : '' }}">
                        </div>
                        <div class="col-1">
                            <label for="" style="visibility: hidden;">filter</label>
                            <button class="btn btn-success form-control" onclick="searchData()">Filter</button>
                        </div>
                        <div class="col-2">
                            <label for="" style="visibility: hidden;">filter</label>
                            <button {{isset($requestNumber)?"disabled":''}} onclick="RequestNumberModal()" type="button" title="Request Number"
                                class="btn btn-secondary">Request
                                Numbers</button>
                        </div>
                </div>
                </form>
            </div> --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Account Number</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($banks as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->account_number }}</td>
                                            <td>Active</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">No data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                       
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
    {{-- redepostit modal --}}
    <div class="modal fade show" id="modal-redeposit" style=" padding-right: 17px;" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered custom-modal" style="">
            <div class="modal-content" style="height: 100%;">
                <div class="modal-header">
                    <h4 class="modal-title">Redeposit</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ url('clients/redeposit') }}" method="POST" id="redeposit-form">
                    @csrf
                    <div class="px-3">
                        <input type="hidden" id="deposit-id" name="depositId">
                        <div class="form-group mt-3">
                            <label for="">Amount <span class="text-danger">*</span></label>
                            <input type="number" placeholder="1000" id="amount-input" name="amount" class="form-control"
                                value="">
                            <span class="text-danger error-amount"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="redeposit()" class="btn btn-success">Submit</button>
                        <button type="button" data-dismiss="modal" aria-label="Close"
                            class="btn btn-default">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- delete client modal --}}
    <div class="modal fade show" id="request-number" style=" padding-right: 17px;" aria-modal="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Request to see clients numbers</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ url('clients/numberRequests') }}" method="POST">
                    @csrf
                    <input type="hidden" name="agent_id" id="agent_id">
                    <div class="modal-body">
                        <h4>Are you sure you want to request to reveal numbers?</h4>
                    </div>
                    <div class="modal-footer ">
                        <button type="submit" class="btn btn-danger">Yes</button>
                        <button type="button" data-dismiss="modal" aria-label="Close"
                            class="btn btn-default">Cancel</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        function searchClients() {
            event.preventDefault();
            const url = new URL(window.location.href);
            const searchValue = $('#searchInput').val().trim();
            const from_date = $('.from_date').val();
            const toDate = $('.to_date').val();
            url.searchParams.set('search', searchValue);
            url.searchParams.set('from_date', from_date ?? '');
            url.searchParams.set('toDate', toDate ?? '');
            $('#search-form').attr('action', url.toString()).submit();
        }

        function openRedepositModal(id) {
            $(`#modal-redeposit`).modal("show");
            $(`#deposit-id`).val(id);
        }

        function redeposit() {
            event.preventDefault();
            let amount = $('#amount-input').val()
            if (!amount) {
                $('.error-amount').html('Please enter amount')
            } else {
                $('.error-amount').html('')
                $('#redeposit-form').submit();
            }
        }

        function RequestNumberModal() {
            $(`#request-number`).modal("show");
        }
    </script>
@endsection
