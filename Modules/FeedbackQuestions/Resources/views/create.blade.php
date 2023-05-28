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
        <li class="breadcrumb-item active">Create Feedback Question</li>
      </ol>
    </div>
  </div>
</div>
<form action="{{url('feedback-questions/store')}}" method="post" enctype="multipart/form-data">
  @csrf
  <div class="row">
    <div class="col-12 col-md-12">
      <div class="card card-primary">
        <div class="card-header bg-white">
          <h4>Create Feedback Question</h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="form-group col-md-6">
              <label>Feedback Type</label>
              <select name="feedback_type" class="form-control">
                <option value="">Select Feedback Type</option>
                @if(is_array(FeedBackTypes()) && count(FeedBackTypes())>0)
                @foreach(FeedBackTypes() as $key => $type)
                <option value="{{$key}}">{{$type}}</option>
                @endforeach
                @endif
              </select>
            </div>
            <div class="form-group col-md-6">
              <label>Question Type</label>
              <select name="question_type" id="question-type" class="form-control">
                <option value="">Question Type</option>
                <option value="text">Text</option>
                <option value="check-box">Check Box</option>
                <option value="radio-button">Radio Button</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-12">
              <label>Question</label>
              <input type="text" class="form-control" name="question"  placeholder="Question">
            </div>
            <div class="col-md-12 text-end mt-1" hidden id="add-option-btn-container">
              <button type="button" class="btn btn-success" id="add-option-btn">+</button>
            </div>
          </div>
          <div class="row" id="questions-option"></div>
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
<script>
$(document).ready(function() {
$(document).on("change","#question-type", function(){
var question_type=$("#question-type").val();
if(question_type=="text" || question_type==""){
$("#add-option-btn-container").attr("hidden", true);
$("#questions-option").html('');
}
else{
$("#add-option-btn-container").removeAttr("hidden");
}
});
$(document).on("click","#add-option-btn", function(){
var option_html=`<div class="form-group col-md-3 position-relative option-container">
  <a href="javascript:void(0)" class="btn btn-danger btn-sm btn-option-remove">x</a>
  <label>Option</label>
  <input type="text" class="form-control" name="option[]"  placeholder="Option">
</div>`;
var question_type=$("#question-type").val();
if(question_type=="text" || question_type==""){
$("#add-option-btn-container").attr("hidden", true);
$("#questions-option").html('');
}
else{
$("#add-option-btn-container").removeAttr("hidden");
$("#questions-option").append(option_html);
}
});
$(document).on('click', '.btn-option-remove', function() {
$(this).parent().remove();
})
});

</script>
@endsection