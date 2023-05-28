@extends('layouts.template')
@section('title')
Regions
@endsection
@section('content')
<div class="page-title-box">
  <div class="row align-items-center">
    <div class="col-md-8">
      <h6 class="page-title">Regions</h6>
      <ol class="breadcrumb m-0">
        <li class="breadcrumb-item">{{Settings()->portal_name}}</li>
        <li class="breadcrumb-item">Regions</li>
        <li class="breadcrumb-item active">Create Region</li>
      </ol>
    </div>
  </div>
</div>
<form action="{{url('regions/store')}}" method="post" enctype="multipart/form-data">
  @csrf
  <div class="row">
    <div class="col-12 col-md-12">
      <div class="card card-primary">
        <div class="card-header bg-white">
          <h4>Create Region</h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="form-group col-md-6">
              <label>MIS Sync Id</label>
              <input type="text" class="form-control @error('mis_sync_id') is-invalid @enderror" value="{{old('mis_sync_id')}}" name="mis_sync_id" placeholder="MIS Sync ID">
            </div>
            <div class="form-group col-md-6">
              <label>Name</label>
              <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{old('name')}}" placeholder="Name">
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label>Code</label>
              <input type="text" class="form-control  @error('code') is-invalid @enderror" name="code" value="{{old('code')}}" placeholder="Code">
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