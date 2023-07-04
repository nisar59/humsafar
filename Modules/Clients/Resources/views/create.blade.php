@extends('layouts.template')
@section('title')
Client
@endsection
@section('content')
<div class="page-title-box">
  <div class="row align-items-center">
    <div class="col-md-8">
      <h6 class="page-title">Clients</h6>
      <ol class="breadcrumb m-0">
        <li class="breadcrumb-item">{{Settings()->portal_name}}</li>
        <li class="breadcrumb-item">Clients</li>
        <li class="breadcrumb-item active">Create Client</li>
      </ol>
    </div>
  </div>
</div>
<form action="{{url('clients/store')}}" method="post" enctype="multipart/form-data">
  @csrf
  <div class="row">
    <div class="col-12 col-md-12">
      <div class="card card-primary">
        <div class="card-header bg-white">
          <h4>Create Client</h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="form-group col-md-4">
              <label for="">Desks</label>
              <select name="desk_id" class="form-control select2">
                @foreach($desks as $desk)
                <option value="{{$desk->id}}">{{$desk->desk_code}}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-4 form-group">
              <label for="">Client Name</label>
              <input type="text" class="form-control" name="name" value="{{old('name')}}" placeholder="Client Name">
            </div>
            <div class="col-md-4 form-group">
              <label for="">Client Parentage</label>
              <input type="text" class="form-control" name="parentage" value="{{old('parentage')}}" placeholder="Client Parentage">
            </div>

            <div class="col-md-4 form-group">
              <label for="">Date of Birth</label>
              <input type="date" class="form-control" name="dob" value="{{old('dob')}}" placeholder="Date of Birth">
            </div>
            <div class="col-md-4 form-group">
              <label for="">Education</label>
              <select name="education" class="form-control">
                <option value="">select</option>
                @foreach(Education() as $key=> $edu)
                <option value="{{$key}}" @if(old('education')==$key) selected @endif>{{$edu}}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-4 form-group">
              <label for="">Gender</label>
              <select name="gender" class="form-control select2">
                <option value="male" @if(old('gender')=="male") selected @endif>Male</option>
                <option value="female" @if(old('gender')=="female") selected @endif>Female</option>
                <option value="other" @if(old('gender')=="other") selected @endif>Other</option>
              </select>
            </div>
            <div class="col-md-4 form-group">
              <label for="">Marital Status</label>
              <select name="marital_status" class="form-control select2">
                  <option value="">Select</option>
                  @foreach(MaritalStatus() as $mskey=> $ms)
                  <option value="{{$mskey}}" @if(old('marital_status')==$mskey) selected @endif>{{$ms}}</option>
                  @endforeach
              </select>
            </div>
            
            <div class="col-md-4 form-group">
              <label for="">Weight</label>
              <input type="text" class="form-control" value="{{old('weight')}}" name="weight" placeholder="Weight">
            </div>

            <div class="col-md-4 form-group">
              <label for="">Height</label>
              <input type="text" class="form-control" value="{{old('height')}}" name="height" placeholder="Height">
            </div>

            <div class="col-md-4 form-group">
              <label for="">Blood Group</label>
              <select name="blood_group" class="form-control select2">
                  <option value="">Select</option>
                  @foreach(BloodGroups() as $bgkey=> $bg)
                  <option value="{{$bgkey}}" @if(old('blood_group')==$bgkey) selected @endif>{{$bg}}</option>
                  @endforeach
              </select>
            </div>

            <div class="col-md-4 form-group">
              <label for="">Relation</label>
              <select name="relation" class="form-control select2">
                  <option value="">Select</option>
                  @foreach(RelationWith() as $rwkey=> $rw)
                  <option value="{{$rwkey}}" @if(old('relation')==$mskey) selected @endif>{{$rw}}</option>
                  @endforeach
              </select>
            </div>

            <div class="col-md-4 form-group">
              <label for="">Phone No Primary</label>
              <input type="text" class="form-control" value="{{old('phone_primary')}}" name="phone_primary" placeholder="Phone No Primary">
            </div>

            <div class="col-md-4 form-group">
              <label for="">Phone No Secondary</label>
              <input type="text" class="form-control" value="{{old('phone_secondary')}}" name="phone_secondary" placeholder="Phone No Secondary">
            </div>
            <div class="col-md-4 form-group">
              <label for="">Email</label>
              <input type="email" class="form-control" value="{{old('email')}}" name="email" placeholder="Email">
            </div>
            <div class="col-md-4 form-group">
              <label for="">CNIC</label>
              <input type="text" class="form-control" value="{{old('cnic')}}" name="cnic" placeholder="CNIC">
            </div>

            <div class="col-md-4 form-group">
              <label for="">Monthly Income</label>
              <input type="text" class="form-control" value="{{old('monthly_income')}}" name="monthly_income" placeholder="Monthly Income">
            </div>
            <div class="col-md-4 form-group">
              <label for="">Medical Expense</label>
              <input type="text" class="form-control" value="{{old('medical_expense')}}" name="medical_expense" placeholder="Medical Expense">
            </div>

            <div class="col-md-4 form-group">
              <label for="">Address</label>
              <input type="text" class="form-control" value="{{old('address')}}" name="address" placeholder="Address">
            </div>
            <div class="col-md-4 form-group">
              <label for="">Province</label>
              <select name="province" id="province" data-json="{{ProvincesDistricts();}}" class="form-control select2">
                <option value="">select</option>
                @foreach(json_decode(ProvincesDistricts()) as $key=> $pd)
                <option value="{{$key}}" >{{$key}}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4 form-group">
              <label for="">District</label>
              <select name="district" id="district" class="form-control select2">
                <option value="">select</option>
              </select>
            </div>
            <div class="form-group col-md-4">
              <label for="">Packages</label>
              <select name="package_id" class="form-control select2">
                <option value="">Select Package</option>
                @foreach($packages as $package)
                <option value="{{$package->id}}">{{$package->title}} - {{$package->amount}}</option>
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

  $("#province").on('change', function() {
    var json_pd=$(this).data('json');
    var vl=$(this).val();
    if(vl!=''){
    var pds=json_pd[vl];

      $("#district").html('<option value="">select</option>');
    $.each(pds,function (index, val) {
      $("#district").append('<option value="'+val.Name+'">'+val.Name+'</option>');
    });

    }else{
      $("#district").html('<option value="">select</option>');
    }

  });

});
</script>
@endsection