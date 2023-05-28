@extends('layouts.template')
@section('title')
Branches
@endsection
@section('content')
<div class="page-title-box">
  <div class="row align-items-center">
    <div class="col-md-8">
      <h6 class="page-title">Branches</h6>
      <ol class="breadcrumb m-0">
        <li class="breadcrumb-item">{{Settings()->portal_name}}</li>
        <li class="breadcrumb-item">Branches</li>
        <li class="breadcrumb-item active">Create Branch</li>
      </ol>
    </div>
  </div>
</div>
<section class="section">
  <div class="section-body">
    <form action="{{url('branches/store')}}" method="post" enctype="multipart/form-data">
      @csrf
      <div class="row">
        <div class="col-12 col-md-12">
          <div class="card card-primary">
            <div class="card-header bg-white">
              <h4>Create Branch</h4>
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
                <div class="form-group col-md-4">
                  <label>Code</label>
                  <input type="text" class="form-control  @error('code') is-invalid @enderror" name="code" value="{{old('code')}}" placeholder="Code">
                </div>
                <div class="form-group col-md-4">
                  <label>Region</label>
                  <select class="form-control select2" id="region_id" name="region_id">
                    <option value="">Select Regions</option>
                    @foreach($data['regions'] as $region)
                    <option value="{{$region->mis_sync_id}}">{{$region->name}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="regionAreas form-group col-md-4">
                  <label>Areas</label>
                  <select class="form-control select2" id="region_areas" name="area_id">
                    <option value="">Select Areas</option>
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
    <script >
    //change areas based on regions
    $("#region_id").change(function (e) {
    let region_id = $(this).val();
    $.ajaxSetup({
    headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    });
    $.ajax({
    type: "POST",
    url: "{{ url('branches/region-areas') }}",
    data: {region_id:region_id},
    success: function (response) {
    $('#region_areas').empty();
    $('#region_areas').append(response.data);
    $(".select2").select2();
    }
    });
    });
    </script>
    @endsection