@extends('layouts.template')
@section('title')
Logs
@endsection
@section('content')
<div class="page-title-box">
  <div class="row align-items-center">
    <div class="col-md-8">
      <h6 class="page-title">Logs</h6>
      <ol class="breadcrumb m-0">
        <li class="breadcrumb-item">{{Settings()->portal_name}}</li>
        <li class="breadcrumb-item">Logs</li>
        <li class="breadcrumb-item active">Listing</li>
      </ol>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12">
    <div class="card card-primary">
      <div class="card-header bg-white p-0">
        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#home" role="tab">
              <h4>Logs</h4>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#profile" role="tab">
              <h4>System Logs</h4>
            </a>
          </li>
        </ul>
      </div>
      <div class="card-body p-0">
        <!-- Tab panes -->
        <div class="tab-content">
          <div class="tab-pane active p-3" id="home" role="tabpanel">
            <a href="{{url('logs/truncate')}}" class="btn btn-danger" style="position: absolute;right: 8px;top: 8px;"><i class="fas fa-trash-alt"></i></a>
            <div class="table-responsive">
              <table class="table table-sm table-hover table-bordered" id="logs" style="width:100%;">
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th>User</th>
                    <th>Model</th>
                    <th>Model ID</th>
                    <th>Message</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
          <div class="tab-pane p-3" id="profile" role="tabpanel">
            <a href="{{url('system-logs/truncate')}}" class="btn btn-danger" style="position: absolute;right: 8px;top: 8px;"><i class="fas fa-trash-alt"></i></a>
            <div class="table-responsive">
              <table class="table table-sm table-hover table-bordered" id="system-logs" style="width:100%;">
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th>Model</th>
                    <th>Message</th>
                    <th>Date</th>
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
  </div>
</div>
<div id="mdl"></div>
@endsection
@section('js')
<script type="text/javascript">
//Roles table
$(document).ready( function(){

var regions_table = $('#logs').DataTable({
processing: true,
serverSide: true,
ajax: "{{url('logs')}}",
buttons:[],
columns: [
{data: 'user_id', name: 'user_id', orderable:false},
{data: 'model', name: 'model', orderable:false},
{data: 'model_id', name: 'model_id', orderable:false},
{data: 'message', name: 'message', orderable:false},
{data: 'action', name: 'action', orderable: false, class:"d-flex justify-content-center w-auto", searchable: false},
]
});


var regions_table = $('#system-logs').DataTable({
processing: true,
serverSide: true,
ajax: "{{url('system-logs')}}",
buttons:[],
columns: [
{data: 'model', name: 'model', class:'text-center', orderable:false},
{data: 'message', name: 'message', class:'text-center', orderable:false},
{data: 'created_at', name: 'created_at', class:'text-center', orderable:false},
{data: 'action', name: 'action', orderable: false, class:"text-center", searchable: false},
]
});


$(document).on('click', '.show-log', function(e) {
e.preventDefault();
var url=$(this).attr('href');
$.ajax({
url:url,
type:"GET",
success:function(res) {
if(res.success){
$("#mdl").html(res.html);
$("#exampleModal").modal('show');
}else{
error('Something went wrong');
}
},
error:function(err){
error("Something went wrong");
}
});
});
});
</script>
@endsection