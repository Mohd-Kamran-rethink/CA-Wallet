@extends('Admin.index')
@section('content')
    {{-- reports --}}
    <section class="content">
        <div class="content-header">
            <h2 class="">Deposits Reports</h2>
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <form action="{{ url('reports/deposits') }}" method="GET" id="search-form"
                        class="filters d-flex flex-row col-11 pl-0">

                        <div class="col-3 ">
                            <label for="">From</label>
                            <input name="from_date" type="date" class="form-control from_date" id="datePicker"
                                value="{{ isset($startDate) ? $startDate : '' }}">
                        </div>
                        <div class="col-3">
                            <label for="">To</label>
                            <input name="to_date" type="date" class="form-control to_date" id="datePicker"
                                value="{{ isset($endDate) ? $endDate : '' }}">
                        </div>
                        <div class="">
                            <label for="" style="visibility: hidden;">filter</label>
                            <button class="btn btn-success form-control" onclick="searchData()">Filter</button>
                        </div>
                    </form>
                    <form action="{{ url('/report/deposits/export') }}" method="post" id="leads-report-export-form">
                        @csrf
                        <div>
                            <input type="hidden" name="date_from" id="date_from">
                            <input type="hidden" name="date_to" id="date_to">
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
                                        @foreach ($data[0] as $col)
                                            <th>{{ $col }}</th>
                                        @endforeach
                                </tr>

                            </thead>

                            <tbody>
                                @foreach ($data as $key=> $item)
                                @if ($key==0)
                                 @continue   
                                @endif
                                    <tr>
                                        <td>{{ $key }}</td>
                                        @foreach ($item as $col)
                                            <td>{{ $col }}</td>
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
            const filterDateFrom = $('.from_date').val()
            const filterDateTo = $('.to_date').val()
            const date_from = $('#date_from').val(filterDateFrom);
            const date_to = $('#date_to').val(filterDateTo);
            $('#leads-report-export-form').attr('action', '/reports/deposits/export').submit();
        }
    </script>
@endsection
