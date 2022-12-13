<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">    
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.ico') }}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/pdf.css') }}">
    {{-- <link href="{{ asset('css/pdf.css') }}" rel="stylesheet" type="text/css"> --}}
    <style>
        .job_td_padding{
          padding: 3px 2px!important;
        }
        .job_td_detail{
          padding: 3px 2px 3px 20px!important;
        }
        .ptop4{
          padding-top: 4px!important;
        }
    </style>
  </head>
  <body>
    <header class="clearfix">
      @if($company_logo_exists==true)
            <div id="logo">
                <img src="{{ request()->getSchemeAndHttpHost().'/user-uploads/company-logo/'.$companies->logo }}">
            </div>
        @endif
      <div id="company" class="clearfix">
        <div><b>{{ $companies->company_name}}</b></div>
        <div>ABN: {{ $companies->abn}}</div>
        <div>{{ $companies->address}}</div>
        <div>Phone: {{ $companies->phone}}</div>
        <div><a href="mailto:{{ $companies->email}}">{{ $companies->email}}</a></div>
      </div>
    </header>
    <h1>Proof of Delivery</h1>

    <div id="customer_detail">
      <table>
        <tbody>
        <tr>
          <td style="padding-left: 2rem;">
            <div><b>{{ $crm_leads->name }}</b></div>
            @if($customer_detail)
              <div><b>{{ $customer_detail->billing_address }}</b></div>
              <div><b>{{ $customer_detail->billing_suburb }}</b></div>
              <div><b>{{ $customer_detail->billing_post_code }}</b></div>
            @endif
            <div><b><a href="mailto:{{ $crm_contact_email }}">{{  $crm_contact_email }}</a></b></div>
            <div><b>{{ $crm_contact_phone }}</b></div>
          </td>
          <td style="padding-top: 0px;">
            <table>
              <tr>
                <td><b>Job No:</b></td>
                <td><b>{{ $job->job_number }}</b></td>
              </tr>
              <tr>
                <td><b>Job Date:</b></td>
                <td><b>{{ isset($job->job_date)? date('d/m/Y', strtotime($job->job_date)):''}}</b></td>
              </tr>
                @if($customer_detail)
                <tr>
                  <td><b>Account No:</b></td>
                  <td><b>{{$customer_detail->account_number}}</b></td>
                 </tr>
                @endif
            </table>
          </td>
        </tr>
      </tbody>
      </table>
    </div>


    <div id="job_detail">
    <table>
    <tbody>
      <tr>
        <td>
            <table>
              <tr>
                <td class="noborder ptop4"><b>From:</b></td>
                <td class="noborder pl-20">
                  {!! ($job->pickup_address == '')?'':$job->pickup_address.'<br/> ' !!}
                  {!! ($job->pickup_suburb == '')?'':$job->pickup_suburb.'<br/> ' !!} {{ $job->pickup_post_code }}
                </td>
              </tr>
            </table>
        </td>
        <td>
          <table>
            <tr>
              <td class="noborder ptop4"><b>To:</b></td>
              <td class="noborder pl-20">
                {!! ($job->drop_off_address == '')?'':$job->drop_off_address.'<br/> ' !!}
                {!! ($job->delivery_suburb == '')?'':$job->delivery_suburb.'<br/> ' !!} {{ $job->drop_off_post_code }}
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          <table>
            <tr>
              <td class="noborder no-bottom-padding inner-table-td ptop4"><b>Bedrooms:</b></td>
              <td class="noborder no-bottom-padding">{{ $job->pickup_bedrooms }}</td>
            </tr>
            <tr>
              <td class="noborder no-bottom-padding inner-table-td ptop4"><b>Property Type:</b></td>
              <td class="noborder no-bottom-padding">{{ $job->pickup_property_type }}</td>
            </tr>
            <tr>
              <td class="noborder no-bottom-padding inner-table-td ptop4"><b>Access:</b></td>
              <td class="noborder no-bottom-padding">{{ $job->pickup_access_restrictions }}</td>
            </tr>
          </table>
        </td>
        <td>
          <table>
            <tr>
              <td class="noborder no-bottom-padding inner-table-td ptop4"><b>Bedrooms:</b></td>
              <td class="noborder no-bottom-padding">{{ $job->drop_off_bedrooms }}</td>
            </tr>
            <tr>
              <td class="noborder no-bottom-padding inner-table-td ptop4"><b>Property Type:</b></td>
              <td class="noborder no-bottom-padding">{{ $job->drop_off_property_type }}</td>
            </tr>
            <tr>
              <td class="noborder no-bottom-padding inner-table-td ptop4"><b>Access:</b></td>
              <td class="noborder no-bottom-padding">{{ $job->drop_off_access_restrictions }}</td>
            </tr>
          </table>
        </td>
      </tr>
    </tbody>
    </table>
    </div>

    <div id="job_sign">
      <table>
        <tbody>
          <tr>
            <td>
                @if($is_customer_signature==1)
                <img src="{{$customer_sign}}" style="max-height:150px;margin: 0 auto;"/>
                @endif
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <main class="mt-3">
      <table>
        <tbody>
              <tr>
                <td class="terms">
                  {!! $companies->pod_instructions !!}
                </td>
              </tr>
        </tbody>
      </table>
    </main>
    
    <footer>
        <div id="pageFooter">
        <table style="width: 100%">
        <tr>
          <td style="text-align: left;">{{ $companies->company_name }}</td>
          <td style="text-align: right;font-weight:normal">Generated Date: <?=date("d/m/Y h:i a")?></td>
        </tr>
      </table>
    </div>
    </footer>
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