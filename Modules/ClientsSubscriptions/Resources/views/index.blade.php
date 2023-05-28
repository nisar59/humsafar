@extends('layouts.template')
@section('title')
Subscriptions & Services
@endsection
@section('content')
<div class="page-title-box">
  <div class="row align-items-center">
    <div class="col-md-8">
      <h6 class="page-title">Subscriptions & Services</h6>
      <ol class="breadcrumb m-0">
        <li class="breadcrumb-item">{{Settings()->portal_name}}</li>
        <li class="breadcrumb-item">Subscriptions & Services</li>
        <li class="breadcrumb-item active">listing</li>
      </ol>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12">
    <div class="card card-primary" id="filters-container">
      <div class="card-header bg-white" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
        <h4><i class="fas fa-filter"></i> Filters</h4>
      </div>
      <div class="card-body p-0">
        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#filters-container">
          <div class="p-3 accordion-body">
            <div class="row">
              <div class="col-md-3 form-group">
                <label for="">Desk Code</label>
                <input type="text" class="form-control filters" name="desk_code" placeholder="Desk Code">
              </div>
              <div class="col-md-3 form-group">
                <label for="">Packages</label>
                <select name="package_id" class="form-control filters select2">
                  <option value="">Select</option>
                  @foreach(AllPackages() as $package)
                    <option value="{{$package->id}}">{{$package->title}}</option>
                  @endforeach
                </select> 
              </div>
              <div class="col-md-3 form-group">
                <label for="">Subscription Date</label>
                <input type="date" class="form-control filters" name="subscription_date" placeholder="Subscription Date">
              </div>
              <div class="col-md-3 form-group">
                <label for="">Services</label>
                <select class="form-control filters" name="services">
                  <option value="">Select</option>
                  <option value="1">Active</option>
                  <option value="0">Pending</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="card card-primary">
      <div class="card-header bg-white"> 
        <div class="row">
        <h4 class="col-md-6">Subscriptions & Services</h4>
        <div class="col-md-6 text-end">
          <a class="btn btn-success" href="{{url('clients-subscriptions/export')}}" id="export"><i class="fas fa-cloud-download-alt"></i></a>
          <a class="btn btn-info" href="javascript:void(0)" data-bs-target="#exampleModal" data-bs-toggle="modal"><i class="fas fa-cloud-upload-alt"></i></a>
        </div>
      </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm table-hover table-bordered" id="data_table" style="width:100%;">
            <thead class="text-center bg-primary text-white">              
              <th>ID</th>
              <th>Desk Code</th>
              <th>Client Name</th>
              <th>User Name</th>
              <th>Package</th>
              <th>Amount</th>
              <th>Subscription Date</th>
              <th>Expire Date</th>
              <th>Services</th>
              <th>Modified By</th>
              <th>Action</th>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="mdl">
@include('clientssubscriptions::bulk-services')
</div>
@endsection
@section('js')
<script type="text/javascript">
//Roles table
$(document).ready( function(){
  var data_table;
  function DataTableInit(data={}) {
  data_table = $('#data_table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url:"{{url('clients-subscriptions')}}",
      data:data,
    },
    buttons:[],
    columns: [
      {data: 'id', name: 'id'},
      {data: 'desk_id', name: 'desk_id'},
      {data: 'client_id', name: 'client_id'},
      {data: 'user_id', name: 'user_id'},
      {data: 'package_id', name: 'package_id'},
      {data: 'amount', name: 'amount'},
      {data: 'subscription_date', name: 'subscription_date'},
      {data: 'expire_date', name: 'expire_date'},
      {data: 'services', name: 'services', class:'text-center', orderable: false, searchable: false},
      {data: 'modified_by', name: 'modified_by', class:"text-center", orderable: false, searchable: false},
      {data: 'action', name: 'action', orderable: false, class:"d-flex justify-content-center w-auto", searchable: false},
    ],
    "fnDrawCallback": function() {
        $('[data-bs-toggle="tooltip"]').tooltip({
          html:true,
        });
      },
    });
}

DataTableInit();


$(document).on('change', '.filters', function () {
var data={};
$('.filters').each(function() {
data[$(this).attr('name')]=$(this).val();
});
data_table.destroy();
DataTableInit(data);
});

$(document).on('click', '#export', function (e) {
  e.preventDefault();
  var url=$(this).attr('href');
  var parameters='?';

  $('.filters').each(function() {
  parameters+=$(this).attr('name')+'='+$(this).val()+'&';
  });

window.location=url+parameters;
});

});
</script>
@endsection