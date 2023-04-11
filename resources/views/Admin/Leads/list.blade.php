@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Leads</h1>
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
                    <form action="{{ url('leads') }}" method="GET" id="search-form">
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
                        <a href="{{ url('leads/add') }}" class="btn btn-primary">Add Single Lead</a>
                        <a href="{{ url('leads/import') }}" class="btn btn-primary">Import Leads</a>
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
                                            <th>Source</th>
                                            <th>Date</th>
                                            <th>Name</th>
                                            <th>Number</th>
                                            <th>Language</th>
                                            <th>ID Name</th>
                                            <th>Agent</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($leads as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->source }}</td>
                                                <td>{{ $item->date }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->number }}</td>
                                                <td>{{ $item->language }}</td>
                                                <td>{{ $item->idName }}</td>
                                                <td>{{ $item->Agent }}</td>
                                                <td>
                                                    <a href="{{ url('leads/edit/?id=' . $item->id) }}"
                                                        title="Edit this lea" class="btn btn-primary"><i
                                                            class="fa fa-pen"></i></a>
                                                    <button title="Delte this lead"
                                                        onclick="manageModal({{ $item->id }})"
                                                        class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                                </td>


                                            </tr>
                                        @endforeach

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
    <div class="modal fade show" id="modal-default" style=" padding-right: 17px;" aria-modal="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Delete user</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ url('/sources/delete') }}" method="POST">
                    @csrf
                    <input type="hidden" name="deleteId" id="deleteInput">
                    <input type="hidden" name="role" id="deleteInput" value="agent">
                    <div class="modal-body">
                        <h4>Are you sure you want to delete this source?</h4>
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
        const searchData = () => {
            event.preventDefault();
            const url = new URL(window.location.href);

            const searchValue = $('#searchInput').val().trim();
            url.searchParams.set('search', searchValue);
            $('#search-form').attr('action', url.toString()).submit();
        }
    </script>
@endsection
