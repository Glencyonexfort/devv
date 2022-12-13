<?php

namespace App\Http\Controllers;

use App\CRMContactDetail;
use App\CRMContacts;
use App\CRMLeads;
use App\CRMLeadStatuses;
use App\CRMOpPipelineStatuses;
use App\CRMOpportunities;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\InvoiceItems;
use App\JobsCleaning;
use App\JobsCleaningAdditionalInfo;
use App\JobsCleaningPricing;
use App\JobsCleaningQuoteFormSetup;
use App\JobsCleaningShifts;
use App\JobsCleaningTeamRoster;
use App\JobsCleaningTeams;
use App\ListOptions;
use App\ListTypes;
use App\OrganisationSettings;
use App\Payment;
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
use Stripe\Stripe;


class QuoteLeaseCleaningController extends Controller
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
    public function create($id, $company_id, $city_id, $discount)
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
            $view_data['discount'] = floatval($discount);
            $view_data['city_id'] = intval($city_id);

            if (isset($request['session_data']) && !empty($request['session_data'])) {
                $session_data = unserialize($request['session_data']);
            }

            $view_data['cleaning_form_setup'] = JobsCleaningQuoteFormSetup::where('tenant_id', '=', $tenant_id)->where('servicing_city_id', '=', $city_id)->where('job_type_id', '=', '2')->first();
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
            $view_data['discount'] = '0';
            $view_data['city_id'] = '0';
            $view_data['something'] = 'wrong';
        }

        $view_data['tenant_api_details'] = TenantApiDetail::where(['tenant_id' => $id, 'provider' => 'GoogleMaps'])->first();
        return view('quote-lease-cleaning.index', $view_data);
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
            $view_data['city_id'] = '0';
            $view_data['discount'] = '0';
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
                $view_data['city_id'] = $request['city_id'];
                $view_data['discount'] = $request['discount'];
                $tenant_id = $view_data['tenant_id'];

                if ($request['step'] == '1') {
                    $session_data['step1'] = $request;
                    $step = 2;
                }

                if ($request['step'] == '2') {
                    $session_data['step2'] = $request;
                    $step = 3;
                }

                if ($request['step'] == '3') {
                    $session_data['step3'] = $request;
                    $step = 4;
                }

                if ($request['step'] == '4') {
                    $session_data['step4'] = $request;
                    $step = 5;

                    $step1_ary = $session_data['step1'];
                    $step2_ary = $session_data['step2'];
                    $step3_ary = $session_data['step3'];
                    $step4_ary = $session_data['step4'];

                    $total_cost = 0;

                    $tmp_ary = @explode(' ', $step1_ary['bedrooms']);
                    $step1_ary['bedrooms'] = trim($tmp_ary[0]);
                    $tmp_ary = @explode(' ', $step1_ary['bathrooms']);
                    $step1_ary['bathrooms'] = trim($tmp_ary[0]);

                    $vstorey = $step1_ary['story'];
                    $vcarpet = $step1_ary['carpeted'];
                    $vbedrooms = $step1_ary['bedrooms'];
                    $vbathrooms = $step1_ary['bathrooms'];

                    $view_data['organisation_settings'] = OrganisationSettings::where('tenant_id', '=', $tenant_id)->first();
                    $taxes = Tax::where('tenant_id', '=', $tenant_id)->first();
                    $cleaning_pricing = JobsCleaningPricing::select('price', 'tax_id')->where('tenant_id', '=', $tenant_id)
                        ->where('bedrooms', '=', $vbedrooms)
                        ->where('bathrooms', '=', $vbathrooms)
                        ->where('carpet', '=', $vcarpet)
                        ->where('storey', '=', $vstorey)->first();
                    if ($cleaning_pricing) {
                        $taxes = Tax::where('id', '=', $cleaning_pricing->tax_id)->first();
                        $total_cost = $cleaning_pricing->price * (1 + $taxes->rate_percent / 100);
                    }

                    $extras = $step2_ary['extras'];
                    if (is_array($extras) && count($extras) > 0) {
                        foreach ($extras as $key => $val) {
                            if (intval($val) > 0) {
                                $product = Products::where('tenant_id', '=', $view_data['tenant_id'])->where('id', '=', $key)->first();
                                if ($product) {
                                    $taxes = Tax::where(['id' => $product->tax_id, 'tenant_id' => $tenant_id])->first();
                                    $total_cost += floatval($product->price) * intval($val) * (1 + floatval($taxes->rate_percent) / 100);
                                }
                            }
                        }
                    }

                    $shift = JobsCleaningShifts::where('tenant_id', '=', $request['tenant_id'])->where('id', '=', $step1_ary['start_time'])->first();
                    $view_data['total_cost'] = number_format((float)$total_cost, 2, '.', '');
                    $view_data['bed'] = $vbedrooms;
                    $view_data['bath'] = $vbathrooms;
                    $view_data['date'] = $step1_ary['date'];
                    $view_data['time'] = $shift->shift_display_start_time;
                }
            }

            $view_data['cleaning_form_setup'] = JobsCleaningQuoteFormSetup::where('tenant_id', '=', $tenant_id)->where('servicing_city_id', '=', $view_data['city_id'])->where('job_type_id', '=', '2')->first();
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
            $view_data['discount'] = '0';
            $view_data['something'] = 'wrong';
        }

        $view_data['tenant_api_details'] = TenantApiDetail::where(['tenant_id' => $request['tenant_id'], 'provider' => 'GoogleMaps'])->first();
        return view('quote-lease-cleaning.index', $view_data);
    }


    public function payLater()
    {
        $session_data = array('step1' => array(), 'step2' => array(), 'step3' => array(), 'step4' => array());
        $request = Input::get();
        $view_data['tenant_id'] = $request['tenant_id'];
        $view_data['company_id'] = $request['company_id'];
        $view_data['city_id'] = $request['city_id'];
        $view_data['discount'] = $request['discount'];

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
            ->where('crm_contact_details.detail', '=', $step4_ary['email'])->first();

        $tenant_api_details = TenantApiDetail::where(['tenant_id' => $view_data['tenant_id'], 'provider' => 'Xero'])->first();

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

        $cleaning_form_setup = JobsCleaningQuoteFormSetup::where('tenant_id', '=', $view_data['tenant_id'])->where('servicing_city_id', '=', $view_data['city_id'])->where('job_type_id', '=', '2')->first();
        // $pipeline_status = CRMOpPipelineStatuses::select('pipeline_status')->where('tenant_id', '=', $view_data['tenant_id'])->where('id', '=', $cleaning_form_setup->quoted_op_status_id)->first();
        $pipeline_status = CRMOpPipelineStatuses::select('pipeline_status')->where('tenant_id', '=', $view_data['tenant_id'])->where('sort_order', '=', '1')->first();

        $obj = new CRMOpportunities();
        $obj->lead_id = $lead_id;
        $obj->op_type = 'Cleaning';
        $obj->op_status = $pipeline_status->pipeline_status;
        $obj->est_job_date = $step1_ary['date'];
        $obj->op_frequency = 'One-time';
        $obj->tenant_id = $view_data['tenant_id'];
        $obj->save();

        $opportunity_id = $obj->id;

        $res = JobsCleaning::select(DB::raw('MAX(job_number) as max_job_number'))->where('tenant_id', '=', $view_data['tenant_id'])->first();
        $new_job_number = intval($res->max_job_number) + 1;

        $tmp_ary = @explode(' ', $step1_ary['bedrooms']);
        $step1_ary['bedrooms'] = trim($tmp_ary[0]);
        $tmp_ary = @explode(' ', $step1_ary['bathrooms']);
        $step1_ary['bathrooms'] = trim($tmp_ary[0]);

        $obj = new JobsCleaning();
        $obj->company_id = $view_data['company_id'];
        $obj->opportunity = 'Y';
        $obj->job_status = 'New';
        $obj->crm_opportunity_id = $opportunity_id;
        $obj->job_number = $new_job_number;
        $obj->customer_id = $lead_id;
        $obj->job_date = $step1_ary['date'];
        $obj->preferred_time_range = $step1_ary['start_time'];
        $obj->stories = ($step1_ary['story']=='N')?0:1;
        $obj->address = $step1_ary['cleaning_address'];
        $obj->carpeted = $step1_ary['carpeted'];
        $obj->bedrooms = intval($step1_ary['bedrooms']);
        $obj->bathrooms = intval($step1_ary['bathrooms']);
        $obj->tenant_id = $view_data['tenant_id'];
        $obj->job_type_id = '2';
        $obj->save();

        $job_cleaning_id = $obj->job_id;

        // $obj = new Invoice();
        // $obj->tenant_id = $view_data['tenant_id'];
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

        $tmp_ary = @explode(' ', $step1_ary['bedrooms']);
        $step1_ary['bedrooms'] = trim($tmp_ary[0]);
        $tmp_ary = @explode(' ', $step1_ary['bathrooms']);
        $step1_ary['bathrooms'] = trim($tmp_ary[0]);

        $vstorey = $step1_ary['story'];
        $vcarpet = $step1_ary['carpeted'];
        $vbedrooms = $step1_ary['bedrooms'];
        $vbathrooms = $step1_ary['bathrooms'];

        $view_data['organisation_settings'] = OrganisationSettings::where('tenant_id', '=', $view_data['tenant_id'])->first();
        $taxes = Tax::where('tenant_id', '=', $view_data['tenant_id'])->first();
        $cleaning_pricing = JobsCleaningPricing::select('price', 'tax_id')->where('tenant_id', '=', $view_data['tenant_id'])
            ->where('bedrooms', '=', $vbedrooms)
            ->where('bathrooms', '=', $vbathrooms)
            ->where('carpet', '=', $vcarpet)
            ->where('storey', '=', $vstorey)->first();
        if ($cleaning_pricing) {
            $taxes = Tax::where(['id' => $cleaning_pricing->tax_id, 'tenant_id' => $view_data['tenant_id']])->first();
            $total_sub_cost = $cleaning_pricing->price * (1 + $taxes->rate_percent / 100);
        }

        $extras = $step2_ary['extras'];
        if (is_array($extras) && count($extras) > 0) {
            foreach ($extras as $key => $val) {
                if (intval($val) > 0) {
                    $product = Products::where('tenant_id', '=', $view_data['tenant_id'])->where('id', '=', $key)->first();
                    $taxes = Tax::where(['id' => $product->tax_id, 'tenant_id' => $view_data['tenant_id']])->first();
                    $total_extra_cost += floatval($product->price) * intval($val) * (1 + floatval($taxes->rate_percent) / 100);
                }
            }
        }

        $total_cost = floatval($total_sub_cost) + floatval($total_extra_cost);
        if ($cleaning_pricing) {
            $taxes = Tax::where('id', '=', $cleaning_pricing->tax_id)->first();
        }

        //(jobs_cleaning_quote_form_setup.product_description)
        
        $obj = new Quotes();
        $obj->tenant_id = $view_data['tenant_id'];
        $obj->quote_number  = $new_job_number;
        $obj->crm_opportunity_id  = $opportunity_id;
        $obj->sys_job_type = 'Cleaning';
        $obj->job_id  = $job_cleaning_id;
        $obj->discount  = $view_data['discount'];      
        $obj->quote_date = Carbon::now();
        $obj->save();

        $quote_id = $obj->id;

        $obj = new QuoteItem();
        $obj->tenant_id = $view_data['tenant_id'];
        $obj->quote_id  = $quote_id;
        $obj->name = 'Main service';
        $obj->description = $cleaning_form_setup->product_description;
        $obj->type = 'Item';
        $obj->quantity = 1;
        $obj->unit_price = $total_sub_cost;
        $amount = (floatval($total_sub_cost) * (1 + $taxes->rate_percent / 100));
        $obj->amount = $amount;
        $obj->tax_id = ($cleaning_pricing->tax_id ?? 0);
        $obj->save();

        $extras = $step2_ary['extras'];
        if (is_array($extras) && count($extras) > 0) {
            foreach ($extras as $key => $val) {
                if (intval($val) > 0) {
                    $product = Products::where('tenant_id', '=', $view_data['tenant_id'])->where('id', '=', $key)->first();
                    if ($product) {
                        $taxes = Tax::where(['id' => $product->tax_id, 'tenant_id' => $view_data['tenant_id']])->first();

                        $obj = new QuoteItem();
                        $obj->tenant_id = $view_data['tenant_id'];
                        $obj->quote_id  = $quote_id;
                        $obj->name = 'Extra service';
                        $obj->description = $product->name;
                        $obj->type = $product->product_type;
                        $obj->unit_price = $product->price;
                        $obj->quantity = intval($val);
                        $amount = (floatval($product->price) * intval($val) * (1 + $taxes->rate_percent / 100));
                        $obj->amount = $amount;
                        $obj->tax_id = ($product->tax_id ?? 0);
                        $obj->save();
                    }
                }
            }
        }

        
        // $obj = new InvoiceItems();
        // $obj->tenant_id = $view_data['tenant_id'];
        // $obj->invoice_id  = $invoice_id;
        // $obj->item_name = 'Main service';
        // $obj->item_summary = 'End of Lease Cleaning';
        // $obj->type = 'item';
        // $obj->quantity = 1;
        // $obj->unit_price = $total_sub_cost;
        // $amount = (floatval($total_sub_cost) * (1 + $taxes->rate_percent / 100));
        // $obj->amount = $amount;
        // $obj->tax_id = ($cleaning_pricing->tax_id ?? 0);
        // $obj->save();

        $extra_questions = $step3_ary['question'];
        if (is_array($extra_questions) && count($extra_questions) > 0) {
            foreach ($extra_questions as $key => $val) {
                $qs = ListTypes::select('id', 'list_name')->where(['id' => $key, 'tenant_id' => $view_data['tenant_id']])->first();
                $ans = ListOptions::select('id', 'list_option')->where(['id' => $val, 'tenant_id' => $view_data['tenant_id']])->first();

                $obj = new JobsCleaningAdditionalInfo();
                $obj->tenant_id = $view_data['tenant_id'];
                $obj->job_id  = $job_cleaning_id;
                $obj->question = $qs->list_name;
                $obj->reply = $ans->list_option;
                $obj->save();
            }
        }

        // $extras = $step2_ary['extras'];
        // if (is_array($extras) && count($extras) > 0) {
        //     foreach ($extras as $key => $val) {
        //         if (intval($val) > 0) {
        //             $product = Products::where('tenant_id', '=', $view_data['tenant_id'])->where('id', '=', $key)->first();
        //             if ($product) {
        //                 $taxes = Tax::where('id', '=', $product->tax_id)->first();

        //                 $obj = new InvoiceItems();
        //                 $obj->tenant_id = $view_data['tenant_id'];
        //                 $obj->invoice_id  = $invoice_id;
        //                 $obj->item_name = 'Extra service';
        //                 $obj->item_summary = $product->name;
        //                 $obj->type = $product->product_type;
        //                 $obj->unit_price = $product->price;
        //                 $obj->quantity = intval($val);
        //                 $amount = (floatval($product->price) * intval($val) * (1 + $taxes->rate_percent / 100));
        //                 $obj->amount = $amount;
        //                 $obj->tax_id = ($product->tax_id ?? 0);
        //                 $obj->save();
        //             }
        //         }
        //     }
        // }

        $jobdate = $step1_ary['date'];
        $jobshift = $step1_ary['start_time'];

        // $cleaning_teams = JobsCleaningTeams::where('tenant_id', '=', $view_data['tenant_id'])
        //     ->where('job_type_id', '=', '2')
        //     ->where('active', '=', 'Y')
        //     ->orderBy('team_priority', 'ASC')->get();
        // if ($cleaning_teams) {
        //     foreach ($cleaning_teams as $team) {
        //         $roster = JobsCleaningTeamRoster::where('job_date', '=', $jobdate)
        //             ->where('team_id', '=', $team->id)
        //             ->where('job_type_id', '=', '2')
        //             ->where('job_shift_id', '=', $jobshift)->first();
        //         if ($roster) {
        //             // do nothing
        //         } else {
        //             $obj = new JobsCleaningTeamRoster();
        //             $obj->tenant_id = $view_data['tenant_id'];
        //             $obj->job_date  = $jobdate;
        //             $obj->job_type_id = '2';
        //             $obj->job_shift_id = $jobshift;
        //             $obj->job_hours = $cleaning_form_setup->min_hours;
        //             $obj->job_id  = $job_cleaning_id;
        //             $obj->team_id = $team->id;
        //             $obj->save();
        //             break;
        //         }
        //     }
        // }

        $view_data['new_job_number'] = $new_job_number;

        $view_data['step'] = '6';
        return view('quote-lease-cleaning.index', $view_data);
    }

    public function payNow()
    {
        $session_data = array('step1' => array(), 'step2' => array(), 'step3' => array(), 'step4' => array());
        $request = Input::get();
        $view_data['tenant_id'] = $request['tenant_id'];
        $view_data['company_id'] = $request['company_id'];
        $view_data['city_id'] = $request['city_id'];
        $view_data['total_cost'] = $request['total_cost'];
        $stripeToken = $request['stripeToken'];

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
            ->where('crm_contact_details.detail', '=', $step4_ary['email'])->first();

        $tenant_api_details = TenantApiDetail::where(['tenant_id' => $view_data['tenant_id'], 'provider' => 'Xero'])->first();

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

        $cleaning_form_setup = JobsCleaningQuoteFormSetup::where('tenant_id', '=', $view_data['tenant_id'])->where('servicing_city_id', '=', $view_data['city_id'])->where('job_type_id', '=', '2')->first();
        $pipeline_status = CRMOpPipelineStatuses::select('pipeline_status')->where('tenant_id', '=', $view_data['tenant_id'])->where('id', '=', $cleaning_form_setup->quoted_op_status_id)->first();

        $obj = new CRMOpportunities();
        $obj->lead_id = $lead_id;
        $obj->op_type = 'Cleaning';
        $obj->op_status = $pipeline_status->pipeline_status;
        $obj->est_job_date = $step1_ary['date'];
        $obj->op_frequency = 'One-time';
        $obj->tenant_id = $view_data['tenant_id'];
        $obj->save();

        $opportunity_id = $obj->id;

        $res = JobsCleaning::select(DB::raw('MAX(job_number) as max_job_number'))->where('tenant_id', '=', $view_data['tenant_id'])->first();
        $new_job_number = intval($res->max_job_number) + 1;

        $tmp_ary = @explode(' ', $step1_ary['bedrooms']);
        $step1_ary['bedrooms'] = trim($tmp_ary[0]);
        $tmp_ary = @explode(' ', $step1_ary['bathrooms']);
        $step1_ary['bathrooms'] = trim($tmp_ary[0]);

        $obj = new JobsCleaning();
        $obj->company_id = $view_data['company_id'];
        $obj->opportunity = 'N';
        $obj->job_status = 'New';
        $obj->crm_opportunity_id = $opportunity_id;
        $obj->job_number = $new_job_number;
        $obj->customer_id = $lead_id;
        $obj->job_date = $step1_ary['date'];
        $obj->preferred_time_range = $step1_ary['start_time'];
        $obj->address = $step1_ary['cleaning_address'];
        $obj->carpeted = $step1_ary['carpeted'];
        $obj->bedrooms = intval($step1_ary['bedrooms']);
        $obj->bathrooms = intval($step1_ary['bathrooms']);
        $obj->tenant_id = $view_data['tenant_id'];
        $obj->job_type_id = '2';
        $obj->save();

        $job_cleaning_id = $obj->job_id;

        $obj = new Invoice();
        $obj->tenant_id = $view_data['tenant_id'];
        $obj->job_id  = $job_cleaning_id;
        $obj->sys_job_type  = 'Cleaning';
        $obj->invoice_number = $new_job_number;
        $obj->issue_date = date('Y-m-d');
        $obj->due_date = date('Y-m-d');
        $obj->status  = 'paid';
        //------->  
        $obj->save();

        $invoice_id = $obj->id;

        $total_cost = 0;
        $total_sub_cost = 0;
        $total_extra_cost = 0;

        $tmp_ary = @explode(' ', $step1_ary['bedrooms']);
        $step1_ary['bedrooms'] = trim($tmp_ary[0]);
        $tmp_ary = @explode(' ', $step1_ary['bathrooms']);
        $step1_ary['bathrooms'] = trim($tmp_ary[0]);

        $vstorey = $step1_ary['story'];
        $vcarpet = $step1_ary['carpeted'];
        $vbedrooms = $step1_ary['bedrooms'];
        $vbathrooms = $step1_ary['bathrooms'];

        $view_data['organisation_settings'] = OrganisationSettings::where('tenant_id', '=', $view_data['tenant_id'])->first();
        $taxes = Tax::where('tenant_id', '=', $view_data['tenant_id'])->first();
        $cleaning_pricing = JobsCleaningPricing::select('price', 'tax_id')->where('tenant_id', '=', $view_data['tenant_id'])
            ->where('bedrooms', '=', $vbedrooms)
            ->where('bathrooms', '=', $vbathrooms)
            ->where('carpet', '=', $vcarpet)
            ->where('storey', '=', $vstorey)->first();
        if ($cleaning_pricing) {
            $taxes = Tax::where('id', '=', $cleaning_pricing->tax_id)->first();
            $total_sub_cost = $cleaning_pricing->price * (1 + $taxes->rate_percent / 100);
        }

        $extra_questions = $step3_ary['question'];
        if (is_array($extra_questions) && count($extra_questions) > 0) {
            foreach ($extra_questions as $key => $val) {
                $qs = ListTypes::select('id', 'list_name')->where(['id' => $key, 'tenant_id' => $view_data['tenant_id']])->first();
                $ans = ListOptions::select('id', 'list_option')->where(['id' => $val, 'tenant_id' => $view_data['tenant_id']])->first();

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
                    $taxes = Tax::where(['id' => $product->tax_id, 'tenant_id' => $view_data['tenant_id']])->first();
                    $total_extra_cost += floatval($product->price) * intval($val) * (1 + floatval($taxes->rate_percent) / 100);
                }
            }
        }

        $total_cost = floatval($total_sub_cost) + floatval($total_extra_cost);

        if ($cleaning_pricing) {
            $taxes = Tax::where(['id' => $cleaning_pricing->tax_id, 'tenant_id' => $view_data['tenant_id']])->first();
        }

        $obj = new InvoiceItems();
        $obj->tenant_id = $view_data['tenant_id'];
        $obj->invoice_id  = $invoice_id;
        $obj->item_name = 'Main service';
        $obj->item_summary = $cleaning_form_setup->product_description;
        $obj->type = 'Item';
        $obj->quantity = 1;
        $obj->unit_price = $total_sub_cost;
        $amount = (floatval($total_sub_cost) * (1 + $taxes->rate_percent / 100));
        $obj->amount = $amount;
        $obj->tax_id = ($cleaning_pricing->tax_id ?? 0);
        $obj->save();

        $extras = $step2_ary['extras'];
        if (is_array($extras) && count($extras) > 0) {
            foreach ($extras as $key => $val) {
                if (intval($val) > 0) {
                    $product = Products::where('tenant_id', '=', $view_data['tenant_id'])->where('id', '=', $key)->first();
                    $taxes = Tax::where(['id' => $product->tax_id, 'tenant_id' => $view_data['tenant_id']])->first();

                    $obj = new InvoiceItems();
                    $obj->tenant_id = $view_data['tenant_id'];
                    $obj->invoice_id  = $invoice_id;
                    $obj->item_name = 'Extra service';
                    $obj->item_summary = $product->name;
                    $obj->type = $product->product_type;
                    $obj->unit_price = $product->price;
                    $obj->quantity = intval($val);
                    $amount = (floatval($product->price) * intval($val) * (1 + $taxes->rate_percent / 100));
                    $obj->amount = $amount;
                    $obj->tax_id = ($product->tax_id ?? 0);
                    $obj->save();
                }
            }
        }

        $extra_questions = $step3_ary['question'];
        if (is_array($extra_questions) && count($extra_questions) > 0) {
            foreach ($extra_questions as $key => $val) {
                $qs = ListTypes::select('id', 'list_name')->where(['id' => $key, 'tenant_id' => $view_data['tenant_id']])->first();
                $ans = ListOptions::select('id', 'list_option')->where(['id' => $val, 'tenant_id' => $view_data['tenant_id']])->first();

                $obj = new JobsCleaningAdditionalInfo();
                $obj->tenant_id = $view_data['tenant_id'];
                $obj->job_id  = $job_cleaning_id;
                $obj->question = $qs->list_name;
                $obj->reply = $ans->list_option;
                $obj->save();
            }
        }

        // $obj = new Payment();
        // $obj->tenant_id = $view_data['tenant_id'];
        // $obj->invoice_id  = $invoice_id;
        // $obj->amount = $total_cost;
        // $obj->gateway  = 'Stripe';
        // // $obj->transaction_id   = null;
        // $obj->status  = 'complete';
        // $obj->save();
        //Add Payment 
        $response = $this->stripePayment($new_job_number, $invoice_id, $view_data['total_cost'], $view_data['tenant_id'], $step4_ary['email'], $stripeToken);
        if ($response['status'] == 0) {
            $view_data['status'] = 0;
            $view_data['error_msg'] = $response['msg'];
        } else {
            $view_data['status'] = 1;
        }
        //--->

        $jobdate = $step1_ary['date'];
        $jobshift = $step1_ary['start_time'];

        $cleaning_teams = JobsCleaningTeams::where('tenant_id', '=', $view_data['tenant_id'])
            ->where('job_type_id', '=', '2')
            ->where('active', '=', 'Y')
            ->orderBy('team_priority', 'ASC')->get();
        if ($cleaning_teams) {
            foreach ($cleaning_teams as $team) {
                $roster = JobsCleaningTeamRoster::where('job_date', '=', $jobdate)
                    ->where('team_id', '=', $team->id)
                    ->where('job_type_id', '=', '2')
                    ->where('job_shift_id', '=', $jobshift)->first();
                if ($roster) {
                    // do nothing
                } else {
                    $obj = new JobsCleaningTeamRoster();
                    $obj->tenant_id = $view_data['tenant_id'];
                    $obj->job_date  = $jobdate;
                    $obj->job_type_id = '2';
                    $obj->job_shift_id = $jobshift;
                    $obj->job_hours = $cleaning_form_setup->min_hours;
                    $obj->job_id  = $job_cleaning_id;
                    $obj->team_id = $team->id;
                    $obj->save();
                    break;
                }
            }
        }
        $view_data['new_job_number'] = $new_job_number;

        $view_data['step'] = '7';
        return view('quote-lease-cleaning.index', $view_data);
    }

    public function ajaxStartTime()
    {
        $request = Input::get();
        $output = ['options' => ''];
        $found = false;

        if (isset($request['date']) && !empty($request['date'])) {

            $new_date = @explode('/', $request['date']);
            $request['date'] = $new_date[2] . '-' . $new_date[1] . '-' . $new_date[0];

            $num_of_teams = JobsCleaningTeams::where('tenant_id', '=', $request['tenant_id'])->where('job_type_id', '=', '2')->where('active', '=', 'Y')->count('*');
            $shifts = JobsCleaningShifts::where('tenant_id', '=', $request['tenant_id'])->where('job_type_id', '=', '2')->get();
            if ($shifts) {
                foreach ($shifts as $shift) {
                    $num_of_rosters =  JobsCleaningTeamRoster::where('tenant_id', '=', $request['tenant_id'])->where('job_type_id', '=', '2')->where('job_date', '=', $request['date'])->where('job_shift_id', '=', $shift->id)->count('*');
                    if (intval($num_of_rosters) < intval($num_of_teams)) {
                        $output['options'] .= '<option value="' . $shift->id . '">' . $shift->shift_display_start_time . '</option>';
                        $found = true;
                    }
                }
            }

            if (!$found) {
                $output['options'] = '<option value="">No slots available for the day</option>';
            }
        }
        return response()->json($output);
    }

    public function sess()
    {
        // $data = Session::all();
        // dd($data);
    }

    private function stripePayment($job_number, $invoice_id, $total_cost, $tenant_id, $stripeEmail, $stripeToken)
    {
        // Stripe API secret key
        $secret_key = env('STRIPE_SECRET');
        Stripe::setApiKey($secret_key);

        $tenant_api_details = TenantApiDetail::where(['tenant_id' => $tenant_id, 'provider' => 'Stripe'])->first();
        if (!$tenant_api_details) {
            $response = array(
                'status' => 0,
                'msg' => 'Stripe account is not connected!'
            );
            return $response;
        }
        //-----------------
        $invoice = Invoice::where('id', '=', $invoice_id)->first();

        $response = array();
        // Check whether stripe token is not empty
        if (!empty($stripeToken)) {

            // Get token, card and item info
            $token  = $stripeToken;
            $email  = $stripeEmail;

            if (isset($invoice->stripe_one_off_customer_id) && !empty($invoice->stripe_one_off_customer_id)) {
                $stripeCustomerId = $invoice->stripe_one_off_customer_id;
                $old_customer = 1;
            } else {
                try {
                    // Add customer to stripe
                    $customer = \Stripe\Customer::create(array(
                        'email' => $email,
                        'source'  => $token
                    ), ['stripe_account' => $tenant_api_details->variable1]);
                    $stripeCustomerId = $customer->id;
                } catch (\Stripe\Error\OAuth\OAuthBase $e) {
                    $response = array(
                        'status' => 0,
                        'msg' => $e->getMessage()
                    );
                    return $response;
                }
                $old_customer = 0;
            }
            try {
                // Charge a credit or a debit card
                $charge = \Stripe\Charge::create(array(
                    'customer' => $stripeCustomerId,
                    'amount'   => $total_cost * 100,
                    'currency' => 'AUD',
                    //'source'  => $token,
                    'description' => 'Amount deposit for job number ' . $job_number,
                ), ['stripe_account' => $tenant_api_details->variable1]);
            } catch (\Stripe\Error\OAuth\OAuthBase $e) {
                $response = array(
                    'status' => 0,
                    'msg' => $e->getMessage()
                );
                return $response;
            }
            // Retrieve charge details
            $chargeJson = $charge->jsonSerialize();

            // Check whether the charge is successful
            if ($chargeJson['amount_refunded'] == 0 && empty($chargeJson['failure_code']) && $chargeJson['paid'] == 1 && $chargeJson['captured'] == 1) {
                // Order details 
                $amount = $chargeJson['amount'];
                $currency = $chargeJson['currency'];
                $txnID = $chargeJson['balance_transaction'];
                $status = $chargeJson['status'];
                $transactionID = $chargeJson['id'];
                $payerName = $chargeJson['source']['name'];

                // If payment succeeded
                if ($status == 'succeeded') {
                    if ($invoice) {
                        //Add Invoice Payment
                        $payment = new Payment();
                        $payment->tenant_id = $tenant_id;
                        $payment->invoice_id = $invoice->id;
                        $payment->gateway = 'Stripe';
                        $payment->transaction_id = $transactionID;
                        $payment->remarks = 'Job confirmation payment';
                        $payment->amount = $total_cost;
                        $payment->paid_on = Carbon::now();
                        $payment->created_at = Carbon::now();
                        $payment->save();
                        // 
                    }
                    $totalAmount = InvoiceItems::where(['invoice_id' => $invoice->id, 'tenant_id' => $tenant_id])->sum('amount');
                    $paidAmount = Payment::where(['invoice_id' => $invoice->id, 'tenant_id' => $tenant_id])->sum('amount');
                    if ($paidAmount < $totalAmount) {
                        $invoice->status = 'partial';
                    } elseif ($paidAmount == $totalAmount) {
                        $invoice->status = 'paid';
                    }
                    if ($old_customer == 0) {
                        $invoice->stripe_one_off_customer_id = $stripeCustomerId;
                    }
                    //Update Invoice Status
                    $invoice->save();

                    $response = array(
                        'status' => 1,
                        'is_redirect' => 0,
                        'msg' => 'Your payment was successful.',
                        'txnData' => $chargeJson
                    );
                } else {
                    $response = array(
                        'status' => 0,
                        'msg' => 'Transaction has been failed.'
                    );
                }
            } else {
                $response = array(
                    'status' => 0,
                    'msg' => 'Transaction has been failed.'
                );
            }
        } else {
            $response = array(
                'status' => 0,
                'msg' => 'Form submission error...'
            );
        }
        // Return response
        return $response;
    }
}
