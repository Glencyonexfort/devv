<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">    
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.ico') }}"/>

    <link rel="stylesheet" href="{{ URL::asset('css/pdf.css') }}">
    {{-- <link href="{{ asset('css/pdf.css') }}" rel="stylesheet" type="text/css"> --}}
  </head>
  <body>
    <header class="clearfix">
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
              <div><b>ABN:</b> {{ $companies->abn}}</div>
              <div>{{ $companies->address}}</div>
              <div><b>Phone:</b> {{ $companies->phone}}</div>
              <div><b>Email:</b> <a href="mailto:{{ $companies->email}}">{{ $companies->email}}</a></div>
            </div>
          </td>
        </tr>
      </table>
    </header>
    <h1>Estimate</h1>

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
          </td>
          <td style="padding-top: 0px;">
            <table>
              <tr>
                <td><b>Estimate No:</b></td>
                <td><b>{{ $quote->quote_number }}</b></td>
              </tr>
              <tr>
                <td><b>Job Date:</b></td>
                <td><b>{{ isset($job->job_date)? date('d/m/Y', strtotime($job->job_date)):''}}</b></td>
              </tr>
              <tr>
                <td><b>Estimate Start Time:</b></td>
                <td><b>{{ isset($job->job_start_time)? date('h:i A', strtotime($job->job_start_time)):''}}</b></td>
              </tr>
              @if($crm_leads->lead_type == "Commercial")
                <tr>
                  <td><b>Account No:</b></td>
                  <td><b>{{$customer_detail->account_number}}</b></td>
                </tr>
              @endif
              <tr>
                <td><b>Date:</b></td>
                <td><b>{{ isset($quote->quote_date)? date('d/m/Y', strtotime($quote->quote_date)):''}}</b></td>
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
                <td class="noborder" style="padding-top:4px"><b>From:</b></td>
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
              <td class="noborder" style="padding-top:4px"><b>To:</b></td>
              <td class="noborder pl-20">
                {!! ($job->drop_off_address == '')?'':$job->drop_off_address.'<br/> ' !!}
                {!! ($job->delivery_suburb == '')?'':$job->delivery_suburb.'<br/> ' !!} {{ $job->drop_off_post_code }}
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </tbody>
    </table>
    </div>
    
    <main>
      <table>
        <thead>
          <tr>
            <th class="desc">Estimate Details</th>
            <th>Qty</th>
            <th>Rate</th>
            <th>Total (Ex)</th>
            <th>Tax</th>
            <th>Total (Inc)</th>
          </tr>
        </thead>
        <tbody>
          @if($quoteItems)
            <?php
                $grand_total_inc = 0;
                $grand_total_ex = 0;
                $grand_total_tax = 0;
              ?>
            @foreach ($quoteItems as $qitm)
            <?php
                $line_total_inc = $qitm->amount;
                $line_total_ex = $qitm->quantity*$qitm->unit_price;
                $line_total_tax = $line_total_inc-$line_total_ex;

                $grand_total_inc += $line_total_inc;
                $grand_total_ex += $line_total_ex;
                $grand_total_tax += $line_total_tax;
                // dd($qitm);
            ?>
              <tr>
                <td class="desc" style="padding-top:0px;padding-bottom:0px">
                  <b>{{  isset($qitm->name)? $qitm->name:'' }}</b> <br/>
                  <span class="pl-20" style="display: block;"> {!! isset($qitm->description)?html_entity_decode(nl2br($qitm->description)):'' !!} </span>
                </td>
                <td class="qty">{{ isset($qitm->quantity)? number_format((float)($qitm->quantity), 2, '.', ''):''}}</td>
                <td class="unit">{{ isset($qitm->unit_price)? number_format((float)($qitm->unit_price), 2, '.', ''):''}}</td>
                <td class="total">{{ number_format((float)$line_total_ex, 2, '.', '') }}</td>
                <td class="tax">{{ number_format((float)$line_total_tax, 2, '.', '') }}</td>
                <td class="total">{{ number_format((float)$line_total_inc, 2, '.', '') }}</td>
              </tr>
            @endforeach
          @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align:left;">
                  Estimate Total 
                <span style="float: right;">{{ $global->currency_code }}</span>
                </td>
                <td>{{ number_format((float)$grand_total_ex, 2, '.', ',') }}</td>
                <td>{{ number_format((float)$grand_total_tax, 2, '.', ',') }}</td>
                <td>{{ number_format((float)$grand_total_inc, 2, '.', ',') }}</td>
            </tr>
        </tfoot>
      </table>
    </main>
    
    <footer>
        <table>
          <tbody>
            <tr>
              <td>
                <table>
                  <tbody>
                    <tr>
                      <td>Total Tax</td>
                      <td>{{ number_format((float)$grand_total_tax, 2, '.', ',') }}</td>
                    </tr>
                    @if($quote->discount > 0)
                    <tr>
                      <td>Discount</td>
                      <td>
                          <?php
                            if($quote->discount_type=="percent"){
                                $grand_total_inc = $grand_total_inc - ($quote->discount / 100 * $grand_total_inc);
                                echo $quote->discount.'%';
                            }else{
                                $grand_total_inc = $grand_total_inc - $quote->discount;
                                echo number_format((float)$quote->discount, 2, '.', '');
                            }
                          ?>
                      </td>
                    </tr>
                    @endif
                    <tr>
                      <td>Total</td>
                      <td>{{ number_format((float)($grand_total_inc), 2, '.', ',') }}</td>
                    </tr>
                    <tr>
                      <td style="font-size: 14px;">Deposit Required</td>
                      <td>{{ number_format((float)($deposit_required), 2, '.', ',') }}</td>
                    </tr>
                    <tr>
                    @if($stripe_connected==1 && $deposit_required>0)
                      <div>
                          <a href="{{ $url_link }}" class="book_now_btn">BOOK NOW</a>
                      </div>
                    @endif
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          </tbody>
        </table>
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