@extends('layouts.template')
@section('title')
Packages
@endsection
@section('content')
<div class="page-title-box">
  <div class="row align-items-center">
    <div class="col-md-8">
      <h6 class="page-title">Packages</h6>
      <ol class="breadcrumb m-0">
        <li class="breadcrumb-item">{{Settings()->portal_name}}</li>
        <li class="breadcrumb-item">Packages</li>
        <li class="breadcrumb-item active">Edit Package</li>
      </ol>
    </div>
  </div>
</div>
<form action="{{url('packages/update/'.$data['package']->id)}}" method="post" enctype="multipart/form-data">
  @csrf
  <div class="row">
    <div class="col-12 col-md-12">
      <div class="card card-primary">
        <div class="card-header bg-white">
          <h4>Edit Packages</h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="form-group col-md-6">
              <label>Title</label>
              <input type="text" class="form-control  @error('title') is-invalid @enderror" value="{{$data['package']->title}}" name="title" placeholder="Title">
            </div>
            <div class="form-group col-md-6">
              <label>Amount</label>
              <input type="text" class="form-control @error('amount') is-invalid @enderror" value="{{$data['package']->amount}}" name="amount" placeholder="Amount">
            </div>
            <div class="form-group col-md-6">
              <label>Compensation</label>
              <input type="number" class="form-control @error('compensation') is-invalid @enderror" name="compensation" value="{{$data['package']->compensation}}" placeholder="Compensation">
            </div>             
            <div class="form-group col-md-6">
              <label>Subscription Type</label>
              <select class="form-control @error('subscription_type') is-invalid @enderror" name="subscription_type">
                <option value="">Select Subscription Type</option>
                @foreach(subscriptionTypes() as $key => $subscription_type)
                <option value="{{$key}}" @if($data['package']->subscription_type==$key) selected @endif>{{$subscription_type['title']}}</option>
                @endforeach
              </select>
            </div>
<!--             <div class="form-group col-md-6">
              <label>Subscription Duration</label>
              <input type="text" class="form-control @error('subscription_duration') is-invalid @enderror" name="subscription_duration" value="{{$data['package']->subscription_duration}}" placeholder="Subscription Duration">
            </div> -->
          </div>
        </div>
        <div class="card-footer text-end">
          <button class="btn btn-primary mr-1" type="submit">Update</button>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection
@section('js')
@endsection