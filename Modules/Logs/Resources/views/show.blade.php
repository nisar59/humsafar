<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Logs Detail </h5>
        <span class='badge @if($log->log_type=="created") bg-success @elseif($log->log_type=="updated") bg-info @else bg-danger @endif'>{{strtoupper($log->log_type)}}
        </span>
      </div>
      <div class="modal-body">
      	<div class="row mb-3">
      	<div class="col-8">
      		<b>User Name:</b> {{$log->user!=null ? $log->user->name : ''}} <br>
      		<b>Date:</b> {{$log->created_at->format('Y-m-d H:i')}}
      	</div>
      	<div class="col-4">
      		<b>Model Name:</b> {{$log->model}}<br>
      		<b>Model ID:</b> {{$log->model_id}}
      	</div>
      	</div>
        <div class="row">
          <div class="col-12">
          <table class="table table-sm table-hover table-bordered" style="width:100%;">
            <thead class="text-center bg-primary text-white">
          			<th>Previouse</th>
          			<th>New</th>
          		</thead>
          		<tbody>
          			@php
          			$log_data=json_decode($log->data);
		            $previous=(array) $log_data->previous;
		            $new=(array) $log_data->new;
          			@endphp
          			<tr>
          				<td class="w-50 text-break">
          				@if(count($previous)<1)
          				<center> Empty </center>
          				@endif
          				@foreach($previous as $key =>$data)
          				<b>{{$key}}</b> : {{$data}} <br>
          				@endforeach
          				</td>

          				<td class="w-50 text-break">
          				@foreach($new as $key =>$data)
          				<b>{{$key}}</b> : {{$data}} <br>
          				@endforeach
          				</td>
          			</tr>
          		</tbody>
          	</table>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
  </div>
</div>