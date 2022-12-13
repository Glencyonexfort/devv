<?php

namespace App\Http\Controllers\API;

use App\Companies;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\CRMContactDetail;
use App\CRMContacts;
use App\CRMLeads;
use App\CRMLeadStatuses;
use App\CRMOpPipelineStatuses;
use App\CRMOpportunities;
use App\Invoice;
use App\InvoiceItems;
use App\JobsCleaning;
use App\JobsCleaningAdditionalInfo;
use App\JobsCleaningPricing;
use App\JobsCleaningQuoteFormSetup;
use App\OrganisationSettings;
use App\Payment;
use App\Products;
use App\QuoteItem;
use App\Quotes;
use App\Tax;
use App\Tenant;
use App\TenantApiDetail;
use App\TenantServicingCities;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Stripe\Stripe;


class EOLCleaningController extends BaseController
{
    public function createEOLCleaningOpportunity(Request $request)
    {
        
        $data_ary['tenant_id'] = $request->tenant_id;
        $data_ary['company_id'] = $request->company;
        $data_ary['city_id'] = $request->city_id;
        $data_ary['discount'] = $request->discount;
        $data_ary['name'] = $request->fld_full_name;
        $data_ary['phone'] = $request->fld_mobile;
        $data_ary['email'] = $request->fld_email;  
        $data_ary['date'] = $request->fld_job_date;  
        $data_ary['suburb'] = $request->fld_suburb;  
        $data_ary['bedrooms'] = $request->fld_bedrooms;
        $data_ary['bathrooms'] = $request->fld_bathrooms;
        $data_ary['storeys'] = $request->fld_storeys;
        $data_ary['carpet'] = $request->fld_carpet;
        $data_ary['message'] = $request->fld_message;        

        $data_ary['date'] = date('Y-m-d', strtotime($data_ary['date']));
        $contact_detail = CRMContactDetail::select('crm_contacts.lead_id')
            ->join('crm_contacts', 'crm_contact_details.contact_id', '=', 'crm_contacts.id')
            ->where('crm_contact_details.tenant_id', '=', $data_ary['tenant_id'])
            ->where('crm_contact_details.detail', '=', $data_ary['email'])->first();

        //$tenant_api_details = TenantApiDetail::where(['tenant_id' => $data_ary['tenant_id'], 'provider' => 'Xero'])->first();

        $lead_id = 0;
        if ($contact_detail) {

            $lead_id = $contact_detail->lead_id;
            $contact_id = 0;

            $contacts = CRMContacts::select('id')->where('lead_id', '=', $lead_id)
                ->where('tenant_id', '=', $data_ary['tenant_id'])
                ->where('name', '=', $data_ary['name'])->first();
            if ($contacts) {
                $contact_id = $contacts->id;
            } else {
                $obj = new CRMContacts();
                $obj->name = $data_ary['name'];
                $obj->lead_id = $lead_id;
                $obj->tenant_id = $data_ary['tenant_id'];
                $obj->save();
                $contact_id = $obj->id;
            }

            $contact_detail = CRMContactDetail::select('id')->where('detail', '=', $data_ary['phone'])
                ->where('tenant_id', '=', $data_ary['tenant_id'])
                ->where('contact_id', '=', $contact_id)->first();
            if ($contact_detail) {
                // do nothing
            } else {
                $obj = new CRMContactDetail();
                $obj->contact_id = $contact_id;
                $obj->detail_type = 'Mobile';
                $obj->detail = $data_ary['phone'];
                $obj->tenant_id = $data_ary['tenant_id'];
                $obj->save();
            }
        } else {

            $lead_status = CRMLeadStatuses::select('lead_status')->where('tenant_id', '=', $data_ary['tenant_id'])->where('sort_order', '=', '1')->first();

            $obj = new CRMLeads();
            $obj->name = $data_ary['name'];
            $obj->lead_status = $lead_status->lead_status;
            $obj->tenant_id = $data_ary['tenant_id'];
            $obj->save();

            $lead_id = $obj->id;

            $obj = new CRMContacts();
            $obj->name = $data_ary['name'];
            $obj->lead_id = $lead_id;
            $obj->tenant_id = $data_ary['tenant_id'];
            $obj->save();

            $contact_id = $obj->id;

            $obj = new CRMContactDetail();
            $obj->contact_id = $contact_id;
            $obj->detail_type = 'Email';
            $obj->detail = $data_ary['email'];
            $obj->tenant_id = $data_ary['tenant_id'];
            $obj->save();

            $obj = new CRMContactDetail();
            $obj->contact_id = $contact_id;
            $obj->detail_type = 'Mobile';
            $obj->detail = $data_ary['phone'];
            $obj->tenant_id = $data_ary['tenant_id'];
            $obj->save();
        }

        $cleaning_form_setup = JobsCleaningQuoteFormSetup::where('tenant_id', '=', $data_ary['tenant_id'])->where('servicing_city_id', '=', $data_ary['city_id'])->where('job_type_id', '=', '2')->first();
        // $pipeline_status = CRMOpPipelineStatuses::select('pipeline_status')->where('tenant_id', '=', $data_ary['tenant_id'])->where('id', '=', $cleaning_form_setup->quoted_op_status_id)->first();
        $pipeline_status = CRMOpPipelineStatuses::select('pipeline_status')->where('tenant_id', '=', $data_ary['tenant_id'])->where('sort_order', '=', '1')->first();

        $obj = new CRMOpportunities();
        $obj->lead_id = $lead_id;
        $obj->op_type = 'Cleaning';
        $obj->op_status = $pipeline_status->pipeline_status;
        $obj->est_job_date = $data_ary['date'];
        $obj->op_frequency = 'One-time';
        $obj->tenant_id = $data_ary['tenant_id'];
        $obj->save();

        $opportunity_id = $obj->id;

        $res = JobsCleaning::select(DB::raw('MAX(job_number) as max_job_number'))->where('tenant_id', '=', $data_ary['tenant_id'])->first();
        $new_job_number = intval($res->max_job_number) + 1;

        $tmp_ary = @explode(' ', $data_ary['bedrooms']);
        $step1_ary['bedrooms'] = trim($tmp_ary[0]);
        $tmp_ary = @explode(' ', $data_ary['bathrooms']);
        $step1_ary['bathrooms'] = trim($tmp_ary[0]);

        $obj = new JobsCleaning();
        $obj->company_id = $data_ary['company_id'];
        $obj->opportunity = 'Y';
        $obj->job_status = 'New';
        $obj->crm_opportunity_id = $opportunity_id;
        $obj->job_number = $new_job_number;
        $obj->customer_id = $lead_id;
        $obj->job_date = $data_ary['date'];
        //$obj->preferred_time_range = $step1_ary['start_time'];
        $obj->stories = $data_ary['storeys'];
        $obj->address = $data_ary['suburb'];
        $obj->carpeted = $data_ary['carpet'];
        $obj->bedrooms = $data_ary['bedrooms'];
        $obj->bathrooms = $data_ary['bathrooms'];
        $obj->tenant_id = $data_ary['tenant_id'];
        $obj->comments = $data_ary['message'];
        $obj->job_type_id = '2';
        $obj->save();

        $job_cleaning_id = $obj->job_id;

        // $obj = new Invoice();
        // $obj->tenant_id = $data_ary['tenant_id'];
        // $obj->job_id  = $job_cleaning_id;
        // $obj->sys_job_type  = 'Cleaning';
        // $obj->invoice_number = $new_job_number;
        // $obj->issue_date = date('Y-m-d');
        // $obj->due_date = date('Y-m-d');
        // $obj->status  = 'paid'; 
        // $obj->save();

        // $invoice_id = $obj->id;

        $total_cost = 0;
        $total_sub_cost = 0;
        $total_extra_cost = 0;

        $vstorey = ($data_ary['storeys']>0)?'Y':'N';
        $vcarpet = $data_ary['carpet'];
        $vbedrooms = $data_ary['bedrooms'];
        $vbathrooms = $data_ary['bathrooms'];

        $organisation_data['organisation_settings'] = OrganisationSettings::where('tenant_id', '=', $data_ary['tenant_id'])->first();
        $taxes = Tax::where('tenant_id', '=', $data_ary['tenant_id'])->first();
        $cleaning_pricing = JobsCleaningPricing::select('price', 'tax_id')->where('tenant_id', '=', $data_ary['tenant_id'])
            ->where('bedrooms', '=', $vbedrooms)
            ->where('bathrooms', '=', $vbathrooms)
            ->where('carpet', '=', $vcarpet)
            ->where('storey', '=', $vstorey)->first();
        if ($cleaning_pricing) {
            $taxes = Tax::where('id', '=', $cleaning_pricing->tax_id)->first();
            $total_sub_cost = $cleaning_pricing->price * (1 + $taxes->rate_percent / 100);
        }

        $total_cost = floatval($total_sub_cost) + floatval($total_extra_cost);
        if ($cleaning_pricing) {
            $taxes = Tax::where('id', '=', $cleaning_pricing->tax_id)->first();
            $amount = (floatval($total_sub_cost) * (1 + $taxes->rate_percent / 100));
        }else{
            $amount = floatval($total_sub_cost);
        }

        //(jobs_cleaning_quote_form_setup.product_description)
        
        $obj = new Quotes();
        $obj->tenant_id = $data_ary['tenant_id'];
        $obj->quote_number  = $new_job_number;
        $obj->crm_opportunity_id  = $opportunity_id;
        $obj->sys_job_type = 'Cleaning';
        $obj->job_id  = $job_cleaning_id;
        $obj->discount  = $data_ary['discount'];      
        $obj->quote_date = Carbon::now();
        $obj->save();

        $quote_id = $obj->id;

        $obj = new QuoteItem();
        $obj->tenant_id = $data_ary['tenant_id'];
        $obj->quote_id  = $quote_id;
        $obj->name = 'Main service';
        $obj->description = ($cleaning_form_setup)?$cleaning_form_setup->product_description:"";
        $obj->type = 'Item';
        $obj->quantity = 1;
        $obj->unit_price = $total_sub_cost;        
        $obj->amount = $amount;
        $obj->tax_id = ($cleaning_pricing->tax_id ?? 0);
        $obj->save();        

        $response = array(
            'status' => 'Cleaning Opportuniry has been created.',
            'new_job_number' => $new_job_number
        );
        $result = $this->sendResponse($response, 'Success');
        return $result;
    }

    public function getCompanylist(Request $request){
        $tenant_id = $request->tenant_id;
        // if(empty($tenant_id) || $tenant_id <= 0){
        //     return $this->sendError('notValid', 'Tenant ID is not valid.');
        // }
        $list=[];
        $companies = Companies::where(['tenant_id'=>$tenant_id,'active'=>'Y'])->get();
        if($companies){
            foreach($companies as $company){
                $d['id']=$company->id;
                $d['company_name']=$company->company_name;
                $list[]=$d;
            }
        }
        return $list;
    }

    public function getCitylist(Request $request){
        $tenant_id = $request->tenant_id;
        if(empty($tenant_id) || $tenant_id <= 0){
            return $this->sendError('notValid', 'Tenant ID is not valid.');
        }
        $list=[];
        $cities = TenantServicingCities::where(['tenant_id'=>$tenant_id,'deleted'=>0])->get();
        if($cities){
            foreach($cities as $city){
                $d['id']=$city->id;
                $d['servicing_city']=$city->servicing_city;
                $list[]=$d;
            }
        }
        return $list;
    }
}
