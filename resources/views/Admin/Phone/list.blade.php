@extends('Admin.index')
@section('content')
    <?php $platforms = ['wa' => 'Whatsapp', 'wa_c' => 'Whatsapp Clone', 'wa_b' => 'Whatsapp Business']; ?>
    <?php $devices = ['galaxy_a04' => 'Galaxy A04', 'galaxy_a03_core' => 'Galaxy A03 Core', 'redmi_9a' => 'Redmi 9A', 'oppo_f1s' => 'Oppo F1S', 'galaxy_a03' => 'Galaxy A03', 'galaxy_s22' => 'Galaxy S22']; ?>
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
                    <form class="filters d-flex flex-row  col-lg-7 mt-2" action="{{ url('leads/list') }}" method="GET"
                        id="search-form">
                        <div class="input-group input-group-md col-3 " style="width: 150px;">
                            <input type="text" value="{{ isset($searchTerm) ? $searchTerm : '' }}" name="table_search"
                                class="form-control float-right" placeholder="Search" id="searchInput">
                        </div>
                        <div class="input-group col-3">
                            <select name="platform_seach" id="platform_seach" class="form-control">
                                <option value="">--Filter By Platform--</option>
                                <option {{ isset($platform_search) && $platform_search == 'wati' ? 'selected' : '' }}
                                    value="wati">Wati</option>
                                <option {{ isset($platform_search) && $platform_search == 'wa' ? 'selected' : '' }}
                                    value="wa">Whatsapp</option>
                                <option {{ isset($platform_search) && $platform_search == 'wa_b' ? 'selected' : '' }}
                                    value="wa_b">Whatsapp Business</option>
                                <option {{ isset($platform_search) && $platform_search == 'wa_c' ? 'selected' : '' }}
                                    value="wa_c">Whatsapp Clone</option>
                            </select>
                        </div>
                        <div class="input-group col-3">
                            <select name="status" id="status_search" class="form-control">
                                <option value="">--Filter By Status--</option>
                                <option {{ isset($status) && $status == 'review' ? 'selected' : '' }} value="review">Review
                                </option>
                                <option {{ isset($status) && $status == 'otp' ? 'selected' : '' }} value="otp">OTP Needed
                                </option>
                                <option {{ isset($status) && $status == 'active' ? 'selected' : '' }} value="active">Assigned
                                </option>
                                <option {{ isset($status) && $status == 'inactive' ? 'selected' : '' }} value="inactive">In
                                    Active</option>
                                <option {{ isset($status) && $status == 'banned' ? 'selected' : '' }} value="banned">Banned
                                </option>
                            </select>
                        </div>
                        <div class="input-group col-3">
                            <select name="filetByAgent" id="" class="form-control">
                                <option value="">--Filer by Agent--</option>
                                @foreach ($agents as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                                    
                        <div class="input-group ">
                            <button class="btn btn-success" onclick="searchData()">Filter</button>
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
                                            <th>Platform</th>
                                            <th>Agent</th>
                                            <th>Device Name</th>
                                            <th>Device Code</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($numbers as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->number ?? '' }}</td>
                                                <td>
                                                    @if ($item->platform == 'wa')
                                                        Whatsapp
                                                    @elseif($item->platform == 'wa_b')
                                                        Whatsapp Business
                                                    @elseif($item->platform == 'wa_c')
                                                        Whatsapp Clone
                                                    @elseif($item->platform == 'wati')
                                                        Wati
                                                    @endif

                                                </td>
                                                <td>{{ $item->agentName ?? 'Spare' }}</td>
                                                <td>{{ $item->device_name ?? '' }}</td>
                                                <td>{{ $item->device_code }}</td>
                                                <td style="text-transform: capitalize">
                                                    {{ $item->status == 'active' && $item->PhoneAgent ? 'Assigned' : $item->status }}
                                                </td>
                                                <td>

                                                    <a href="{{ url('phone-numbers/edit?id=' . $item->id) }}"
                                                        title="Edit this source" class="btn btn-primary"><i
                                                            class="fa fa-pen"></i></a>
                                                    <button title="Change Status"
                                                        onclick="reassignModal({{ $item->id }})"
                                                        class="btn btn-danger">Reassign</button>
                                                    <button title="Change Status"
                                                        onclick="manageModal({{ $item->id }})"
                                                        class="btn btn-danger">Change Status</button>
                                                    <a href="{{ url('phone-numbers/history?id=' . $item->id) }}"
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
                            <select style="width: 100%" class="form-control searchOptions" name="agent" id="status">
                                <option value="0">--Choose--</option>
                                <option value="0">SPARE</option>
                                @foreach ($agents as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!--
                                <div class="form-group mx-3">
                                    <label for="">Platform</label>
                                    <select class="form-control" name="platform" id="status">
                                        <option value="0">--Choose--</option>
                                        <option value="whatsapp">WhatsApp</option>
                                        <option value="whatsapp_b">WhatsApp Business</option>
                                        <option value="wati_b">Whatsapp Clone</option>
                                        <option value="wati">WATI</option>
                                      
                                    </select>
                                </div> -->

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
                            <option value="review">Review</option>
                            <option value="otp">OTP Needed</option>
                            <option value="assigned">Assigned</option>
                            <option value="inactive">In Active</option>
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
            const platformSeach = $('#platform_seach').val();
            const agentSearch = $('#agent_id_search').val();
            url.searchParams.set('search', searchValue);
            url.searchParams.set('platform_seach', searchValue);
            url.searchParams.set('agent_id_search', searchValue);
            $('#search-form').attr('action', url.toString()).submit();
        }

        function reassignModal(id) {
            console.log(id);
            $('#reassignModal').modal('show')
            $('#reasignID').val(id)
        }
    </script>
@endsection
