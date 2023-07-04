<table>
    <thead>
    <tr>
    <th>First Name</th>
     <th>Last Name</th>
     <th>Email</th>
     <th>Phone</th>
     <th>Age</th>
     <th>Company Email</th>
     <th>Weight</th>
     <th>Height</th>
     <th>Marital Status</th>     
     <th>Gender</th>
     <th>Blood Group</th>
     <th>Date of Birth</th>
     <th>Address</th>
     <th>Employee ID</th>
     <th>CNIC</th>
     <th>Country</th>
     <th>State</th>
     <th>City</th>
     <th>Expiry Date</th>
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
                       {{Carbon\Carbon::parse($client->dob)->age}}
                    @endif
                </td> 

                <td>
                hamsafartelehealth@gmail.com
                </td> 

                <td>
                    @if($client!=null)
                       {{$client->weight}}
                    @endif
                </td> 

                <td>
                    @if($client!=null)
                       {{$client->height}}
                    @endif
                </td> 

                <td>
                    @if($client!=null)
                        @if(isset(MaritalStatus()[$client->marital_status]))
                            {{ MaritalStatus()[$client->marital_status] }}
                        @endif
                    @endif
                </td>                 

                <td>
                    @if($client!=null)
                       {{$client->gender}}
                    @endif
                </td> 

                <td>
                    @if($client!=null)
                       {{$client->blood_group}}
                    @endif
                </td> 

                <td>
                    @if($client!=null)
                       {{Carbon\Carbon::parse($client->dob)->format('m/d/Y');}}
                    @endif
                </td> 

                <td>
                    @if($client!=null)
                       {{$client->address}}
                    @endif
                </td> 

                <td>
                    @if($client!=null)
                       {{$client->cnic}}
                    @endif
                </td> 

                <td>
                    @if($client!=null)
                       {{$client->cnic}}
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
                    {{Carbon\Carbon::parse($sub->expire_date)->format('m/d/Y');}}
                </td>

            </tr>
        @endforeach
    </tbody>
</table>