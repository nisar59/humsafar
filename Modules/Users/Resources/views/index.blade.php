@extends('layouts.template')
@section('title')
Users
@endsection
@section('content')
<div class="page-title-box">
  <div class="row align-items-center">
    <div class="col-md-8">
      <h6 class="page-title">Users</h6>
      <ol class="breadcrumb m-0">
        <li class="breadcrumb-item">{{Settings()->portal_name}}</li>
        <li class="breadcrumb-item">users</li>
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
              <div class="col-md-4 form-group">
                <label for="">Name</label>
                <input type="text" class="form-control filters" name="name" placeholder="Name">
              </div>
              <div class="col-md-4 form-group">
                <label for="">CNIC</label>
                <input type="text" class="form-control filters" name="cnic" placeholder="CNIC">
              </div>
              <div class="col-md-4 form-group">
                <label for="">Phone</label>
                <input type="text" class="form-control filters" name="phone" placeholder="Phone">
              </div>
              <div class="col-md-6 form-group">
                <label for="">Employee Code</label>
                <input type="text" class="form-control filters" name="emp_code" placeholder="Employee Code">
              </div>
              <div class="col-md-6 form-group">
                <label for="">Branch</label>
                <select name="branch_id" class="form-control filters select2">
                  <option value="">Select</option>
                  @foreach(AllBranches() as $branch)
                    <option value="{{$branch->mis_sync_id}}">{{$branch->name}}</option>
                  @endforeach
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
          <h4 class="col-md-6">Users</h4>
          <div class="col-md-6 text-end">
            <a href="{{url('users/create')}}" class="btn btn-success">+</a>
            <a href="{{url('users/import')}}" class="btn btn-info"><i class="fas fa-cloud-upload-alt"></i></a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm table-hover table-bordered" id="data_table" style="width:100%;">
            <thead class="text-center bg-primary text-white">
              <tr>
                <th>Name</th>
                <th>Role</th>
                <th>Father Name</th>
                <th>CNIC</th>
                <th>Phone</th>
                <th>Employee Code</th>
                <th>Role Name</th>
                <th>Status</th>
                <th>Branch</th>
                <th>Access Level</th>
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
  var data_table;
  function DataTableInit(data={}) {
  data_table = $('#data_table').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url:"{{url('users')}}",
        data:data,
        },
      buttons:[],
      columns: [
            {data: 'name', name: 'name'},
            {data: 'role', name: 'role'},
            {data: 'father_name', name: 'father_name'},
            {data: 'cnic', name: 'cnic'},
            {data: 'phone', name: 'phone'},
            {data: 'emp_code', name: 'emp_code'},
            {data: 'role_name', name: 'role_name'},
            {data: 'status', name: 'status', class:'text-center'},
            {data: 'branch_id', name: 'branch_id'},
            {data: 'access_level', name: 'access_level',class:'text-center'},
            {data: 'action', name: 'action', orderable: false, class:"d-flex justify-content-center w-auto", searchable: false},
      ]
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


});
</script>
@endsection