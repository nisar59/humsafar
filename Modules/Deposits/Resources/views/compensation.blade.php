<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-lg" id="deposit-detail" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Compensation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-8">
                        <strong>Desk Code:</strong> {{@$deposit->desk->desk_code}}<br>
                        <strong>Deposit Date:</strong> {{\Carbon\Carbon::parse($deposit->desposit_date)->format('d-m-Y')}}<br>
                        <strong>Deposit Slip No:</strong> {{$deposit->deposit_slip_no}}<br>
                        <strong>Deposit Amount:</strong> {{number_format($deposit->amount)}}
                    </div>
                    <div class="col-4 text-end">
                        <img width="100" class="img" height="100" src="{{asset('img/deposit-slips/'.$deposit->deposit_slip)}}">
                    </div>
                </div>
                <form class="row" id="compensation-form" action="{{url('deposits/compensation-store/'.$deposit->id)}}" method="post">
                    @csrf
                    <div class="col-md-12">
                        <table class="table table-sm table-hover table-bordered">
                            <thead class="text-center">
                                <th>Due Compensation</th>
                                <th>Paid Compensation</th>
                                <th>Pending Compensation</th>
                            </thead>
                            <tbody>
                                <tr class="text-center">
                                    <td>{{number_format($deposit->due_compensation)}}</td>
                                    <td>{{number_format($deposit->paid_compensation)}}</td>
                                    <td>{{number_format($deposit->pending_compensation)}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if($deposit->pending_compensation>0)
                    <div class="col-12 form-group">
                        <label>Enter compensation to pay</label>
                        <input type="number" name="compensation_to_pay" class="form-control" value="{{$deposit->pending_compensation}}" placeholder="Enter compensation to pay">
                    </div>
                    @endif
                </form>
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    @if($deposit->pending_compensation>0)
                    <button type="submit" onclick="$('#compensation-form').submit();" class="btn btn-primary">Submit</button>
                    @endif
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->