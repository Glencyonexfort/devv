<?php

namespace App\Http\Controllers;

use App\CRMContactDetail;
use App\CRMContacts;
use App\CRMLeads;
use App\CRMLeadStatuses;
use App\CRMOpPipelineStatuses;
use App\CRMOpportunities;
use App\Http\Controllers\Controller;
use App\JobsCleaning;
use App\JobsCleaningAdditionalInfo;
use App\JobsCleaningQuoteFormSetup;
use App\ListOptions;
use App\ListTypes;
use App\OrganisationSettings;
use App\Products;
use App\QuoteItem;
use App\Quotes;
use App\Tax;
use App\Tenant;
use App\TenantApiDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class QuoteCleaningController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    // public function show_form($id)
    // {
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id, $company_id)
    {
        $session_data = array('step1' => array(), 'step2' => array(), 'step3' => array(), 'step4' => array());
        $request = Input::get();
        $tenant = Tenant::select('tenant_name')->where('tenant_id', $id)->first();
        if ($tenant) {
            $view_data['step'] = '1';
            $step = Input::get('step');
            if ($step) {
                $view_data['step'] = $step;
            }
            $tenant_id = intval($id);
            $view_data['tenant_id'] = intval($tenant_id);
            $view_data['company_id'] = intval($company_id);
            
            if (isset($request['session_data']) && !empty($request['session_data'])) {
                $session_data = unserialize($request['session_data']);
            }

            $view_data['cleaning_form_setup'] = JobsCleaningQuoteFormSetup::where('tenant_id', '=', $tenant_id)->where('job_type_id', '=', '1')->first();
            $view_data['products'] = Products::where('tenant_id', '=', $tenant_id)->where('category_id', '=', $view_data['cleaning_form_setup']->services_category_id)->get();
            $view_data['time_list'] = ListOptions::select('id', 'list_option')->where('tenant_id', '=', $tenant_id)->where('list_type_id', '=', $view_data['cleaning_form_setup']->time_selector_list_type_id)->get();
            $view_data['often_list'] = ListOptions::select('id', 'list_option')->where('tenant_id', '=', $tenant_id)->where('list_type_id', '=', '8')->get();
            $view_data['extras_list'] = Products::where('tenant_id', '=', $tenant_id)->where('category_id', '=', $view_data['cleaning_form_setup']->extras_category_id)->get();

            $questions_list_ids = $view_data['cleaning_form_setup']->questions_list_type_id;
            $questions_list_ids_ary = explode(',', $questions_list_ids);
            $question_list =  array();
            foreach ($questions_list_ids_ary as $qid) {
                $question_list[$qid]['question'] = ListTypes::select('id', 'list_name')->where('tenant_id', '=', $tenant_id)->where('id', '=', $qid)->first();
                $question_list[$qid]['list'] = ListOptions::select('id', 'list_option')->where('tenant_id', '=', $tenant_id)->where('list_type_id', '=', $qid)->get();
            }
            $view_data['question_list'] = $question_list;

            $view_data['step1_ary'] = $session_data['step1'];
            $view_data['step2_ary'] = $session_data['step2'];
            $view_data['step3_ary'] = $session_data['step3'];
            $view_data['step4_ary'] = $session_data['step4'];
            $view_data['session_data'] = serialize($session_data);
        } else {
            $view_data['step'] = '0';
            $view_data['tenant_id'] = '0';
            $view_data['company_id'] = '0';
            $view_data['something'] = 'wrong';
        }

        $view_data['tenant_api_details'] = TenantApiDetail::where(['tenant_id' => $id, 'provider' => 'GoogleMaps'])->first(); 
        return view('quote-cleaning.index', $view_data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $session_data = array('step1' => array(), 'step2' => array(), 'step3' => array(), 'step4' => array());
        $request = Input::get();
        $tenant = Tenant::select('tenant_name')->where('tenant_id', $request['tenant_id'])->first();
        if ($tenant) {
            $view_data['tenant_id'] = '0';
            $view_data['company_id'] = '0';
            $view_data['step1_ary'] = array();
            $view_data['step2_ary'] = array();
            $view_data['step3_ary'] = array();
            $view_data['step4_ary'] = array();

            if (isset($request['session_data']) && !empty($request['session_data'])) {
                $session_data = unserialize($request['session_data']);
                unset($request['session_data']);
            }

            $step = 1;
            if (isset($request['step'])) {
                $view_data['tenant_id'] = $request['tenant_id'];
                $view_data['company_id'] = $request['company_id'];
                $tenant_id = $view_data['tenant_id'];

                if ($request['step'] == '1') {
                    $session_data['step1'] = [
                        'products' => $request['products'],
                        'cleaners' => $request['cleaners'],
                        'hours' => $request['hours'],
                        'date' => $request['date'],
                        'time' => $request['time'],
                        'how_often' => $request['how_often']
                    ];
                    $step = 2;
                }

                if ($request['step'] == '2') {
                    $session_data['step2'] = ['extras' => $request['extras']];
                    $step = 3;
                }

                if ($request['step'] == '3') {
                    $session_data['step3'] = ['question' => $request['question']];
                    $step = 4;
                }

                if ($request['step'] == '4') {
                    $session_data['step4'] = [
                        'name' => $request['name'],
                        'email' => $request['email'],
                        'phone' => $request['phone'],
                        'cleaning_address' => $request['cleaning_address'],
                        'other_details' => $request['other_details']
                    ];
                    $step = 5;


                    $step1_ary = $session_data['step1'];
                    $step2_ary = $session_data['step2'];
                    $step3_ary = $session_data['step3'];
                    $step4_ary = $session_data['step4'];

                    $total_cost = 0;

                    $view_data['organisation_settings'] = OrganisationSettings::where('tenant_id', '=', $tenant_id)->first();
                    $product = Products::where('tenant_id', '=', $tenant_id)->where('id', '=', $step1_ary['products'])->first();
                    $taxes = Tax::where('id', '=', $product->tax_id)->first();

                    if (isset($product->price_type) && $product->price_type == 'Hourly') {
                        $total_cost = ($product->price * (1 + (floatval($taxes->rate_percent) / 100))) * (intval($step1_ary['cleaners']) * intval($step1_ary['hours']));
                    } else {
                        $total_cost = $product->price * (1 + floatval($taxes->rate_percent) / 100);
                    }

                    $extras = $step2_ary['extras'];
                    if (is_array($extras) && count($extras) > 0) {
                        foreach ($extras as $key => $val) {
                            if (intval($val) > 0) {
                                $product = Products::where('tenant_id', '=', $view_data['tenant_id'])->where('id', '=', $key)->first();
                                $total_cost += floatval($product->price) * intval($val) * (1 + floatval($taxes->rate_percent) / 100);
                            }
                        }
                    }

                    $view_data['product'] = Products::where('tenant_id', '=', $tenant_id)->where('id', '=', $step1_ary['products'])->first();
                    $view_data['total_cost'] = number_format((float)$total_cost, 2, '.', '');
                    $view_data['date'] = $step1_ary['date'];
                    $view_data['time'] = $step1_ary['time'];
                    $view_data['how_often'] = $step1_ary['how_often'];
                }
            }

            $view_data['cleaning_form_setup'] = JobsCleaningQuoteFormSetup::where('tenant_id', '=', $tenant_id)->where('job_type_id', '=', '1')->first();
            $view_data['products'] = Products::where('tenant_id', '=', $tenant_id)->where('category_id', '=', $view_data['cleaning_form_setup']->services_category_id)->get();
            $view_data['time_list'] = ListOptions::select('id', 'list_option')->where('tenant_id', '=', $tenant_id)->where('list_type_id', '=', $view_data['cleaning_form_setup']->time_selector_list_type_id)->get();
            $view_data['often_list'] = ListOptions::select('id', 'list_option')->where('tenant_id', '=', $tenant_id)->where('list_type_id', '=', '8')->get();
            $view_data['extras_list'] = Products::where('tenant_id', '=', $tenant_id)->where('category_id', '=', $view_data['cleaning_form_setup']->extras_category_id)->get();

            $questions_list_ids = $view_data['cleaning_form_setup']->questions_list_type_id;
            $questions_list_ids_ary = explode(',', $questions_list_ids);
            $question_list =  array();
            foreach ($questions_list_ids_ary as $qid) {
                $question_list[$qid]['question'] = ListTypes::select('id', 'list_name')->where('tenant_id', '=', $tenant_id)->where('id', '=', $qid)->first();
                $question_list[$qid]['list'] = ListOptions::select('id', 'list_option')->where('tenant_id', '=', $tenant_id)->where('list_type_id', '=', $qid)->get();
            }
            $view_data['question_list'] = $question_list;

            $view_data['step'] = $step;

            $view_data['step1_ary'] = $session_data['step1'];
            $view_data['step2_ary'] = $session_data['step2'];
            $view_data['step3_ary'] = $session_data['step3'];
            $view_data['step4_ary'] = $session_data['step4'];
            $view_data['session_data'] = serialize($session_data);
        } else {
            $view_data['step'] = '0';
            $view_data['tenant_id'] = '0';
            $view_data['something'] = 'wrong';
        }

        $view_data['tenant_api_details'] = TenantApiDetail::where(['tenant_id' => $request['tenant_id'], 'provider' => 'GoogleMaps'])->first();
        return view('quote-cleaning.index', $view_data);
    }

    public function payLater()
    {
        $session_data = array('step1' => array(), 'step2' => array(), 'step3' => array(), 'step4' => array());
        $request = Input::get();
        $view_data['tenant_id'] = $request['tenant_id'];
        $view_data['company_id'] = $request['company_id'];

        if (isset($request['session_data']) && !empty($request['session_data'])) {
            $session_data = unserialize($request['session_data']);
        }

        $step1_ary = $session_data['step1'];
        $step2_ary = $session_data['step2'];
        $step3_ary = $session_data['step3'];
        $step4_ary = $session_data['step4'];


        $new_date = @explode('/', $step1_ary['date']);
        $step1_ary['date'] = $new_date[2] . '-' . $new_date[1] . '-' . $new_date[0];

        $contact_detail = CRMContactDetail::select('crm_contacts.lead_id')
            ->join('crm_contacts', 'crm_contact_details.contact_id', '=', 'crm_contacts.id')
            ->where('crm_contact_details.tenant_id', '=', $view_data['tenant_id'])
            ->where('crm_contact_details.detail', '=', $step4_ary['email'])
            ->first();

        $lead_id = 0;
        if ($contact_detail) {

            $lead_id = $contact_detail->lead_id;
            $contact_id = 0;

            $contacts = CRMContacts::select('id')->where('lead_id', '=', $lead_id)
                ->where('tenant_id', '=', $view_data['tenant_id'])
                ->where('name', '=', $step4_ary['name'])->first();
            if ($contacts) {
                $contact_id = $contacts->id;
            } else {
                $obj = new CRMContacts();
                $obj->name = $step4_ary['name'];
                $obj->lead_id = $lead_id;
                $obj->tenant_id = $view_data['tenant_id'];
                $obj->save();
                $contact_id = $obj->id;
            }

            $contact_detail = CRMContactDetail::select('id')->where('detail', '=', $step4_ary['phone'])
                ->where('tenant_id', '=', $view_data['tenant_id'])
                ->where('contact_id', '=', $contact_id)->first();
            if ($contact_detail) {
                // do nothing
            } else {
                $obj = new CRMContactDetail();
                $obj->contact_id = $contact_id;
                $obj->detail_type = 'Mobile';
                $obj->detail = $step4_ary['phone'];
                $obj->tenant_id = $view_data['tenant_id'];
                $obj->save();
            }
        } else {

            $lead_status = CRMLeadStatuses::select('lead_status')->where('tenant_id', '=', $view_data['tenant_id'])->where('sort_order', '=', '1')->first();

            $obj = new CRMLeads();
            $obj->name = $step4_ary['name'];
            $obj->lead_status = $lead_status->lead_status;
            $obj->tenant_id = $view_data['tenant_id'];
            $obj->save();

            $lead_id = $obj->id;

            $obj = new CRMContacts();
            $obj->name = $step4_ary['name'];
            $obj->lead_id = $lead_id;
            $obj->tenant_id = $view_data['tenant_id'];
            $obj->save();

            $contact_id = $obj->id;

            $obj = new CRMContactDetail();
            $obj->contact_id = $contact_id;
            $obj->detail_type = 'Email';
            $obj->detail = $step4_ary['email'];
            $obj->tenant_id = $view_data['tenant_id'];
            $obj->save();

            $obj = new CRMContactDetail();
            $obj->contact_id = $contact_id;
            $obj->detail_type = 'Mobile';
            $obj->detail = $step4_ary['phone'];
            $obj->tenant_id = $view_data['tenant_id'];
            $obj->save();
        }

        $cleaning_form_setup = JobsCleaningQuoteFormSetup::where('tenant_id', '=', $view_data['tenant_id'])->where('job_type_id', '=', '1')->first();
        $pipeline_status = CRMOpPipelineStatuses::select('pipeline_status')->where('tenant_id', '=', $view_data['tenant_id'])->where('id', '=', $cleaning_form_setup->quoted_op_status_id)->first();

        $obj = new CRMOpportunities();
        $obj->lead_id = $lead_id;
        $obj->op_type = 'Cleaning';
        $obj->op_status = $pipeline_status->pipeline_status;
        $obj->est_job_date = $step1_ary['date'];
        $obj->op_frequency = $step1_ary['how_often'];
        $obj->tenant_id = $view_data['tenant_id'];
        $obj->save();

        $opportunity_id = $obj->id;

        $res = JobsCleaning::select(DB::raw('MAX(job_number) as max_job_number'))->where('tenant_id', '=', $view_data['tenant_id'])->first();
        $new_job_number = intval($res->max_job_number) + 1;

        $obj = new JobsCleaning();
        $obj->company_id = $view_data['company_id'];
        $obj->opportunity = 'Y';
        $obj->crm_opportunity_id = $opportunity_id;
        $obj->job_number = $new_job_number;
        $obj->customer_id = $lead_id;
        $obj->job_date = $step1_ary['date'];
        $obj->preferred_time_range = $step1_ary['time'];
        $obj->job_frequency = $step1_ary['how_often'];
        $obj->number_of_workers = intval($step1_ary['cleaners']);
        $obj->number_of_hours = intval($step1_ary['hours']);
        $obj->address = $step4_ary['cleaning_address'];
        $obj->tenant_id = $view_data['tenant_id'];
        $obj->job_type_id = '1';
        $obj->save();


        $job_cleaning_id = $obj->job_id;

        $obj = new Quotes();
        $obj->tenant_id = $view_data['tenant_id'];
        $obj->quote_number  = $new_job_number;
        $obj->crm_opportunity_id = $opportunity_id;
        $obj->sys_job_type  = 'Cleaning';
        $obj->job_id  = $job_cleaning_id;
        $obj->quote_date  = date('Y-m-d');
        $obj->save();

        $quote_id = $obj->id;

        $product = Products::where('tenant_id', '=', $view_data['tenant_id'])->where('id', '=', $step1_ary['products'])->first();
        $tax = Tax::where(['id' => $product->tax_id])->first();

        $obj = new QuoteItem();
        $obj->tenant_id = $view_data['tenant_id'];
        $obj->quote_id  = $quote_id;
        $obj->name = 'Main service';
        $obj->description = $product->name;
        $obj->type = $product->product_type;
        $obj->unit_price = $product->price;
        $qty = 1;
        if ($product->price_type == 'Hourly') {
            $qty = intval($step1_ary['cleaners']) * intval($step1_ary['hours']);
            $obj->quantity = $qty;
        } else {
            $obj->quantity = 1;
        }
        $amount = (floatval($product->price) * intval($qty) * (1 + floatval($tax->rate_percent) / 100));
        $obj->amount = $amount;
        $obj->tax_id = $product->tax_id;
        $obj->save();

        $extra_questions = $step3_ary['question'];
        if (is_array($extra_questions) && count($extra_questions) > 0) {
            foreach ($extra_questions as $key => $val) {
                $qs = ListTypes::select('id', 'list_name')->where(['id' => $key])->first();
                $ans = ListOptions::select('id', 'list_option')->where(['id' => $val])->first();

                $obj = new JobsCleaningAdditionalInfo();
                $obj->tenant_id = $view_data['tenant_id'];
                $obj->job_id  = $job_cleaning_id;
                $obj->question = $qs->list_name;
                $obj->reply = $ans->list_option;
                $obj->save();
            }
        }
        
        $extras = $step2_ary['extras'];
        if (is_array($extras) && count($extras) > 0) {
            foreach ($extras as $key => $val) {
                if (intval($val) > 0) {
                    $product = Products::where('tenant_id', '=', $view_data['tenant_id'])->where('id', '=', $key)->first();
                    $tax = Tax::where('id', '=', $product->tax_id)->first();

                    $obj = new QuoteItem();
                    $obj->tenant_id = $view_data['tenant_id'];
                    $obj->quote_id  = $quote_id;
                    $obj->name = 'Extra service';
                    $obj->description = $product->name;
                    $obj->type = $product->product_type;
                    $obj->unit_price = $product->price;
                    $obj->quantity = intval($val);
                    $amount = (floatval($product->price) * intval($val) * (1 + floatval($tax->rate_percent) / 100));
                    $obj->amount = $amount;
                    $obj->tax_id = $product->tax_id;
                    $obj->save();
                }
            }
        }
        $view_data['new_job_number'] = $new_job_number;

        $view_data['step'] = '6';
        
        $view_data['tenant_api_details'] = TenantApiDetail::where(['tenant_id' => $request['tenant_id'], 'provider' => 'GoogleMaps'])->first();
        return view('quote-cleaning.index', $view_data);
    }

    public function payNow()
    {
        $session_data = array('step1' => array(), 'step2' => array(), 'step3' => array(), 'step4' => array());
        $request = Input::get();
        $view_data['tenant_id'] = $request['tenant_id'];
        $view_data['company_id'] = $request['company_id'];

        if (isset($request['session_data']) && !empty($request['session_data'])) {
            $session_data = unserialize($request['session_data']);
        }

        $step1_ary = $session_data['step1'];
        $step2_ary = $session_data['step2'];
        $step3_ary = $session_data['step3'];
        $step4_ary = $session_data['step4'];


        $new_date = @explode('/', $step1_ary['date']);
        $step1_ary['date'] = $new_date[2] . '-' . $new_date[1] . '-' . $new_date[0];

        $contact_detail = CRMContactDetail::select('crm_contacts.lead_id')
            ->join('crm_contacts', 'crm_contact_details.contact_id', '=', 'crm_contacts.id')
            ->where('crm_contact_details.tenant_id', '=', $view_data['tenant_id'])
            ->where('crm_contact_details.detail', '=', $step4_ary['email'])
            ->first();

        $lead_id = 0;
        if ($contact_detail) {

            $lead_id = $contact_detail->lead_id;
            $contact_id = 0;

            $contacts = CRMContacts::select('id')->where('lead_id', '=', $lead_id)
                ->where('tenant_id', '=', $view_data['tenant_id'])
                ->where('name', '=', $step4_ary['name'])->first();
            if ($contacts) {
                $contact_id = $contacts->id;
            } else {
                $obj = new CRMContacts();
                $obj->name = $step4_ary['name'];
                $obj->lead_id = $lead_id;
                $obj->tenant_id = $view_data['tenant_id'];
                $obj->save();
                $contact_id = $obj->id;
            }

            $contact_detail = CRMContactDetail::select('id')->where('detail', '=', $step4_ary['phone'])
                ->where('tenant_id', '=', $view_data['tenant_id'])
                ->where('contact_id', '=', $contact_id)->first();
            if ($contact_detail) {
                // do nothing
            } else {
                $obj = new CRMContactDetail();
                $obj->contact_id = $contact_id;
                $obj->detail_type = 'Mobile';
                $obj->detail = $step4_ary['phone'];
                $obj->tenant_id = $view_data['tenant_id'];
                $obj->save();
            }
        } else {

            $lead_status = CRMLeadStatuses::select('lead_status')->where('tenant_id', '=', $view_data['tenant_id'])->where('sort_order', '=', '1')->first();

            $obj = new CRMLeads();
            $obj->name = $step4_ary['name'];
            $obj->lead_status = $lead_status->lead_status;
            $obj->tenant_id = $view_data['tenant_id'];
            $obj->save();

            $lead_id = $obj->id;

            $obj = new CRMContacts();
            $obj->name = $step4_ary['name'];
            $obj->lead_id = $lead_id;
            $obj->tenant_id = $view_data['tenant_id'];
            $obj->save();

            $contact_id = $obj->id;

            $obj = new CRMContactDetail();
            $obj->contact_id = $contact_id;
            $obj->detail_type = 'Email';
            $obj->detail = $step4_ary['email'];
            $obj->tenant_id = $view_data['tenant_id'];
            $obj->save();

            $obj = new CRMContactDetail();
            $obj->contact_id = $contact_id;
            $obj->detail_type = 'Mobile';
            $obj->detail = $step4_ary['phone'];
            $obj->tenant_id = $view_data['tenant_id'];
            $obj->save();
        }

        $cleaning_form_setup = JobsCleaningQuoteFormSetup::where('tenant_id', '=', $view_data['tenant_id'])->where('job_type_id', '=', '1')->first();
        $pipeline_status = CRMOpPipelineStatuses::select('pipeline_status')->where('tenant_id', '=', $view_data['tenant_id'])->where('id', '=', $cleaning_form_setup->quoted_op_status_id)->first();

        $obj = new CRMOpportunities();
        $obj->lead_id = $lead_id;
        $obj->op_type = 'Cleaning';
        $obj->op_status = $pipeline_status->pipeline_status;
        $obj->est_job_date = $step1_ary['date'];
        $obj->op_frequency = $step1_ary['how_often'];
        $obj->tenant_id = $view_data['tenant_id'];
        $obj->save();

        $opportunity_id = $obj->id;

        $res = JobsCleaning::select(DB::raw('MAX(job_number) as max_job_number'))->where('tenant_id', '=', $view_data['tenant_id'])->first();
        $new_job_number = intval($res->max_job_number) + 1;

        $obj = new JobsCleaning();
        $obj->company_id = $view_data['company_id'];
        $obj->opportunity = 'Y';
        $obj->crm_opportunity_id = $opportunity_id;
        $obj->job_number = $new_job_number;
        $obj->customer_id = $lead_id;
        $obj->job_date = $step1_ary['date'];
        $obj->preferred_time_range = $step1_ary['time'];
        $obj->job_frequency = $step1_ary['how_often'];
        $obj->number_of_workers = intval($step1_ary['cleaners']);
        $obj->number_of_hours = intval($step1_ary['hours']);
        $obj->address = $step4_ary['cleaning_address'];
        $obj->tenant_id = $view_data['tenant_id'];
        $obj->job_type_id = '1';
        $obj->save();

        $job_cleaning_id = $obj->job_id;

        $obj = new Quotes();
        $obj->tenant_id = $view_data['tenant_id'];
        $obj->quote_number  = $new_job_number;
        $obj->crm_opportunity_id = $opportunity_id;
        $obj->sys_job_type  = 'Cleaning';
        $obj->job_id  = $job_cleaning_id;
        $obj->quote_date  = date('Y-m-d');
        $obj->save();

        $quote_id = $obj->id;

        $product = Products::where('tenant_id', '=', $view_data['tenant_id'])->where('id', '=', $step1_ary['products'])->first();
        $tax = Tax::where(['id' => $product->tax_id])->first();

        $obj = new QuoteItem();
        $obj->tenant_id = $view_data['tenant_id'];
        $obj->quote_id  = $quote_id;
        $obj->name = 'Main service';
        $obj->description = $product->name;
        $obj->type = $product->product_type;
        $obj->unit_price = $product->price;
        $qty = 1;
        if ($product->price_type == 'Hourly') {
            $qty = intval($step1_ary['cleaners']) * intval($step1_ary['hours']);
            $obj->quantity = $qty;
        } else {
            $obj->quantity = 1;
        }
        $amount = (floatval($product->price) * intval($qty) * (1 + floatval($tax->rate_percent) / 100));
        $obj->amount = $amount;
        $obj->tax_id = $product->tax_id;
        $obj->save();

        $extra_questions = $step3_ary['question'];
        if (is_array($extra_questions) && count($extra_questions) > 0) {
            foreach ($extra_questions as $key => $val) {
                $qs = ListTypes::select('id', 'list_name')->where(['id' => $key])->first();
                $ans = ListOptions::select('id', 'list_option')->where(['id' => $val])->first();

                $obj = new JobsCleaningAdditionalInfo();
                $obj->tenant_id = $view_data['tenant_id'];
                $obj->job_id  = $job_cleaning_id;
                $obj->question = $qs->list_name;
                $obj->reply = $ans->list_option;
                $obj->save();
            }
        }

        $extras = $step2_ary['extras'];
        if (is_array($extras) && count($extras) > 0) {
            foreach ($extras as $key => $val) {
                if (intval($val) > 0) {
                    $product = Products::where('tenant_id', '=', $view_data['tenant_id'])->where('id', '=', $key)->first();
                    $tax = Tax::where(['id' => $product->tax_id])->first();

                    $obj = new QuoteItem();
                    $obj->tenant_id = $view_data['tenant_id'];
                    $obj->quote_id  = $quote_id;
                    $obj->name = 'Extra service';
                    $obj->description = $product->name;
                    $obj->type = $product->product_type;
                    $obj->unit_price = $product->price;
                    $obj->quantity = intval($val);
                    $amount = (floatval($product->price) * intval($val) * (1 + floatval($tax->rate_percent) / 100));
                    $obj->amount = $amount;
                    $obj->tax_id = $product->tax_id;
                    $obj->save();
                }
            }
        }
        $view_data['new_job_number'] = $new_job_number;

        $view_data['step'] = '7';
        return view('quote-cleaning.index', $view_data);
    }

    public function sess()
    {
        $data = Session::all();
        dd($data);
    }
}
