@extends('layouts.template')
@section('title')
Branches
@endsection
@section('content')
        <section class="section">
          <div class="section-body">
            <form action="{{url('branches/update/'.$data['branches']->id)}}" method="post" enctype="multipart/form-data">
              @csrf
            <div class="row">
              <div class="col-12 col-md-12">
                <div class="card card-primary">
                  <div class="card-header">
                    <h4>Edit Branches</h4>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="form-group col-md-6">
                        <label>MIS Sync Id</label>
                        <input type="text" class="form-control @error('mis_sync_id') is-invalid @enderror" value="{{$data['branches']->mis_sync_id}}" name="mis_sync_id" placeholder="MIS Sync ID">
                      </div>
                      <div class="form-group col-md-6">
                        <label>Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{$data['branches']->name}}" placeholder="Name">
                      </div>
                    </div>
                  <div class="row">
                    <div class="form-group col-md-4">
                      <label>Code</label>
                      <input type="text" class="form-control  @error('code') is-invalid @enderror" name="code" value="{{$data['branches']->code}}" placeholder="Code">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Regions</label><br/>
                        <select name="region_id" id="region_id" loaded-region-id="{{ $data['branches']->region_id  }}" class="form-control select2 @error('region_id') is-invalid @enderror">
                          <option value="">Select regions</option>
                          @foreach($data['regions'] as $region)
                           <option @if($data['branches']->region_id == $region->mis_sync_id) selected @endif value="{{$region->mis_sync_id}}">{{$region->name}}</option> 
                          @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Areas</label><br/>
                        <select name="area_id" id="region_areas" loaded-area-id="{{ $data['branches']->area_id  }}" class="form-control @error('area_id') is-invalid @enderror">
                           
                        </select>
                    </div>
                  </div>
                    
                  </div>
                  <div class="card-footer text-right">
                    <button class="btn btn-primary mr-1" type="submit">Update</button>
                  </div>
                </div>
              </div>
              </div>
            </form>
          </div>
        </section>
@endsection
@section('js')
<script >

    // load areas of edit regions
    let loaded_region_id = $('#region_id').attr('loaded-region-id');
    let loaded_area_id= $('#region_areas').attr('loaded-area-id');
    $.ajax({
        type: "GET",
        url: "{{ url('branches/load-edit-areas') }}"+'/'+loaded_region_id+'/'+loaded_area_id,
        success: function (response) {
            $('#region_areas').append(response.data);
        }
    });


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
            }
        });
    });

  </script>
@endsection
