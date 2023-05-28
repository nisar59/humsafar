<table>
    <thead>
    <tr>
      <th>Client Name</th>
      <th>Client CNIC</th>
      <th>Package</th>
      <th>Amount</th>
      <th>Subscription Date</th>
      <th>Expire Date</th>
    </tr>
    </thead>
    <tbody>
        @foreach($subscriptions as $sub)
            <tr>
                <td>
                    @if(ClientDetail($sub->client_id)!=null)
                       {{ClientDetail($sub->client_id)->name}}
                    @endif
                </td>
                <td>
                    @if(ClientDetail($sub->client_id)!=null)
                       {{ClientDetail($sub->client_id)->cnic}}
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
            </tr>
        @endforeach
    </tbody>
</table>