@extends('Admin.index')
@section('content')
    {{-- reports --}}
    <section class="content">
        <div class="content-header">
            <h2 class="">Leads Reports</h2>
            <div class="card">
                <div class="card-header d-flex justify-content-between ">
                    <form action="{{ url('reports/leads') }}" method="GET" id="search-form"
                        class="filters d-flex flex-row col-11 pl-0">

                        <div class="col-3 ">
                            <label for="">Assigned From</label>
                            <input name="from_date" type="date" class="form-control from_date" id="datePicker"
                                value="{{ isset($startDate) ? $startDate : '' }}">
                        </div>
                        <div class="col-3">
                            <label for="">Assigned To</label>
                            <input name="to_date" type="date" class="form-control to_date" id="datePicker"
                                value="{{ isset($endDate) ? $endDate : '' }}">
                        </div>
                        <div class="col-3 ">
                            <label for="">Create At</label>
                            <input name="created_from_date"  type="date" class="form-control created_from_date" id="datePicker"
                                value="{{ isset($created_from_date) ? $created_from_date : '' }}">
                        </div>
                       
                        <div class="">
                            <label for="" style="visibility: hidden;">filter</label>
                            <button class="btn btn-success form-control" onclick="searchData()">Filter</button>
                        </div>
                    </form>
                    <form action="{{ url('/report/leads/export') }}" method="post" id="leads-report-export-form">
                        @csrf
                        <div>
                            <input type="hidden" name="date_from" id="date_from">
                            <input type="hidden" name="date_to" id="date_to">
                            <input type="hidden" name="created_from_date" id="export_created_from_date">
                            <label for="" style="visibility: hidden;"> d</label>
                            <button onclick="exportData()" class="btn btn-success form-control">Export</button>
                        </div>
                    </form>

                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Name</th>
                                    <th>Total Leads</th>
                                    <th>Not Processed Leads</th>
                                    @foreach ($statuses as $item)
                                        <th>{{ $item->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody>


                                @foreach ($data as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        @foreach ($row as $cell)
                                            <td class="text-center">{{ $cell }}</td>
                                        @endforeach

                                    </tr>
                                @endforeach




                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <script>
        const exportData = () => {
            event.preventDefault();
            var currentUrl = window.location.href;
            const filterDateFrom = $('.from_date').val()
            const filterDateTo = $('.to_date').val()
            const created_from_date = $('.created_from_date').val()
            const date_from = $('#date_from').val(filterDateFrom);
            const date_to = $('#date_to').val(filterDateTo);
            const export_created_date_from = $('#export_created_from_date').val(created_from_date);
            const url = currentUrl.replace(/\?.*/, '') + '/export' + window.location.search;

            $('#leads-report-export-form').attr('action', url).submit();
        }
    </script>
@endsection
