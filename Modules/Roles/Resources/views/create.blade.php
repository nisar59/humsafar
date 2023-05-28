@extends('layouts.template')
@section('title')
Roles & Permissions
@endsection
@section('content')
<div class="page-title-box">
  <div class="row align-items-center">
    <div class="col-md-8">
      <h6 class="page-title">Roles & Permissions</h6>
      <ol class="breadcrumb m-0">
        <li class="breadcrumb-item">{{Settings()->portal_name}}</li>
        <li class="breadcrumb-item">Roles & Permissions</li>
        <li class="breadcrumb-item active">Create Role</li>
      </ol>
    </div>
  </div>
</div>
<form action="{{url('roles/store')}}" method="post">
  @csrf
  <div class="row">
    <div class="col-12 col-md-12">
      <div class="card card-primary">
        <div class="card-header bg-white">
          <h4>Add Roles & Permissions</h4>
        </div>
        <div class="card-body">
          <div class="form-group">
            <label>Role</label>
            <input type="text" class="form-control" name="role" placeholder="Role">
          </div>
          <div class="form-group row">
            @foreach(AllPermissions() as $name=> $permissions)
            <div class="col-4">
              <label class="d-block text-capitalize">{{$name}}</label>
              @foreach($permissions as $permission)
              <div class="form-check">
                <input class="form-check-input" name="permissions[]" value="{{$name.'.'.$permission}}" type="checkbox" id="defaultCheck{{$name.$permission}}">
                <label class="form-check-label text-capitalize" for="defaultCheck{{$name.$permission}}">
                  {{$permission}}
                </label>
              </div>
              @endforeach
            </div>
            @endforeach
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