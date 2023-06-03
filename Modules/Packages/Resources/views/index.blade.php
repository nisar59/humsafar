@extends('layouts.template')
@section('title')
Packages
@endsection
@section('content')
<div class="page-title-box">
  <div class="row align-items-center">
    <div class="col-md-8">
      <h6 class="page-title">Packages</h6>
      <ol class="breadcrumb m-0">
        <li class="breadcrumb-item">{{Settings()->portal_name}}</li>
        <li class="breadcrumb-item">Packages</li>
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
          <h4 class="col-md-6">Packages</h4>
          <div class="col-md-6 text-end">
            <a href="{{url('packages/create')}}" class="btn btn-success">+</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm table-hover table-bordered" id="users" style="width:100%;">
            <thead class="text-center bg-primary text-white">
              <tr>
                <th>Title</th>
                <th>Amount</th>
                <th>Compensation</th>
                <th>Subscription Type</th>
<!--                 <th>Subscription Duration</th>
 -->                <th>Status</th>
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
var packages_table = $('#users').DataTable({
processing: true,
serverSide: true,
ajax: "{{url('packages')}}",
buttons:[],
columns: [
{data: 'title', name: 'title'},
{data: 'amount', name: 'amount'},
{data: 'compensation', name: 'compensation'},
{data: 'subscription_type', name: 'subscription_type'},
//{data: 'subscription_duration', name: 'subscription_duration'},
{data: 'status', name: 'status', class:'text-center'},
{data: 'modified_by', name: 'modified_by', class:"text-center", orderable: false, searchable: false},
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