<?php

namespace App;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class CRMLeads extends Model
{
    use Notifiable;
    protected $table = 'crm_leads';

    protected $fillable = [
        'tenant_id','name', 'description', 'lead_status','created_by','updated_by'
    ];

    public function opportunities()
    {
        return $this->hasMany(CRMOpportunities::class, 'lead_id', 'id');
    }

    public function tasks()
    {
        return $this->hasMany(CRMTasks::class, 'lead_id', 'id');
    }

    public function contacts()
    {
        return $this->hasMany(CRMContacts::class, 'lead_id', 'id');
    }

    public function generateQuote($opportunity_id, $global){

        try{
        $this->opportunity = CRMOpportunities::where('id', '=', $opportunity_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        if($this->opportunity){
            $this->taxs = Tax::where(['tenant_id' => auth()->user()->tenant_id])->first();
            $this->sub_total = 0;
            $this->quote_total = 0;
            $this->total_tax = 0;
            $this->deposit_required = 0;
            $this->booking_fee = 0;
            $this->count = 0;
            $this->show_estimate_range=0;
            $this->estimate_lower_percent=0;
            $this->stripe_connected=0;
            $this->sub_total_after_discount=0;

            $stripe = TenantApiDetail::where('tenant_id', auth()->user()->tenant_id)
                    ->where('provider', 'Stripe')->first();
            if($stripe){
                if(isset($stripe->account_key) && !empty($stripe->account_key)){
                    $this->stripe_connected=1;
                }
            }

            // Job Moving Price for the tenant-------------------//
            $job_price_additional = DB::table('jobs_moving_pricing_additional as t1')
                ->select('t1.*')
                ->where(['t1.tenant_id' => auth()->user()->tenant_id])
                ->first();
            if($this->opportunity->op_type=="Moving"){
                $this->job = JobsMoving::where('crm_opportunity_id', '=', $this->opportunity->id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            }elseif($this->opportunity->op_type=="Cleaning"){
                $jobs_cleaning_auto_quoting = DB::table('jobs_cleaning_auto_quoting as t1')
                ->select('t1.*')
                ->where(['t1.tenant_id' => auth()->user()->tenant_id])
                ->first();
                $this->job = JobsCleaning::where('crm_opportunity_id', '=', $this->opportunity->id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            }
            $this->quote = Quotes::where('crm_opportunity_id', '=', $this->opportunity->id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
            if ($this->quote) {
                $this->quoteItems = QuoteItem::where('quote_id', '=', $this->quote->id)->get();

                $sub_total = QuoteItem::select(DB::raw('sum(quote_items.unit_price * quote_items.quantity) as total'))
                    ->where('quote_items.quote_id', '=', $this->quote->id)->first();
                $this->sub_total = $sub_total->total;                

                // if ($this->quoteItems) {
                //     foreach ($this->quoteItems as $qitm) {
                //         $subtotal = floatval($qitm->amount);
                //         $this->quote_total += $subtotal;
                //         if (isset($this->taxs->rate_percent) && floatval($qitm->amount) > 0)
                //             $this->tax_total += floatval($this->taxs->rate_percent) * ((floatval($subtotal)) / 100);
                //     }
                // }

                if($this->taxs){
                    $rate_percent=$this->taxs->rate_percent;
                }
                if($this->quote->discount_type=="percent"){
                    $this->sub_total_after_discount = $this->sub_total - ($this->quote->discount/100 * $this->sub_total);
                }else{
                    $this->sub_total_after_discount = $this->sub_total - $this->quote->discount;
                }
                $this->total_tax = ($rate_percent * $this->sub_total_after_discount)/100;
                $this->quote_total = $this->total_tax + $this->sub_total_after_discount; 

            }
            if($this->quote->deposit_required>0 && $this->quote->deposit=='Y'){
                $this->deposit_required = $this->quote->deposit_required;
            }else{
                if($this->quote->deposit=='N'){
                    $this->deposit_required = 0;
                }else{
                    if($this->opportunity->op_type=="Moving"){
                        if ($this->job->price_structure == 'Fixed') {
                            if ($job_price_additional->is_deposit_for_fixed_pricing_fixed_amt == 'Y') {
                                $this->deposit_required = $job_price_additional->deposit_amount_fixed_pricing;
                            } else {
                                $this->deposit_required = $job_price_additional->deposit_percent_fixed_pricing * $this->quote_total;
                            }
                        }else {
                            if($job_price_additional->hourly_pricing_has_booking_fee=='Y'){
                                $this->booking_fee = $job_price_additional->hourly_pricing_booking_fee;
                            }else{
                                if ($job_price_additional->is_deposit_for_hourly_pricing_fixed_amt == 'Y') {
                                    $this->deposit_required = $job_price_additional->deposit_amount_hourly_pricing;
                                } else {
                                    $this->deposit_required = $job_price_additional->deposit_percent_hourly_pricing * $this->quote_total;
                                }
                            }
                    }
                }elseif($this->opportunity->op_type=="Cleaning"){
                    $this->deposit_required = $jobs_cleaning_auto_quoting->deposit_amount;
                }   
            } 
        }
            //Show Estimate Range 
            if($job_price_additional->hourly_pricing_min_pricing_percent>0){
                $this->show_estimate_range=1;
                $this->estimate_lower_percent=$job_price_additional->hourly_pricing_min_pricing_percent;
            }
            //--

            // following line removed in FORT-34 
            // $this->deposit_required = (floatval($this->grand_total) / 100) * 25;

            if ($this->job) {
                $this->companies = Companies::where('id', '=', $this->job->company_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
                // dd($this->companies);
            }
            $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', auth()->user()->tenant_id)->first();
            $this->crm_leads = CRMLeads::where('id', '=', $this->opportunity->lead_id)->first();
            $this->crm_contacts = CRMContacts::where('lead_id', '=', $this->opportunity->lead_id)->first();
            $this->crm_contact_email = CRMContactDetail::where('contact_id', '=', $this->crm_contacts->id)->where('detail_type', '=', 'Email')->first();
            $this->crm_contact_phone = CRMContactDetail::where('contact_id', '=', $this->crm_contacts->id)->where('detail_type', '=', 'Mobile')->first();
            $this->company_logo_exists = false;
            
            //Book now url
            if($this->opportunity->op_type=="Moving"){
                if($this->job->price_structure=='Hourly' && $job_price_additional->hourly_pricing_has_booking_fee=='Y'){
                    $this->url_params = base64_encode('quote_id=' . $this->quote->id . '&booking_fee=' . $this->booking_fee);
                    $this->url_link = request()->getSchemeAndHttpHost() . '/pay-now-booking-fee/' . $this->url_params;
                    $is_booking_fee=1;
                }else{
                    $this->url_params = base64_encode('quote_id=' . $this->quote->id . '&deposit_required=' . $this->deposit_required);
                    $this->url_link = request()->getSchemeAndHttpHost() . '/pay-now/' . $this->url_params;
                    $is_booking_fee=0;
                }
            }elseif($this->opportunity->op_type=="Cleaning"){
                $this->url_params = base64_encode('quote_id=' . $this->quote->id . '&deposit_required=' . $this->deposit_required);
                $this->url_link = request()->getSchemeAndHttpHost() . '/pay-now/' . $this->url_params;
                $is_booking_fee=0;
            }
            $filename = time();
            if (isset($this->companies)) {

                $file_number = 1;
                if (!empty($this->quote->quote_file_name)) {
                    $filename = str_replace('.pdf', '', $this->quote->quote_file_name);
                    $fn_ary = explode('_', $filename);
                    $file_number = intval($fn_ary[2]) + 1;
                }

                if (File::exists(public_path() . '/user-uploads/company-logo/' . $this->companies->logo)) {
                    $this->company_logo_exists = true;
                }

                //return view('admin.crm-leads.quote', $this->data);
                $company_name = self::cleanString($this->companies->company_name);

                $filename = 'Estimate_' . $company_name . '_'  . $this->quote->quote_number.'_'.$file_number. '.pdf';

                if (File::exists(public_path() . '/quote-files/' . $filename)) {
                    File::delete(public_path() . '/quote-files/' . $filename);
                }

                $this->customer_detail = CustomerDetails::where('customer_id', '=', $this->job->customer_id)->first();
                $this->invoice_settings = InvoiceSetting::where('tenant_id', auth()->user()->tenant_id)->first();

                $pdf = app('dompdf.wrapper');
                $pdf->loadView('admin.crm-leads.quote', 
                [
                    'global'=>$global,
                    'organisation_settings'=>$this->organisation_settings,
                    'companies'=>$this->companies,
                    'invoice_settings'=>$this->invoice_settings,
                    'settings'=>$this->settings,
                    'company_logo_exists'=>$this->company_logo_exists,
                    'count'=>0,
                    'crm_contact_phone'=>$this->crm_contact_phone,
                    'crm_contact_email'=>$this->crm_contact_email,
                    'crm_contacts'=>$this->crm_contacts,
                    'crm_leads'=>$this->crm_leads,
                    'job'=>$this->job,
                    'quote'=>$this->quote,
                    'quoteItems'=>$this->quoteItems,
                    'taxs'=>$this->taxs,
                    'deposit_required'=>$this->deposit_required,
                    'sub_total'=>$this->sub_total,
                    'total_tax'=>$this->total_tax,
                    'quote_total'=>$this->quote_total,
                    'sub_total_after_discount'=>$this->sub_total_after_discount,
                    'url_link'=>$this->url_link,
                    'is_booking_fee'=>$is_booking_fee,
                    'booking_fee'=>$this->booking_fee,
                    'show_estimate_range' => $this->show_estimate_range,
                    'estimate_lower_percent' => $this->estimate_lower_percent,
                    'stripe_connected' => $this->stripe_connected,
                    'customer_detail' => $this->customer_detail,
                    'balance_payment' => $this->balance_payment

                ]);
                // return $pdf->stream(); // to view pdf
                // return $pdf->download('tmp.pdf');
                $pdf->save(public_path().'/quote-files/' . $filename);                                

                $this->quote->quote_file_name = $filename;
                $this->quote->save();
                $response['error'] = 0;
                $response['message'] = 'Estimate PDF generated successfully.';
                return $response;
        }
    }
        } catch (Exception $ex) {
            $response['error'] = 1;
            $response['message'] = $ex->getMessage();
            return $response;
        }
    }

    public function generateInsuranceQuote($opportunity_id,$tenant_id){
        try{
            $opportunity = CRMOpportunities::where('id', '=', $opportunity_id)->where('tenant_id', '=', $tenant_id)->first();
            $job = JobsMoving::where('crm_opportunity_id', '=', $opportunity->id)->where('tenant_id', '=', $tenant_id)->first();
            
            $crm_leads = CRMLeads::where('id', '=', $opportunity->lead_id)->first();
            $crm_contacts = CRMContacts::where('lead_id', '=', $opportunity->lead_id)->first();
            $crm_contact_email = CRMContactDetail::where('contact_id', '=', $crm_contacts->id)->where('detail_type', '=', 'Email')->first();
            $customer_email = ($crm_contact_email)? $crm_contact_email->detail:'';

            if($job->pickup_suburb==null || $job->delivery_suburb==null){
                $res = [
                    'status' => 0,
                    'message' => 'Pickup Suburb or Delivery Suburb cannot be empty'
                ];
                return $res;
            }
            if($job->company_id == null || $job->company_id == 0){
                $res = [
                    'status' => 0,
                    'message' => 'Job Company cannot be empty'
                ];
                return $res;
            }else{
                $company = Companies::findOrFail($job->company_id);
                $company_name = $company->company_name;
            }
            $params = array(
                'key' => $company_name,
                'name' => $crm_leads->name,
                'email' => $customer_email,
                'fromAddress' => $job->pickup_suburb,
                'toAddress' => $job->delivery_suburb,
                'departure' => date('Y-m-d', strtotime($job->job_date)),
                'reference' => $job->job_number,
                'base64' => true
            );
            if($job->total_cbm != null){
                $cubic_volume = number_format((float)$job->total_cbm, 2, '.', '');
            }else{
                $cubic_volume = 1.0;
            }
            $goods_value = number_format((float)$job->goods_value, 2, '.', '');
            if($job->insurance_based_on=='cbm'){
                $params['cubic'] = $cubic_volume;
            }else{
                $params['value'] = $goods_value;
            }

            $data = json_encode($params);
            //print_r($params);exit;
            $api_url = "https://coverfreightonline.com.au/api/v3/home";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
             ));
            curl_setopt($curl, CURLOPT_URL, $api_url);
            curl_setopt( $curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec($curl);
            $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
            
            if($status==200){
                $result = json_decode($response,true);
                //print_r($result);exit;
                if(!$result['success']){
                    $res = [
                        'status' => 0,
                        'message' => $result['comment']
                    ];
                }else{
                    $pdf = base64_decode ($result['base64']);
                    $filename = 'Insurance_Quote_' .$job->job_id.'_'.$result['quote']. '.pdf';
                    file_put_contents(public_path().'/insurance-quote/'.$filename, $pdf);
                    $job->insurance_file_name = $filename;
                    if($job->insurance_based_on=='cbm'){
                        $job->total_cbm = $cubic_volume;
                    }else{
                        $job->goods_value = $goods_value;
                    }
                    $job->save();
                    $res = [
                        'status' => 1,
                        'message' => 'Insurance Quote has been generated successfully'
                    ];
                }
            }else{
                // $error_msg = curl_error($curl);
                // echo '<pre>';print_r($error_msg);exit;
            }
            curl_close($curl);
        } catch (\Exception $ex) {
            $res = [
                'status' => 0,
                'message' => $ex->getMessage()
            ];
         }
        return $res;
    }

    protected static function cleanString($string) {
        $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
     
        return preg_replace('/[^A-Za-z\_]/', '', $string); // Removes special chars.
     }
}