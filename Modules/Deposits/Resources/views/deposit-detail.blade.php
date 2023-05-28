<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-lg" id="deposit-detail" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Deposit Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-8">
                        <strong>Desk Code:</strong> {{$deposit->desk->desk_code}}<br>
                        <strong>Deposit Date:</strong> {{\Carbon\Carbon::parse($deposit->desposit_date)->format('d-m-Y')}}<br>
                        <strong>Deposit Slip No:</strong> {{$deposit->deposit_slip_no}}<br>
                        <strong>Deposit Amount:</strong> {{$deposit->amount}}
                    </div>
                    <div class="col-4 text-end">
                        <img width="100" class="img" height="100" src="{{asset('img/deposit-slips/'.$deposit->deposit_slip)}}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-sm table-hover table-bordered">
                            <thead class="text-center">
                                <th>Sr No#</th>
                                <th>Client</th>
                                <th>Package</th>
                                <th>Amount</th>
                                <th>Transaction ID</th>
                                <th>Subscription Date</th>
                            </thead>
                            <tbody>

                                @if($deposit->clientsubscriptions!=null && count($deposit->clientsubscriptions)>0)
                                @foreach($deposit->clientsubscriptions as $key=> $clientsub)
                                <tr class="text-center">
                                    <td>{{$key+1}}</td>
                                    <td>{{@ClientDetail($clientsub->client_id)->name}}</td>
                                    <td>@if(PackageDetail($clientsub->package_id)!=null) {{PackageDetail($clientsub->package_id)->title}} @endif</td>
                                    <td>{{$clientsub->amount}}</td>
                                    <td>{{$clientsub->transaction_no}}</td>
                                    <td>{{\Carbon\Carbon::parse($clientsub->subscription_date)->format('d-m-Y')}}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->