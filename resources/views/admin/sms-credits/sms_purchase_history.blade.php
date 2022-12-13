<table>
    <thead>
        <th>Gateway</th>
        <th>Transaction ID</th>
        <th>Transaction Date</th>
        <th>Credit Purchased</th>
        <th>Payment Amount</th>
        <th>Status</th>
    </thead>
    <tbody>
        @foreach ($sms_purchase_history as $d)   
        <?php
            $customer='';
            $customer =\App\CRMLeads::where('id', '=', $d->customer_id)->pluck('name')->first();
        ?>             
        <tr>
            <td>{{ $d->gateway }}</td>
            <td>{{ $d->transaction_id }}</td>
            <td>{{ date($global->date_format,strtotime($d->transaction_date)) }}</td>  
            <td>{{ $d->qty_purchased }}</td>
            <td>{{  $global->currency_symbol}}{{ number_format((float)$d->payment_amount, 2, '.', ',') }}</td>
            <td><span class="badge badge-success" style="text-transform: capitalize;">{{ $d->status }}</span></td>
        </tr>
        @endforeach
    </tbody>
</table>