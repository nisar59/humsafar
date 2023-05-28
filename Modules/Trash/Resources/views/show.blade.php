@extends('layouts.template')
@section('title')
Trash
@endsection
@section('css')
<style>
.dropdown-menu{
  margin-top: -80px !important;
}
</style>
@endsection
@section('content')
<div class="page-title-box">
  <div class="row align-items-center">
    <div class="col-md-8">
      <h6 class="page-title">Trash</h6>
      <ol class="breadcrumb m-0">
        <li class="breadcrumb-item">{{Settings()->portal_name}}</li>
        <li class="breadcrumb-item">Trash</li>
        <li class="breadcrumb-item active">{{$module}}</li>
      </ol>
    </div>
    <div class="col-md-4 text-end">
      <a href="{{url('trash')}}" class="btn btn-primary">Back</a>
    </div>
  </div>
</div>
<div class="row">
  @if(count($data)<1)
    <center>Empty Trash <br><a href="{{url('trash')}}">Go Back</a></center>
  @endif
  @foreach($data as $value)
  <div class="col-md-2 dropend col-sm-2 col-xl-2 text-center position-relative">
    <input type="checkbox" class="position-absolute d-none">
    <img class="w-50 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" src="{{asset('img/file.png')}}" alt="">
    <div><strong class="">{{@$value->name}} {{@$value->title}} {{@$value->transaction_no}} {{@$value->question}} @if($module=='clients_feedback') {{@$value->client_id}}-{{@$value->feedback_type}} @endif</strong></div>
    <div class="dropdown-menu">
       <a class="dropdown-item border-bottom" href="{{url('trash/restore/'.$module.'/'.$value->id)}}"><i class="fas fa-undo"></i> Restore</a>
        <a class="dropdown-item border-bottom" href="{{url('trash/destroy/'.$module.'/'.$value->id)}}"><i class="fas fa-trash"></i> Delete</a>
        <a class="dropdown-item border-bottom detail" data-detail="{{json_encode($value)}}" href="javascript:void(0)"><i class="fas fa-info-circle"></i> Detail</a>
    </div>
  </div>
  @endforeach
</div>
@endsection
@section('js')
<script type="text/javascript">
//Roles table
$(document).ready( function(){

$(document).on('click', '.detail', function () {
  var details=$(this).data('detail');
  var strng='';
  for (key in details) {
    strng+="<b>"+key+" :</b> "+details[key]+"<br><br>";
  }
  console.log(strng);
  Swal.fire({
    html:strng,
  });
});

});
</script>
@endsection