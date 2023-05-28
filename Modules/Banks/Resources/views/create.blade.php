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
        <li class="breadcrumb-item active">Create Bank</li>
      </ol>
    </div>
  </div>
</div>
<form action="{{url('banks/store')}}" method="post" enctype="multipart/form-data">
  @csrf
  <div class="row">
    <div class="col-12 col-md-12">
      <div class="card card-primary">
        <div class="card-header bg-white">
          <h4>Add Bank</h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="form-group col-md-6">
              <label>Name</label>
              <input type="text" class="form-control" value="{{old('name')}}" name="name" placeholder="Name">
            </div>
            <div class="form-group col-md-6">
              <label>Account Title</label>
              <input type="text" class="form-control" name="account_title" value="{{old('account_title')}}" placeholder="Account Title">
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label>Account No</label>
              <input type="text" class="form-control" name="account_no" value="{{old('account_no')}}" placeholder="Account Number">
            </div>
            <div class="form-group col-md-6">
              <label>Code</label>
              <input type="text" class="form-control" value="{{old('code')}}" name="code" placeholder="Code">
            </div>
          </div>
        </div>
        <div class="card-footer text-end">
          <button class="btn btn-primary mr-1" type="submit">Submit</button>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection
@section('js')
@endsection