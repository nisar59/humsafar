@extends('layouts.template')
@section('title')
Users
@endsection
@section('content')
<div class="page-title-box">
  <div class="row align-items-center">
    <div class="col-md-8">
      <h6 class="page-title">Users</h6>
      <ol class="breadcrumb m-0">
        <li class="breadcrumb-item">{{Settings()->portal_name}}</li>
        <li class="breadcrumb-item">users</li>
        <li class="breadcrumb-item active">Edit User</li>
      </ol>
    </div>
  </div>
</div>
<form action="{{url('users/update/'.$data['user']->id)}}" method="post" enctype="multipart/form-data">
  @csrf
  <div class="row">
    <div class="col-12 col-md-12">
      <div class="card card-primary">
        <div class="card-header bg-white">
          <h4>Edit Users</h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="form-group col-md-6">
              <label>Name</label>
              <input type="text" class="form-control" value="{{$data['user']->name}}" name="name" placeholder="Name">
            </div>
            <div class="form-group col-md-6">
              <label>Father Name</label>
              <input type="text" class="form-control" name="father_name" value="{{$data['user']->father_name}}" placeholder="Father Name">
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label>CNIC</label>
              <input type="text" class="form-control" name="cnic" value="{{$data['user']->cnic}}" placeholder="CNIC">
            </div>
            <div class="form-group col-md-6">
              <label>Email</label>
              <input type="email" class="form-control" value="{{$data['user']->email}}" name="email" placeholder="Email">
            </div>
            <div class="form-group col-md-6">
              <label>Phone</label>
              <input type="text" class="form-control" name="phone" value="{{$data['user']->phone}}" placeholder="Phone">
            </div>
            <div class="form-group col-md-6">
              <label>Password</label>
              <input type="password" class="form-control" name="password" placeholder="Password">
              <p class="text-muted">leave empty, if you don't want to update the password</p>
            </div>
          </div>
          <div class="row">
            @if(count($data['user']->roles)>0 && $data['user']->roles[0]->name=='super-admin')
            <div class="form-group col-md-6">
              <label>Role</label>
              <select class="form-control" disabled>
                <option value="">Super admin</option>
              </select>
            </div>
            @else
            <div class="form-group col-md-6">
              <label>Role</label>
              <select class="form-control select2" name="role">
                @foreach($data['role'] as $role)
                <option value="{{$role->name}}" @if(count($data['user']->roles)>0 && $data['user']->roles[0]->name==$role->name) selected @endif>{{$role->name}} </option>
                @endforeach
              </select>
            </div>
            @endif
            <div class="form-group col-md-6">
              <label>Role Name</label>
              <input type="text" class="form-control" name="role_name" value="{{$data['user']->role_name}}" placeholder="Role Name">
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label>Employee Code</label>
              <input type="text" class="form-control" name="emp_code" value="{{$data['user']->emp_code}}" placeholder="Employee Code">
            </div>
            <div class="form-group col-md-6">
              <label>Access Level</label>
              <input type="text" class="form-control" value="{{$data['user']->access_level}}" name="access_level" placeholder="Access Level">
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-12">
              <label>Branch</label>
                <select name="branch_id" class="form-control select2">
                  @foreach(AllBranches() as $branch)
                    <option value="{{$branch->mis_sync_id}}" {{$data['user']->branch_id==$branch->mis_sync_id ? 'selected' : ''}}>{{$branch->name}}</option>
                  @endforeach
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