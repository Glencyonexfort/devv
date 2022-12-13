<table>
    <thead>
        <th>Job #</th>
        <th>Invoice</th>
        <th>System Job Type</th>
        <th>Customer Name</th>
        <th>Invoice Date</th>
        <th>Invoice Amuont</th>
        <th>Payment Status</th>
    </thead>
    <tbody>
        
        @foreach ($invoices as $d)   
        <?php
            $customer='';
            $customer =\App\CRMLeads::where('id', '=', $d->customer_id)->pluck('name')->first();
        ?>             
        <tr>
            <td>{{ $d->job_number }}</td>
            <td>{{ $d->invoice_number }}</td>
            <td>{{ $d->job_type }}</td>
            <td>{{ $customer }}</td>
            <td>{{ date($global->date_format,strtotime($d->issue_date)) }}</td>            
            <td>{{  $global->currency_symbol}}{{ number_format((float)$d->getTotalAmount(), 2, '.', '') }}</td>
            <td>{{ strtoupper($d->status) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>