@extends('layouts.template')
@section('title')
Deposits
@endsection
@section('content')
<div class="page-title-box">
  <div class="row align-items-center">
    <div class="col-md-8">
      <h6 class="page-title">Deposits</h6>
      <ol class="breadcrumb m-0">
        <li class="breadcrumb-item">{{Settings()->portal_name}}</li>
        <li class="breadcrumb-item">Deposits</li>
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
                <label for="">Deposit Slip No</label>
                <input type="text" class="form-control filters" name="deposit_slip_no" placeholder="Deposit Slip No">
              </div>
              <div class="col-md-3 form-group">
                <label for="">Deposit Date</label>
                <input type="date" class="form-control filters" name="desposit_date" placeholder="Deposit Date">
              </div>
              <div class="col-md-3 form-group">
                <label for="">Verification</label>
                <select class="form-control filters" name="is_verified">
                  <option value="">Select</option>
                  <option value="0">Pending</option>
                  <option value="1">Verified</option>
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
        <h4 class="col-md-6">Deposits</h4>
        <div class="col-md-6 text-end">
          <a class="btn btn-success" href="{{url('deposits/export')}}" id="export"><i class="fas fa-cloud-download-alt"></i></a>
          <a class="btn btn-info" href="javascript:void(0)" data-bs-target="#exampleModal" data-bs-toggle="modal"><i class="fas fa-cloud-upload-alt"></i></a>
        </div>
      </div>
      </div>
      <div class="card-body">
          <table class="table table-sm table-hover table-bordered" id="data_table" style="width:100%;">
            <thead class="text-center bg-primary text-white">            
              <tr>
                <th>Desk Code</th>
                <th>User</th>
                <th>Amount</th>
                <th>Deposit Slip No</th>
                <th>Deposit Date</th>
                <th>Deposit slip</th>
<!--                 <th>Subscription IDs</th>
 -->                <th>Verification</th>
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
<div id="mdl">
@include('deposits::bulk-verify')
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
      url:"{{url('deposits')}}",
      data:data,
      },
    buttons:[],
    columns: [
      {data: 'desk_id', name: 'desk_id'},
      {data: 'user_id', name: 'user_id'},
      {data: 'amount', name: 'amount'},
      {data: 'deposit_slip_no', name: 'deposit_slip_no'},
      {data: 'desposit_date', name: 'desposit_date'},
      {data: 'deposit_slip', name: 'deposit_slip', class:"text-center"},
      //{data: 'client_subscription_ids', name: 'client_subscription_ids'},
      {data: 'is_verified', name: 'is_verified', class:"text-center", orderable: false, searchable: false},
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


$(document).on("click", '.show-details', function() {
var url=$(this).data('href');
  $.ajax({
    url:url,
    type:"GET",
    success:function(res){
      if(res.success){
      $("#mdl").html(res.data);
      $("#deposit-detail").modal('show');
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