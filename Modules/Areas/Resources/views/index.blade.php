@extends('layouts.template')
@section('title')
Areas
@endsection
@section('content')
<div class="page-title-box">
  <div class="row align-items-center">
    <div class="col-md-8">
      <h6 class="page-title">Areas</h6>
      <ol class="breadcrumb m-0">
        <li class="breadcrumb-item">{{Settings()->portal_name}}</li>
        <li class="breadcrumb-item">Areas</li>
        <li class="breadcrumb-item active">Listing</li>
      </ol>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12">
    <div class="card card-primary">
      <div class="card-header bg-white">
        <div class="row">
          <h4 class="col-md-6">Areas</h4>
          <div class="col-md-6 text-end">
            <a href="{{url('areas/create')}}" class="btn btn-success">+</a>
            <a href="{{url('areas/import')}}" class="btn btn-info"><i class="fas fa-cloud-upload-alt"></i></a>
            <a href="{{url('artisan/network:areas')}}" class="btn btn-primary">MIS</i></a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm table-hover table-bordered" id="users" style="width:100%;">
            <thead class="text-center bg-primary text-white">              
              <tr>
                <th>Name</th>
                <th>Code</th>
                <th>Region</th>
                <th>Status</th>
                <th>Modified By</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('js')
<script type="text/javascript">
//Roles table
$(document).ready( function(){
var areas_table = $('#users').DataTable({
processing: true,
serverSide: true,
ajax: "{{url('areas')}}",
buttons:[],
columns: [
{data: 'name', name: 'name'},
{data: 'code', name: 'code'},
{data: 'region_id', name: 'region_id.name'},
{data: 'status', name: 'status', class:'text-center'},
{data: 'modified_by', name: 'modified_by', class:'text-center', orderable: false, searchable: false},
{data: 'action', name: 'action', orderable: false, class:"d-flex justify-content-center w-auto", searchable: false},
],

"fnDrawCallback": function() {
    $('[data-bs-toggle="tooltip"]').tooltip({
      html:true,
    });
  }
});
});
</script>
@endsection