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
              <div>ABN: {{ $companies->abn}}</div>
              <div>{{ $companies->address}}</div>
              <div>Phone: {{ $companies->phone}}</div>
              <div><a href="mailto:{{ $companies->email}}">Email: {{ $companies->email}}</a></div>
            </div>
          </td>
        </tr>
      </table>
    </header>
    <h1>Inventory List</h1>

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
                <td><b>Job No:</b></td>
                <td><b>{{ isset($job->job_number)? $job->job_number : ''}}</b></td>
              </tr>
              <tr>
                <td><b>Job Date:</b></td>
                <td><b>{{ isset($job->job_date)? date('d/m/Y', strtotime($job->job_date)):''}}</b></td>
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
    
    <main style="margin-top: 10px;">
      <table>
        <thead>
          <tr>
            <th class="desc">Inventory Details</th>
            <th>Qty</th>
            @if($pricingAdditional->inventory_pdf_show_cbm=='Y')<th>Total CBM</th>@endif;
          </tr>
        </thead>
        <tbody>
          @if($inventory_items)
            <?php
                $total_items = 0;
                $total_cbm = 0;
              ?>
            @foreach ($inventory_items as $item)
            <?php
              if($item->misc_item=='Y'){
                $item_cbm = $item->misc_item_cbm*$item->quantity;
              }else{
                $item_cbm = $item->cbm*$item->quantity;
              }
              $total_items += $item->quantity;
              $total_cbm += $item_cbm;
            ?>
              <tr>
                @if ($item->item_name != null)
                  <td class="desc" style="padding: 10px;">
                    <b>{{  isset($item->item_name)? $item->item_name:'' }}</b> <br/>
                  </td>
                @else
                  <td class="desc" style="padding: 10px;">
                    <b>{{  isset($item->misc_item_name)? $item->misc_item_name:'' }}</b> <br/>
                  </td>
                @endif
                <td class="qty">{{ isset($item->quantity)? number_format((float)($item->quantity), 2, '.', ''):''}}</td>
                @if($pricingAdditional->inventory_pdf_show_cbm=='Y')<td>{{ number_format((float)($item_cbm), 2, '.', '') }}</td>@endif;
              </tr>
            @endforeach
          @endif
        </tbody>
        <tfoot>
            <tr>
                <td style="text-align:left;">
                  Total Items 
                </td>
                <td>{{ number_format((float)($total_items), 2, '.', '') }}</td>
                @if($pricingAdditional->inventory_pdf_show_cbm=='Y')<td>{{ number_format((float)($total_cbm), 2, '.', '') }}</td>@endif
            </tr>
        </tfoot>
      </table>
    </main>
    
    <footer>
        <table>
          <tbody>
            <tr>
              <td>
                @if($crm_leads->lead_type=="Residential")
    
                @else
                  @if($customer_detail)
                    {!! $customer_detail->payment_instructions !!}
                  @endif
                @endif
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