@extends('layouts.template')
@section('title')
Banks
@endsection
@section('content')
<div class="page-title-box">
  <div class="row align-items-center">
    <div class="col-md-8">
      <h6 class="page-title">Banks</h6>
      <ol class="breadcrumb m-0">
        <li class="breadcrumb-item">{{Settings()->portal_name}}</li>
        <li class="breadcrumb-item">Banks</li>
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
          <h4 class="col-md-6">Banks</h4>
          <div class="col-md-6 text-end">
            <a href="{{url('banks/create')}}" class="btn btn-success">+</a>
            <a href="{{url('banks/import')}}" class="btn btn-info"><i class="fas fa-cloud-upload-alt"></i></a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm table-hover table-bordered" id="banks" style="width:100%;">
            <thead class="text-center bg-primary text-white">
              <tr>
                <th>Name</th>
                <th>Account Title</th>
                <th>Account Number</th>
                <th>Code</th>
                <th>Status</th>
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
$(document).ready( function(){
    var banks_table = $('#banks').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{url('banks')}}",
    buttons:[],
    columns: [
        {data: 'name', name: 'name'},
        {data: 'account_title', name: 'account_title'},
        {data: 'account_no', name: 'account_no'},
        {data: 'code', name: 'code'},
        {data: 'status', name: 'status', class:'text-center'},
        {data: 'action', name: 'action', orderable: false, class:"d-flex justify-content-center w-auto", searchable: false},
     ]
    });
});
</script>
@endsection