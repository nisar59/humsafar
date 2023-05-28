@extends('layouts.template')
@section('title')
Roles & Permissions
@endsection
@section('content')
<div class="page-title-box">
  <div class="row align-items-center">
    <div class="col-md-8">
      <h6 class="page-title">Roles & Permissions</h6>
      <ol class="breadcrumb m-0">
        <li class="breadcrumb-item">{{Settings()->portal_name}}</li>
        <li class="breadcrumb-item">Roles & Permissions</li>
        <li class="breadcrumb-item active">listing</li>
      </ol>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12">
    <div class="card card-primary">
      <div class="card-header bg-white">
        <div class="row">
          <h4 class="col-md-6">Roles & Permissions</h4>
          <div class="col-md-6 text-end">
            <a href="{{url('roles/create')}}" class="btn btn-success">+</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm table-hover table-bordered" id="roles" style="width:100%;">
            <thead class="text-center bg-primary text-white">
              <tr>
                <th>Name</th>
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
var roles_table = $('#roles').DataTable({
processing: true,
serverSide: true,
ajax: "{{url('roles')}}",
buttons:[],
columnDefs: [ {
"targets": 1,
"orderable": false,
"searchable": false
} ],
columns: [
{data: 'name', name: 'name'},
{data: 'action', name: 'action', orderable: false, class:"d-flex justify-content-center w-auto", searchable: false},
]
});
});
</script>
@endsection