<html lang="en">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>{{ 'Quote_Job' . $invoice->invoice_number . '_' . $job->job_number }}</title>
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
                /*background: #dadada;*/
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
        $color = "#C88228";
        @endphp
        <header class="clearfix">
            @if($company->logo && file_exists(public_path('user-uploads') . '/company-logo/' . $company->logo))
            <div id="logo">
                <img 
                    height="105" 
                    width="200" 
                    src="{{ asset('user-uploads/company-logo/' . $company->logo) }}">
            </div>
            @endif
        </header>
        <main>
            <table cellspacing="0" cellpadding="0" class="simple">
                @if($company->company_name)
                <tr style="background-color: #dddddd;">
                    <th style="width: 70%">
                        <h3>{{ $company->company_name }}</h3>
                    </th>
                    <th style="width: 30%">ABN: {{ $company->abn }}</th>
                </tr>
                @endif
                @if($company->address)
                <tr>
                    <td colspan="2">
                        <strong>Address:</strong> {{ $company->address }}</td>
                </tr>
                @endif
            </table>
            <table><tr><td></td></tr></table>
            <table cellspacing="0" cellpadding="0" class="simple">
                <tr style="background-color:{{$color}};color: #fff;font-weight: bold;">
                    <th style="width: 70%"><h1>Quote # {{$invoice->invoice_number}}</h1></th>
                    <th style="width: 30%">{{$invoice->sys_job_type}}</th>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" class="simple">
                <tbody>
                    <tr>
                        <td style="width: 30%">Job Date</td>
                        <td style="width: 70%">{{ $job->job_date->format($global->date_format) }}</td>
                    </tr>
                    <tr>
                        <td style="width: 30%">Customer</td>
                        <td>{{ strtoupper($job->customer->first_name) }} {{ strtoupper($job->customer->last_name) }}</td>
                    </tr>
                    <tr>
                        <td style="width: 30%">Contact No</td>
                        <td>{{ $job->customer->phone }}</td>
                    </tr>
                    <tr>
                        <td style="width: 30%">Email</td>
                        <td>{{ $job->customer->email }}</td>
                    </tr>
                </tbody>
            </table>
            <table cellspacing="0" cellpadding="0" class="simple">
                <tbody>
                    <tr style="background-color:{{$color}};color: #fff;">
                        <th>Job Description</th>
                    </tr>
                    <tr>
                        <td>
                            <table cellspacing="0" cellpadding="0" class="simple">
                                <tr style="background-color: #dddddd;">
                                    <th>Pickup Details:</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <td>Pickup Address</td>
                                    <td>{{ $job->pickup_address ? $job->pickup_address : '' }}</td>
                                </tr>
                                <tr>
                                    <td>Property Type:</td>
                                    <td>{{ $job->pickup_property_type ? $job->pickup_property_type : '' }}</td>
                                </tr>
                                <tr>
                                    <td>Bedrooms:</td>
                                    <td>{{ $job->pickup_bedrooms ? $job->pickup_bedrooms : '' }}</td>
                                </tr>
                                <tr>
                                    <td>Access Restrictions:</td>
                                    <td>{{ $job->pickup_access_restrictions ? $job->pickup_access_restrictions : '' }}</td>
                                </tr>
                            </table>


                            <table cellspacing="0" cellpadding="0" class="simple">
                                <tr style="background-color: #dddddd;">
                                    <th>Delivery Details:</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <td>Delivery Address</td>
                                    <td>{{ $job->drop_off_address ? $job->drop_off_address : '' }}</td>
                                </tr>
                                <tr>
                                    <td>Property Type:</td>
                                    <td>{{ $job->drop_off_property_type ? $job->drop_off_property_type : '' }}</td>
                                </tr>
                                <tr>
                                    <td>Bedrooms:</td>
                                    <td>{{ $job->drop_off_bedrooms ? $job->drop_off_bedrooms : '' }}</td>
                                </tr>

                                <tr>
                                    <td>Access Restrictions:</td>
                                    <td>{{ $job->drop_off_access_restrictions ? $job->drop_off_access_restrictions : '' }}</td>
                                </tr>
                            </table>

                            <table cellspacing="0" cellpadding="0" class="simple" style="float:left;margin-top: 10px;">
                                <tr>
                                    <td>Total Cubic Meters (CBM):</td>
                                    <td>{{ ($job->total_cbm && $job->total_cbm != 0) ? $job->total_cbm : '' }}</td>
                                </tr>


                            </table>

                        </td>
                    </tr>
                </tbody>
            </table>
        </main>
    </body>

</html>