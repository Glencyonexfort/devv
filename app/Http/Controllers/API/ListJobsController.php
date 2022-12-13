<?php

namespace App\Http\Controllers\API;

use App\Companies;
use App\CRMActivityLog;
use App\CRMActivityLogAttachment;
use App\CRMContactDetail;
use App\CRMContacts;
use App\CRMLeads;
use App\CustomerDetails;
use App\DailyDriverVehicleCheck;
use App\DailyDriverVehicleCheckDetails;
use App\Http\Controllers\Admin\VehiclesDailyChecklistController;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Invoice;
use App\InvoiceItems;
use App\InvoiceItemsForApproval;
use App\InvoiceSetting;
use App\JobsCleaning;
use App\JobsCleaningTeamMembers;
use App\JobsCleaningTeamRoster;
use App\JobsMoving;
use App\JobsMovingChecklist;
use App\JobsMovingInventory;
use App\JobsMovingLegs;
use App\JobsMovingLegsTeam;
use App\JobsMovingLegTrips;
use App\JobsMovingOHSChecklist;
use App\Mail\sendMail;
use App\MovingInventoryDefinitions;
use App\MovingInventoryGroups;
use App\OfflinePaymentMethod;
use App\OHSChecklist;
use App\OrganisationSettings;
use App\Payment;
use App\PplPeople;
use App\Product;
use App\Products;
use App\QuoteItem;
use App\Quotes;
use App\Setting;
use App\SysNotificationSetting;
use App\Tax;
use App\TenantApiDetail;
use App\User;
use App\VehicleChecklistDefinition;
use App\VehicleChecklistGroup;
use App\Vehicles;
use Carbon\Carbon;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;
use Stripe\Stripe;

class ListJobsController extends BaseController
{
    /**
     * Start Job.
     */
    public function startJob(Request $request)
    {
        try {
            $sys_job_type = $request->sys_job_type;
            if ($sys_job_type == "Moving") {
                //Get requested parameters
                $leg_id = $request->leg_id;
                $actual_start_location = $request->actual_start_location;
                $leg = JobsMovingLegs::where('id', $leg_id)->first();
                $organization_setting = OrganisationSettings::where('tenant_id', $leg->tenant_id)->first();

                //update records
                $data = JobsMovingLegs::where('id', $leg_id)
                    ->update([
                        'actual_start_time' => Carbon::createFromFormat('Y-m-d H:i:s', now())->setTimezone($organization_setting->timezone),
                        'actual_start_location' => $actual_start_location,
                        'leg_status' => 'Picked',
                    ]);
            } elseif ($sys_job_type == "Cleaning") {
                $job_id = $request->job_id;
                //update records
                $data = JobsCleaningTeamRoster::where('job_id', $job_id)
                    ->update([
                        'actual_start_time' => Carbon::now(),
                    ]);
            }
            if (!$data) {
                return $this->sendError('notFound', 'Records not found.');
            }
            return $this->sendResponse($data, 'Job started successfully');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    /**
     * Complete Job.
     */
    public function completeJob(Request $request)
    {
        try {
            $sys_job_type = $request->sys_job_type;
            if ($sys_job_type == "Moving") {
                //Get requested parameters
                $leg_id = $request->leg_id;
                $actual_finish_location = $request->actual_finish_location;
                $leg = JobsMovingLegs::where('id', $leg_id)->first();
                $organization_setting = OrganisationSettings::where('tenant_id', $leg->tenant_id)->first();
                //update records
                $data = JobsMovingLegs::where('id', $leg_id)
                    ->update([
                        'actual_finish_time' => Carbon::createFromFormat('Y-m-d H:i:s', now())->setTimezone($organization_setting->timezone),
                        'actual_finish_location' => $actual_finish_location,
                        'leg_status' => 'Delivered',
                    ]);
            } elseif ($sys_job_type == "Cleaning") {
                $job_id = $request->job_id;
                //update records
                $data = JobsCleaningTeamRoster::where('job_id', $job_id)
                    ->update([
                        'actual_finish_time' => Carbon::now(),
                    ]);
            }
            if (!$data) {
                return $this->sendError('notFound', 'Records not found.');
            }
            return $this->sendResponse($data, 'Job completed successfully');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    /**
     * Accept Job.
     */
    public function acceptJob(Request $request)
    {
        //try{
        //Get requested parameters
        $sys_job_type = $request->sys_job_type;
        if ($sys_job_type == "Moving") {
            if (empty($request->leg_id) || $request->leg_id <= 0) {
                return $this->sendError('notValid', 'Leg ID is not valid.');
            }
            $leg_id = $request->leg_id;
            //update records
            $leg = JobsMovingLegs::where('id', '=', $leg_id)->first();
            $leg->leg_status = 'Confirmed';
            $leg->save();
            //Email Notification
            $email_temp = SysNotificationSetting::where('id', '=', 3)->first(); //
            if ($email_temp) {
                if ($email_temp->send_email == 'Y') {
                    $job = JobsMoving::select('jobs_moving.*')
                        ->join('jobs_moving_legs', 'jobs_moving_legs.job_id', '=', 'jobs_moving.job_id')
                        ->where('jobs_moving_legs.id', '=', $leg_id)->first();
                    if ($job) {
                        $company_email = DB::table('companies')->where('id', '=', $job->company_id)->pluck('email')->first();
                        if ($company_email != "") {
                            $user_name = "";
                            $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', $leg->tenant_id)->first();
                            $this->tenant_api_details = \App\TenantApiDetail::where(['tenant_id' => $leg->tenant_id, 'provider' => 'PostMarkApp'])->first();

                            $user = PplPeople::select('user_id')->where('id', '=', $leg->driver_id)->first();
                            if ($user) {
                                $user_name = User::where('id', '=', $user->user_id)->pluck('name')->first();
                            }

                            $params = [
                                'driver_name' => $user_name,
                                'job_number' => $job->job_number,
                            ];
                            $subject = $email_temp->notification_subject;
                            if (preg_match_all("/{(.*?)}/", $subject, $m)) {
                                foreach ($m[1] as $i => $varname) {
                                    $subject = str_replace($m[0][$i], sprintf('%s', $params[$varname]), $subject);
                                }
                            }
                            $template = $email_temp->notification_message;
                            if (preg_match_all("/{(.*?)}/", $template, $m)) {
                                foreach ($m[1] as $i => $varname) {
                                    $template = str_replace($m[0][$i], sprintf('%s', $params[$varname]), $template);
                                }
                            }

                            $email_data['lead_id'] = $job->customer_id;
                            $email_data['from_email'] = $this->tenant_api_details->from_email;
                            $email_data['from_name'] = $this->organisation_settings->company_name;
                            $email_data['reply_to'] = $this->tenant_api_details->from_email;
                            $email_data['to'] = $company_email;
                            $email_data['email_subject'] = $subject;
                            $email_data['email_body'] = $template;
                            Config::set('mail.username', $this->tenant_api_details->smtp_user);
                            Config::set('mail.password', $this->tenant_api_details->smtp_secret);
                            Mail::to($email_data['to'])->send(new sendMail($email_data));

                        }
                    }
                }
            }
            if (!$leg) {
                return $this->sendError('notFound', 'Records not found.');
            }
            return $this->sendResponse($leg, 'Job accepted successfully');

        } elseif ($sys_job_type == "Cleaning") {
            $job_id = $request->job_id;
            //update records
            $data = JobsCleaningTeamRoster::where('job_id', $job_id)
                ->update([
                    'roster_status' => 'Confirmed',
                ]);
            $email_temp = SysNotificationSetting::where('id', '=', 4)->first(); //
            if ($email_temp) {
                if ($email_temp->send_email == 'Y') {
                    $job = JobsCleaning::where('job_id', '=', $job_id)->first();
                    if ($job) {
                        $company_email = DB::table('companies')->where('id', '=', $job->company_id)->pluck('email')->first();
                        if ($company_email != "") {
                            $user_name = "";
                            $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', $job->tenant_id)->first();
                            $this->tenant_api_details = \App\TenantApiDetail::where(['tenant_id' => $job->tenant_id, 'provider' => 'PostMarkApp'])->first();

                            $team = JobsCleaningTeamRoster::select('jobs_cleaning_team_roster.team_id')
                                ->where('jobs_cleaning_team_roster.job_id', '=', $job_id)->first();
                            if ($team) {
                                $user = JobsCleaningTeamMembers::select('ppl_people.user_id')
                                    ->join('ppl_people', 'ppl_people.id', '=', 'jobs_cleaning_team_members.person_id')
                                    ->where('jobs_cleaning_team_members.team_id', '=', $team->team_id)->first();
                                if ($user) {
                                    $user_name = User::where('id', '=', $user->user_id)->pluck('name')->first();
                                }
                            }

                            $params = [
                                'team_lead_name' => $user_name,
                                'job_number' => $job->job_number,
                            ];
                            $subject = $email_temp->notification_subject;
                            if (preg_match_all("/{(.*?)}/", $subject, $m)) {
                                foreach ($m[1] as $i => $varname) {
                                    $subject = str_replace($m[0][$i], sprintf('%s', $params[$varname]), $subject);
                                }
                            }
                            $template = $email_temp->notification_message;
                            if (preg_match_all("/{(.*?)}/", $template, $m)) {
                                foreach ($m[1] as $i => $varname) {
                                    $template = str_replace($m[0][$i], sprintf('%s', $params[$varname]), $template);
                                }
                            }

                            $email_data['lead_id'] = $job->customer_id;
                            $email_data['from_email'] = $this->tenant_api_details->from_email;
                            $email_data['from_name'] = $this->organisation_settings->company_name;
                            $email_data['reply_to'] = $this->tenant_api_details->from_email;
                            $email_data['to'] = $company_email;
                            $email_data['email_subject'] = $subject;
                            $email_data['email_body'] = $template;
                            Config::set('mail.username', $this->tenant_api_details->smtp_user);
                            Config::set('mail.password', $this->tenant_api_details->smtp_secret);
                            Mail::to($email_data['to'])->send(new sendMail($email_data));

                        }
                    }
                }
            }
            if (!$data) {
                return $this->sendError('notFound', 'Records not found.');
            }
            return $this->sendResponse($data, 'Job accepted successfully');
        }

        // } catch (\Exception $ex) {
        //     return $this->sendError('Exception', $ex->getMessage());
        // }
    }

    /**
     * Job list Data.
     */
    public function getJobList(Request $request)
    {
        try {
            //Get requested parameters
            $user_id = $request->user_id;
            $sys_job_type = $request->sys_job_type;
            $job_status = $request->job_status;

            if ($sys_job_type == "Moving") {
                //get records
                $data = JobsMoving::select(
                    'jobs_moving_legs.id as leg_id',
                    'jobs_moving.job_number',
                    'jobs_moving.job_id',
                    'jobs_moving_legs.leg_date as job_date',
                    'jobs_moving.customer_id as lead_id',
                    'crm_leads.name as customer_name',
                    'jobs_moving_legs.pickup_address as pickup_address',
                    'jobs_moving_legs.est_start_time as start_time',
                    'jobs_moving_legs.est_finish_time as finish_time',
                    'jobs_moving_legs_team.driver as is_driver',
                    DB::raw('TIMEDIFF(jobs_moving_legs.est_finish_time,jobs_moving_legs.est_start_time) as duration')
                )
                    ->leftjoin('jobs_moving_legs', 'jobs_moving_legs.job_id', 'jobs_moving.job_id')
                    ->leftjoin('crm_leads', 'crm_leads.id', 'jobs_moving.customer_id')
                    ->join('jobs_moving_legs_team', 'jobs_moving_legs_team.leg_id', 'jobs_moving_legs.id')
                    ->where(['jobs_moving_legs_team.people_id' => $user_id, 'jobs_moving.deleted' => 0])
                    ->when($job_status, function ($query) use ($job_status) {
                        
                    })
                    ->when($job_status, function ($query) use ($job_status) {
                        if ($job_status == 'Confirmed') {
                            $query->where('jobs_moving_legs.leg_status','!=', 'Awaiting Confirmation');
                            return $query->where(DB::raw('DATE(jobs_moving_legs.leg_date)'), '>=', Carbon::today()->toDateString());
                            //return $query->whereIn('jobs_moving_legs.leg_status', ['Confirmed', 'Picked', 'Delivered']);
                        } elseif($job_status == 'Completed'){
                                $query->where('jobs_moving_legs.leg_status','!=', 'Awaiting Confirmation');
                                return $query->where(DB::raw('DATE(jobs_moving_legs.leg_date)'), '<', Carbon::today()->toDateString());
                        } elseif ($job_status == 'Awaiting Confirmation') {
                                $query->where('jobs_moving_legs.leg_status', $job_status);
                                return $query->where(DB::raw('DATE(jobs_moving_legs.leg_date)'), '>=', Carbon::today()->toDateString());
                        }
                    })
                    ->orderBy('jobs_moving_legs.leg_date', 'ASC')
                    ->orderBy('jobs_moving_legs.est_start_time', 'ASC')
                    ->get();
            } elseif ($sys_job_type == "Cleaning") {
                //get records
                $team_id = JobsCleaningTeamMembers::where(['person_id' => $user_id])->pluck('team_id')->first();
                if ($team_id) {
                    $data = JobsCleaning::select(
                        'jobs_cleaning.job_number',
                        'jobs_cleaning.job_id',
                        'jobs_cleaning.job_date as job_date',
                        'jobs_cleaning.address as address',
                        'jobs_cleaning_team_roster.actual_start_time as actual_start_time',
                        'jobs_cleaning_team_roster.actual_finish_time as actual_finish_time',
                        'jobs_cleaning_shifts.shift_display_start_time as shift_display_start_time',
                        'crm_leads.name as customer_name'
                    )
                        ->leftjoin('jobs_cleaning_shifts', 'jobs_cleaning_shifts.job_type_id', 'jobs_cleaning.job_type_id')
                        ->leftjoin('jobs_cleaning_team_roster', 'jobs_cleaning_team_roster.job_id', 'jobs_cleaning.job_id')
                        ->leftjoin('crm_leads', 'crm_leads.id', 'jobs_cleaning.customer_id')
                        ->where(DB::raw('DATE(jobs_cleaning.job_date)'), $datefilter, Carbon::today()->toDateString())
                        ->where('jobs_cleaning_team_roster.team_id', $team_id)
                        ->where('jobs_cleaning_team_roster.roster_status', '=', $job_status)
                        ->groupBy('jobs_cleaning.job_id')
                        ->orderBy('jobs_cleaning.job_date', 'ASC')
                        ->get();
                } else {
                    return $this->sendError('NotFound', 'Team ID not found against user');
                }
            }
            if (!$data || count($data) <= 0) {
                return $this->sendError('notFound', 'Records not found.');
            }
            return $this->sendResponse($data, 'Job List.');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    /**
     * Job detail Data.
     */
    public function getJobDetail(Request $request)
    {
        try {
            $sys_job_type = $request->sys_job_type;
            if ($sys_job_type == "Moving") {
                if (empty($request->job_id) || $request->job_id <= 0) {
                    return $this->sendError('notValid', 'Job ID is not valid.');
                }
                if (empty($request->user_id) || $request->user_id <= 0) {
                    return $this->sendError('notValid', 'User ID is not valid.');
                }
                if (empty($request->leg_id) || $request->leg_id <= 0) {
                    return $this->sendError('notValid', 'Leg ID is not valid.');
                }
                //Get requested parameters
                $job_id = $request->job_id;
                $leg_id = $request->leg_id;
                $user_id = $request->user_id;

                //get records
                $job = JobsMoving::select(
                    'job_number',
                    'company_id',
                    'pickup_access_restrictions',
                    'drop_off_access_restrictions',
                    'customer_id',
                    'total_cbm'
                )
                    ->where('job_id', $job_id)
                    ->first();

                $leg = JobsMovingLegs::select('*')->where('id', $leg_id)->first();
                $is_driver = JobsMovingLegsTeam::where(['leg_id'=> $leg_id, 'people_id'=>$user_id])->pluck('driver')->first();

                if (!$job || !$leg) {
                    return $this->sendError('notFound', 'Record not found.');
                }
                $company = Companies::where(['id' => $job->company_id, 'active' => 'Y'])->first();
                if ($company) {
                    $customer_sign_off_checklist = $company->customer_sign_off_checklist;
                    $customer_pre_job_checklist = $company->customer_pre_job_checklist;
                } else {
                    $customer_sign_off_checklist = "";
                    $customer_pre_job_checklist = "";
                }

                $vehicle = Vehicles::select('vehicle_name', 'license_plate_number')->where('id', $leg->vehicle_id)->first();
                $vehicle_name = '';
                $vehicle_registration = '';
                if ($vehicle) {
                    $vehicle_name = $vehicle->vehicle_name;
                    $vehicle_registration = $vehicle->license_plate_number;
                }
                $total_hours = $this->calculateTimeDuration($leg->actual_start_time, $leg->actual_finish_time);

                $lead_name = CRMLeads::where('id', $job->customer_id)->pluck('name')->first();

                $mobile = DB::table('crm_contacts')
                    ->leftjoin('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                    ->where(['crm_contacts.lead_id' => $job->customer_id, 'crm_contact_details.detail_type' => 'Mobile'])
                    ->pluck('detail')
                    ->first();

                $checklist = JobsMovingChecklist::select('checklist as list')->where('job_id', $job_id)->get();

                $logs = CRMActivityLogAttachment::select('crm_activity_log_attachments.id as attachment_id', 'crm_activity_log_attachments.attachment_type as name', 'crm_activity_log_attachments.attachment_content as content')
                    ->leftjoin('crm_activity_log', 'crm_activity_log.id', 'crm_activity_log_attachments.log_id')
                    ->where(['lead_id' => $job->customer_id, 'log_type' => 7])->get();

                $response_log = [];
                if ($logs) {
                    foreach ($logs as $log) {
                        $image_url = substr($log->content, strrpos($log->content, '/public') + 1);
                        $lg['attachment_id'] = $log->attachment_id;
                        $lg['name'] = $log->name;
                        $lg['content'] = url('/' . $image_url);
                        $response_log[] = $lg;
                    }
                }
                if ($leg->customer_sign != null) {
                    $customer_sign = substr($leg->customer_sign, strrpos($leg->customer_sign, '/public') + 1);
                    $customer_sign = url('/' . $customer_sign);
                } else {
                    $customer_sign = null;
                }

                if ($leg->customer_sign_pre_job != null) {
                    $customer_sign_pre_job = substr($leg->customer_sign_pre_job, strrpos($leg->customer_sign_pre_job, '/public') + 1);
                    $customer_sign_pre_job = url('/' . $customer_sign_pre_job);
                } else {
                    $customer_sign_pre_job = null;
                }

                $pickup_geo_location = explode(',', $leg->pickup_geo_location);
                $drop_off_geo_location = explode(',', $leg->drop_off_geo_location);

                $response['job_number'] = $job->job_number;
                $response['customer_name'] = $lead_name;
                $response['is_driver'] = $is_driver;
                $response['lead_id'] = $job->customer_id;
                $response['leg_status'] = $leg->leg_status;
                $response['has_multiple_trips'] = $leg->has_multiple_trips;
                $response['pickup_address'] = $leg->pickup_address;
                $response['drop_off_address'] = $leg->drop_off_address;
                $response['est_start_time'] = $leg->est_start_time;
                $response['est_finish_time'] = $leg->est_finish_time;
                $response['actual_start_time'] = $leg->actual_start_time;
                $response['actual_finish_time'] = $leg->actual_finish_time;
                $response['total_hours'] = $total_hours;
                $response['description'] = $leg->notes;
                $response['pickup_access'] = $job->pickup_access_restrictions;
                $response['drop_off_access'] = $job->drop_off_access_restrictions;
                $response['pickup_geo_location'] = $pickup_geo_location;
                $response['drop_off_geo_location'] = $drop_off_geo_location;
                $response['mobile'] = $mobile;
                $response['vehicle_name'] = $vehicle_name;
                $response['vehicle_registration'] = $vehicle_registration;
                $response['checklist'] = $checklist;
                $response['crm_activity_log_attachments'] = $response_log;
                $response['customer_sign'] = $customer_sign;
                $response['customer_sign_off_checklist'] = $customer_pre_job_checklist;
                $response['customer_sign_pre_job'] = $customer_sign_pre_job;
                $response['customer_pre_job_checklist'] = $customer_sign_off_checklist;
                $response['total_cbm'] = $job->total_cbm;
            } elseif ($sys_job_type == "Cleaning") {
                if (empty($request->job_id) || $request->job_id <= 0) {
                    return $this->sendError('notValid', 'Job ID is not valid.');
                }
                //Get requested parameters
                $job_id = $request->job_id;

                $job = JobsCleaning::select(
                    'jobs_cleaning.*',
                    'jobs_cleaning_team_roster.actual_start_time as actual_start_time',
                    'jobs_cleaning_team_roster.actual_finish_time as actual_finish_time',
                    'jobs_cleaning_shifts.shift_display_start_time as shift_display_start_time',
                    'crm_leads.name as customer_name'
                )
                    ->leftjoin('jobs_cleaning_shifts', 'jobs_cleaning_shifts.job_type_id', 'jobs_cleaning.job_type_id')
                    ->leftjoin('jobs_cleaning_team_roster', 'jobs_cleaning_team_roster.job_id', 'jobs_cleaning.job_id')
                    ->leftjoin('crm_leads', 'crm_leads.id', 'jobs_cleaning.customer_id')
                    ->where('jobs_cleaning.job_id', $job_id)
                    ->first();

                $mobile = DB::table('crm_contacts')
                    ->leftjoin('crm_contact_details', 'crm_contacts.id', '=', 'crm_contact_details.contact_id')
                    ->where(['crm_contacts.lead_id' => $job->customer_id, 'crm_contact_details.detail_type' => 'Mobile'])
                    ->pluck('detail')
                    ->first();
                $clean_type = DB::table('sys_cleaning_job_types')
                    ->where(['id' => $job->job_type_id])
                    ->pluck('job_type_name')
                    ->first();
                $included_items = DB::table('invoices')
                    ->select('invoice_items.item_summary as description')
                    ->leftjoin('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                    ->where('invoices.job_id', $job->job_id)
                    ->where('invoices.sys_job_type', 'Cleaning')
                    ->where('invoice_items.item_name', 'Main service')
                    ->get();
                $extra_items = DB::table('invoices')
                    ->select('invoice_items.item_summary as description')
                    ->leftjoin('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                    ->where('invoices.job_id', $job->job_id)
                    ->where('invoices.sys_job_type', 'Cleaning')
                    ->where('invoice_items.item_name', 'Extra service')
                    ->get();

                $company = Companies::where(['id' => $job->company_id, 'active' => 'Y'])->first();
                if ($company) {
                    $customer_sign_off_checklist = $company->customer_sign_off_checklist;
                } else {
                    $customer_sign_off_checklist = "";
                }

                $logs = CRMActivityLogAttachment::select('crm_activity_log_attachments.id as attachment_id', 'crm_activity_log_attachments.attachment_type as name', 'crm_activity_log_attachments.attachment_content as content')
                    ->leftjoin('crm_activity_log', 'crm_activity_log.id', 'crm_activity_log_attachments.log_id')
                    ->where(['lead_id' => $job->customer_id, 'log_type' => 7])->get();
                $response_log = [];
                if ($logs) {
                    foreach ($logs as $log) {
                        $image_url = substr($log->content, strrpos($log->content, '/public') + 1);
                        $lg['attachment_id'] = $log->attachment_id;
                        $lg['name'] = $log->name;
                        $lg['content'] = url('/' . $image_url);
                        $response_log[] = $lg;
                    }
                }

                $response['job_number'] = $job->job_number;
                $response['customer_name'] = $job->customer_name;
                $response['mobile'] = $mobile;
                $response['job_date'] = $job->job_date;
                $response['address'] = $job->address;
                $response['actual_start_time'] = $job->actual_start_time;
                $response['actual_finish_time'] = $job->actual_finish_time;
                $response['shift_display_start_time'] = $job->shift_display_start_time;
                $response['comments'] = $job->comments;
                $response['clean_type'] = $clean_type;
                $response['bedrooms'] = $job->bedrooms;
                $response['bathrooms'] = $job->bathrooms;
                $response['stories'] = $job->stories;
                $response['carpeted'] = $job->carpeted;
                $response['included_services'] = $included_items;
                $response['extra_services'] = $extra_items;
                $response['crm_activity_log_attachments'] = $response_log;
                $response['customer_sign_off_checklist'] = $customer_sign_off_checklist;
            }
            return $this->sendResponse($response, 'Job Detail');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    public function getPaymentDetails(Request $request)
    {
        try {
            //Get requested parameters
            $job_id = $request->job_id;
            $sys_job_type = $request->sys_job_type;
            if ($sys_job_type == "Moving") {
                $invoice = DB::table('invoices')
                    ->where('job_id', $job_id)
                    ->where('sys_job_type', 'Moving')
                    ->select('id', 'stripe_one_off_customer_id','regenerated')
                    ->first();

                //get paid amount
                $total = DB::table('invoice_items')
                    ->leftjoin('invoices', 'invoices.id', 'invoice_items.invoice_id')
                    ->where('invoices.id', $invoice->id)
                    ->where('invoices.sys_job_type', 'Moving')
                    ->sum('invoice_items.amount');

                //get pending amount
                $payment = DB::table('payments')
                    ->leftjoin('invoices', 'invoices.id', 'payments.invoice_id')
                    ->where('invoices.id', $invoice->id)
                    ->where('invoices.sys_job_type', 'Moving')
                    ->sum('payments.amount');

            } elseif ($sys_job_type == "Cleaning") {
                $invoice = DB::table('invoices')
                    ->where('job_id', $job_id)
                    ->where('sys_job_type', 'Cleaning')
                    ->select('id', 'stripe_one_off_customer_id','regenerated')
                    ->first();

                //get paid amount
                $total = DB::table('invoice_items')
                    ->leftjoin('invoices', 'invoices.id', 'invoice_items.invoice_id')
                    ->where('invoices.id', $invoice->id)
                    ->where('invoices.sys_job_type', 'Cleaning')
                    ->sum('invoice_items.amount');

                //get pending amount
                $payment = DB::table('payments')
                    ->leftjoin('invoices', 'invoices.id', 'payments.invoice_id')
                    ->where('invoices.id', $invoice->id)
                    ->where('invoices.sys_job_type', 'Cleaning')
                    ->sum('payments.amount');

            }

            if (isset($invoice->stripe_one_off_customer_id) && !empty($invoice->stripe_one_off_customer_id)) {
                $old_stripe_payment_detail = 'Y';
            } else {
                $old_stripe_payment_detail = 'N';
            }

            $total = number_format((float) $total, 2, '.', '');
            $payment = number_format((float) $payment, 2, '.', '');

            $pending_amount = number_format($total - $payment, 2, '.', '');

            if (!$total) {
                return $this->sendError('notFound', 'Records not found.');
            }
            $response = [
                'total_amount_inc_tax' => $total,
                'paid_amount' => $payment,
                'pending_amount' => $pending_amount,
                'invoice_id' => $invoice->id,
                'regenerated' => $invoice->regenerated,
                'old_stripe_payment_detail' => $old_stripe_payment_detail,
                'job_id' => $job_id,
            ];

            return $this->sendResponse($response, 'Payment Details.');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    public function getPaymentMethods(Request $request)
    {
        try {
            $tenant_id = $request->tenant_id;
            $tenant_api_details = TenantApiDetail::where(['tenant_id' => $tenant_id, 'provider' => 'Stripe'])->first();
            $response = [];
            if ($tenant_api_details) {
                $s['method'] = 'Stripe';
                $s['description'] = '<p>Stripe</p>';
                $response[] = $s;
            }
            $payment_methods = OfflinePaymentMethod::where('tenant_id', '=', $tenant_id)->get();
            foreach ($payment_methods as $method) {
                $m['method'] = $method->name;
                $m['description'] = $method->description;
                $response[] = $m;
                unset($m);
            }
            return $this->sendResponse($response, 'Payment Methods.');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    public function invoicePay(Request $request)
    {
        try {
            if (empty($request->invoice_id) || $request->invoice_id <= 0) {
                return $this->sendError('notValid', 'Invoice ID is not valid.');
            }
            if (empty($request->tenant_id) || $request->tenant_id <= 0) {
                return $this->sendError('notValid', 'Tenant ID is not valid.');
            }
            if (empty($request->amount) || $request->amount <= 0) {
                return $this->sendError('notValid', 'Amount should not be empty.');
            }
            $tenant_id = $request->tenant_id;
            $method = $request->method;
            $reference = $request->reference;
            $amount = $request->amount;
            $invoice_id = $request->invoice_id;

            $obj = new Payment();
            $obj->tenant_id = $tenant_id;
            $obj->invoice_id = $invoice_id;
            $obj->gateway = $method;
            $obj->remarks = $reference;
            $obj->amount = $amount;
            $obj->paid_on = Carbon::now();
            $obj->created_at = Carbon::now();
            $obj->save();
            return $this->sendResponse('Success', 'Payment has been done.');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }

    }    

    public function getProducts(Request $request)
    {
        try {
            //Get requested parameters
            $tenant_id = $request->tenant_id;
            $data = Products::select(
                'products.id as product_id',
                'products.name as item_name',
                'products.description as item_summary',
                'products.product_type as type',
                'products.price as unit_price',
                'products.tax_id',
                'taxes.rate_percent as tax_rate'
            )->leftjoin('taxes', 'taxes.id', 'products.tax_id')
                ->where('products.tenant_id', $tenant_id)
                ->where('products.active', 1)
                ->get();

            if (!$data || count($data) <= 0) {
                return $this->sendError('notFound', 'Records not found.');
            }
            return $this->sendResponse($data, 'Product List.');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    public function getOffsiders(Request $request)
    {
        try {
            //Get requested parameters
            $leg_id = $request->leg_id;
            $offsiders = JobsMovingLegsTeam::where(['leg_id'=> $leg_id])->get();
            if ($offsiders) {
                    $offsider_list = [];
                    foreach ($offsiders as $offsider) {
                        $user = PplPeople::where('id', "=", $offsider->people_id)->first();
                        if ($user) {
                            $s['people_id'] = $offsider->people_id;
                            $s['first_name'] = $user->first_name;
                            $s['last_name'] = $user->last_name;
                            $s['mobile'] = $user->mobile;
                            $s['is_driver'] = $offsider->driver;
                            $offsider_list[] = $s;
                            unset($s);
                        }

                    }
            } else {
                return $this->sendResponse('Success', 'No Offsider available.');
            }
            return $this->sendResponse($offsider_list, 'Offsider List.');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    public function getJobInventory(Request $request)
    {
        try {
            if (empty($request->job_id) || $request->job_id <= 0) {
                return $this->sendError('notValid', 'Job ID is not valid.');
            }
            if (empty($request->tenant_id) || $request->tenant_id <= 0) {
                return $this->sendError('notValid', 'Tenant ID is not valid.');
            }
            $job_id = $request->job_id;
            $tenant_id = $request->tenant_id;
            $inventory_groups = MovingInventoryGroups::where('tenant_id', '=', $tenant_id)->get();
            $getInventoryItems = MovingInventoryDefinitions::where('tenant_id', '=', $tenant_id)->get();

            if ($inventory_groups) {
                $groups = [];
                foreach ($inventory_groups as $group) {
                    $count = 0;
                    $inv = [];
                    foreach ($getInventoryItems as $item) {
                        if ($group->group_id == $item->group_id) {
                            $moving_inv = JobsMovingInventory::where('inventory_id', '=', $item->id)->where('job_id', '=', $job_id)->where('quantity', '>', 0)->first();
                            if ($moving_inv) {
                                $count++;
                                $i['name'] = $item->item_name;
                                $i['qty'] = $moving_inv->quantity;
                                $inv[] = $i;
                                unset($i);
                                unset($moving_inv);
                            }
                        }
                    }
                    $s['category'] = $group->group_name;
                    $s['count'] = $count;
                    $s['items'] = $inv;
                    $groups[] = $s;
                    unset($s);
                    unset($inv);
                }
                $other_count = 0;
                $o_inv = [];
                $other_inv = JobsMovingInventory::where('misc_item', 'Y')->where('job_id', '=', $job_id)->where('quantity', '>', 0)->get();
                if ($other_inv) {
                    foreach ($other_inv as $item) {
                        $other_count++;
                        $i['name'] = $item->misc_item_name;
                        $i['qty'] = $item->quantity;
                        $o_inv[] = $i;
                        unset($i);
                    }
                    $os['category'] = 'Miscellaneous Items';
                    $os['count'] = $other_count;
                    $os['items'] = $o_inv;
                    $groups[] = $os;
                }
            } else {
                return $this->sendError('notFound', 'Tenant ID is not valid.');
            }
            return $this->sendResponse($groups, 'Inventory List.');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    public function getJobTrips(Request $request)
    {
        try {
            //Get requested parameters
            if (empty($request->leg_id) || $request->leg_id <= 0) {
                return $this->sendError('notValid', 'Leg ID is not valid.');
            }
            $leg_id = $request->leg_id;
            $data = JobsMovingLegTrips::select(
                'trip_number',
                'pickup_address as from_location',
                'drop_off_address as to_location',
                'trip_notes'
            )
                ->where('jobs_moving_leg_id', $leg_id)
                ->get();

            if (!$data || count($data) <= 0) {
                return $this->sendError('notFound', 'Records not found.');
            }
            return $this->sendResponse($data, 'Trip List.');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }


    public function push()
    {
        $url = "https://fcm.googleapis.com/fcm/send";
        $token = "fVvuCXvGRTOqwyIR98vi4N:APA91bGmJwabrkPZeyunKEVMlZzm6zgizzVjGzc-hH0PCe2WvoHHeJ6cxcbkTRKv-NrfUEiwzqoh3a3HAGSogRBj4jxIuoCq5yXvAMATHR1FrZBaY6lR4iZYW6Gf5rxg0N4Kg6jx7yIR";
        $serverKey = 'AAAAuVVnmUM:APA91bFyFv7JWoyUWeeF7lIneyHk-MNmjnZ19rEHCX5h8Bf3s5_0EsKZKo-eXMpLWW2wXU0sgs45SzrRPsI-yi74Vh2aLy2DIa-N1LggLp35fx_AVuAaGebTv0aYHpD1i6rnBFQ1SxWZ';
        $title = "Hello Saqib";
        $body = "Ki haal Chal ay";
        $notification = array('title' => $title, 'body' => $body, 'sound' => 'default', 'badge' => '1');
        $arrayToSend = array('to' => $token, 'notification' => $notification, 'priority' => 'high');
        $json = json_encode($arrayToSend);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key=' . $serverKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //Send the request
        $response = curl_exec($ch);
        //Close request
        if ($response === false) {
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
    }
    /**
     * Get pending invoice items for approval
     */
    public function getPendingApprovalInvoiceItems(Request $request)
    {
        try {
            //Get requested parameters
            if (empty($request->invoice_id) || $request->invoice_id <= 0) {
                return $this->sendError('notValid', 'Invoice ID is not valid.');
            }
            if (empty($request->tenant_id) || $request->tenant_id <= 0) {
                return $this->sendError('notValid', 'Tenant ID is not valid.');
            }
            $invoice_id = $request->invoice_id;
            $tenant_id = $request->tenant_id;
            $response = [];
            $record = InvoiceItemsForApproval::where(['invoice_id' => $invoice_id, 'tenant_id' => $tenant_id, 'approved' => 'N'])->get();
            if ($record) {
                foreach ($record as $data) {
                    $tax_rate=NULL;
                    $tax_rate = Tax::where('id',$data->tax_id)->pluck('rate_percent')->first();
                    $d['id'] = $data->id;
                    $d['tenant_id'] = $data->tenant_id;
                    $d['invoice_id'] = $data->invoice_id;
                    $d['product_id'] = $data->product_id;
                    $d['item_name'] = $data->item_name;
                    $d['item_summary'] = $data->item_summary;
                    $d['type'] = $data->type;
                    $d['quantity'] = $data->quantity;
                    $d['unit_price'] = number_format((float) $data->unit_price, 2, '.', '');
                    $d['amount'] = number_format((float) $data->amount, 2, '.', '');
                    $d['tax_id'] = $data->tax_id;
                    $d['tax_rate'] = $tax_rate;
                    $d['approved'] = $data->approved;
                    $response[] = $d;
                }
            }
            if (!$response || count($response) <= 0) {
                return $this->sendError('notFound', 'Records not found.');
            }
            return $this->sendResponse($response, 'Pending Approval Invoice Item List.');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }
    /**
     * Send Invoice Items for Approval
     */
    public function sendForApprovalAndPayment(Request $request)
    {
        //try{
        if (empty($request->invoice_id) || $request->invoice_id <= 0) {
            return $this->sendError('notValid', 'Invoice ID is not valid.');
        }
        if (empty($request->tenant_id) || $request->tenant_id <= 0) {
            return $this->sendError('notValid', 'Tenant ID is not valid.');
        }
        $items = $request->items;
        $invoice_id = $request->invoice_id;
        $tenant_id = $request->tenant_id;

        $invoice = Invoice::where('id', '=', $invoice_id)->first();
        $email_temp = SysNotificationSetting::where('id', '=', 13)->first(); //

        if ($invoice->sys_job_type == "Moving") {
            $job = DB::table('jobs_moving')->where('job_id', '=', $invoice->job_id)->first();
        } elseif ($invoice->sys_job_type == "Cleaning") {
            $job = DB::table('jobs_cleaning')->where('job_id', '=', $invoice->job_id)->first();
        }

        $lead = DB::table('crm_leads')->where('id', '=', $job->customer_id)->first();
        $crm_contacts = CRMContacts::where('lead_id', '=', $lead->id)->first();
        $cust_email = null;
        if ($crm_contacts) {
            $cust_email = CRMContactDetail::select('crm_contact_details.detail')
                ->where(['crm_contact_details.contact_id' => $crm_contacts->id, 'crm_contact_details.detail_type' => 'Email'])
                ->first();
            if ($cust_email) {
                $cust_email = $cust_email->detail;
            }
        }
        //Delete old data
        InvoiceItemsForApproval::where('invoice_id', '=', $invoice_id)->where('tenant_id', '=', $tenant_id)->delete();
        //--->
        if (isset($items)) {
            foreach ($items as $item) {
                if ($item['quantity'] > 0) {
                    if (!isset($item['price']) || empty($item['price']) || $item['price'] <= 0) {
                        $unit_price = $item['unit_price'];
                    }else{
                        $unit_price = $item['price'];
                    }
                    $obj = new InvoiceItemsForApproval();
                    $obj->tenant_id = $tenant_id;
                    $obj->invoice_id = $invoice_id;
                    $obj->product_id = $item['product_id'];
                    $obj->item_name = $item['item_name'];
                    $obj->item_summary = $item['item_summary'];
                    $obj->type = $item['type'];
                    $obj->quantity = $item['quantity'];
                    $obj->unit_price = $unit_price;
                    $obj->amount = ($obj->quantity * $obj->unit_price) * (1 + $item['tax_rate'] / 100);
                    $obj->tax_id = $item['tax_id'];
                    $obj->approved = 'N';
                    $obj->save();
                    unset($obj);
                }
            }

            $url_params = base64_encode('invoice_id=' . $invoice_id);
            $url_link = request()->getSchemeAndHttpHost() . '/pay-now-pending-amount/' . $url_params;

            $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', $tenant_id)->first();
            $this->tenant_api_details = \App\TenantApiDetail::where(['tenant_id' => $tenant_id, 'provider' => 'PostMarkApp'])->first();

            $params = [
                'additional_invoice_items_pay' => $url_link,
                'first_name' => $crm_contacts->name,
            ];
            if ($email_temp && $cust_email) {
                if ($email_temp->send_email == 'Y') {
                    $template = $email_temp->notification_message;
                    if (preg_match_all("/{(.*?)}/", $template, $m)) {
                        foreach ($m[1] as $i => $varname) {
                            $template = str_replace($m[0][$i], sprintf('%s', $params[$varname]), $template);
                        }
                    }
                    $email_data['lead_id'] = $lead->id;
                    $email_data['from_email'] = $this->tenant_api_details->from_email;
                    $email_data['from_name'] = $this->organisation_settings->company_name;
                    $email_data['reply_to'] = $this->tenant_api_details->from_email;
                    $email_data['to'] = $cust_email;
                    $email_data['email_subject'] = $email_temp->notification_subject;
                    $email_data['email_body'] = $template;
                    Config::set('mail.username', $this->tenant_api_details->smtp_user);
                    Config::set('mail.password', $this->tenant_api_details->smtp_secret);
                    Mail::to($email_data['to'])->send(new sendMail($email_data));

                    //Add Activity Log
                    $data['log_message'] = $email_data['email_body'];
                    $data['lead_id'] = $lead->id;
                    $data['job_id'] = $invoice->job_id;
                    $data['log_from'] = $email_data['from_email'];
                    $data['log_to'] = $email_data['to'];
                    $data['log_subject'] = $email_data['email_subject'];
                    $data['tenant_id'] = $tenant_id;
                    //$data['user_id'] = auth()->user()->id;
                    $data['log_type'] = 3; // Activity Email
                    $data['log_date'] = Carbon::now();
                    $model = CRMActivityLog::create($data);

                }

                if ($email_temp->send_sms == 'Y') {

                }
            }

            return $this->sendResponse('Items Added', 'Invoice items sent for approval.');
        } else {
            return $this->sendError('notValid', 'No invoice items added');
        }
        // if(!$data || count($data) <= 0){
        //     return $this->sendError('notFound', 'Records not found.');
        // }
        // } catch (\Exception $ex) {
        //     return $this->sendError('Exception', $ex->getMessage());
        // }
    }
    /**
     * Add Items to Invoice and Approved table
     */
    public function addItemsToInvoice(Request $request)
    {
        try {
            if (empty($request->invoice_id) || $request->invoice_id <= 0) {
                return $this->sendError('notValid', 'Invoice ID is not valid.');
            }
            if (empty($request->tenant_id) || $request->tenant_id <= 0) {
                return $this->sendError('notValid', 'Tenant ID is not valid.');
            }            
            $items = $request->items;
            $invoice_id = $request->invoice_id;
            $tenant_id = $request->tenant_id;
            //Delete old data
            InvoiceItemsForApproval::where('invoice_id', '=', $invoice_id)->where('tenant_id', '=', $tenant_id)->delete();
            //--->
            if (isset($items)) {
                foreach ($items as $item) {
                    if ($item['quantity'] > 0) {
                        if (!isset($item['price']) || empty($item['price']) || $item['price'] <= 0) {
                            $unit_price = $item['unit_price'];
                        }else{
                            $unit_price = $item['price'];
                        }
                        $obj = new InvoiceItemsForApproval();
                        $obj->tenant_id = $tenant_id;
                        $obj->invoice_id = $invoice_id;
                        $obj->product_id = $item['product_id'];
                        $obj->item_name = $item['item_name'];
                        $obj->item_summary = $item['item_summary'];
                        $obj->type = $item['type'];
                        $obj->quantity = $item['quantity'];
                        $obj->unit_price = $unit_price;
                        $obj->amount = ($obj->quantity * $obj->unit_price) * (1 + $item['tax_rate'] / 100);                        
                        $obj->tax_id = $item['tax_id'];
                        $obj->approved = 'Y';
                        $obj->save();
                        unset($obj);

                        $obj_item = InvoiceItems::where(['invoice_id' => $invoice_id, 'product_id' => $item['product_id']])->first();
                        //if product not already in the invoice
                        if (!$obj_item) {
                            $obj_item = new InvoiceItems();
                        }
                        //Insert Items Into Invoice -->
                        $obj_item->tenant_id = $tenant_id;
                        $obj_item->invoice_id = $invoice_id;
                        $obj_item->item_name = $item['item_name'];
                        $obj_item->product_id = $item['product_id'];
                        $obj_item->item_summary = $item['item_summary'];
                        $obj_item->tax_id = $item['tax_id'];
                        $obj_item->type = $item['type'];
                        $obj_item->quantity = $item['quantity'];
                        $obj_item->unit_price = $unit_price;
                        $obj_item->amount = ($obj_item->quantity * $obj_item->unit_price) * (1 + $item['tax_rate'] / 100);
                        $obj_item->save();
                        unset($obj_item);
                        //-->
                    }
                }
                $invoice = Invoice::where('id', '=', $invoice_id)->first();
                $totalAmount = InvoiceItems::where('invoice_id', $invoice->id)->sum('amount');
                $paidAmount = Payment::where('invoice_id', $invoice->id)->sum('amount');
                if ($paidAmount < $totalAmount && $paidAmount > 0) {
                    $invoice->status = 'partial';
                } elseif ($paidAmount == $totalAmount) {
                    $invoice->status = 'paid';
                } else {
                    $invoice->status = 'unpaid';
                }
                //Update Invoice Status
                $invoice->save();

                return $this->sendResponse('Items Added', 'Invoice items has been added.');
            } else {
                return $this->sendError('notValid', 'No invoice items added');
            }
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }
    /**
     * Send Invoice PDf to Customer
     */
    public function emailInvoiceToCustomer(Request $request)
    {
        //try{
        if (empty($request->invoice_id) || $request->invoice_id <= 0) {
            return $this->sendError('notValid', 'Invoice ID is not valid.');
        }
        if (empty($request->tenant_id) || $request->tenant_id <= 0) {
            return $this->sendError('notValid', 'Tenant ID is not valid.');
        }
        $invoice_id = $request->invoice_id;
        $tenant_id = $request->tenant_id;

        $invoice = Invoice::where('id', '=', $invoice_id)->first();
        if ($invoice) {
            if ($invoice->sys_job_type == "Moving") {
                $job = DB::table('jobs_moving')->where('job_id', '=', $invoice->job_id)->first();
            } elseif ($invoice->sys_job_type == "Cleaning") {
                $job = DB::table('jobs_cleaning')->where('job_id', '=', $invoice->job_id)->first();
            }

            $lead = DB::table('crm_leads')->where('id', '=', $job->customer_id)->first();
            $crm_contacts = CRMContacts::where('lead_id', '=', $lead->id)->first();
            $cust_email = null;
            if ($crm_contacts) {
                $cust_email = CRMContactDetail::select('crm_contact_details.detail')
                    ->where(['crm_contact_details.contact_id' => $crm_contacts->id, 'crm_contact_details.detail_type' => 'Email'])
                    ->first();
                if ($cust_email) {
                    $cust_email = $cust_email->detail;
                }
            }
            $this->company = Companies::where('id', '=', $job->company_id)->first();
            $this->tenant_api_details = \App\TenantApiDetail::where(['tenant_id' => $tenant_id, 'provider' => 'PostMarkApp'])->first();

            if ($cust_email) {
                $file_url = public_path('invoice-files') . '/' . $invoice->file_original_name;
                if (!empty($invoice->file_original_name) && file_exists($file_url)) {
                    $invoice_pdf_url = 'invoice-files/' . $invoice->file_original_name;

                    $email_data['lead_id'] = $lead->id;
                    $email_data['from_email'] = $this->company->email;
                    $email_data['from_name'] = $this->company->contact_name;
                    $email_data['reply_to'] = $this->company->email;
                    $email_data['to'] = $cust_email;
                    $email_data['email_subject'] = "Invoice Attached";
                    $email_data['email_body'] = "Hello, please see attached invoice for the job";
                    $email_data['invoice_pdf'] = $invoice_pdf_url;
                    Config::set('mail.username', $this->tenant_api_details->smtp_user);
                    Config::set('mail.password', $this->tenant_api_details->smtp_secret);
                    Mail::to($email_data['to'])->send(new sendMail($email_data));

                    //Add Activity Log
                    $data['log_message'] = $email_data['email_body'];
                    $data['lead_id'] = $lead->id;
                    $data['job_id'] = $invoice->job_id;
                    $data['log_from'] = $email_data['from_email'];
                    $data['log_to'] = $email_data['to'];
                    $data['log_subject'] = $email_data['email_subject'];
                    $data['tenant_id'] = $tenant_id;
                    //$data['user_id'] = auth()->user()->id;
                    $data['log_type'] = 3; // Activity Email
                    $data['log_date'] = Carbon::now();
                    $model = CRMActivityLog::create($data);
                }
            }

            return $this->sendResponse('Invoice Sent', 'Invoice PDF sent to customer');
        } else {
            return $this->sendError('notValid', 'Invoice not sent.');
        }
    }

    /**
     * Upload Image to Job.
     */
    public function uploadAttachmentToJob(Request $request)
    {
        //return $this->sendResponse($request->all(), 'Attachment data');

        //try {
        if (empty($request->job_id) || $request->job_id <= 0) {
            return $this->sendError('notValid', 'Job ID is not valid.');
        }
        if (empty($request->tenant_id) || $request->tenant_id <= 0) {
            return $this->sendError('notValid', 'Tenant ID is not valid.');
        }
        if (empty($request->user_id) || $request->user_id <= 0) {
            return $this->sendError('notValid', 'User ID is not valid.');
        }

        $job = null;
        $job_id = $request->job_id;
        $tenant_id = $request->tenant_id;
        $user_id = $request->user_id;
        $sys_job_type = $request->sys_job_type;
        $attachments = $request->file('attachment');

        if ($sys_job_type == "Moving") {
            $job = JobsMoving::where('job_id', '=', $job_id)->where('tenant_id', '=', $tenant_id)->first();
        } elseif ($sys_job_type == "Cleaning") {
            $job = JobsCleaning::where('job_id', '=', $job_id)->where('tenant_id', '=', $tenant_id)->first();
        }

        if (!$job) {
            return $this->sendError('notRecord', 'Job data not found');
        }

        foreach ($attachments as $attachment) {
            $image = $attachment;
            $input['imagename'] = $job_id . '-' . date('Y') . '-' . $image->getClientOriginalName();

            $destinationPath = public_path('/user-uploads/tenants/' . $tenant_id);
            File::isDirectory($destinationPath) or File::makeDirectory($destinationPath, 0777, true, true);
            $img = Image::make($image->getRealPath());
            $img->save($destinationPath . '/' . $input['imagename']);
            // $type = pathinfo($input['imagename']);
            // $extension = $type['extension'];

            $log = new CRMActivityLog();
            $log->tenant_id = $tenant_id;
            if ($job) {
                $log->lead_id = $job->customer_id;
            }
            $log->user_id = $user_id;
            $log->log_type = 7;
            $log->log_message = 'Upload from Mobile app';
            $log->log_date = date('Y-m-d h:i:s');
            $log->save();

            $logAtt = new CRMActivityLogAttachment();
            $logAtt->tenant_id = $tenant_id;
            $logAtt->log_id = $log->id;
            $logAtt->attachment_type = $input['imagename'];
            $logAtt->attachment_content = $destinationPath . '/' . $input['imagename'];
            $logAtt->created_by = $user_id;
            $logAtt->created_at = date('Y-m-d h:i:s');
            $logAtt->save();
        }

        return $this->sendResponse('Success', 'Attachment uploaded to job.');
        return $this->sendError('notValid', 'Attachment data not valid.');
        // } catch (\Exception $ex) {
        //     return $this->sendError('Exception', $ex->getMessage());
        // }
    }

    /**
     * Delete Image from Job.
     */
    public function deleteAttachmentFromJob(Request $request)
    {
        try {
            if (empty($request->attachment_id) || $request->attachment_id <= 0) {
                return $this->sendError('notValid', 'Attachment ID is not valid.');
            }
            $logs = CRMActivityLogAttachment::find($request->attachment_id);
            if (!$logs) {
                return $this->sendError('notFound', 'Record not found.');
            }
            File::delete('user-uploads/tenants/' . $logs->tenant_id . '/' . $logs->attachment_content);
            $logs->delete();
            return $this->sendResponse('Attachment', 'Deleted Attachment from job.');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    /**
     * Customer Sign Off.
     */
    public function customerSignOff(Request $request)
    {
        try {
            if (empty($request->job_id) || $request->job_id <= 0) {
                return $this->sendError('notValid', 'Job ID is not valid.');
            }
            if (empty($request->leg_id) || $request->leg_id <= 0) {
                return $this->sendError('notValid', 'Leg ID is not valid.');
            }
            //Get requested parameters
            $job_id = $request->job_id;
            $leg_id = $request->leg_id;
            if ($request->hasFile('sign_image')) {
                $image = $request->file('sign_image');
                $input['imagename'] = $job_id . '-' . time() . '-' . $image->getClientOriginalName();
                $destinationPath = public_path('/user-uploads/tenants/' . $job_id);
                File::isDirectory($destinationPath) or File::makeDirectory($destinationPath, 0777, true, true);
                $img = Image::make($image->getRealPath());
                $img->save($destinationPath . '/' . $input['imagename']);
                $path = $destinationPath . '/' . $input['imagename'];
            } else {
                $path = null;
            }

            //update records
            $data = JobsMovingLegs::where('id', $leg_id)
                ->update([
                    'leg_status' => 'Completed',
                    'customer_sign' => $path,
                ]);

            //If Every leg_status=Completed then update Job Status to Completed aswell    
            $legs = JobsMovingLegs::where('job_id','=', $job_id)->where('leg_status','<>','Completed')->get();
            if (!$legs || count($legs) == 0) {
                $job = JobsMoving::where('job_id', '=', $job_id)->first();
                    //Set in queue invoice if XERO integration is On
                    $tenant_xero_api = TenantApiDetail::where(['tenant_id' => $job->tenant_id, 'provider' => 'Xero'])->first();
                    if (isset($tenant_xero_api)) {
                        $invoice = Invoice::where(['job_id'=> $job_id,'sys_job_type'=>'Moving'])->where('tenant_id', '=', $job->tenant_id)->first();
                        $invoice->sync_with_xero = 'Y';
                        $invoice->save();
                    }
                    //end
            JobsMoving::where('job_id', $job_id)
                ->update([
                    'job_status' => 'Completed'
                ]);
            }
            //---->
            
            if (!$data) {
                return $this->sendError('notFound', 'Records not found.');
            }
            return $this->sendResponse($data, 'Customer sign off successfully');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    /**
     * Pre Job Customer Sign Off.
     */
    public function customerPreJobSignOff(Request $request)
    {
        try {
            if (empty($request->job_id) || $request->job_id <= 0) {
                return $this->sendError('notValid', 'Job ID is not valid.');
            }
            if (empty($request->leg_id) || $request->leg_id <= 0) {
                return $this->sendError('notValid', 'Leg ID is not valid.');
            }
            //Get requested parameters
            $job_id = $request->job_id;
            $leg_id = $request->leg_id;
            if ($request->hasFile('sign_image')) {
                $image = $request->file('sign_image');
                $input['imagename'] = $job_id . '-' . time() . '-' . $image->getClientOriginalName();
                $destinationPath = public_path('/user-uploads/tenants/' . $job_id);
                File::isDirectory($destinationPath) or File::makeDirectory($destinationPath, 0777, true, true);
                $img = Image::make($image->getRealPath());
                $img->save($destinationPath . '/' . $input['imagename']);
                $path = $destinationPath . '/' . $input['imagename'];
            } else {
                $path = null;
            }

            //update records
            $data = JobsMovingLegs::where('id', $leg_id)
                ->update([
                    'customer_sign_pre_job' => $path,
                ]);

            //---->
            
            if (!$data) {
                return $this->sendError('notFound', 'Records not found.');
            }
            return $this->sendResponse($data, 'Customer sign off successfully');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    /**
     * Save Notes
     */
    public function saveNotes(Request $request)
    {
        try {
            //Get requested parameters
            $tenant_id = $request->tenant_id;
            $user_id = $request->user_id;
            $lead_id = $request->lead_id;
            $log_message = $request->log_message;

            if (empty($tenant_id) || $tenant_id <= 0) {
                return $this->sendError('notValid', 'Tenant ID is not valid.');
            }
            if (empty($user_id) || $user_id <= 0) {
                return $this->sendError('notValid', 'User ID is not valid.');
            }
            if (empty($lead_id) || $lead_id <= 0) {
                return $this->sendError('notValid', 'Lead ID is not valid.');
            }
            if (empty($log_message) || $log_message == '') {
                return $this->sendError('notValid', 'Message cannot be empty.');
            }

            $data['log_message'] = $log_message;
            $data['lead_id'] = $lead_id;
            $data['tenant_id'] = $tenant_id;
            $data['user_id'] = $user_id;
            $data['log_type'] = 15; // Activity Notes
            $data['log_date'] = Carbon::now();
            $model = CRMActivityLog::create($data);
            return $this->sendResponse('Saved', 'Notes has been saved.');

        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    /**
     * Notes List
     */
    public function getNotesList(Request $request)
    {
        try {
            //Get requested parameters
            $tenant_id = $request->tenant_id;
            $lead_id = $request->lead_id;
            if (empty($tenant_id) || $tenant_id <= 0) {
                return $this->sendError('notValid', 'Tenant ID is not valid.');
            }
            if (empty($lead_id) || $lead_id <= 0) {
                return $this->sendError('notValid', 'Lead ID is not valid.');
            }
            $notes = CRMActivityLog::where(['tenant_id' => $tenant_id, 'lead_id' => $lead_id, 'log_type' => 15])->orderBy('id', 'DESC')->get();

            if (count($notes)) {
                $notes_list = [];
                foreach ($notes as $note) {
                    $user_name = "";
                    $user = PplPeople::where('user_id', "=", $note->user_id)->first();
                    if ($user) {
                        $user_name = $user->first_name . ' ' . $user->last_name;
                    }
                    $d['log_message'] = $note->log_message;
                    $d['user_name'] = $user_name;
                    $d['log_date'] = \Carbon\Carbon::parse($note->log_date)->diffForHumans();
                    $notes_list[] = $d;
                    unset($d);
                }

            } else {
                return $this->sendError('Record not found.');
            }
            return $this->sendResponse($notes_list, 'Notes List.');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    /**
     * Get Job Addresses
     */
    public function getJobAddress(Request $request)
    {
        try {
            //Get requested parameters
            $tenant_id = $request->tenant_id;

            $tenant_api_details = \App\TenantApiDetail::where(['tenant_id' => $tenant_id, 'provider' => 'GoogleMaps'])->first();
            if ($tenant_api_details) {
                $api_key = $tenant_api_details->account_key;
            } else {
                //$api_key="AIzaSyB2SMtaVBlqC5v72gqS716BX8R5oXklaFc";
            }
            //get records
            $data = JobsMovingLegs::select(
                'jobs_moving_legs.job_id',
                'jobs_moving_legs.pickup_address as pickup_address',
                'jobs_moving_legs.drop_off_address as delivery_address',
                'jobs_moving_legs.est_start_time as start_time',
                'jobs_moving_legs.est_finish_time as finish_time'
            )
                ->where(['jobs_moving_legs.tenant_id' => $tenant_id])
                ->where(DB::raw('DATE(jobs_moving_legs.leg_date)'), '>=', Carbon::today()->toDateString())
                ->get();

            if (!$data || count($data) <= 0) {
                return $this->sendError('notFound', 'Records not found.');
            } else {
                foreach ($data as $d) {
                    $paddress = $d->pickup_address; //
                    $daddress = $d->delivery_address; //

                    $prepAddr = str_replace(' ', '+', $paddress);
                    $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $prepAddr . '&sensor=false&key=' . $api_key;
                    $geocode = $this->curl_get_file_contents($url);

                    $output = json_decode($geocode);
                    $latitude = substr(($output->results[0]->geometry->location->lat), 0, 15);
                    $longitude = substr(($output->results[0]->geometry->location->lng), 0, 15);

                    $deliveryAddr = str_replace(' ', '+', $daddress);
                    $url2 = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $deliveryAddr . '&sensor=false&key=' . $api_key;
                    $geocode2 = $this->curl_get_file_contents($url2);

                    $output2 = json_decode($geocode2);
                    $latitude2 = substr(($output2->results[0]->geometry->location->lat), 0, 15);
                    $longitude2 = substr(($output2->results[0]->geometry->location->lng), 0, 15);

                    $response[] = [
                        'job_id' => $d->job_id,
                        'start_time' => $d->start_time,
                        'finish_time' => $d->finish_time,
                        'pickup_address' => $d->pickup_address,
                        'pickup_latitude' => $latitude,
                        'pickup_longitude' => $longitude,
                        'delivery_address' => $d->delivery_address,
                        'delivery_latitude' => $latitude2,
                        'delivery_longitude' => $longitude2,
                    ];
                }
            }
            return $this->sendResponse($response, 'Job Addresses');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    private function curl_get_file_contents($URL)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        $contents = curl_exec($c);
        curl_close($c);

        if ($contents) {
            return $contents;
        } else {
            return false;
        }

    }

    public function stripePay(Request $request)
    {
        $tenant_id = $request->tenant_id;
        $job_id = $request->job_id;
        $invoice_id = $request->invoice_id;
        $booking_fee = 0;
        $deposit_required = $request->deposit_required;
        $stripe_token = $request->stripe_token;
        $old_stripe_payment_detail = $request->old_stripe_payment_detail;

        $deposit_required = number_format((float) $deposit_required, 2, '.', '');

        // Stripe API secret key
        $secret_key = env('STRIPE_SECRET');
        Stripe::setApiKey($secret_key);

        $tenant_api_details = TenantApiDetail::where(['tenant_id' => $tenant_id, 'provider' => 'Stripe'])->first();
        if (!$tenant_api_details) {
            return $this->sendError('Stripe Not Connected');
        }
        //-----------------
        if ($invoice_id == 0) {
            $invoice = Invoice::where('job_id', '=', $job_id)->first();
        } else {
            $invoice = Invoice::where('id', '=', $invoice_id)->first();
        }

        $response = array();
        // Check whether stripe token is not empty
        if ($old_stripe_payment_detail == 'Y') {
            if (isset($invoice->stripe_one_off_customer_id) && !empty($invoice->stripe_one_off_customer_id)) {
                $stripeCustomerId = $invoice->stripe_one_off_customer_id;
                $old_customer = 1;
            } else {
                // Get token, card and item info
                $token = $request->stripe_token;
                $email = $request->stripe_email;
                try {
                    // Add customer to stripe
                    $customer = \Stripe\Customer::create(array(
                        'email' => $email,
                        'source' => $token,
                    ), ['stripe_account' => $tenant_api_details->variable1]);
                    $stripeCustomerId = $customer->id;
                } catch (\Stripe\Error\OAuth\OAuthBase $e) {
                    return $this->sendError($e->getMessage());
                }
                $old_customer = 0;
            }
            try {
                ///Processing Fee calculation
                $invoice_setting = InvoiceSetting::where('tenant_id', $tenant_id)->first();  
                if($invoice_setting->cc_processing_fee_percent > 0){
                    $processing_fee = $deposit_required * $invoice_setting->cc_processing_fee_percent/100;  
                    $processing_fee = number_format((float)$processing_fee, 2, '.', '');
                }else{
                    $processing_fee = 0;
                }
                $deposit_required = $deposit_required+$processing_fee;
                //------->

                // Charge a credit or a debit card
                $charge = \Stripe\Charge::create(array(
                    'customer' => $stripeCustomerId,
                    'amount' => $deposit_required * 100,
                    'currency' => 'AUD',
                    //'source'  => $token,
                    'description' => 'Amount deposit for job number ' . $request->job_number,
                ), ['stripe_account' => $tenant_api_details->variable1]);

            } catch (\Stripe\Error\OAuth\OAuthBase $e) {
                return $this->sendError($e->getMessage());
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
                    if ($invoice && $booking_fee == 0) {
                        //Start::Add Stripe Processing fee line item                                                
                        if($invoice_setting->cc_processing_fee_percent > 0){
                            $processing_item = Product::where('id','=',$invoice_setting->cc_processing_product_id)->first();
                            if($processing_item){
                                $p_deposit_required = $processing_fee;
                                $obj_item = new InvoiceItems();
                                $obj_item->tenant_id = $tenant_id;
                                $obj_item->invoice_id = $invoice->id;
                                $obj_item->product_id = $processing_item->id;
                                $obj_item->item_name = $processing_item->name;
                                $obj_item->item_summary = '';
                                $obj_item->type = $processing_item->product_type;
                                $obj_item->quantity = 1;
                                $obj_item->unit_price = $p_deposit_required;
                                $obj_item->amount = ($obj_item->unit_price * $obj_item->quantity);
                                $obj_item->save();
                                unset($obj_item);
                            }
                        }
                        //end::Processing fee line item
                        //Add Invoice Payment
                        $payment = new Payment();
                        $payment->tenant_id = $tenant_id;
                        $payment->invoice_id = $invoice->id;
                        $payment->gateway = 'Stripe';
                        $payment->transaction_id = $transactionID;
                        $payment->remarks = 'Job confirmation payment';
                        $payment->amount = $deposit_required;
                        $payment->paid_on = Carbon::now();
                        $payment->created_at = Carbon::now();
                        $payment->save();
                        //
                    } else {
                        $quote = Quotes::where('job_id', '=', $job_id)->first();
                        if ($booking_fee == 1) {
                            $binvoice = new Invoice();
                            $binvoice->tenant_id = $tenant_id;
                            $binvoice->job_id = 0;
                            $binvoice->invoice_number = 0;
                            $binvoice->sys_job_type = $quote->sys_job_type;
                            $binvoice->project_id = 1;
                            $binvoice->issue_date = date('Y-m-d');
                            $binvoice->due_date = date('Y-m-d');
                            $binvoice->note = 'Booking Fee';
                            $binvoice->status = 'paid';
                            $binvoice->save();
                            //Booking Fee Line Item
                            $booking_item = new InvoiceItems();
                            $booking_item->tenant_id = $tenant_id;
                            $booking_item->invoice_id = $binvoice->id;
                            $booking_item->item_name = 'Booking Fee for Job number:' . $quote->quote_number;
                            $booking_item->item_summary = '';
                            $booking_item->type = 'Item';
                            $booking_item->unit_price = $deposit_required;
                            $booking_item->amount = $deposit_required;
                            $booking_item->save();
                            unset($booking_item);
                        }

                        $res = Invoice::select(DB::raw('invoice_number'))->where('tenant_id', '=', $tenant_id)->orderBy('id', 'DESC')->first();
                        $new_invoice_number = intval($res->invoice_number) + 1;

                        $invoice = new Invoice();
                        $invoice->tenant_id = $tenant_id;
                        $invoice->job_id = $job_id;
                        $invoice->invoice_number = $quote->quote_number;
                        $invoice->sys_job_type = $quote->sys_job_type;
                        $invoice->project_id = 1;
                        $current_date = date('Y-m-d');
                        $invoice->issue_date = $current_date;
                        $due_after = 15;
                        if ($invoice_setting) {
                            $due_after = $invoice_setting->due_after;
                        }
                        $invoice->due_date = date('Y-m-d', strtotime($current_date . ' + ' . $due_after . ' days'));
                        $invoice->save();
                        $companies = Companies::where('tenant_id', '=', $tenant_id)->first();
                        //Saving Invoice items
                        if ($quote) {
                            $quoteItem = DB::table('quote_items')
                                ->where(['quote_id' => $quote->id])
                                ->get();
                            foreach ($quoteItem as $q) {
                                $obj_item = new InvoiceItems();
                                $obj_item->tenant_id = $tenant_id;
                                $obj_item->invoice_id = $invoice->id;
                                $obj_item->item_name = $q->name;
                                $obj_item->item_summary = $q->description;
                                $obj_item->type = $q->type;
                                $obj_item->quantity = $q->quantity;
                                $obj_item->unit_price = $q->unit_price;
                                $obj_item->amount = $q->amount;
                                $obj_item->save();
                                unset($obj_item);
                            }
                        }

                        //Start::Add Stripe Processing fee line item                        
                        $deposit_required = $deposit_required+$processing_fee;
                        if($invoice_setting->cc_processing_fee_percent > 0){
                            $processing_item = Product::where('id','=',$invoice_setting->cc_processing_product_id)->first();
                            if($processing_item){
                                $p_deposit_required = $processing_fee;
                                $obj_item = new InvoiceItems();
                                $obj_item->tenant_id = $tenant_id;
                                $obj_item->invoice_id = $invoice->id;
                                $obj_item->product_id = $processing_item->id;
                                $obj_item->item_name = $processing_item->name;
                                $obj_item->item_summary = '';
                                $obj_item->type = $processing_item->product_type;
                                $obj_item->quantity = 1;
                                $obj_item->unit_price = $p_deposit_required;
                                $obj_item->amount = ($obj_item->unit_price * $obj_item->quantity);
                                $obj_item->save();
                                unset($obj_item);
                            }
                        }
                        //end::Processing fee line item

                        //Add Invoice Payment

                        $payment = new Payment();
                        $payment->tenant_id = $tenant_id;
                        $payment->invoice_id = $binvoice->id;
                        $payment->remarks = 'Booking Fee Payment';
                        if ($booking_fee == 1) {
                        } else {
                            $payment->invoice_id = $invoice->id;
                            $payment->remarks = 'Job confirmation payment';
                        }
                        $payment->gateway = 'Stripe';
                        $payment->amount = $deposit_required;
                        $payment->paid_on = Carbon::now();
                        $payment->created_at = Carbon::now();
                        $payment->save();
                        //

                        ///Generating Invoice PDF
                        $this->invoice = $invoice;
                        $sub_total = InvoiceItems::select(DB::raw('sum(invoice_items.unit_price * invoice_items.quantity) as total'))
                            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
                            ->where('invoices.job_id', '=', $job_id)->first();
                        $this->sub_total = $sub_total->total;

                        $tax_total = InvoiceItems::select(DB::raw('sum(invoice_items.amount) - sum(invoice_items.unit_price * invoice_items.quantity) as total'))
                            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
                            ->where('invoices.job_id', '=', $job_id)->first();
                        $this->tax_total = $tax_total->total;

                        $grand_total = InvoiceItems::select(DB::raw('sum(invoice_items.amount) as total'))
                            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
                            ->where('invoices.job_id', '=', $job_id)->first();
                        $this->grand_total = $grand_total->total;

                        $total_paid = Payment::select(DB::raw('sum(payments.amount) as total'))
                            ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
                            ->where('invoices.job_id', '=', $job_id)->first();
                        $this->total_paid = $total_paid->total;

                        $this->balance_payment = floatval($this->grand_total) - floatval($this->total_paid);

                        $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', $tenant_id)->first();

                        $this->taxs = Tax::where(['tenant_id' => $tenant_id])->first();
                        $this->job = JobsMoving::where('job_id', '=', $job_id)->where('tenant_id', '=', $tenant_id)->first();
                        $this->companies = Companies::where('tenant_id', '=', $tenant_id)->first();
                        $this->crm_leads = CRMLeads::where('id', '=', $this->job->customer_id)->first();
                        $this->crm_contacts = CRMContacts::where('lead_id', '=', $this->job->customer_id)->first();
                        $this->crm_contact_email = CRMContactDetail::where('contact_id', '=', $this->crm_contacts->id)->where('detail_type', '=', 'Email')->first();
                        $this->crm_contact_phone = CRMContactDetail::where('contact_id', '=', $this->crm_contacts->id)->where('detail_type', '=', 'Mobile')->first();
                        $this->invoice_items = InvoiceItems::where('invoice_id', '=', $invoice->id)->where('tenant_id', '=', $tenant_id)->get();
                        $this->company_logo_exists = false;
                        $this->stripe_connected = 0;
                        $stripe = TenantApiDetail::where('tenant_id', $tenant_id)
                            ->where('provider', 'Stripe')->first();
                        if ($stripe) {
                            if (isset($stripe->account_key) && !empty($stripe->account_key)) {
                                $this->stripe_connected = 1;
                            }
                        }

                        $this->settings = Setting::findOrFail(1);
                        $this->invoiceSetting = InvoiceSetting::first();
                        $file_number = 1;
                        if (!empty($invoice->file_original_name)) {
                            $filename = str_replace('.pdf', '', $invoice->file_original_name);
                            $fn_ary = explode('_', $filename);
                            $file_number = intval($fn_ary[2]) + 1;
                        }

                        $this->url_params = base64_encode('invoice_id=' . $invoice->id . '&payment_amount=' . $this->balance_payment);
                        $this->url_link = request()->getSchemeAndHttpHost() . '/pay-now-inv/' . $this->url_params;

                        $filename = 'Invoice_Job' . $invoice->invoice_number . '_' . $file_number . '.pdf';

                        if ($companies) {
                            if (File::exists(public_path() . '/user-uploads/app-logo/' . $this->companies->logo)) {
                                $this->company_logo_exists = true;
                            }
                        }
                        $pdf = app('dompdf.wrapper');
                        $pdf->loadView('admin.list-jobs.invoice', [
                            'global' => $this->global,
                            'organisation_settings' => $this->organisation_settings,
                            'companies' => $this->companies,
                            'invoice' => $invoice,
                            'invoiceSetting' => $this->invoiceSetting,
                            'settings' => $this->settings,
                            'company_logo_exists' => $this->company_logo_exists,
                            'invoice_items' => $this->invoice_items,
                            'count' => 0,
                            'crm_contact_phone' => $this->crm_contact_phone,
                            'crm_contact_email' => $this->crm_contact_email,
                            'crm_contacts' => $this->crm_contacts,
                            'crm_leads' => $this->crm_leads,
                            'job' => $this->job,
                            'taxs' => $this->taxs,
                            'total_paid' => $this->total_paid,
                            'balance_payment' => $this->balance_payment,
                            'grand_total' => $this->grand_total,
                            'tax_total' => $this->tax_total,
                            'sub_total' => $this->sub_total,
                            'stripe_connected' => $this->stripe_connected,
                            'url_link' => $this->url_link,
                        ]);
                        $pdf->save('invoice-files/' . $filename);

                        if (File::exists(public_path() . '/invoice-files/' . $this->invoice->file_original_name)) {
                            File::delete(public_path() . '/invoice-files/' . $this->invoice->file_original_name);
                        }
                        $invoice->file_original_name = $filename;
                        $invoice->save();

                    }
                    if ($booking_fee == 0) {
                        $totalAmount = InvoiceItems::where('invoice_id', $invoice->id)->sum('amount');
                        $paidAmount = Payment::where('invoice_id', $invoice->id)->sum('amount');
                        if ($paidAmount < $totalAmount && $paidAmount > 0) {
                            $invoice->status = 'partial';
                        } elseif ($paidAmount == $totalAmount) {
                            $invoice->status = 'paid';
                        } else {
                            $invoice->status = 'unpaid';
                        }
                        if ($old_customer == 0) {
                            $invoice->stripe_one_off_customer_id = $stripeCustomerId;
                        }
                        //Update Invoice Status
                        $invoice->save();
                    }

                    //--Update Job Satatus
                    // JobsMoving::where(['job_id' => $job_id, 'tenant_id' => $tenant_id])
                    //     ->update([
                    //         'opportunity' => 'N',
                    //         //'job_status' => 'New',
                    //     ]);
                    //----
                    $response = array(
                        'status' => 'Your payment was successful',
                        'txnData' => $chargeJson,
                    );
                    $result = $this->sendResponse($response, 'Success');
                } else {
                    $result = $this->sendError($response, 'Transaction has been failed');
                }
            } else {
                $result = $this->sendError($response, 'Transaction has been failed');
            }
        } else {
            $result = $this->sendError('stripe_token is not valid');
        }

        return $result;
    }

    public function updateActualhours(Request $request)
    {
        $leg_id = $request->leg_id;
        if (empty($leg_id) || $leg_id <= 0) {
            return $this->sendError('notValid', 'Leg ID is not valid.');
        }
        $actual_start_time = date("H:i", strtotime($request->input('actual_start_time')));
        $actual_finish_time = date("H:i", strtotime($request->input('actual_finish_time')));

        JobsMovingLegs::where('id', '=', $leg_id)->update([
            'actual_start_time' => $actual_start_time,
            'actual_finish_time' => $actual_finish_time,
        ]);
        //Response
        $total_hours = $this->calculateTimeDuration($actual_start_time, $actual_finish_time);
        $response = array(
            'status' => 'Your Actual was updated.',
            'total_hours' => $total_hours,
        );
        $result = $this->sendResponse($response, 'Success');
        return $result;
    }

    public function calculateTimeDuration($actual_start_time, $actual_finish_time)
    {
        if ($actual_start_time != null && $actual_finish_time != null) {
            $start = Carbon::parse($actual_start_time);
            $end = Carbon::parse($actual_finish_time);
            $minutes = $end->diffInMinutes($start) / 60;
            $total_hours = ceil($minutes * 4) / 4;
            $total_hours = number_format((float) $total_hours, 2, '.', '');
        } else {
            $total_hours = 0;
        }
        return $total_hours;
    }
    /**
     * Generate Invoice PDF
     */
    public function generateInvoice(Request $request)
    {

        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '3000M');
        $job_id = $request->job_id;
        $tenant_id = $request->tenant_id;
        $manual_action = $request->manual_action;

        if (empty($job_id) || $job_id <= 0) {
            return $this->sendError('notValid', 'Job ID is not valid.');
        }

        $this->sub_total = 0;
        $this->grand_total = 0;
        $this->tax_total = 0;
        $this->total_paid = 0;
        $this->balance_payment = 0;
        $this->count = 0;
        $this->stripe_connected = 0;
        $this->invoice_settings = InvoiceSetting::where('tenant_id', $tenant_id)->first();
        $stripe = TenantApiDetail::where('tenant_id', $tenant_id)
            ->where('provider', 'Stripe')->first();
        if ($stripe) {
            if (isset($stripe->account_key) && !empty($stripe->account_key)) {
                $this->stripe_connected = 1;
            }
        }

        $job_legs = JobsMovingLegs::where('job_id', '=', $job_id)->where('tenant_id', '=', $tenant_id)->get();
        $total_duration = 0;
        if (count($job_legs)) {
            foreach ($job_legs as $j) {
                $total_duration += $j->calculateTimeDurationForUpdate();
            }
        }

        $this->invoice = Invoice::where(['job_id' => $job_id, 'sys_job_type' => "Moving"])
            ->where('tenant_id', '=', $tenant_id)
            ->first();
        $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', $tenant_id)->first();
        

        if (!$this->invoice) {
/*          $this->quotes = Quotes::where('job_id', '=', $job_id)->where('tenant_id', '=', $tenant_id)->first();
            $this->quote_items = QuoteItem::where('quote_id', '=', $this->quotes->id)->where('tenant_id', '=', $tenant_id)->get();  
            $obj = new Invoice();
            $obj->tenant_id = $this->quotes->tenant_id;
            $obj->job_id = $this->quotes->job_id;
            $obj->sys_job_type = $this->quotes->sys_job_type;
            $obj->invoice_number = $this->quotes->quote_number;
            $current_date = date('Y-m-d');
            $obj->issue_date = $current_date;
            $due_after = 15;
            if ($this->invoice_settings) {
                $due_after = $this->invoice_settings->due_after;
            }
            $obj->due_date = date('Y-m-d', strtotime($current_date . ' + ' . $due_after . ' days'));
            $obj->currency_id = $this->organisation_settings->currency_id;
            $obj->status = 'unpaid';
            $obj->created_at = date('Y-m-d');

            $obj->save();
            $this->invoice = $obj;
            // dd($this->quote_items);
            if ($this->quote_items) {
                foreach ($this->quote_items as $quoteItem) {
                    $obj_item = new InvoiceItems();
                    $obj_item->tenant_id = $this->invoice->tenant_id;
                    $obj_item->invoice_id = $this->invoice->id;
                    // $obj_item->item_name = $quoteItem->name;
                    $obj_item->item_summary = $quoteItem->description;
                    $obj_item->unit_price = $quoteItem->unit_price;
                    $obj_item->type = $quoteItem->type;
                    if ($total_duration > 0) {
                        $obj_item->quantity = $total_duration;
                    } else {
                        $obj_item->quantity = $quoteItem->quantity;
                    }
                    $obj_item->amount = $quoteItem->amount;
                    $obj_item->tax_id = $quoteItem->tax_id;
                    $obj_item->created_at = date('Y-m-d');
                    // $obj_item->created_by = auth()->user()->id;
                    $obj_item->save();
                }
            }
            */
            return $this->sendError('notFound', 'Invoice not found.');
        } else {
            $invoice_items = InvoiceItems::where(['invoice_id' => $this->invoice->id, 'type' => 'Service'])->get();
            if (count($invoice_items)) {
                foreach ($invoice_items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        if ($product->hourly_pricing_min_hours > 0 && $product->hourly_pricing_min_hours > $total_duration) {
                            $total_duration = $product->hourly_pricing_min_hours;
                        }
                    }

                    if ($item->tax_id != null) {
                        $rate_percent = Tax::where('id', '=', $item->tax_id)->pluck('rate_percent')->first();
                    } else {
                        $rate_percent = 0;
                    }
                    if ($total_duration > 0) {
                        $item->quantity = $total_duration;
                    }
                    $item->amount = $item->quantity * $item->unit_price * (1 + $rate_percent / 100);
                    $item->save();
                }
            }
        }

        $this->taxs = Tax::select('taxes.*')
            ->where(['taxes.tenant_id' => $tenant_id, 'invoice_items.invoice_id' => $this->invoice->id])
            ->whereNotNull('invoice_items.tax_id')
            ->join('invoice_items', 'invoice_items.tax_id', '=', 'taxes.id')->first();

        $sub_total = InvoiceItems::select(DB::raw('sum(invoice_items.unit_price * invoice_items.quantity) as total'))
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->where('invoices.job_id', '=', $job_id)->where('invoices.sys_job_type', '=', 'Moving')->first();
        $this->sub_total = $sub_total->total;

        $total_paid = Payment::select(DB::raw('sum(payments.amount) as total'))
            ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->where('invoices.job_id', '=', $job_id)->where('invoices.sys_job_type', '=', 'Moving')->first();
        $this->total_paid = $total_paid->total;
        $this->job = JobsMoving::where('job_id', '=', $job_id)->where('tenant_id', '=', $tenant_id)->first();

        $this->companies = Companies::where('tenant_id', '=', $tenant_id)->first();
        $this->crm_leads = CRMLeads::where('id', '=', $this->job->customer_id)->first();
        $this->customer_detail = CustomerDetails::where('customer_id', '=', $this->job->customer_id)->first();
        $this->crm_contacts = CRMContacts::where('lead_id', '=', $this->job->customer_id)->first();
        $this->crm_contact_email = CRMContactDetail::where('contact_id', '=', $this->crm_contacts->id)->where('detail_type', '=', 'Email')->first();
        $this->crm_contact_phone = CRMContactDetail::where('contact_id', '=', $this->crm_contacts->id)->where('detail_type', '=', 'Mobile')->first();
        $this->invoice_items = InvoiceItems::where('invoice_id', '=', $this->invoice->id)->where('tenant_id', '=', $tenant_id)->get();
        $this->company_logo_exists = false;

        $this->settings = Setting::findOrFail(1);

        //Calculating Total Values
        if ($this->invoice->discount > 0) {
            if ($this->invoice->discount_type == "percent") {
                $this->sub_total_after_discount = $this->sub_total - ($this->invoice->discount / 100 * $this->sub_total);
            } else {
                $this->sub_total_after_discount = $this->sub_total - $this->invoice->discount;
            }
        } else {
            $this->sub_total_after_discount = $this->sub_total;
        }
        if ($this->taxs) {
            $this->tax_total = ($this->taxs->rate_percent * $this->sub_total_after_discount) / 100;
        } else {
            $this->tax_total = 0;
        }
        $this->invoice_total = $this->tax_total + $this->sub_total_after_discount;
        $this->balance_payment = $this->invoice_total - $this->total_paid;
        //END:: Calculation values

        $this->url_params = base64_encode('invoice_id=' . $this->invoice->id . '&payment_amount=' . $this->balance_payment);
        $this->url_link = request()->getSchemeAndHttpHost() . '/pay-now-inv/' . $this->url_params;

        //return view('admin.list-jobs.invoice', $this->data);

        $file_number = 1;
        if (!empty($this->invoice->file_original_name)) {
            $filename = str_replace('.pdf', '', $this->invoice->file_original_name);
            $fn_ary = explode('_', $filename);
            $file_number = intval($fn_ary[2]) + 1;
        }

        $filename = 'Invoice_Job_' . $this->invoice->invoice_number . '_' . rand() . '.pdf';

        if ($this->companies) {
            if (File::exists(public_path() . '/user-uploads/app-logo/' . $this->companies->logo)) {
                $this->company_logo_exists = true;
            }
        }

        $current_date = date('Y-m-d');
        $this->invoice->issue_date = $current_date;
        $due_after = 15;
        if ($this->invoice_settings) {
            $due_after = $this->invoice_settings->due_after;
        }
        $this->invoice->due_date = date('Y-m-d', strtotime($current_date . ' + ' . $due_after . ' days'));

        $pdf = app('dompdf.wrapper');
        $html = view('admin.list-jobs.invoice', [
            'global' => $this->global,
            'organisation_settings' => $this->organisation_settings,
            'companies' => $this->companies,
            'invoice' => $this->invoice,
            'invoice_settings' => $this->invoice_settings,
            // 'settings' => $this->settings,
            'company_logo_exists' => $this->company_logo_exists,
            'invoice_items' => $this->invoice_items,
            'count' => 0,
            'crm_contact_phone' => $this->crm_contact_phone,
            'crm_contact_email' => $this->crm_contact_email,
            'crm_contacts' => $this->crm_contacts,
            'crm_leads' => $this->crm_leads,
            'customer_detail'=>$this->customer_detail,
            'job' => $this->job,
            'taxs' => $this->taxs,
            'tax_total' => $this->tax_total,
            'sub_total_after_discount' => $this->sub_total_after_discount,
            'invoice_total' => $this->invoice_total,
            'sub_total' => $this->sub_total,
            'total_paid' => $this->total_paid,
            'balance_payment' => $this->balance_payment,
            'url_link' => $this->url_link,
            'stripe_connected' => $this->stripe_connected,
            'is_storage_invoice' => 0
        ]);
        $pdf->loadHtml($html, 'UTF-8');
        $pdf->getDomPDF()->set_option("enable_php", true);
        // return $pdf->stream(); // to view pdf
        // return $pdf->download('tmp.pdf');
        $pdf->save('invoice-files/' . $filename);

        if (File::exists(public_path() . '/invoice-files/' . $this->invoice->file_original_name)) {
            File::delete(public_path() . '/invoice-files/' . $this->invoice->file_original_name);
        }
        $this->invoice->file_original_name = $filename;
        if($manual_action=='Y'){
            $this->invoice->regenerated='Y';
        }
        $this->invoice->save();
        return $this->sendResponse('Invoice Generated', 'Invoice has been generated.');
    }

    public function getJobPackingMaterialIssueList(Request $request)
    {
        try {
            //Get requested parameters
            $job_id = $request->job_id;
            $tenant_id = $request->tenant_id;
            $data = DB::table('inv_job_items_issued')->select([
                                'inv_job_items_issued.*',
                                'products.name',
                                'products.description',
                            ])
                            ->where(['inv_job_items_issued.tenant_id' => $tenant_id, 'inv_job_items_issued.job_id' => $job_id])
                            ->leftjoin('products', 'products.id', 'inv_job_items_issued.item_id')
                            ->get();

            if (!$data || count($data) <= 0) {
                return $this->sendError('notFound', 'Records not found.');
            }
            return $this->sendResponse($data, 'Material Issue List.');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    public function updateJobPackingMaterialIssue(Request $request)
    {
        try {
            //Get requested parameters
            $job_id = $request->job_id;
            $issue_id = $request->issue_id;
            $quantity = $request->quantity;
            $data = DB::table('inv_job_items_issued')
                        ->where(['id' => $issue_id, 'job_id' => $job_id])
                        ->update([
                            'quantity' => $quantity
                        ]);    

            if (!$data) {
                return $this->sendError('notFound', 'Records not found.');
            }
            return $this->sendResponse($data, 'Material Issue Update SuccessFully!.');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    public function deleteJobPackingMaterialIssue(Request $request)
    {
        try {
            //Get requested parameters
            $job_id = $request->job_id;
            $issue_id = $request->issue_id;
            $data = DB::table('inv_job_items_issued')
                        ->where(['id' => $issue_id, 'job_id' => $job_id])
                        ->delete();   

            if (!$data) {
                return $this->sendError('notFound', 'Records not found.');
            }
            return $this->sendResponse($data, 'Material Issue Deleted SuccessFully!.');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    public function getStockableItems(Request $request)
    {
        try {
            //Get requested parameters
            $tenant_id = $request->tenant_id;
            $data = DB::table('products')
                            ->where(['tenant_id' => $tenant_id, 'stockable' => 'Y', 'active' => 1])
                            ->get();

            if (!$data || count($data) <= 0) {
                return $this->sendError('notFound', 'Records not found.');
            }
            return $this->sendResponse($data, 'Stockable Items List.');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    public function addJobPackingMaterialIssue(Request $request)
    {
        try {
            //Get requested parameters
            $job_id = $request->job_id;
            $item_id = $request->item_id;
            $quantity = $request->quantity;
            $tenant_id = $request->tenant_id;
            $user_id = $request->user_id;
            $data = DB::table('inv_job_items_issued')
                        ->insert([
                            'tenant_id' => $tenant_id,
                            'item_id' => $item_id,
                            'sys_job_type' => 'Moving',
                            'job_id' => $job_id,
                            'quantity' => $quantity,
                            'created_date' => Carbon::now(),
                            'created_by' => $user_id
                        ]);    

            if (!$data) {
                return $this->sendError('notFound', 'Records not found.');
            }
            return $this->sendResponse($data, 'Material Issue Added SuccessFully!.');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    public function getJobPackingMaterialReturnList(Request $request)
    {
        try {
            //Get requested parameters
            $job_id = $request->job_id;
            $tenant_id = $request->tenant_id;
            $data = DB::table('inv_job_items_returned')->select([
                                'inv_job_items_returned.*',
                                'products.name',
                                'products.description',
                            ])
                            ->where(['inv_job_items_returned.tenant_id' => $tenant_id, 'inv_job_items_returned.job_id' => $job_id])
                            ->leftjoin('products', 'products.id', 'inv_job_items_returned.item_id')
                            ->get();

            if (!$data || count($data) <= 0) {
                return $this->sendError('notFound', 'Records not found.');
            }
            return $this->sendResponse($data, 'Material Return List.');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    public function updateJobPackingMaterialReturn(Request $request)
    {
        try {
            //Get requested parameters
            $job_id = $request->job_id;
            $return_id = $request->return_id;
            $quantity = $request->quantity;
            $data = DB::table('inv_job_items_returned')
                        ->where(['id' => $return_id, 'job_id' => $job_id])
                        ->update([
                            'quantity' => $quantity
                        ]);    

            if (!$data) {
                return $this->sendError('notFound', 'Records not found.');
            }
            return $this->sendResponse($data, 'Material Return Update SuccessFully!.');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    public function deleteJobPackingMaterialReturn(Request $request)
    {
        try {
            //Get requested parameters
            $job_id = $request->job_id;
            $return_id = $request->return_id;
            $data = DB::table('inv_job_items_returned')
                        ->where(['id' => $return_id, 'job_id' => $job_id])
                        ->delete();   

            if (!$data) {
                return $this->sendError('notFound', 'Records not found.');
            }
            return $this->sendResponse($data, 'Material return Deleted SuccessFully!.');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    public function addJobPackingMaterialReturn(Request $request)
    {
        try {
            //Get requested parameters
            $job_id = $request->job_id;
            $item_id = $request->item_id;
            $quantity = $request->quantity;
            $tenant_id = $request->tenant_id;
            $user_id = $request->user_id;
            $data = DB::table('inv_job_items_returned')
                        ->insert([
                            'tenant_id' => $tenant_id,
                            'item_id' => $item_id,
                            'sys_job_type' => 'Moving',
                            'job_id' => $job_id,
                            'quantity' => $quantity,
                            'created_date' => Carbon::now(),
                            'created_by' => $user_id
                        ]);    

            if (!$data) {
                return $this->sendError('notFound', 'Records not found.');
            }
            return $this->sendResponse($data, 'Material Return Added SuccessFully!.');
        } catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    public function updateGenerateInvoicePackingMaterial(Request $request)
    {
        $tenant_id = $request->tenant_id;
        $invoice = Invoice::where(['job_id' => $request->job_id, 'sys_job_type' => 'Moving'])->first();
        $issued_items = DB::table('inv_job_items_issued')->where('job_id', $request->job_id)->get();
        foreach($issued_items as $item)
        {
            $returned_item = DB::table('inv_job_items_returned')->where(['job_id' => $request->job_id, 'item_id' => $item->item_id])->first();
            if($returned_item)
            {
                $material_quantity[] = ['item_id' => $returned_item->item_id, 'quantity' => $item->quantity - $returned_item->quantity];
            }
            else
            {
                $material_quantity[] = ['item_id' => $item->item_id, 'quantity' => $item->quantity];
            }
        }
        if($invoice)
        {
            for($i = 0; $i < count($material_quantity); $i++)
            {
                $product = DB::table('products')->where('id', $material_quantity[$i]['item_id'])->first();
                $tax = Tax::where(['id' => $product->tax_id, 'tenant_id' => $tenant_id])->first();
                $invoice_item = InvoiceItems::where(['invoice_id' => $invoice->id, 'product_id' => $material_quantity[$i]['item_id']])->first();
                if($invoice_item)
                {
                    if($material_quantity[$i]['quantity'] != $invoice_item->quantity)
                    {
                        if($material_quantity[$i]['quantity'] == 0 || $material_quantity[$i]['quantity'] < 0)
                        {   
                            $invoice_item->delete();
                        }
                        else
                        {
                            $invoice_item->quantity = $material_quantity[$i]['quantity'];
                            if($tax)
                            {
                                $total_amount = ($product->price*$material_quantity[$i]['quantity'])*(1 + $tax->rate_percent/100);
                            }
                            else
                            {
                                $total_amount = $product->price*$material_quantity[$i]['quantity'];
                            }
                            $invoice_item->amount = floatval($total_amount);
                            $invoice_item->update();
                        }
                    }
                }
                else
                {
                    if($material_quantity[$i]['quantity'] != 0 && $material_quantity[$i]['quantity'] > 0)
                    {
                        if($tax)
                        {
                            $total_amount = ($product->price*$material_quantity[$i]['quantity'])*(1 + $tax->rate_percent/100);
                        }
                        else
                        {
                            $total_amount = $product->price*$material_quantity[$i]['quantity'];
                        }
                        $job = JobsMoving::where('job_id', $request->job_id)->first();
                        $new_invoice_item = new InvoiceItems();
                        $new_invoice_item->tenant_id = $tenant_id;
                        $new_invoice_item->invoice_id = $invoice->id;
                        $new_invoice_item->product_id = $material_quantity[$i]['item_id'];
                        $new_invoice_item->item_name = $product->name;
                        $new_invoice_item->item_summary = $product->description;
                        $new_invoice_item->type = 'Item';
                        $new_invoice_item->quantity = $material_quantity[$i]['quantity'];
                        $new_invoice_item->unit_price = $product->price;
                        $new_invoice_item->amount = floatval($total_amount);
                        $new_invoice_item->tax_id = $product->tax_id;
                        $new_invoice_item->created_at = Carbon::now();
                        $new_invoice_item->save();
                    }
                }
                
            }
        }
        else
        {
            return $this->sendError('notFound', 'Invoice Not Found.');
        }
        
        return $this->sendResponse($invoice, 'Update Generate Invoice  SuccessFully!.');
    }

    //start:: OHS checkilist secions
    public function getOhsChecklist(Request $request){
        try{
            if (empty($request->job_id) || $request->job_id <= 0) {
                return $this->sendError('notValid', 'Job ID is not valid.');
            }
            if (empty($request->tenant_id) || $request->tenant_id <= 0) {
                return $this->sendError('notValid', 'Tenant ID is not valid.');
            }
            $tenant_id = $request->tenant_id;
            $job_id = $request->job_id;
            $data = [];
            $pickup = [];
            $dropoff = [];

            $checklist = OHSChecklist::select('checklist as title')->where('tenant_id', $tenant_id)->get();
            $data['checklist'] = $checklist;

            $ohs_pickup_record = JobsMovingOHSChecklist::where(['job_id'=>$job_id, 'tenant_id'=>$tenant_id,'pickup_delivery'=>'Pickup'])->get();
            $ohs_delivery_record = JobsMovingOHSChecklist::where(['job_id'=>$job_id, 'tenant_id'=>$tenant_id,'pickup_delivery'=>'Delivery'])->get();
            //if OHS checklist already submitted
            // Pickup :: OHS checklist
            
            if($ohs_pickup_record->count()>0){
                foreach($ohs_pickup_record as $record){
                        $p['title'] = $record->checklist;
                        $p['risk'] = $record->risk;
                        $p['control_measures'] = $record->control_measures;
                        $pickup[]=$p;
                        unset($p);
                }
                $data['pickup_already_submit']='Y';
                $data['pickup']=$pickup;
            }else{
                $data['pickup_already_submit']='N';
            }
            if($ohs_delivery_record->count()>0){
                // Delivery :: OHS checklist
                $dropoff = [];
                foreach($ohs_delivery_record as $record){
                        $d['title'] = $record->checklist;
                        $d['risk'] = $record->risk;
                        $d['control_measures'] = $record->control_measures;
                        $dropoff[]=$d;
                        unset($d);
                }
                $data['delivery_already_submit']='Y';
                $data['delivery']=$dropoff;
            }else{
                $data['delivery_already_submit']='N';
            }  
            return $this->sendResponse($data, 'OHS Checklist.');
        }catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    public function addOhsChecklist(Request $request){
        try{
            if (empty($request->tenant_id) || $request->tenant_id <= 0) {
                return $this->sendError('notValid', 'Tenant ID is not valid.');
            }
            if (empty($request->user_id) || $request->user_id <= 0) {
                return $this->sendError('notValid', 'User ID is not valid.');
            }
            if (empty($request->job_id) || $request->job_id <= 0) {
                return $this->sendError('notValid', 'Job ID is not valid.');
            }
            if (empty($request->pickup_delivery) || $request->pickup_delivery =='') {
                return $this->sendError('notValid', 'Pickup_Delivery field is empty.');
            }
            $tenant_id = $request->tenant_id;
            $job_id = $request->job_id;
            $user_id = $request->user_id;
            $pickup_delivery = $request->pickup_delivery;
            $checklist = $request->checklist;

            if (!isset($checklist)) {
                return $this->sendError('notValid', 'Checklist is not valid.');
            }
            foreach ($checklist as $list) {
                $ohs = new JobsMovingOHSChecklist();
                $ohs->tenant_id = $tenant_id;
                $ohs->job_id = $job_id;
                $ohs->pickup_delivery = $pickup_delivery;
                $ohs->checklist = $list['title'];
                $ohs->risk = $list['risk'];
                $ohs->control_measures = $list['control_measures'];
                $ohs->created_by = $user_id;
                $ohs->save();
            }
            return $this->sendResponse('Success', $pickup_delivery.' OHS Checklist has been added.');
        }catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }
    //end:: OHS checkilist secions

    //start:: Vehicle checkilist secions

    public function getVehicleChecklist(Request $request){
        try{
            if (empty($request->tenant_id) || $request->tenant_id <= 0) {
                return $this->sendError('notValid', 'Tenant ID is not valid.');
            }
            if (empty($request->user_id) || $request->user_id <= 0) {
                return $this->sendError('notValid', 'User ID is not valid.');
            }
            // if (empty($request->vehicle_id) || $request->vehicle_id <= 0) {
            //     return $this->sendError('notValid', 'Job ID is not valid.');
            // }

            $tenant_id = $request->tenant_id;
            $user_id = $request->user_id;
            // $vehicle_id = $request->vehicle_id;
            $organization_setting = OrganisationSettings::where('tenant_id', $tenant_id)->first();
            $today = Carbon::now($organization_setting->timezone);
            $check_record = DailyDriverVehicleCheck::where([
                'tenant_id'=>$tenant_id,
                'driver_id'=>$user_id
                ])
                ->whereDate('date_of_check', '=', $today)->first();
            if($check_record){
                $data = [];
                $data['already_submit'] = 'Y';
                $vehicle = Vehicles::where('id', $check_record->vehicle_id)->pluck('vehicle_name')->first();
                $checklist_details = DailyDriverVehicleCheckDetails::select('checklist as title', 'status')->where(['daily_check_id'=>$check_record->id,'tenant_id' => $tenant_id])->get();

                $data['vehicle'] = $vehicle;
                $data['start_odometer'] = $check_record->start_odometer;
                $data['fuel_percent'] = $check_record->fuel_percent;
                $data['notes'] = $check_record->notes;
                $data['checklist'] = $checklist_details;
                return $this->sendResponse($data, 'Daily Vehicle Checklist.');
            }else{
                $vehicles = Vehicles::select('id','vehicle_name', 'license_plate_number')->where(['tenant_id' => $tenant_id,'active'=>'Y'])->get();
                $checklist_group = VehicleChecklistGroup::where(['tenant_id' => $tenant_id,'deleted'=>'N'])->get();
                $data = [];
                $data['already_submit'] = 'N';
                $data['vehicles']=$vehicles;
                if($checklist_group){
                    foreach ($checklist_group as $group) {
                        $list['group'] = $group->checklist_group;
                        $definitions = VehicleChecklistDefinition::select('checklist as title')->where(['group_id'=>$group->id, 'tenant_id' => $tenant_id, 'deleted'=>'N'])->get();
                        if ($definitions) {
                            $list['checklist'] = $definitions;
                            unset($definitions);
                        }
                        $data['list'][] = $list;
                        unset($list);
                    }
                }else{
                    return $this->sendError('notRecord', 'No vehicle checklist group available.');
                }
                return $this->sendResponse($data, 'Vehicle Checklist.');
            }
        }catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }

    public function addVehicleChecklist(Request $request){
        try{
            if (empty($request->tenant_id) || $request->tenant_id <= 0) {
                return $this->sendError('notValid', 'Tenant ID is not valid.');
            }
            if (empty($request->user_id) || $request->user_id <= 0) {
                return $this->sendError('notValid', 'User ID is not valid.');
            }
            if (empty($request->vehicle_id) || $request->vehicle_id <= 0) {
                return $this->sendError('notValid', 'Vehicle ID is not valid.');
            }

            $tenant_id = $request->tenant_id;
            $user_id = $request->user_id;
            $vehicle_id = $request->vehicle_id;
            $start_odometer = $request->start_odometer;
            $fuel_percent = $request->fuel_percent;
            $notes = $request->notes;
            $checklist = $request->checklist;

            $organization_setting = OrganisationSettings::where('tenant_id', $tenant_id)->first();
            $date_of_check = Carbon::now($organization_setting->timezone)->format('Y-m-d');
            $time_of_check = Carbon::now($organization_setting->timezone)->format('H:i:s');

            if (!isset($checklist)) {
                return $this->sendError('notValid', 'Checklist is not valid.');
            }

            $dailyCheck = new DailyDriverVehicleCheck();
            $dailyCheck->tenant_id = $tenant_id;
            $dailyCheck->driver_id = $user_id;
            $dailyCheck->vehicle_id = $vehicle_id;
            $dailyCheck->start_odometer = $start_odometer;
            $dailyCheck->fuel_percent = $fuel_percent;
            $dailyCheck->notes = $notes;
            $dailyCheck->date_of_check = $date_of_check;
            $dailyCheck->time_of_check = $time_of_check;

            if($dailyCheck->save()){
                foreach ($checklist as $list) {
                    $dailyCheckDetail = new DailyDriverVehicleCheckDetails();
                    $dailyCheckDetail->tenant_id = $tenant_id;
                    $dailyCheckDetail->daily_check_id = $dailyCheck->id;
                    $dailyCheckDetail->checklist_group = $list['group'];
                    $dailyCheckDetail->checklist = $list['title'];
                    $dailyCheckDetail->status = $list['status'];
                    $dailyCheckDetail->created_by = $user_id;
                    $dailyCheckDetail->save();
                    unset($dailyCheckDetail);
                }
            }else{
                return $this->sendError('Error', 'Something went wrong!');
            }
            return $this->sendResponse('Success','Vehicle Checklist has been added.');
        }catch (\Exception $ex) {
            return $this->sendError('Exception', $ex->getMessage());
        }
    }
    //start:: Vehicle checkilist secion
}
