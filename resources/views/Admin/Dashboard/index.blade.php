@extends('Admin.index')
@section('content')
<section class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="font-weight-bold">Dashboard</h1>
         </div>
      </div>
   </div>
   @if (isset($lastEntry) && $lastEntry->actions == 'break')
   <div class="alert alert-danger" role="alert">
      You are on break please change your break status
   </div>
   @endif
</section>
<section class="content">
   <div class="container-fluid">
      <div class="row">
         @if (session('user')->role == 'manager' && session('user')->is_admin == 'Yes')
         <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
               <div class="inner">
                  <h3>{{ $managers->count() }}</h3>
                  <p>All Managers</p>
               </div>
               <div class="icon">
                  <i class="fa fa-users"></i>
               </div>
               <a href="{{ url('/managers') }}" class="small-box-footer">More info <i
                  class="fas fa-arrow-circle-right"></i></a>
            </div>
         </div>
         @endif
         @if (session('user')->role == 'manager')
         <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
               <div class="inner">
                  <h3>{{ $agents->count() }}</h3>
                  <p>All Agents</p>
               </div>
               <div class="icon">
                  <i class="fa fa-users"></i>
               </div>
               <a href="{{ url('/agents') }}" class="small-box-footer">More info <i
                  class="fas fa-arrow-circle-right"></i></a>
            </div>
         </div>
         @endif
         @if (session('user')->role == 'agent')
         <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
               <div class="inner">
                  <h3>44</h3>
                  <p>Total Leads</p>
               </div>
               <div class="icon">
                  <i class="ion ion-person-add"></i>
               </div>
               <a href="#" class="small-box-footer">More info <i
                  class="fas fa-arrow-circle-right"></i></a>
            </div>
         </div>
         <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
               <div class="inner">
                  <h3>65</h3>
                  <p>Completed Leads</p>
               </div>
               <div class="icon">
                  <i class="ion ion-pie-graph"></i>
               </div>
               <a href="#" class="small-box-footer">More info <i
                  class="fas fa-arrow-circle-right"></i></a>
            </div>
         </div>
         @endif
      </div>
   </div>
</section>
{{-- incosistent clients --}}
<section class="content mt-4">
   <div class="row mb-2">
      <div class="col-sm-6">
         <h4 class="font-weight-bold px-2">InCosistent Clients</h4>
      </div>
   </div>
   <div class="card-body ">
    <div class="table-responsive">
        
      <table class="table table-hover text-nowrap">
         <thead>
            <tr>
               <th>S.No.</th>
               <th>Client Name</th>
               <th>Client ID Name</th>
               <th>Last Deposit Amount</th>
               <th>Since Days</th>
               <th>Last Deposit Date</th>
            </tr>
         </thead>
         <tbody>
            @forelse ($clients as $item)
            <tr>
               <td>{{ $loop->iteration }}</td>
               <td>{{ $item->name??'--' }}</td>
               <td>{{ $item->ca_id }}</td>
               <td>{{ $item->depositHistories->last()->amount ?? '' }}</td>
               <td>{{ $item->days_since_last_deposit }}</td>
               <td>{{ $item->depositHistories->last()->created_at->format("d-m-Y H:i:s") }}</td>
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
   <div class="card-footer clearfix">
      {{ $clients->links('pagination::bootstrap-4') }}
   </div>
   </div>
</section>
@endsection