<table>
    <thead>
    <tr>
    <th>First Name</th>
     <th>Last Name</th>
     <th>Email</th>
     <th>Phone</th>
     <th>Company Email</th>
     <th>Employee ID</th>
     <th>Marital Status</th>
     <th>Date of Birth</th>
     <th>CNIC</th>
     <th>Gender</th>
     <th>Country</th>
     <th>State</th>
     <th>City</th>
     <th>Expiry Date</th>
     <th>Relation</th>
    </tr>
    </thead>
    <tbody>
        @foreach($subscriptions as $sub)

            @php
                $client=ClientDetail($sub->client_id);
            @endphp

            <tr>
                <td>
                    @if($client!=null)
                       {{$client->name}}
                    @endif
                </td>                
                <td>
                    @if($client!=null)
                       {{$client->parentage}}
                    @endif
                </td> 
                <td>
                    @if($client!=null)
                       {{$client->email}}
                    @endif
                </td> 
                <td>
                    @if($client!=null)
                       {{$client->phone_primary}}
                    @endif
                </td> 
                <td>
                    @if($client!=null)
                       {{$client->email}}
                    @endif
                </td> 
                <td>
                    @if($client!=null)
                       {{$client->cnic}}
                    @endif
                </td> 
                <td>
                    @if($client!=null)
                       @if($client->marital_status==1)
                        Married
                       @else
                        Unmarried
                       @endif
                    @endif
                </td> 
                <td>
                    @if($client!=null)
                       {{Carbon\Carbon::parse($client->dob)->format('d-m-Y');}}
                    @endif
                </td> 
                <td>
                    @if($client!=null)
                       {{$client->cnic}}
                    @endif
                </td> 
                <td>
                    @if($client!=null)
                       {{$client->gender}}
                    @endif
                </td> 
                <td>
                    Pakistan
                </td>
                <td>
                    @if($client!=null)
                       {{$client->province}}
                    @endif                
                </td>
                <td>
                    @if($client!=null)
                       {{$client->district}}
                    @endif                
                </td>
                <td>
                    {{Carbon\Carbon::parse($sub->expire_date)->format('d-m-Y');}}
                </td>
                <td>
                    {{isset(RelationWith()[$client->relation]) ? RelationWith()[$client->relation] : 'Beneficiary']}}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>