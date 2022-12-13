<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">    
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- <link rel="stylesheet" href="{{ URL::asset('css/pdf.css') }}"> --}}
    <link href="{{ asset('css/pdf.css') }}" rel="stylesheet" type="text/css">
  </head>
  <body>
    <div id="container">
      <div id="memo">
          @if($company_logo_exists==true)
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
                <span id="title">TAX INVOICE</span>
                <div class="separator"></div>
                <span id="number">#{{ $invoice->invoice_number .' - '. $invoice->inv_version }}</span>        
              </div>
        </td>
    </tr>
    <tr>
        <td style="width: 40%">
            <table id="invoice-info">
                <tr>
                    <td style="padding:2px 0;"><span style="font-weight:bold!important">Invoice detail:</span></td>
                    <td></td>
                </tr>
                <tr>
                    <td style="padding:2px 0"><span>Invoice Date</span></td>
                    <td style="padding:2px 0"><span>{{ isset($invoice->issue_date)? date('d/m/Y', strtotime($invoice->issue_date)):''}}</span></td>
                </tr>
                <tr>
                  <td style="padding:2px 0"><span>Job Date</span></td>
                  <td style="padding:2px 0"><span>{{ isset($job->job_date)? date('d/m/Y', strtotime($job->job_date)):''}}</span></td>
                </tr>
                <tr>
                    <td style="padding:2px 0"><span>Invoice No.</span></td>
                    <td style="padding:2px 0"><span>{{ isset($invoice->invoice_number)? ( $invoice->invoice_number .' - '. $invoice->inv_version):''}}</span></td>
                </tr>
                <tr>
                    <td style="padding:2px 0"><span>TOTAL INVOICE</span></td>
                    <td style="padding:2px 0"><strong>{{$organisation_settings->currency_symbol}}{{number_format((float)($invoice_total), 2, '.', ',')}}</strong></td>
                </tr>
              </table>
        </td>
        <td style="width: 10%"></td>
        <td style="width: 50%">
          <table id="invoice-info">
            <tr>
                <td style="padding:2px 0;"><span style="font-weight:bold!important">Invoice to:</span></td>
            </tr>
            <tr>
                <td style="padding:2px 0"><span class="client-name">{{ $crm_leads->name }}</span></td>
            </tr>
            <tr>
              <td style="padding:2px 0"><span><a href="mailto:{{ ($crm_contact_email)? $crm_contact_email->detail:''}}">{{ ($crm_contact_email)? $crm_contact_email->detail:''}}</a></span></td>
            </tr>
            <tr>
                <td style="padding:2px 0">
                  @if($invoice->sys_job_type=="Moving")
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
    </table>
    <div id="items">
        
                <table cellpadding="0" cellspacing="0" style="table-layout:fixed">
                  <tr>
                    <th style="width:10%"></th>
                    <th style="width:35%">Item Description</th>
                    <th style="width:15%">Quantity</th>
                    <th style="width:20%">Unit Price</th>
                    <th style="width:20%">Line Total</th>
                  </tr>
                  @if($invoice_items)
                        @foreach ($invoice_items as $qitm)
                    <tr>
                        <td style="text-align: left;">{{ ++$count }}</td>
                        <td><span>{{ isset($qitm->item_name)? $qitm->item_name:''}} <br/>
                          {!! isset($qitm->item_summary)?html_entity_decode(nl2br($qitm->item_summary)):'' !!}
                        </span></td>
                        <td><span>{{ isset($qitm->quantity)? $qitm->quantity:''}}</span></td>
                        <td><span>{{$organisation_settings->currency_symbol}}{{ isset($qitm->unit_price)? number_format((float)($qitm->unit_price), 2, '.', ''):''}}</span></td>
                        <td><span>{{$organisation_settings->currency_symbol}}{{ number_format((float)($qitm->quantity*$qitm->unit_price), 2, '.', '') }}</span></td>                
                    </tr>
                  @endforeach
                @endif
                  
                </table>
                
              </div>
    <table>
    <tr>
        <td>
            <div id="sums">
      
                <table cellpadding="0" cellspacing="0">
                  <tr>
                    <th>Sub Total</th>
                    <td>{{$organisation_settings->currency_symbol}} {{ number_format((float)($sub_total), 2, '.', ',') }}</td>
                  </tr>
                  @if($invoice->discount>0)
                  <tr>
                    <th>Discount                       
                        {{-- {{ ucfirst($invoice->discount_type) }} --}}
                    </th>
                    <td>
                      @if($invoice->discount_type=="percent")
                        {{ $invoice->discount.'%' }}
                      @else
                          {{$organisation_settings->currency_symbol}}{{ number_format((float)$invoice->discount, 2, '.', '') }}
                      @endif
                    </td>
                  </tr>

                  <tr>
                    <th>Sub Total After Discount</th>
                    <td>{{$organisation_settings->currency_symbol}} {{ number_format((float)($sub_total_after_discount), 2, '.', ',') }}</td>
                  </tr>
                  @endif

                  @if($taxs)
                  <tr>
                    <th>{{ $taxs->tax_name}} {{ floatval($taxs->rate_percent)}}%</th>
                    <td>{{$organisation_settings->currency_symbol}} {{ number_format((float)($tax_total), 2, '.', ',') }}</td>
                  </tr>
                  @endif
                  
                  <tr class="amount-total">
                    <th>Total <span style="text-transform: none;font-size: 12px">incl GST</span></th>
                    <td>{{$organisation_settings->currency_symbol}} {{ number_format((float)($invoice_total), 2, '.', ',') }}</td>
                  </tr>   
                  <tr data-hide-on-quote="true">
                    <th>Paid To Date</th>
                    <td>{{$organisation_settings->currency_symbol}} {{ number_format((float)($total_paid), 2, '.', ',') }}</td>
                  </tr>  
                  <tr data-hide-on-quote="true">
                    <th>Balance Payment</th>
                    <td>{{$organisation_settings->currency_symbol}} {{ number_format((float)($balance_payment), 2, '.', ',') }}</td>
                  </tr> 
                  
                  <tr>
                    @if($balance_payment>0 && $stripe_connected==1)
                        <div>
                            <a href="{{ $url_link }}" class="book_now_btn">PAY NOW</a>
                        </div>
                    @endif
                  </tr>
                  
                </table>
                <br/>
                
                
                
             </div>
        </td>
    </tr>
    <tr>
      <td>
          <div id="terms" style="margin-top: 290px;">   
               @if($invoice_settings)
                  {!! $invoice_settings->invoice_terms !!}
              @endif
          </div> 
      </td>
  </tr>
    
</table>   

    </div>

  </body>
</html>