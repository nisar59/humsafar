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
        <li class="breadcrumb-item active">Edit Desk</li>
      </ol>
    </div>
  </div>
</div>

<form action="{{url('desks/update/'.$data->id)}}" method="post" enctype="multipart/form-data">
  @csrf
  <div class="row">
    <div class="col-12 col-md-12">
      <div class="card card-primary">
        <div class="card-header bg-white">
          <h4>Edit Desk</h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="form-group col-md-6">
              <label>Desk Code</label>
              <input type="text" class="form-control" readonly value="{{$data->desk_code}}" placeholder="Desk Code">
            </div>
            <div class="form-group col-md-6">
              <label>Branch</label>
              <input type="text" class="form-control" readonly value="{{$data->branch_code}}" placeholder="Branch">
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label>Associated User</label>
              <select name="user_id" class="form-control select2">
                <option value="">Select</option>
                @foreach($users as $user)
                <option value="{{$user->id}}" @if($data->user_id==$user->id && $data->deskuser()->exists()) selected @endif>{{$user->name}} - {{$user->cnic}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-md-6">
              <label>Status</label>
              <select name="status" class="form-control">
                <option value="1" @if($data->status==1) selected @endif>Active</option>
                <option value="0" @if($data->status==0) selected @endif>Deactive</option>
              </select>
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