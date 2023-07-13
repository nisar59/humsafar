@extends('layouts.template')
@section('title')
Settings
@endsection
@section('content')
<div class="page-title-box">
  <div class="row align-items-center">
    <div class="col-md-8">
      <h6 class="page-title">Panel Settings</h6>
      <ol class="breadcrumb m-0">
        <li class="breadcrumb-item">{{Settings()->portal_name}}</li>
        <li class="breadcrumb-item">Panel Settings</li>
        <li class="breadcrumb-item active">Panel Settings</li>
      </ol>
    </div>
  </div>
</div>
@php
$sett=$data['settings'];
$logo=url('public/img/images.png');
$favicon=url('public/img/images.png');
if($sett->portal_logo!='' AND file_exists(public_path('img/settings/'.$sett->portal_logo))){
$logo=url('public/img/settings/'.$sett->portal_logo);
}
if($sett->portal_favicon!='' AND file_exists(public_path('img/settings/'.$sett->portal_favicon))){
$favicon=url('public/img/settings/'.$sett->portal_favicon);
}
@endphp
<form action="{{url('settings/store')}}" method="post" enctype="multipart/form-data">
  @csrf
  <div class="row">
    <div class="col-12 col-md-12">
      <div class="card card-primary">
        <div class="card-body p-0">
          <div class="row">
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
              <div class="list-group nav-tabs penal-settings" role="tablist">
                <a class="list-group-item text-center rounded-0 active" data-bs-toggle="tab" href="#main-settings" role="tab">
                 Main Settings
                </a>
                <a class="list-group-item text-center rounded-0" data-bs-toggle="tab" href="#application-logs" role="tab">
                  Application Logs
                </a>
                <a class="list-group-item text-center rounded-0" data-bs-toggle="tab" href="#sms-configurations" role="tab">
                  SMS Configurations
                </a>
                <a class="list-group-item text-center rounded-0" data-bs-toggle="tab" href="#mobile-app-configurations" role="tab">
                  Mobile App Configurations
                </a>

              </div>
            </div>
            <!-- Tab panes -->
            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
              <div class="tab-content">
                <div class="tab-pane active p-3" id="main-settings" role="tabpanel">
                  <div class="row">
                    <div class="form-group col-md-6">
                      <label>Panel Name</label>
                      <input type="text" class="form-control" name="panel_name" value="{{$sett->portal_name}}" placeholder="Panel Name">
                    </div>
                    <div class="form-group col-md-6">
                      <label>Panel Email</label>
                      <input type="email" class="form-control" name="panel_email" value="{{$sett->portal_email}}" placeholder="Panel Email">
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-md-6">
                      <label>Panel Logo</label>
                      <input type="file" class="form-control" name="panel_logo" id="panel_logo">
                    </div>
                    <div class="form-group col-md-6">
                      <label>Panel Favicon</label>
                      <input type="file" class="form-control" name="panel_favicon" id="panel_favicon">
                    </div>
                  </div>
                </div>
                <div class="tab-pane p-3" id="application-logs" role="tabpanel">
                  <div class="row">

                  <div class="col-md-4 form-group">
                    <label for="">Logging</label>
                    <select name="logging" class="form-control">
                      <option value="1" {{$sett->logging=='1' ? 'selected' : ''}}>Yes</option>
                      <option value="0" {{$sett->logging=='0' ? 'selected' : ''}}>No</option>
                    </select>                  
                  </div>

                    <div class="col-md-4 form-group">
                    <label for="">Logs will be deleted older Than</label>
                    <input type="number" min="1" value="{{$sett->logs_duration!=null ? $sett->logs_duration : 7}}" class="form-control" name="logs_duration" placeholder="Logs will be deleted older Than">
                  </div>
                    <div class="col-md-4 form-group">
                    <label for="">Duration type</label>
                    <select name="logs_duration_type" class="form-control">
                      <option value="days" {{$sett->logs_duration_type=='days' ? 'selected' : ''}}>Days</option>
                      <option value="weeks" {{$sett->logs_duration_type=='weeks' ? 'selected' : ''}}>Weeks</option>
                      <option value="months" {{$sett->logs_duration_type=='months' ? 'selected' : ''}}>Months</option>
                      <option value="years" {{$sett->logs_duration_type=='years' ? 'selected' : ''}}>Years</option>
                    </select>
                  </div>
                  </div>
                </div>
                <div class="tab-pane p-3" id="sms-configurations" role="tabpanel">
                 <div class="row">
                  <div class="col-md-12 form-group">
                    <label>SMS Notifications</label>
                    <select name="sms_notifications" class="form-control">
                      <option value="1" {{$sett->sms_notifications==1 ? 'selected' : ''}}>Enabled</option>
                      <option value="0" {{$sett->sms_notifications==0 ? 'selected' : ''}}>Disabled</option>
                    </select>    
                  </div>
                </div>
                <div class="row" id="sendsms">
                  <div class="col-12"><hr>
                    <p class="fw-bold m-0">Test SMS Notifications</p>
                  </div>
                  <div class="col-12">
                    <label for="">Phone No</label>
                    <input type="number" name="phone" class="form-control" placeholder="Enter Phone No">
                  </div>

                  <div class="col-12">
                    <label for="">Text</label>
                    <textarea name="text"class="form-control" placeholder="Enter Text"></textarea>
                  </div>

                  <div class="col-12 text-end mt-2">
                    <button type="button" id="sendsms-button" class="btn btn-info btn-sm">Send</button>
                  </div>
                 </div>
                </div>
                <div class="tab-pane p-3" id="mobile-app-configurations" role="tabpanel">
                 <div class="row">
                  <div class="col-md-6 form-group">
                    <label>App Version</label>
                      <input type="text" class="form-control" value="{{$sett->version}}" name="version" placeholder="Mobile App Version">   
                  </div>
                  <div class="col-md-6 form-group">
                    <label>App URL</label>
                      <input type="text" class="form-control" value="{{$sett->app_url}}" name="app_url" placeholder="Mobile App URL">   
                  </div>

                 </div>
                </div>


              </div>
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
<script>
  $(document).ready(function() {
      $(document).on('click', '#sendsms-button', function() {
        $("#sendsms").wrap('<form id="form-sendsms" action="{{ url('settings/sms-test/') }}" method="post"></form>');
        $("#form-sendsms").prepend('@csrf');

        $("#form-sendsms").submit();



      });
  });
</script>
@endsection