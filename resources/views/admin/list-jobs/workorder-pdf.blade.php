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
          padding: 0px 2px!important;
        }
        .job_td_detail{
          padding: 0px 2px 3px 20px!important;
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
    <h1>Work Order</h1>

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
            <tr><td class="noborder no-bottom-padding inner-table-td "><b style="font-size:12px;">Pickup</b><td></tr>
            <tr>
              <td class="noborder no-bottom-padding inner-table-td "><b>Bedrooms:</b></td>
              <td class="noborder no-bottom-padding">{{ $job->pickup_bedrooms }}</td>
            </tr>
            <tr>
              <td class="noborder no-bottom-padding inner-table-td "><b>Property Type:</b></td>
              <td class="noborder no-bottom-padding">{{ $job->pickup_property_type }}</td>
            </tr>
            <tr>
              <td class="noborder no-bottom-padding inner-table-td "><b>Access:</b></td>
              <td class="noborder no-bottom-padding">{{ $job->pickup_access_restrictions }}</td>
            </tr>
          </table>
        </td>
        <td>
          <table>
            <tr><td class="noborder no-bottom-padding inner-table-td "><b style="font-size:12px;">Drop off</b><td></tr>
            <tr>
              <td class="noborder no-bottom-padding inner-table-td "><b>Bedrooms:</b></td>
              <td class="noborder no-bottom-padding">{{ $job->drop_off_bedrooms }}</td>
            </tr>
            <tr>
              <td class="noborder no-bottom-padding inner-table-td "><b>Property Type:</b></td>
              <td class="noborder no-bottom-padding">{{ $job->drop_off_property_type }}</td>
            </tr>
            <tr>
              <td class="noborder no-bottom-padding inner-table-td "><b>Access:</b></td>
              <td class="noborder no-bottom-padding">{{ $job->drop_off_access_restrictions }}</td>
            </tr>
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
                  <td class="noborder job_td_padding"><b>Vehicle:</b></td>
                  <td class="noborder job_td_detail">
                    {{ $vehicle_name }}
                  </td>
                </tr>
                <tr>
                  <td class="noborder job_td_padding"><b>Truck Size:</b></td>
                  <td class="noborder job_td_detail">
                    {{ $vehicle_payload }}
                  </td>
                </tr>
                <tr>
                  <td class="noborder job_td_padding"><b>Start Time:</b></td>
                  <td class="noborder job_td_detail">
                    {{ date('H:i',strtotime($start_time)) }}
                  </td>
                </tr>
                <tr>
                  <td class="noborder job_td_padding"><b>Price Structure:</b></td>
                  <td class="noborder job_td_detail">
                    @if($invoice_items)
                      {{ $invoice_items->item_name }}
                    @endif
                  </td>
                </tr>
                <tr>
                  <td class="noborder job_td_padding"><b>Rate:</b></td>
                  <td class="noborder job_td_detail">
                    @if($invoice_items)
                      {{ $global->currency_symbol.$invoice_items->unit_price }}
                    @endif
                  </td>
                </tr>
                <tr>
                  <td class="noborder job_td_padding"><b>Call out Fee:</b></td>
                  <td class="noborder job_td_detail">
                    @if($call_out_fee)
                      {{ $global->currency_symbol.$call_out_fee->unit_price }}
                    @endif
                  </td>
                </tr>
                <tr>
                  <td class="noborder job_td_padding"><b>Deposit Paid:</b></td>
                  <td class="noborder job_td_detail">
                    @if($payment_amount)
                      {{ $global->currency_symbol.$payment_amount->amount }}
                    @endif
                  </td>
                </tr>
                <tr>
                  <td class="noborder job_td_padding"><b>Payment Method:</b></td>
                  <td class="noborder job_td_detail">
                    @if($payment_amount)
                      {{ $payment_amount->gateway }}
                    @endif
                  </td>
                </tr>
                <tr>
                  <td class="noborder job_td_padding"><b>Payment Reference:</b></td>
                  <td class="noborder job_td_detail">
                    @if($payment_amount)
                      {{ $payment_amount->remarks }}
                    @endif
                  </td>
                </tr>
              </table>
          </td>
          <td>
            <table>
              <tr>
                <td class="noborder job_td_padding"><b>Number of Men:</b></td>
                <td class="noborder job_td_detail">
                    @if($count_offsiders)
                      {{ $count_offsiders }}
                    @endif
                </td>
              </tr>
              <tr>
                <td class="noborder job_td_padding"><b>Driver:</b></td>
                <td class="noborder job_td_detail">
                  @foreach($drivers as $d)
                    @if($d->id==$job_driver_id)
                        {{ $d->name }}
                        @break
                    @endif
                  @endforeach
                </td>
              </tr>
              <tr>
                <td class="noborder job_td_padding"><b>Offsiders:</b></td>
                <td class="noborder job_td_detail">
                  @if($offsiders)                           
                    @foreach($offsiders as $offsider)
                        @foreach($people as $p)
                            @if($p->id==$offsider->people_id)
                                {{ $p->name }},
                                @break
                            @endif
                        @endforeach
                    @endforeach
                  @endif
                </td>
              </tr>
              <tr>
                <td class="noborder job_td_padding"><b>Dispatch Notes:</b></td>
                <td class="noborder job_td_detail">
                  {{ $dispatch_notes }}
                </td>
              </tr>
            </table>
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
                  {!! $companies->work_order_instructions !!}
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