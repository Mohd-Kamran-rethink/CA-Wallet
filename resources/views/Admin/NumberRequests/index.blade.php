@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Agents</h1>
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

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Agent Name</th>
                                            <th>Agent Email</th>
                                            <th>Agent Phone</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($requests as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->email }}</td>
                                                <td>{{ $item->phone }}</td>
                                                <td>
                                                    <button title="Approve number requests"
                                                        onclick="approveNumberRequests({{ $item->userID }},'Yes')"
                                                        class="btn btn-success">Approve</button>
                                                    <button title="Reject Request"
                                                        onclick="approveNumberRequests({{ $item->userID }},'No')"
                                                        class="btn btn-danger">Reject</button>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- mannual lead add modal --}}
    <div class="modal fade show" id="approval-modal" style=" padding-right: 17px;" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="title-of-modal">Take action for this request</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                <div class="modal-footer ">
                    <form action="{{url('clients/request')}}" method="POST">
                        @csrf
                        <input type="hidden" id="AgentID" name="agent_id">
                        <input type="hidden" id="status" name="status">
                        <button  type="submit" class="btn btn-success"
                        id="mass-status-change-button">Yes</button>
                        <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-default">Cancel</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <script>
        function approveNumberRequests(id,status)
        {
            $('#approval-modal').modal('show');
            $('#AgentID').val(id);
            $('#status').val(status);
            if(status=='Yes')
            {
                $('#title-of-modal').html("Are you sure you want to unreveal clients phone number to this agent?");
            }
            else
            {
                $('#title-of-modal').html("Are you sure you want to reject this request?");

            }

            
        }
    </script>
@endsection
