@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 d-flex justify-content-between">
                    <h1>Deposit History</h1>
                    <a href="{{url('/clients')}}" class="btn btn-sm btn-primary">Go Back</a>
               
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
            <div class="card-body">
                
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Date</th>
                                            <th>type</th>
                                            <th>Amount</th>
                                           
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($depositHistory as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td>
                                                <td>{{ $item->type }}</td>
                                                <td>{{ $item->amount }}</td>
                                                
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
                                {{ $depositHistory->links('pagination::bootstrap-4') }}
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
