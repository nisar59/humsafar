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
        <li class="breadcrumb-item active">Show</li>
      </ol>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12">
    <div class="card card-primary">
      <div class="card-header bg-white">
        <div class="row">
          <div class="col-6">        
            <h4>Log Detail</h4>
          </div>
          <div class="col-6">
          <a class="btn btn-primary float-end" href="{{url('public/exports/'.$log->file_name)}}"><i class="fa fa-download"></i></a>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-body">
        <div class="row">
          <div class="col-md-3">
          <label>File Name</label>
          <p><a href="{{url('public/exports/'.$log->file_name)}}">{{$log->file_name}}</a></p>
          </div> 
          <div class="col-md-3">
          <label>Success</label>
            <p>{{$log->success}}</p>
          </div> 
          <div class="col-md-3">
          <label>Failed</label>
            <p>{{$log->failed}}</p>
          </div>
          <div class="col-md-3">
          <label>Date</label>
            <p>{{$log->created_at->format('Y-m-d')}}</p>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <a href="{{url('logs')}}" class="btn btn-primary float-end">Back</a>
      </div>
    </div>
  </div>
</div>
@endsection
@section('js')

@endsection