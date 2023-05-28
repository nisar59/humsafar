@extends('layouts.template')
@section('title')
Feedback
@endsection
@section('content')
<div class="page-title-box">
  <div class="row align-items-center">
    <div class="col-md-8">
      <h6 class="page-title">Feedback</h6>
      <ol class="breadcrumb m-0">
        <li class="breadcrumb-item">{{Settings()->portal_name}}</li>
        <li class="breadcrumb-item">Feedback</li>
        <li class="breadcrumb-item active">Feedback Detail</li>
      </ol>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12 col-md-12">
    <div class="card card-primary">
      <div class="card-header bg-white">
        <h4>Feedback Detail</h4>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-8">
            <strong>Desk Code:</strong>
            @if(ClientDetail($data->client_id)!=null && $client=ClientDetail($data->client_id))
            {{ DeskDetail($client->desk_id)!=null ? DeskDetail($client->desk_id)->desk_code : null; }}
            @endif
            <br>
            <strong>Client Name:</strong>
            {{ ClientDetail($data->client_id)!=null ? ClientDetail($data->client_id)->name : null; }}
          </div>
          <div class="col-md-4">
            <strong>Feedback Date:</strong> {{\Carbon\Carbon::parse($data->created_at)->format('d-m-Y');}}<br>
            <strong>Feedback Type:</strong>
            @if($data->feedback_type=="positive")
            <span class="badge bg-success">Positive</span>
            @else
            <span class="badge bg-danger">Negative</span>
            @endif
          </div>
        </div><hr>
        <div class="row">
          <div class="col-12">
          <table class="table table-sm table-hover table-bordered" style="width:100%;">
            <thead class="text-center bg-primary text-white">                
              <th>Sr No</th>
                <th>Question</th>
                <th>Question Type</th>
                <th>Response</th>
              </thead>
              <tbody>
                @if($data->response()->exists() && $data->response!=null)
                @foreach($data->response as $key=> $resp)
                  <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$resp->question!=null ? $resp->question->question : ''}}</td>
                    <td>{{$resp->question!=null ? $resp->question->question_type : ''}}</td>
                    <td>{{$resp->response}}</td>
                  </tr>
                @endforeach
                @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
<!--       <div class="card-footer text-end">
        <a href="{{url('feedback')}}" class="btn btn-primary mr-1" type="submit">Back</a>
      </div> -->
    </div>
  </div>
</div>
@endsection
@section('js')
@endsection