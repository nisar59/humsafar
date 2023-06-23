<table>
    <thead>
    <tr>
      <th>Desk Code</th>
      <th>User</th>
      <th>Account No</th>
      <th>Amount</th>
      <th>Deposit Slip No</th>
      <th>Deposit Date</th>
    </tr>
    </thead>
    <tbody>
        @foreach($deposits as $deposit)
            <tr>
                <td>
                    @if($deposit->desk()->exists() && $deposit->desk!=null)
                        {{ $deposit->desk->desk_code }}
                    @endif
                </td>
                <td>
                    @if($deposit->user()->exists() && $deposit->user!=null)
                        {{ $deposit->user->name }}
                    @endif  
                </td>
                <td>
                    @if($deposit->user()->exists() && $deposit->user!=null)
                        {{ $deposit->user->bank_account_no }}
                    @endif  
                </td>
                <td>
                    {{$deposit->amount}}
                </td>
                <td>
                    {{$deposit->deposit_slip_no}}
                </td>
                <td>
                    {{Carbon\Carbon::parse($deposit->desposit_date)->format('d-m-Y')}}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>