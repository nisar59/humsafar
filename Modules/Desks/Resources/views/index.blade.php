@extends('layouts.template')
@section('title')
Desks
@endsection
@section('content')
<div class="page-title-box">
  <div class="row align-items-center">
    <div class="col-md-8">
      <h6 class="page-title">Desks</h6>
      <ol class="breadcrumb m-0">
        <li class="breadcrumb-item">{{Settings()->portal_name}}</li>
        <li class="breadcrumb-item">Desks</li>
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
              <div class="col-md-6 form-group">
                <label for="">Desk Code</label>
                <input type="text" class="form-control filters" name="desk_code" placeholder="Desk Code">
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
          <h4 class="col-md-6">Desks</h4>
          <div class="col-md-6 text-end">
            <a href="{{url('desks/create')}}" class="btn btn-success">+</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm table-hover table-bordered" id="data_table" style="width:100%;">
            <thead class="text-center bg-primary text-white">
              <tr>
                <th>Desk Code</th>
                <th>Branch</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Role Name</th>
                <th>Status</th>
                <th>Association</th>
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
  var data_table;
function DataTableInit(data={}) {
  data_table = $('#data_table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url:"{{url('desks')}}",
        data:data,
        },    
    buttons:[],
    columns: [
      {data: 'desk_code', name: 'desk_code'},
      {data: 'branch_id', name: 'branch_id'},
      {data: 'user_name', name: 'user_name'},
      {data: 'user_phone', name: 'user_phone'},
      {data: 'role_name', name: 'role_name'},
      {data: 'status', name: 'status',class:'text-center'},
      {data: 'is_associated', name: 'is_associated', class:'text-center'},
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


});
</script>
@endsection