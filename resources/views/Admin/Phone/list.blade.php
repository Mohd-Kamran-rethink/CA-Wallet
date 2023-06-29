@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Phone Numbers</h1>
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
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-between align-items-centers">
                    <form action="{{ url('phone-numbers') }}" method="GET" id="search-form">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="text" value="{{ isset($searchTerm) ? $searchTerm : '' }}" name="table_search"
                                class="form-control float-right" placeholder="Search" id="searchInput">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default" onclick="searchData()" id="search-button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <div>
                        <a href="{{ url('phone-numbers/add') }}" class="btn btn-primary">Add New Number</a>
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
                                            <th>Number</th>
                                            <th>Wati Agent</th>
                                            <th>WhatsApp Agent</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($numbers as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->number }}</td>
                                                <td>{{ $item->watiAgent??'-' }}</td>
                                                <td>{{ $item->WhatsAppAgent??'-' }}</td>
                                                <td style="text-transform: capitalize">{{ $item->status }}</td>
                                                <td>
                                                    
                                                    <a href="{{ url('phone-numbers/edit?id='.$item->id) }}"
                                                        title="Edit this source" class="btn btn-primary"><i
                                                            class="fa fa-pen"></i></a>
                                                            <button title="Change Status"
                                                            onclick="reassignModal({{$item->id}})"
                                                            class="btn btn-danger">Reassign</button>
                                                    <button title="Change Status"
                                                        onclick="manageModal({{ $item->id }})"
                                                        class="btn btn-danger">Change Status</button>
                                                        <a href="{{ url('phone-numbers/history?id='.$item->id) }}"
                                                            title="History" class="btn btn-warning">History</a>
                                                            
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
                            {{-- <div class="card-footer clearfix">
                                {{ $numbers->links('pagination::bootstrap-4') }}
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade show" id="reassignModal" style=" padding-right: 17px;" aria-modal="true" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Change Status</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="{{ url('/phone-numbers/reassign') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" id="reasignID">
                            <div class="form-group mx-3">
                                <label for="">Agent</label>
                                <select class="form-control" name="agent" id="status">
                                    <option value="0">--Choose--</option>
                                    @foreach ($agents as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mx-3">
                                <label for="">Platform</label>
                                <select class="form-control" name="platform" id="status">
                                    <option value="0">--Choose--</option>
                                    <option value="whatsapp">WhatsApp</option>
                                    <option value="wati">Wati</option>
                                  
                                </select>
                            </div>
                                    
                        <div class="modal-footer ">
                            <button type="submit" class="btn btn-danger">Submit</button>
                            <button type="button" data-dismiss="modal" aria-label="Close"
                                class="btn btn-default">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade show" id="modal-default" style=" padding-right: 17px;" aria-modal="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Change Status</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ url('/phone-numbers/change-status') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="deleteInput">
                        <div class="form-group mx-3">
                            <label for="">Select</label>
                            <select class="form-control" name="status" id="status">
                                <option value="0">--Choose--</option>
                                <option value="active">Active</option>
                                <option value="inactive">InActive</option>
                                <option value="banned">Banned</option>
                            </select>
                        </div>
                    <div class="modal-footer ">
                        <button type="submit" class="btn btn-danger">Submit</button>
                        <button type="button" data-dismiss="modal" aria-label="Close"
                            class="btn btn-default">Cancel</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        const searchData = () => {
            event.preventDefault();
            const url = new URL(window.location.href);

            const searchValue = $('#searchInput').val().trim();
            url.searchParams.set('search', searchValue);
            $('#search-form').attr('action', url.toString()).submit();
        }
        function reassignModal(id)
        {
            console.log(id);
            $('#reassignModal').modal('show')
            $('#reasignID').val(id)
        }
    </script>
@endsection
