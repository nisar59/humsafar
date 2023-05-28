@extends('layouts.template')

@section('content')
        <section class="section">
          <div class="section-body">
            
            <form action="{{url('users/store')}}" method="post" enctype="multipart/form-data">
              @csrf  
            <div class="row">  
              <div class="col-12 col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Add Users</h4>
                  </div>
                  <div class="card-body">
                    <div class="row">
                    <div class="form-group col-md-6">
                      <label>Name</label>
                      <input type="text" class="form-control" name="name" placeholder="Name">
                    </div>
                    <div class="form-group col-md-6">
                      <label>Email</label>
                      <input type="email" class="form-control" name="email" placeholder="Email">
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-md-6">
                      <label>Password</label>
                      <input type="password" class="form-control" name="password" placeholder="Password">
                    </div>
                    <div class="form-group col-md-6">
                      <label>Role</label>
                      <select class="form-control" name="role">
                        @foreach($data['role'] as $role)
                        <option value="{{$role->name}}">{{$role->name}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-md-6">
                      <label>Image</label>
                      <input type="file" class="form-control" name="image" id="image" onchange="document.getElementById('image-display').src = window.URL.createObjectURL(this.files[0])">
                    </div>
                    <div class="form-group col-md-6">
                      <img src="{{url('public/img/images.png')}}" class="image-display" id="image-display" width="100" height="100">
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-md-6">
                      <label>Inventory Expiration time</label>
                      <select class="form-control" name="inventory_expire_time">
                      @for($i=2; $i<=100; $i++)
                        <option value="{{$i}}">{{$i}} Days</option>
                      @endfor
                      </select>
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
