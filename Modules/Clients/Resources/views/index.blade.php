@extends('layouts.template')
@section('title')
Clients
@endsection
@section('content')
@php($req=request())
<div class="page-title-box">
  <div class="row align-items-center">
    <div class="col-md-8">
      <h6 class="page-title">Clients</h6>
      <ol class="breadcrumb m-0">
        <li class="breadcrumb-item">{{Settings()->portal_name}}</li>
        <li class="breadcrumb-item">Clients</li>
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
                <label for="">Desk</label>
                <input type="text" class="form-control filters" name="desk_code" placeholder="Desk Code">
              </div>              
              <div class="col-md-6 form-group">
                <label for="">Name</label>
                <input type="text" class="form-control filters" name="name" placeholder="Name">
              </div>
              <div class="col-md-4 form-group">
                <label for="">CNIC</label>
                <input type="text" class="form-control filters" name="cnic" placeholder="CNIC">
              </div>
              <div class="col-md-4 form-group">
                <label for="">Phone</label>
                <input type="text" class="form-control filters" name="phone_primary" placeholder="Phone">
              </div>
              <div class="col-md-4 form-group">
                <label for="">Status</label>
                <select name="status" class="form-control filters">
                  <option value="">Select</option>
                  <option value="1" @if($req->active=='1') selected @endif>Active</option>
                  <option value="0" @if($req->active=='0') selected @endif>Deactive</option>
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
          <h4 class="col-md-6">Clients</h4>
          <div class="col-md-6 text-end">
            <a href="{{url('clients/create')}}" class="btn btn-success">+</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm table-hover table-bordered" id="data_table" style="width:100%;">
            <thead class="text-center bg-primary text-white">
              <tr>
                <th>Desk Code</th>
                <th>Name</th>
                <th>Parentage</th>
                <th>DOB</th>
                <th>Education</th>
                <th>Gender</th>
                <th>Phone</th>
                <th>CNIC</th>
                <th>Monthly Income</th>
                <th>Address</th>
                <th>Medical Expense</th>
                <th>Client Status</th>
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
<div id="mdl"></div>
@endsection
@section('js')
<script type="text/javascript">
//Roles table
$(document).ready( function(){
  var data_table;
  function DataTableInit() {
    
    var data={};
    $('.filters').each(function() {
    data[$(this).attr('name')]=$(this).val();
    });
    
  data_table = $('#data_table').DataTable({
        processing: true,
        serverSide: true,
        ajax:{
          url:"{{url('clients')}}",
          data:data,
        },
        buttons:[],
        columns: [
            {data: 'desk_id', name: 'desk_id'},
            {data: 'name', name: 'name'},
            {data: 'parentage', name: 'parentage'},
            {data: 'dob', name: 'dob'},
            {data: 'education', name: 'education'},
            {data: 'gender', name: 'gender'},
            {data: 'phone_primary', name: 'phone_primary'},
            {data: 'cnic', name: 'cnic'},
            {data: 'monthly_income', name: 'monthly_income'},
            {data: 'address', name: 'address'},
            {data: 'medical_expense', name: 'medical_expense'},
            {data: 'status', name: 'status', class:'text-center'},
            {data: 'modified_by', name: 'modified_by', orderable: false, class:'text-center', searchable: false},
            {data: 'action', name: 'action', orderable: false, class:"d-flex justify-content-center border-0 w-auto", searchable: false},
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
  data_table.destroy();
  DataTableInit();
});




$(document).on("click", '.client-subscription', function() {
  var url=$(this).data('href');
  $.ajax({
      url:url,
      type:"GET",
      success:function(res){
        if(res.success){
          $("#mdl").html(res.data);
          $("#exampleModal").modal('show');
        }
      else{
        error('Something went wrong');
      }
      },
      error:function(err) {
        error('Something went wrong');
      }
  });

});



});
</script>
@endsection