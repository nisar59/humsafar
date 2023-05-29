<table>
    <thead>
    <tr>
    <th>Client CNIC</th>
     <th>Client Name</th>
     <th>Client Email</th>
      <th>Package</th>
      <th>Amount</th>
      <th>Subscription Date</th>
      <th>Expire Date</th>
      <th>Relation</th>
    </tr>
    </thead>
    <tbody>
        @foreach($subscriptions as $sub)
            <tr>
                <td>
                    @if(ClientDetail($sub->client_id)!=null)
                       {{ClientDetail($sub->client_id)->cnic}}
                    @endif
                </td>                
                <td>
                    @if(ClientDetail($sub->client_id)!=null)
                       {{ClientDetail($sub->client_id)->name}}
                    @endif
                </td>
                <td>
                    @if(ClientDetail($sub->client_id)!=null)
                       {{ClientDetail($sub->client_id)->email}}
                    @endif
                </td>
                <td>
                    @if(PackageDetail($sub->package_id)!=null)
                       {{PackageDetail($sub->package_id)->title}}
                    @endif
                </td>
                <td>
                    {{number_format($sub->amount)}}
                </td>
                <td>
                    {{Carbon\Carbon::parse($sub->subscription_date)->format('d-m-Y');}}
                </td>
                <td>
                    {{Carbon\Carbon::parse($sub->expire_date)->format('d-m-Y');}}
                </td>
                <td>Self</td>
            </tr>
        @endforeach
    </tbody>
</table>