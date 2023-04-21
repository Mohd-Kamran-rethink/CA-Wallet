@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Clients</h1>
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

                    <div>
                        <a href="{{ url('clients/add') }}" class="btn btn-primary">Add New Client</a>
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
                                            <th>ID Name</th>
                                            <th>Phone</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($clients as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->ca_id }}</td>
                                                <td>{{ $item->number }}</td>
                                                <td>
                                                    <button onclick="openRedepositModal({{ $item->id }})"
                                                        class="btn btn-secondary">Redeposit</button>
                                                    <a href="{{ url('clients/edit/?id=' . $item->id) }}"
                                                        title="Edit this client" class="btn btn-primary"><i
                                                            class="fa fa-pen"></i></a>
                                                    <button title="Delte this client"
                                                        onclick="manageModal({{ $item->id }})" class="btn btn-danger"><i
                                                            class="fa fa-trash"></i></button>
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
                                {{ $clients->links('pagination::bootstrap-4') }}
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
                <form action="{{ url('clients/redeposit') }}" method="POST">
                    @csrf
                    <div class="px-3">
                        <input type="hidden" id="deposit-id" name="depositId">
                        <div class="form-group mt-3">
                            <label for="">Amount <span class="text-danger">*</span></label>
                            <input type="number" placeholder="1000" name="amount" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer ">
                        <button type="submit" 
                            class="btn btn-success">Submit</button>
                        <button type="button" data-dismiss="modal" aria-label="Close"
                            class="btn btn-default">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- delete client modal --}}
    <div class="modal fade show" id="modal-default" style=" padding-right: 17px;" aria-modal="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Delete user</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ url('/clients/delete') }}" method="POST">
                    @csrf
                    <input type="hidden" name="deleteId" id="deleteInput">
                    <div class="modal-body">
                        <h4>Are you sure you want to delete this client?</h4>
                    </div>
                    <div class="modal-footer ">
                        <button type="submit" class="btn btn-danger">Delete</button>
                        <button type="button" data-dismiss="modal" aria-label="Close"
                            class="btn btn-default">Cancel</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        function openRedepositModal(id) {
            $(`#modal-redeposit`).modal("show");
            $(`#deposit-id`).val(id);
        }
    </script>
@endsection
