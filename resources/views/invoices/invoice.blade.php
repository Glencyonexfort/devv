<html lang="en">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>{{ 'Invoice_Job' . $invoice->invoice_number . '_' . $job->job_number }}</title>
        <style>
            .clearfix:after {
                content: "";
                display: table;
                clear: both;
            }

            a {
                color: #0087C3;
                text-decoration: none;
            }

            body {
                position: relative;
                width: 100%;
                height: auto;
                margin: 0 auto;
                color: #555555;
                background: #FFFFFF;
                font-size: 14px;
                font-family: Verdana, Arial, Helvetica, sans-serif;
            }

            h2 {
                font-weight: normal;
            }

            header {
                padding: 10px 0;
                margin-bottom: 20px;
                /* border-bottom: 1px solid #AAAAAA; */
            }

            #logo {
                /* float: left;*/
                text-align: center;
                /* margin-top: 11px;*/
            }

            #logo img {
                height: 55px;
                /*margin-bottom: 15px;*/
            }

            #details {
                margin-bottom: 50px;
            }

            #client {
                padding-left: 6px;
                float: left;
            }

            #client .to {
                color: #777777;
            }

            h2.name {
                font-size: 1.2em;
                font-weight: normal;
                margin: 0;
            }

            #invoice h1 {
                color: #0087C3;
                font-size: 2.4em;
                line-height: 1em;
                font-weight: normal;
                margin: 0 0 10px 0;
            }

            #invoice .date {
                font-size: 1.1em;
                color: #777777;
            }

            .status {
                margin-top: 15px;
                padding: 1px 8px 5px;
                font-size: 1.3em;
                width: 80px;
                color: #fff;
                float: right;
                text-align: center;
                display: inline-block;
            }

            .status.unpaid {
                background-color: #E7505A;
            }

            .status.paid {
                background-color: #26C281;
            }

            .status.cancelled {
                background-color: #95A5A6;
            }

            .status.error {
                background-color: #F4D03F;
            }


            #thanks {
                font-size: 2em;
                margin-bottom: 50px;
            }

            #notices {
                padding-left: 6px;
                border-left: 6px solid #0087C3;
            }

            #notices .notice {
                font-size: 1.2em;
            }

            footer {
                color: #777777;
                width: 100%;
                height: 30px;
                position: absolute;
                bottom: 0;
                border-top: 1px solid #AAAAAA;
                padding: 8px 0;
                text-align: center;
            }

            table.simple {
                width: 100%;
                border-spacing: 0;
                margin-bottom: 20px;
                color: #000;
            }

            table.simple th,
            table.simple td {
                white-space: nowrap;
                font-weight: normal;
                padding: 5px 10px;
                text-align: left;
                border: 1px solid #dadada;
            }

            table.simple th {
                background: #dadada;
                font-size: 16px;
            }

            table.simple td {
                font-size: 12px;
                background: #ffffff;
            }

            .mt20 {
                margin-top: 20px;
            }
            .text-right{
                text-align: right !important;
            }
            .text-center{
                text-align: center !important;
            }
        </style>
    </head>

    <body>
        @php
        $color = "#000099";
        @endphp
        @if($company->logo && file_exists(public_path('user-uploads') . '/company-logo/' . $company->logo))
        <header class="clearfix">
            <table>
                <tr style="background-color: white; padding: 0px;">
                    <td style="text-align: center;border: 1px solid white;">
                        <div id="logo">
                            <img 
                                height="80" 
                                style="text-align: center;" 
                                width="160" 
                                src="{{ asset('user-uploads/company-logo/' . $company->logo) }}">
                        </div>
                    </td>
                </tr>
            </table>
        </header>
        @endif
        <h1 style="font-weight: bold; margin-top:10px;margin-bottom:0px;background-color: <?php echo $color; ?>; color: #fff;text-align: center;">Invoice # {{$invoice->invoice_number}}</h1>
        <main>
            <table cellspacing="0" cellpadding="0" class="simple">
                <tr style="background-color: #dddddd;">
                    <th>Company:</th>
                </tr>
                @if($company->company_name)
                <tr>
                    <td><h3>{{ $company->company_name }}</h3></td>
                </tr>
                @endif
                <tr>
                    <td><strong>Address:</strong> {{ $company->address }}</td>
                </tr>
                @if($company->phone)
                <tr>
                    <td><strong>Phone:</strong> {{ $company->phone }}</td>
                </tr>
                @endif
                @if($company->abn)
                <tr>
                    <td><strong>A.B.N:</strong> {{ $company->abn }}</td>
                </tr>
                @endif
            </table>


            <h1 style="font-weight: bold;height:40px; margin-top:10px;margin-bottom:0px;background-color: <?php echo $color; ?>; color: #fff;text-align: center;"></h1>

            <table  cellspacing="0" cellpadding="0" class="simple">
                <tr>
                    <td>
                        <table cellspacing="0" cellpadding="0" class="simple">
                            <tr>
                                <th></th>
                            </tr>
                            <tr>
                                <td><strong>Bill To:</strong> {{ strtoupper($job->customer->first_name) }} {{ strtoupper($job->customer->last_name) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Phone:</strong> {{ $job->customer->phone }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong> {{ $job->customer->email }}</td>
                            </tr>

                        </table>
                    </td>

                    <td>
                        <table cellspacing="0" cellpadding="0" class="simple">
                            <tr>
                                <th></th>
                            </tr>
                            <tr>
                                <td><strong>Tax Invoice No: </strong> {{$invoice->invoice_number}}</td>
                            </tr>
                            <tr>
                                <td><strong>Date: </strong> {{ $job->job_date->format($global->date_format) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Job No: </strong> {{$job_id}}</td>
                            </tr>
                        </table>
                    </td>
                </tr>

            </table>

            <table cellspacing="0" cellpadding="0" class="simple">
                <tr style="background-color: #dddddd;">
                    <th width="85">@lang('modules.invoice.item')</th>
                    <th width="150">@lang('modules.invoice.description')</th>
                    <th width="30">@lang('modules.invoice.qty')</th>
                    <th width="40">@lang('modules.invoice.unit_price')</th>
                     <!-- <th width="40">@lang('modules.invoice.price')</th> -->
                    <th width="100">@lang('modules.invoice.total')(Inc-GST)</th>
                </tr>
                <tr>
                    <td>{{$invoice_items->item_name}}</td>
                    <td>{!! \Illuminate\Support\Str::words($invoice_items->item_summary, 7,'....')  !!}</td>
                    <td class="text-center">{{$invoice_items->quantity}}</td>
                    <td class="text-center">{{$invoice_items->unit_price}}</td>
                    <!-- <td class="text-center">{{$invoice_items->amount}}</td> -->
                    <td>&#36;{{number_format((float)$invoice_items->amount, 2, '.', '')}}</td>
                </tr>
            </table>
            <br/>


            <table cellspacing="0" cellpadding="0" class="simple">
                <tr>
                    <td>Balance Due Days:<br/><strong>Payable on or before Pick Up of Goods</strong></td>
                    <td><strong>Subtotal:</strong></td>
                    <td>&#36;{{number_format((float)$invoice_items->amount/1.1, 2, '.', '')}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td><strong>GST:</strong></td>
                    <td>&#36;{{number_format((float)$invoice_items->amount/11, 2, '.', '')}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td><strong>Total(Incl-GST):</strong></td>
                    <td>&#36;{{number_format((float)$invoice_items->amount, 2, '.', '')}}</td>
                </tr>
                <?php
                $totalPaid = 0;
                foreach ($invoice->payment as $payment) {
                    $totalPaid += $payment->amount;
                }
                ?>
                <tr>
                    <td></td>
                    <td><strong>Paid to Date:</strong></td>
                    <td>&#36;{{number_format((float)$totalPaid, 2, '.', '')}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td style="background-color: #dddddd;">
                        <strong>Balance Due:</strong>
                    </td>
                    <td style="background-color: #dddddd;"><strong>&#36;{{number_format((float)($invoice_items->amount-$totalPaid), 2, '.', '')}}</strong></td>
                </tr>
            </table>

            <h1 style="font-weight: bold;background-color: <?php echo $color; ?>; color: #fff;text-align: center;margin-top:10px;margin-bottom:0px;">Payments Received</h1>
            <table cellspacing="0" cellpadding="0" class="simple">
                <tr style="background-color: #dddddd;">
                    <th>Payment Date</th>
                    <th>Amount</th>
                    <th>Payment method</th>
                </tr>
                @if(count($invoice->payment) > 0)
                @foreach($invoice->payment as $payment)
                <tr>
                    <td><?php echo date('d-m-Y', strtotime($payment->created_at)); ?></td>
                    <td><?php echo $payment->amount; ?></td>
                    <td><?php echo $payment->gateway; ?></td>
                </tr>
                @endforeach
                @else
                <tr><td colspan="3" style="text-align:center;"><h3>No Record Found!</h3></td></tr>
                @endif
            </table>
            <br/>

            <strong>Payment Instructions:</strong><br/>
            <?php
            if ($job->payment_instructions == NULL || $job->payment_instructions == '') {
                echo $job->template_payment_instructions;
            } else {
                echo $job->payment_instructions;
            }
            ?>
        </main>
    </body>

</html>