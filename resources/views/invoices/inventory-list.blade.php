<html lang="en">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>{{ 'InventoryList_Job' . $invoice->invoice_number . '_' . $job->job_number }}</title>
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
                float: left;
                /*margin-top: 11px;*/
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

            table.green {
                width: 100%;
                border-spacing: 0;
                margin-bottom: 20px;
                color: #000;
            }

            table.green>thead>tr>th,
            table.green>tbody>tr>td {
                white-space: nowrap;
                font-weight: normal;
                padding: 5px 10px;
                text-align: left;
                border: 1px solid #00c292;
                font-size: 12px;
            }

            table.green>thead>tr>th {
                background: #00c292;
                font-size: 16px;
                text-transform: uppercase;
            }

            table.green th.big {
                font-size: 18px;
            }

            table.green>tbody>tr>td {
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
        @if($company->logo && file_exists(public_path('user-uploads') . '/company-logo/' . $company->logo))
        <header class="clearfix">
            <table cellpadding="0" cellspacing="0" class="billing">
                <tr>
                    <td>
                        <div id="logo">
                            <img 
                                src="{{ asset('user-uploads/company-logo/' . $company->logo) }}">
                        </div>
                    </td>
                </tr>
            </table>
        </header>
        @endif
        <main>
            <table cellspacing="0" cellpadding="0" class="simple">
                <thead>
                    <tr>
                        <th>{{ $company->company_name }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>@lang('modules.invoice.address'):</strong> {{ $company->address }}</td>
                    </tr>
                </tbody>
            </table>
            <table cellspacing="0" cellpadding="0" class="green">
                <thead>
                    <tr>
                        <th colspan="2" class="big">@lang('modules.invoice.inventory_list')</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width="33%">@lang('modules.invoice.job_no')</td>
                        <td>{{ $job_id }}</td>
                    </tr>
                    <tr>
                        <td>@lang('modules.invoice.customer_name')</td>
                        <td>{{ $job->customer->first_name }} {{ $job->customer->last_name }}</td>
                    </tr>
                    <tr>
                        <td>@lang('modules.invoice.pickup_address')</td>
                        <td>{{ $job->pickup_address }}</td>
                    </tr>
                    <tr>
                        <td>@lang('modules.invoice.delivery_address')</td>
                        <td>{{ $job->drop_off_address }}</td>
                    </tr>
                    <tr>
                        <td>@lang('modules.invoice.job_date')</td>
                        <td>{{ $job->job_date->format($global->date_format) }}</td>
                    </tr>
                </tbody>
            </table>
            <table cellspacing="0" cellpadding="0" class="green">
                <thead>
                    <tr>
                        <th>@lang('modules.invoice.summary')</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <table cellspacing="0" cellpadding="0" class="simple mt20">
                                <thead>
                                    <tr>
                                        <th>@lang('modules.invoice.item_group')</th>
                                        <th>@lang('modules.invoice.item_description')</th>
                                        <th class="text-center">@lang('modules.invoice.quantity')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $total = 0; ?>
                                    @foreach($job_items as $item)
                                    <tr>
                                        <td>{{ $item->group_name }}</td>
                                        @if($item->inventory_id > 9000)
                                        <td>{{ $item->misc_item_name }}</td>
                                        @else
                                        <td>{{ $item->item_name }}</td>
                                        @endif
                                        <td class="text-center">{{ intval($item->quantity) }}</td>
                                    </tr>
                                    <?php $total += $item->quantity; ?>
                                    @endforeach
                                    <tr>
                                        <td colspan="2" class="text-right"><strong>@lang('modules.invoice.total_quantity')</strong></td>
                                        <td class="text-center"><strong>{{ $total }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </main>
    </body>

</html>