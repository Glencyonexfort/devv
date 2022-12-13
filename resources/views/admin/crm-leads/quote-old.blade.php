
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">    
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ URL::asset('css/pdf-estimate.css') }}">
  </head>
  <body>
    <div id="container">
      <div id="memo">
      @if($company_logo_exists)
        <div class="logo">            
            <img src="{{ request()->getSchemeAndHttpHost().'/user-uploads/company-logo/'.$companies->logo }}">            
        </div>
        @endif

        <div class="company-info">
          <span>{{ $companies->company_name}}</span>

          <div class="separator less"></div>

          <span>{{ $companies->address }}</span>

          <br>
          <span><a href="mailto:{{ $companies->email}}">{{ $companies->email}}</a></span>
          <span>{{ $companies->phone}}</span>
          <span>ABN: {{ $companies->abn}}</span>
        </div>
      </div>
<table>    
    <tr>
        <td>
            <div id="invoice-title-number">      
                <span id="title">ESTIMATE</span>
                <div class="separator"></div>
                <span id="number">#{{ $quote->quote_number }}</span>        
              </div>
        </td>
    </tr>
    <tr>
        <td style="width: 40%">
            <table id="invoice-info">
                <tr>
                    <td style="padding:2px 0;"><span style="font-weight:bold!important">Estimate detail:</span></td>
                    <td></td>
                </tr>
                <tr>
                    <td style="padding:2px 0"><span>Estimate Date</span></td>
                    <td style="padding:2px 0"><span>{{ isset($quote->quote_date)? date('d/m/Y', strtotime($quote->quote_date)):''}}</span></td>
                </tr>
                <tr>
                  <td style="padding:2px 0"><span>Job Date</span></td>
                  <td style="padding:2px 0"><span>{{ isset($job->job_date)? date('d/m/Y', strtotime($job->job_date)):''}}</span></td>
                </tr>
                <tr>
                    <td style="padding:2px 0"><span>Estimate No.</span></td>
                    <td style="padding:2px 0"><span>{{ isset($quote->quote_number)? $quote->quote_number:''}}</span></td>
                </tr>
                <tr>
                    <td style="padding:2px 0"><span>TOTAL ESTIMATE</span></td>
                    <td style="padding:2px 0"><strong>{{$organisation_settings->currency_symbol}}{{$quote_total}}</strong></td>
                </tr>
              </table>
        </td>
        <td style="width: 10%"></td>
        <td style="width: 50%">
          <table id="invoice-info">
            <tr>
                <td style="padding:2px 0;"><span style="font-weight:bold!important">Estimate to:</span></td>
            </tr>
            <tr>
                <td style="padding:2px 0"><span class="client-name">{{ $crm_leads->name }}</span></td>
            </tr>
            @if($crm_contact_phone)
                <div>
                    <span style="padding:6px 0;">{{ $crm_contact_phone->detail }}</span>
                </div>
            @endif
            <tr>
                <td style="padding:2px 0">
                  @if($quote->sys_job_type=="Moving")
                    <span>
                      {{ ($job->pickup_address == '')?'':$job->pickup_address.', ' }} {{ ($job->pickup_suburb == '')?'':$job->pickup_suburb.', ' }}
                      {{ ($job->pickup_state == '')?'':$job->pickup_state.', ' }} {{ ($job->pickup_postcode == '')?'':$job->pickup_postcode.', ' }}
                    </span>
                  @else
                    <span>{{ $job->address }}</span>
                    <table>
                      <tr>
                        <td>{{ 'Bedrooms:' }}</td><td>{{ $job->bedrooms }}</td>
                        <td>{{ 'Bathrooms:' }}</td><td>{{ $job->bathrooms }}</td>
                      </tr>
                      <tr>
                        <td>{{ 'Carpet:' }}</td><td>{{ $job->carpeted }}</td>
                        <td>{{ 'Stories:' }}</td><td>{{ $job->stories }}</td>
                      </tr>
                    </table>
                  @endif

                </td>
            </tr>
          </table>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <div id="items">
        
                <table cellpadding="0" cellspacing="0" style="table-layout:fixed">
                  <tr>
                    <th style="width:5%"></th>
                    <th style="width:35%">Item Description</th>
                    <th style="width:20%">Quantity</th>
                    <th style="width:20%">Unit Price</th>
                    <th style="width:20%">Line Total</th>
                  </tr>
                  @if($quoteItems)
                        @foreach ($quoteItems as $qitm)
                    <tr>
                        <td>{{ ++$count }}</td>
                        <td><span>{{ isset($qitm->name)? $qitm->name:''}} <br/>
                          {!! isset($qitm->description)?html_entity_decode(nl2br($qitm->description)):'' !!}
                        </span></td>
                        <td><span>{{ isset($qitm->quantity)? $qitm->quantity:''}}</span></td>
                        <td><span>{{$organisation_settings->currency_symbol}}{{ isset($qitm->unit_price)? number_format((float)($qitm->unit_price), 2, '.', ''):''}}</span></td>
                        <td><span>{{$organisation_settings->currency_symbol}}{{ number_format((float)($qitm->amount), 2, '.', '') }}</span></td>                
                    </tr>
                  @endforeach
                @endif
                  
                </table>
                
              </div>  
        </td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>
            <div id="sums">
      
                <table cellpadding="0" cellspacing="0">
                  <tr>
                    <th>Sub Total</th>
                    <td>{{$organisation_settings->currency_symbol}} {{ number_format((float)($sub_total), 2, '.', '') }}</td>
                  </tr>
                  @if($quote->discount>0)
                  <tr>
                    <th>Discount                       
                        {{-- {{ ucfirst($quote->discount_type) }} --}}
                    </th>
                    <td>
                      @if($quote->discount_type=="percent")
                        {{ $quote->discount.'%' }}
                      @else
                          {{$organisation_settings->currency_symbol}}{{ number_format((float)$quote->discount, 2, '.', '') }}
                      @endif
                    </td>
                  </tr>

                  <tr>
                    <th>Sub Total After Discount</th>
                    <td>{{$organisation_settings->currency_symbol}} {{ number_format((float)($sub_total_after_discount), 2, '.', '') }}</td>
                  </tr>
                  @else
                  <?php
                    $sub_total_after_discount = $sub_total;
                  ?>

                  @endif                  
                  
                  <tr data-iterate="tax">
                    <th>{{ $taxs->tax_name}} {{ floatval($taxs->rate_percent)}}%</th>
                    <td>{{$organisation_settings->currency_symbol}} {{ number_format((float)($total_tax), 2, '.', '') }}</td>
                  </tr>
                  @if($show_estimate_range==1)
                  <?php
                    $lower_value = $estimate_lower_percent*$quote_total;
                  ?>
                  <tr class="amount-total">
                    <th>Estimated Price Range</th>
                    <td>{{$organisation_settings->currency_symbol}} {{ number_format((float)($lower_value), 2, '.', '') }} to <br/> {{$organisation_settings->currency_symbol}} {{ number_format((float)($quote_total), 2, '.', '') }}</td>
                  </tr>
                  @else
                    <tr class="amount-total">
                      <th>Total <span style="text-transform: none;font-size: 12px">incl GST</span></th>
                      <td>{{$organisation_settings->currency_symbol}} {{ number_format((float)($quote_total), 2, '.', '') }}</td>
                    </tr>
                  @endif   

                  @if($is_booking_fee==1)
                  <tr data-hide-on-quote="true">
                    <th>Booking Fee</th>
                    <td>{{$organisation_settings->currency_symbol}} {{ number_format((float)($booking_fee), 2, '.', '')}}</td>
                  </tr>
                  @elseif($is_booking_fee<0)
                  {{-- It is a Cleaning Job --}}
                  @else
                  <tr data-hide-on-quote="true">
                    <th>Deposit Required</th>
                    <td>{{$organisation_settings->currency_symbol}} {{ number_format((float)($deposit_required), 2, '.', '')}}</td>
                  </tr>
                  @endif
                  
                </table>
                <br/>
                @if($stripe_connected==1)
                <div>
                    <a href="{{ $url_link }}" class="book_now_btn">BOOK NOW</a>
                </div>
                @endif
              </div>
        </td>
    </tr>
    {{-- <tr>
        <td colspan="3">
            <div id="terms">      
        <div style="color:#333">TERMS:</div>
        <div>Payment should be made within 30 days by Western union money transfer or phone us</div>
        
      </div> 
        </td>
    </tr> --}}
    
</table>   

    </div>

  </body>
</html>
