@extends('layouts.template')

@section('content')
        <section class="section">
          <div class="section-body">
            
            <form action="{{url('users/update/'.$data['user']->id)}}" method="post" enctype="multipart/form-data">
              @csrf  
            <div class="row">  
              <div class="col-12 col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Edit Users</h4>
                  </div>
                  <div class="card-body">
                    <div class="row">
                    <div class="form-group col-md-6">
                      <label>Name</label>
                      <input type="text" class="form-control" value="{{$data['user']->name}}" name="name" placeholder="Name">
                    </div>
                    <div class="form-group col-md-6">
                      <label>Email</label>
                      <input type="email" class="form-control" value="{{$data['user']->email}}" name="email" placeholder="Email">
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group @if($data['user']->roles[0]->name!='super-admin') col-md-6 @else col-md-12 @endif">
                      <label>Password</label>
                      <input type="password" class="form-control" name="password" placeholder="Password">
                      <p class="text-muted">leave empty, if you don't want to update the password</p>
                    </div>
                    @if($data['user']->roles[0]->name!='super-admin')
                    <div class="form-group col-md-6">
                      <label>Role</label>
                      <select class="form-control" name="role">
                        @foreach($data['role'] as $role)
                        <option value="{{$role->name}}" @if($data['user']->roles[0]->name==$role->name) selected @endif>{{$role->name}}</option>
                        @endforeach
                      </select>
                    </div>
                    @else
                    <input type="hidden" name="role" value="super-admin">
                    @endif
                  </div>
                  <div class="row">
                    <div class="form-group col-md-6">
                      <label>Image</label>
                      <input type="file" class="form-control" name="image" id="image" onchange="document.getElementById('image-display').src = window.URL.createObjectURL(this.files[0])">
                    </div>
                    @php
                    $image_name=$data['user']->image;
                    $image_path=public_path('img/users/'.$image_name);
                    if(file_exists($image_path) AND $image_name!=''){
                      $image_path=url('public/img/users/'.$image_name);
                    }
                    else{
                      $image_path=url('public/img/images.png');
                    }
                    @endphp

                    <div class="form-group col-md-6">
                      <img src="{{$image_path}}" class="image-display" id="image-display" width="100" height="100">
                    </div>
                  </div>
                </div>
                  <div class="card-footer text-right">
                    <button class="btn btn-primary mr-1" type="submit">Submit</button>
                  </div>
                </div>
              </div>
              </div>
            </form>
          </div>
        </section>
@endsection
@section('js')

@endsection
