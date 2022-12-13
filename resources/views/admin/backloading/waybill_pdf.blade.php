<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">    
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- <link rel="stylesheet" href="{{ URL::asset('css/new-css.css') }}"> --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/pdf.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/waybill.css') }}">
  </head>
  <body>
    <header class="clearfix" style="padding: 15px;border: none;margin:0px">
      <table style="width: 100%">
        <tr>
          <td>
            @if($company_logo_exists==true)
                <div id="logo">
                    <img src="{{ request()->getSchemeAndHttpHost().'/user-uploads/company-logo/'.$companies->logo }}">
                </div>
            @endif
          </td>
          <td>
            <div id="company" class="clearfix">
              <div><b>{{ $companies->company_name}}</b></div>
              <div>ABN: {{ $companies->abn}}</div>
              <div>{{ $companies->address}}</div>
              <div>Phone: {{ $companies->phone}}</div>
              <div><a href="mailto:{{ $companies->email}}">{{ $companies->email}}</a></div>
            </div>
          </td>
        </tr>
      </table>
    </header>
    <div class="sub-header">
        <p id="number">WAYBILL: <span>{{ $backloading_trip->waybill_number }}</span></p>
        <p id="date">Printed: <span>{{ date($organisation_settings->date_format) }}</span></p>
    </div>
    <div class="trip_detials">
        <table style="width: 100%">
            <tr>
                <td style="width: 25%">
                    <table>
                        <tr>
                            <td><b>Desc:</b></td>
                            <td>{{ $backloading_trip->trip_name }}</td>
                        </tr>
                        <tr>
                            <td><b>From:</b></td>
                            <td>{{ $backloading_trip->start_city }}</td>
                        </tr>
                        <tr>
                            <th><b>Dep Date:</b></td>
                            <td>{{ $backloading_trip->start_date }}</td>
                        </tr>
                        <tr>
                            <td><b>jobs:</b></td>
                            <td>{{ count($backloading_trip_jobs) }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 25%">
                    <table>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><b>To:</b></td>
                            <td>{{ $backloading_trip->finish_city }}</td>
                        </tr>
                        <tr>
                            <td><b>ETA:</b></td>
                            <td>{{ $backloading_trip->finish_date }}</td>
                        </tr>
                        <tr>
                            <th></th>
                            <td></td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%">
                    <table>
                        <thead>
                            <tr>
                                <td><b>Notes:</b></td>
                                <td>{{ $backloading_trip->notes }}</td>
                            </tr>
                        </thead>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div class="job_header">
        <table style="width: 100%;margin: 5px;">
            <th>
                <td style="width: 23%">
                    <label for="job" style="margin: 20px"># <span style="margin-left: 15px">Job #</span></label>
                </td>
                <td style="width: 20%">
                    <label for="Consignor">Consignor:</label>
                </td>
                <td style="width: 20%">
                    <label for="Consignee">Consignee:</label>
                </td>
                <td style="width: 40%">
                    <label for="Vol">Vol:</label>
                </td>
            </th>
        </table>
    </div>
    <div class="trip_jobs_detail">
        @php
            $count = 1;
            $total_volumn = 0;
        @endphp
        @if (count($jobs_detail))
            @foreach ($jobs_detail as $detail)
                <div class="single_job_detail">
                    <table style="width: 100%;">
                        <th>
                            <td style="width: 20%;vertical-align: top;padding-top: 10px;">
                                <label for="job">{{ $count }} <span style="margin-left: 15px">{{ $detail['job_number'] }}</span></label>
                            </td>
                            <td style="width: 20%;vertical-align: top;padding-top: 10px;padding-left: 10px" class="border-right border-left">
                                <p>{{ $detail['name'] }}<br/>
                                    {{ $detail['pickup_address'] }} <br/>
                                    <span>{{ $detail['mobile'] }}</span></p>
                            </td>
                            <td style="width: 20%;vertical-align: top;padding-top: 10px;padding-left: 10px" class="border-right">
                                <p>{{ $detail['name'] }}<br/>
                                    {{ $detail['drop_off_address'] }} <br/>
                                    <span>{{ $detail['mobile'] }}</span></p>
                            </td>
                            <td style="width: 40%">
                               <p id="cbm"> {{ $detail['total_cbm'] }}</p>
                               <p id="instructions"><b>Instructions:</b> {{ $detail['notes'] }}</p>
                            </td>
                        </th>
                    </table>
                </div>  
                @php
                    $count++;
                    $total_volumn += $detail['total_cbm'];
                @endphp
            @endforeach
        @endif
        <table style="width: 100%">
            <tr>
                <td style="text-align: right;padding-right: 16px;font-weight: bold;font-size: 12px;">
                    <label for="Total">Total Volume: <span>{{ $total_volumn }}</span></label>
                </td>
            </tr>
        </table>
    </div>

    <script type="text/php">
    if (isset($pdf)) {
        $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
        $size = 7;
        $font = $fontMetrics->getFont("Calibri");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width) / 2;
        $y = $pdf->get_height() - 35;
        $pdf->page_text($x, $y, $text, $font, $size);
    }
</script>

  </body>
</html>