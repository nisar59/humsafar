@extends('layouts.template')
@section('title')
Feedback Questions
@endsection
@section('content')
<div class="page-title-box">
  <div class="row align-items-center">
    <div class="col-md-8">
      <h6 class="page-title">Feedback Questions</h6>
      <ol class="breadcrumb m-0">
        <li class="breadcrumb-item">{{Settings()->portal_name}}</li>
        <li class="breadcrumb-item">Feedback Questions</li>
        <li class="breadcrumb-item active">Listing</li>
      </ol>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12">
    <div class="card card-primary">
      <div class="card-header bg-white">
        <div class="row">
          <h4 class="col-md-6">Feedback Questions</h4>
          <div class="col-md-6 text-end">
            <a href="{{url('feedback-questions/create')}}" class="btn btn-success">+</a>
<!--             <a href="{{url('feedback-questions/import')}}" class="btn btn-info"><i class="fas fa-cloud-upload-alt"></i></a>
 -->          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm table-hover table-bordered" id="users" style="width:100%;">
            <thead class="text-center bg-primary text-white">
              <tr>
                <th>Feedback Type</th>
                <th>Question Type</th>
                <th>Question</th>
                <th>status</th>
                <th>Modified By</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('js')
<script type="text/javascript">
//Roles table
$(document).ready( function(){
var regions_table = $('#users').DataTable({
processing: true,
serverSide: true,
ajax: "{{url('feedback-questions')}}",
buttons:[],
columns: [
{data: 'feedback_type', name: 'feedback_type',class:'text-center'},
{data: 'question_type', name: 'question_type',class:'text-center'},
{data: 'question', name: 'question'},
{data: 'status', name: 'status', class:'text-center'},
{data: 'modified_by', name: 'modified_by', class:'text-center', orderable: false, searchable: false},
{data: 'action', name: 'action', orderable: false, class:"d-flex justify-content-center w-auto", searchable: false},
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