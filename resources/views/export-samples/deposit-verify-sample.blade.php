<table>
    <thead>
    <tr>
    <th>deposit_slip_no</th>
    <th>amount</th>
    </tr>
    </thead>
    <tbody>
        @if($data!=null)
        @foreach($data as $vl)
        <tr>
            <td>{{$vl['deposit_slip_no']}}</td>
            <td>{{$vl['amount']}}</td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>