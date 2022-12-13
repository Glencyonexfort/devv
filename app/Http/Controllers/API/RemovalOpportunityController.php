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
use App\JobsCleaningQuoteFormSetup;
use App\JobsMoving;
use Illuminate\Support\Facades\DB;

class RemovalOpportunityController extends BaseController
{
    public function createOpportunity(Request $request)
    {
        $data_ary['tenant_id'] = $request->tenant_id;
        $data_ary['company_id'] = $request->company;
        $data_ary['discount'] = $request->discount;
        $data_ary['name'] = $request->fld_full_name;
        $data_ary['phone'] = $request->fld_mobile;
        $data_ary['email'] = $request->fld_email;  
        $data_ary['date'] = $request->fld_job_date;  
        $data_ary['from_suburb'] = $request->fld_from_suburb;  
        $data_ary['to_suburb'] = $request->fld_to_suburb;
        $data_ary['bedrooms'] = $request->fld_bedrooms;
        $data_ary['livingrooms'] = $request->fld_livingrooms;
        $data_ary['furnishing'] = $request->fld_furnishing;
        $data_ary['property_type'] = $request->fld_property_type;
        $data_ary['message'] = $request->fld_message;        

        $data_ary['date'] = date('Y-m-d', strtotime($data_ary['date']));
        $contact_detail = CRMContactDetail::select('crm_contacts.lead_id')
            ->join('crm_contacts', 'crm_contact_details.contact_id', '=', 'crm_contacts.id')
            ->where('crm_contact_details.tenant_id', '=', $data_ary['tenant_id'])
            ->where('crm_contact_details.detail', '=', $data_ary['email'])->first();

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
        $pipeline_status = CRMOpPipelineStatuses::select('pipeline_status')->where('tenant_id', '=', $data_ary['tenant_id'])->where('sort_order', '=', '1')->first();

        $obj = new CRMOpportunities();
        $obj->lead_id = $lead_id;
        $obj->op_type = 'Moving';
        $obj->op_status = $pipeline_status->pipeline_status;
        $obj->est_job_date = $data_ary['date'];
        $obj->op_frequency = 'One-time';
        $obj->tenant_id = $data_ary['tenant_id'];
        $obj->save();
        $opportunity_id = $obj->id;

        // Get Minimum Goods Value for Insurance Quote
        $minimum_goods_value = DB::table('jobs_moving_pricing_additional as t1')
        ->select('t1.*')
        ->where(['t1.tenant_id' => $data_ary['tenant_id']])->pluck("minimum_goods_value")
        ->first();

        $res = JobsMoving::select(DB::raw('MAX(job_number) as max_job_number'))->where('tenant_id', '=', $data_ary['tenant_id'])->first();
        $new_job_number = intval($res->max_job_number) + 1;

        $obj = new JobsMoving();
        $obj->company_id = $data_ary['company_id'];
        $obj->opportunity = 'Y';
        $obj->job_status = '';
        $obj->crm_opportunity_id = $opportunity_id;
        $obj->job_number = $new_job_number;
        $obj->customer_id = $lead_id;
        $obj->job_date = $data_ary['date'];        
        $obj->pickup_suburb = $data_ary['from_suburb'];
        $obj->delivery_suburb = $data_ary['to_suburb'];
        $obj->pickup_bedrooms = $data_ary['bedrooms'];
        $obj->pickup_living_areas = $data_ary['livingrooms'];
        $obj->pickup_furnishing = $data_ary['furnishing'];
        $obj->pickup_property_type = $data_ary['property_type'];
        $obj->job_type = 'Moving';
        $obj->goods_value = $minimum_goods_value;
        $obj->tenant_id = $data_ary['tenant_id'];
        $obj->other_instructions = $data_ary['message'];
        $obj->save();

        $response = array(
            'status' => 'Moving Opportuniry has been created',
            'new_job_number' => $new_job_number
        );
        $result = $this->sendResponse($response, 'Success');
        return $result;
    }

}
