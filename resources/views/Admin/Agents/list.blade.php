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
                    {{ session('msg-error') }}
                </div>
            @endif
        </div>
    </section>



    <section class="content">
        <div class="card">
            <div class="card-body">

                <div class="mb-3 d-flex justify-content-between align-items-centers row">
                    <form class="filters d-flex flex-row   mt-2" action="{{ url('leads/list') }}" method="GET"
                        id="search-form">
                        <div class="col-3 " style="width: 150px;">
                            <input type="text" value="{{ isset($searchTerm) ? $searchTerm : '' }}" name="table_search"
                                class="form-control float-right" placeholder="Search" id="searchInput">
                        </div>
                        {{-- <div class="col-4">
                            <select name="stateFilter" id="stateFilter" class="form-control">
                                <option value="">--Filter By State--</option>
                                @foreach ($states as $item)
                                    <option {{ isset($stateFilter) && $stateFilter == $item->name ? 'selected' : '' }}
                                        value="{{ $item->name }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div> --}}
                        @if (session('user')->role == 'manager')
                            <div class="col-4">
                                <select name="languageFilter" id="languageFilter" class="form-control">
                                    <option value="">--Filter By Language--</option>
                                    @foreach ($languages as $item)
                                        <option
                                            {{ isset($languageFilter) && $languageFilter == $item->id ? 'selected' : '' }}
                                            value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="input-group ">
                            <button class="btn btn-success" onclick="searchData()">Filter</button>
                        </div>
                    </form>
                    <div>
                        <a href="{{ url('agents/add') }}" class="btn btn-primary">Add New Agent</a>
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
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Languages</th>
                                            <th>Agent Type</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($agentsWithLanguages  as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item['name'] }}</td>
                                                <td>{{ $item['email'] }}</td>
                                                <td>{{ $item['phone'] }}</td>
                                                <td>
                                                    @foreach ($item['languages'] as $language)
                                                        {{ $language }},
                                                    @endforeach
                                                </td>
                                                <td>{{ $item['agent_type'] }}</td>
                                                <td>
                                                    <a class="btn btn-dark"
                                                        href="{{ url('leads?agent_id=' . $item['id']) }}">View Leads</a>
                                                    <a class="btn btn-warning"
                                                        href="{{ url('agents/assign-numbers?id=' . $item['id']) }}">Assign
                                                        Numbers</a>
                                                    <a href="{{ url('agents/edit/?id=' . $item['id']) }}"
                                                        title="Edit this agent" class="btn btn-primary"><i
                                                            class="fa fa-pen"></i></a>
                                                    <button title="Delte this agent"
                                                        onclick="manageModal({{ $item['id'] }})"
                                                        class="btn btn-danger"><i class="fa fa-trash"></i></button>
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
                                {{ $agents->links('pagination::bootstrap-4') }}
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
                <form action="{{ url('/agents/delete') }}" method="POST">
                    @csrf
                    <input type="hidden" name="deleteId" id="deleteInput">
                    <input type="hidden" name="role" id="deleteInput" value="agent">
                    <div class="modal-body">
                        <h4>Are you sure you want to delete this agent?</h4>
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
            const stateFilter = $('#stateFilter').val().trim();
            const languageFilter = $('#languageFilter').val().trim();
            url.searchParams.set('search', searchValue);
            url.searchParams.set('stateFilter', stateFilter);
            url.searchParams.set('languageFilter', languageFilter);
            $('#search-form').attr('action', url.toString()).submit();
        }
    </script>
@endsection
