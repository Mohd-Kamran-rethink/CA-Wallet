@extends('Admin.index')
@section('content')
<style>
    .custom-modal {
        max-width: 70%;
        width: 70%;
        /* max-height: 50%; */
        height: 50%
        
    }

    @media (max-width: 580px) {
        .custom-modal {
            max-width: 100%;
            width: 100%;
        }
    }
    #historytable{overflow-y: scroll}
</style>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Attendance</h1>
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
                <div class="mb-3 d-flex justify-content-between align-items-centers">
                    <form action="{{ url('attendance') }}" method="GET" id="search-form"
                        class="filters d-flex flex-row col-8 pl-0">
                        @if (session('user')->role == 'manager')
                            <div class="input-group col-4">
                                <select name="id" id="agent_id" class="form-control">
                                    <option value="">--Filter By Agent--</option>
                                    @foreach ($agents as $item)
                                        <option {{ isset($querryId) && $querryId == $item->id ? 'selected' : '' }}
                                            value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        <div class="input-group ">
                            <input name="date" type="date" class="form-control" id="datePicker"
                                value="{{ isset($querryDate) ? $querryDate : '' }}">
                        </div>
                        <div class="input-group col-4">
                            <button class="btn btn-success" onclick="searchData()">Filter</button>
                        </div>
                        
                        @endif  
                    </form>
                    <div>
                        <a href="{{ url('managers/add') }}" class="btn btn-primary">Add New Manager</a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Name</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($users as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->date }}</td>
                                                <td>{{ $item->hours }}</td>
                                                <td><button class="btn btn-success"
                                                        onclick="attendanceModal({{ $item->user_id }})">View
                                                        Activity</button></td>

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
    <div class="modal fade show" id="modal-history" style=" padding-right: 17px;" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered custom-modal" style="">
            <div class="modal-content" style="height: 100%;">
                <div class="modal-header">
                    <h4 class="modal-title">User Activity</h4>
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
        const searchData = () => {
            event.preventDefault();
            const url = new URL(window.location.href);

            const searchValue = $('#agent_id').val().trim();
            const date = $('#datePicker').val();
            url.searchParams.set('id', searchValue);
            url.searchParams.set('date', datePicker);
            $('#search-form').attr('action', url.toString()).submit();
        }
        const attendanceModal = (agentId) => {
            $(`#modal-history`).modal("show");
            let date=$('#datePicker').val();
            const loadingSpinner = "<div class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</div>";
            const noData = "<div class='text-center'>No Data Found</div>";
            $("#historytable").html(loadingSpinner);
            $.ajax({
                url: BASE_URL +
                    "/attendance/viewActivity?id=" +
                    agentId+'&date='+date,
                success: function(data) {
                    if(data?.data.length>0)
                    {
                       let tableDate= createTable(data?.data)
                       $("#historytable").html(tableDate);
                    }
                    else
                    {
                        $("#historytable").html(noData);
                    }
                },
            });

        }
        function createTable(data) {
        let table = "<table class='table'>";
        table +=
            "<thead><tr><th>Sr.No</th><th style='width:30%;text-align:center'>Activity</th><th style='width:10%;text-align:center'>Time </th><th>Created at</th></thead>";
        table += "<tbody>";
        data.forEach((item, index) => {
            table +=
                `<tr><td>${index+1}</td><td style='width:30%;text-align:center'>${item.action??'--'}</td><th style='width:10%;text-align:center'>${item.time??'--'}</th><td style="word-wrap">${(item.date) ??'--'}</td></tr>`;
        });
        table += "</tbody></table>";
        return table;
    }
    </script>

@endsection
