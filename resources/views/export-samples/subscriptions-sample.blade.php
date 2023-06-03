<table>
    <thead>
    <tr>
    <th>cnic</th>
    <th>username</th>
    <th>password</th>
    </tr>
    </thead>
    <tbody>
        @if($data!=null)
            @foreach($data as $vl)
                <tr>{{$vl['cnic']}}</tr>
                <tr>{{$vl['username']}}</tr>
                <tr>{{$vl['password']}}</tr>
            @endforeach
        @endif
    </tbody>
</table>