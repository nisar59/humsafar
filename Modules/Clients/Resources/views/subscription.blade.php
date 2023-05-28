<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">{{$client->name}}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12">
            <table class="table table-sm table-hover table-bordered">
              <thead class="text-center">
                <th>Sr No#</th>
                <th>Package</th>
                <th>Amount</th>
                <th>Transaction ID</th>
                <th>Subscription Date</th>
                <th>Expire Date</th>
                <th>Status</th>
                <th>Action</th>
              </thead>
              <tbody>
                @if(count($client->clientsubscriptions)>0)
                @foreach($client->clientsubscriptions as $key=> $clientsub)
                <tr class="text-center">
                <td>{{$key+1}}</td>
                <td>@if(PackageDetail($clientsub->package_id)!=null) {{PackageDetail($clientsub->package_id)->title}} @endif</td>
                <td>{{$clientsub->amount}}</td>
                <td>{{$clientsub->transaction_no}}</td>
                <td>{{\Carbon\Carbon::parse($clientsub->subscription_date)->format('d-m-Y')}}</td>
                <td>{{\Carbon\Carbon::parse($clientsub->expire_date)->format('d-m-Y')}}</td>
                <td>@if($clientsub->expire_date > now()) <span class="badge bg-success">Active</span>@else <span class="badge bg-info">Expired</span> @endif</td>
                <td><a class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete, it will never be undone?')" href="{{url('clients/delete-subscription/'.$clientsub->id)}}"><i class="fas fa-trash-alt"></i></a></td>
                </tr>
                @endforeach
                @endif
              </tbody>
            </table>
          </div>
        </div>
        @if(!$client->activesubscription()->exists() && $client->activesubscription==null)
        <form action="{{url('clients/new-subscription/'.$client->id)}}" method="post" id="subscription-form">
          @csrf
          <div class="row">
            <div class="col-12 form-group">
              <label for="">Subscribe new Package</label>
              <select name="package_id" class="form-control">
                <option value="">Select Package</option>
                @foreach($packages as $package)
                <option value="{{$package->id}}">{{$package->title}} - {{$package->amount}}</option>
                @endforeach
              </select>
            </div>
          </div>
        </form>
        @endif
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        @if(!$client->activesubscription()->exists() && $client->activesubscription==null)
        <button onclick="$('#subscription-form').submit()" class="btn btn-primary">subscribe</button>
        @endif
      </div>
    </div>
  </div>
</div>