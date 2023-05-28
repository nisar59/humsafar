@extends('layouts.template')
@section('title')
Trash
@endsection
@section('content')
<div class="page-title-box">
  <div class="row align-items-center">
    <div class="col-md-8">
      <h6 class="page-title">Trash</h6>
      <ol class="breadcrumb m-0">
        <li class="breadcrumb-item">{{Settings()->portal_name}}</li>
        <li class="breadcrumb-item active">Trash</li>
      </ol>
    </div>
  </div>
</div>
<div class="row">
  @foreach(TrashModules() as $module)
  <div ondblclick="window.location.href='{{url('trash/show/'.$module['model'])}}'" class="col-md-3 col-sm-3 col-xl-3 text-center position-relative mb-4">
    <img class="w-50" src="{{asset('img/folder.png')}}" alt="">
    <div style="margin-top:-20px"><strong class="">{{$module['name']}}</strong></div>
  </div>
  @endforeach
</div>
@endsection
@section('js')
<script type="text/javascript">
//Roles table
$(document).ready( function(){
var regions_table = $('#regions').DataTable({
processing: true,
serverSide: true,
ajax: "{{url('regions')}}",
buttons:[],
columns: [
{data: 'name', name: 'name'},
{data: 'code', name: 'code'},
{data: 'status', name: 'status'},
{data: 'modified_by', name: 'modified_by', class:'text-center', orderable: false, searchable: false},
{data: 'action', name: 'action', class:'text-center', orderable: false, searchable: false},
],

"fnDrawCallback": function() {
    $('[data-bs-toggle="tooltip"]').tooltip({
      html:true,
    });
  }

});
});
</script>
@endsection