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
        <li class="breadcrumb-item active">Create Desk</li>
      </ol>
    </div>
  </div>
</div>

<form action="{{url('desks/create')}}" id="create-desk" enctype="multipart/form-data">
  <div class="row">
    <div class="col-12 col-md-12">
      <div class="card card-primary">
        <div class="card-header bg-white">
          <h4>Add Desk</h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="form-group col-md-12">
              <label>Select a user to Add to a desk</label>
              <select id="user" class="form-control select2">
                <option value="">Select</option>
                @foreach($users as $user)
                <option value="{{$user->id}}">{{$user->name}} - {{$user->cnic}}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
        <div class="card-footer text-end">
          <button class="btn btn-primary mr-1" id="submit" type="submit">Submit</button>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection
@section('js')
<script>
$(document).ready(function() {
$(document).on('submit','#create-desk', function(e){
var user_id=$("#user").val();
var url="{{url('desks/create')}}/"+user_id;
$(this).attr('action', url);
if(user_id==''){
e.preventDefault();
error("Please Select a user first");
}
});
});
</script>
@endsection