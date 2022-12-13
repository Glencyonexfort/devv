<?php

namespace App\Http\Controllers;

use App\Companies;
use App\CRMActivityLog;
use App\CRMContactDetail;
use App\CRMContacts;
use App\CRMLeads;
use App\CRMLeadStatuses;
use App\CRMOpPipelineStatuses;
use App\CRMOpportunities;
use App\EmailTemplates;
use App\GicsBusinessCategory;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\InvoiceItems;
use App\InvoiceSetting;
use App\JobsMoving;
use App\JobsMovingLegs;
use App\JobsMovingPricingAdditional;
use App\JobsMovingLegsTeam;
use App\ListOptions;
use App\ListTypes;
use App\Mail\sendMail;
use App\MovingInventoryDefinitions;
use App\MovingInventoryGroups;
use App\OfflinePaymentMethod;
use App\OrganisationSettings;
use App\Otpx;
use App\Payment;
use App\PplPeople;
use App\Product;
use App\ProductCategories;
use App\PropertyCategoryOptions;
use App\QuoteItem;
use App\Quotes;
use App\RoleUser;
use App\SysCountries;
use App\SysModules;
use App\Tax;
use App\Tenant;
use App\TenantApiDetail;
use App\TenantModules;
use App\User;
use App\Vehicles;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $plan_id = $request->plan_id;
        $subscription_id = '';
        $countries = SysCountries::orderBy('country_name', 'asc')
            ->where('active', 'Y')
            ->get();
        //SysCountries::all()->orderBy('country_name');
        $business_industries = GicsBusinessCategory::all();
        $sys_modules = SysModules::select()->get();
        return view('registration.create', compact('countries', 'business_industries', 'sys_modules', 'plan_id', 'subscription_id'));
    }

    public function generateOpt(Request $request)
    {
        $identifier = $request->email;
        $user_name = $request->user_name;
        $otp = Otpx::generate($identifier);
        $email_data['to'] = $identifier;
        $email_data['from_email'] = 'no-reply@onexfort.com';
        $email_data['from_name'] = 'no-reply@onexfort.com';
        $email_data['email_subject'] = 'Onexfort Registration OTP';
        $email_data['email_body'] = 'Dear, <b>' . $user_name . '</b><br/>
                <p>You have received this email because you are in the process of subscribing to the software application https://onexfort.com. Please enter the following passcode to continue your registration process:</p>
                <h3>Passcode: ' . $otp . '</h3>
                <p>This Passcode is valid only for 5 minutes. After 5 minutes, you need to request a new passcode again.</p>
                <p>If you are not in the process of subscribing to onexfort.com, please ignore this email and no action is required.</p>
                <p>Thanks</p>
                <p>Team Onexfort</p>';
        Mail::to($email_data['to'])->send(new sendMail($email_data));
        $response['error'] = 0;
        return json_encode($response);
    }

    public function verifyOpt(Request $request)
    {
        $verify = Otpx::validate($request->email, $request->otp_code);
        if ($verify['status'] == true) {
            $response['error'] = 0;
            $response['message'] = 'OTP Matched!';
        } else {
            $response['error'] = 1;
            $response['message'] = $verify['message'];
        }
        return json_encode($response);
    }

    public function checkEmail(Request $request)
    {
        $email = $request->input('business_email');
        $isExists = \App\User::where('email', $email)->first();
        //var_dump($isExists);exit;
        if ($isExists) {
            return response()->json('Email address is already taken.');
        } else {
            return response()->json(true);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validate OTP First
        $verify = Otpx::validate($request->business_email, $request->otp_code);
        if ($verify['status'] == true) {
        }else{
            Session::flash('message', 'OTP is invalid or Expired!');
            Session::flash('alert-class', 'alert-warning');
            return redirect('/registration');
        }
        //end
        $countryId = 8;
        $currency_code = 'AUD';
        $currency_symbol = '$';
        if ($request->country && $request->country != '') {
            $countryId = $request->country;
        }
        $sys_country = SysCountries::where('country_id', $countryId)->first();
        if ($sys_country) {
            if ($sys_country->currency_code) {
                $currency_code = $sys_country->currency_code;
            }
            if ($sys_country->currency_symbol) {
                $currency_symbol = $sys_country->currency_symbol;
            }
        }
        $currency = \App\Currency::where('currency_symbol', $currency_symbol)
            ->where('currency_code', $currency_code)
            ->first();
        if ($currency) {
            $currency_id = $currency->id;
        }

        $getSysModule = SysModules::findOrFail($request->sys_module_id);
        $getSysModuleName = $getSysModule->sys_job_type;
        $getSysModuleBusinessCatID = $getSysModule->gics_business_category_id;

        $tenant = new Tenant();
        $name = $request->first_name;
        if ($request->last_name) {
            $name .= ' ' . $request->last_name;
        }
        $tenant->tenant_name = $request->business_name;
        $tenant->tenant_country_id = $countryId;
        $tenant->gics_business_category_id = $getSysModuleBusinessCatID;
        if ($request->plan_id && $request->plan_id != '') {
            $tenant->billing_subscription_id = $request->plan_id;
        }
        $tenant->created_date = date('Y-m-d');
        $tenant->save();

        $tenant_id = $tenant->id;

        //Create Tenant Module
        $tenantModule = new TenantModules();
        $tenantModule->tenant_id = $tenant_id;
        $tenantModule->sys_module_id = $request->sys_module_id;
        $tenantModule->save();

        $find_user = User::where('email', $request->business_email)->first();

        if (!$find_user) {
            $user = new User();
            $user->tenant_id = $tenant_id;
            $user->name = $name;
            $user->email = $request->business_email;
            $user->password = Hash::make($request->password);
            $user->mobile = $request->mobile;
            $user->locale = 'en';
            $user->status = 'active';
            $user->login = 'enable';
            $user->save();
            $user_id = $user->id;
        } else {
            $user_id = $find_user->id;
        }

        //dd($user_id.' - '.$tenant_id);

        $checkRoleUser = RoleUser::where('user_id', $user_id)
            ->first();
        //where('tenant_id', $tenant_id)

        if (!$checkRoleUser) {
            $role_user = new RoleUser();
            $role_user->tenant_id = $tenant_id;
            $role_user->user_id = $user_id;
            $role_user->role_id = 1;
            $role_user->save();
        } /*else {
        $checkRoleUser->tenant_id = $tenant_id;
        $checkRoleUser->role_id = 1;
        $checkRoleUser->id = 1;
        $checkRoleUser->save();
        }*/

        $pplPeople = new PplPeople();
        $pplPeople->tenant_id = $tenant_id;
        $pplPeople->employee_number = 'A01';
        $pplPeople->first_name = $request->first_name;
        $pplPeople->last_name = $request->last_name;
        $pplPeople->mobile = $request->mobile;
        $pplPeople->is_system_user = 'Y';
        $pplPeople->user_id = $user_id;
        $pplPeople->save();

        $organisation_settings = new OrganisationSettings();
        $organisation_settings->tenant_id = $tenant_id;
        $organisation_settings->company_name = $request->business_name;
        $organisation_settings->company_email = $request->business_email;
        $organisation_settings->company_phone = $request->business_phone;
        /*if ($request->hasFile('business_logo')) {  //check the file present or not
        $image = $request->file('business_logo'); //get the file
        $imgName = time() . '.' . $image->getClientOriginalExtension(); //get the  file extention
        $destinationPath = public_path('/user-uploads/tenants'); //public path folder dir
        $image->move($destinationPath, $imgName);  //mve to destination you mentioned
        $organisation_settings->tenant_logo = url('/user-uploads/tenants/' . $imgName);
        }*/
        $organisation_settings->logo = '';
        //'ZBsBf7tHSYR1eKIMhUDwsIhf3B57GAiKW8uP7C25.png';
        $organisation_settings->login_background = 'login-background.jpg';
        $organisation_settings->business_address_1 = $request->business_address1;
        if ($request->business_address2 && $request->business_address2 != '') {
            $organisation_settings->business_address_2 = $request->business_address2;
        }

        $organisation_settings->business_address_city = $request->city;
        $organisation_settings->business_address_postcode = $request->postcode;
        $organisation_settings->business_address_state = $request->state;
        $organisation_settings->business_country_id = $countryId;
        $organisation_settings->currency_id = $currency_id;

        $organisation_settings->currency_code = $currency_code;
        $organisation_settings->currency_symbol = $currency_symbol;

        $organisation_settings->timezone = 'Australia/Melbourne';
        $organisation_settings->date_format = 'd-m-Y';
        $organisation_settings->date_picker_format = 'dd-mm-yyyy';
        $organisation_settings->time_format = 'h:i A';
        $organisation_settings->locale = 'en';
        $organisation_settings->latitude = 26.9124336;
        $organisation_settings->longitude = 75.7872709;
        $organisation_settings->leaves_start_from = 'joining_date';
        $organisation_settings->active_theme = 'default';
        $organisation_settings->last_updated_by = 1;
        $organisation_settings->currency_converter_key = '6c12788708871d0c499d';
        $organisation_settings->task_self = 'yes';
        $organisation_settings->purchase_code = 'bbcf97ce-1c01-44b5-b4d9-772bf5fb6bed';
        $organisation_settings->save();

        $companies = new Companies();
        $company_address = $request->business_address1;

        if ($request->city) {
            $company_address .= ', ' . $request->city;
        }

        if ($request->state) {
            $company_address .= ', ' . $request->state;
        }

        if ($request->postcode) {
            $company_address .= ', ' . $request->postcode;
        }

        $companies->tenant_id = $tenant_id;
        $companies->company_name = $request->business_name;
        $companies->address = $company_address;
        $companies->contact_name = $name;
        $companies->email = $request->business_email;
        $companies->phone = $request->mobile;
        $companies->abn = $request->abn;

        if ($request->hasFile('business_logo')) { //check the file present or not
            $image = $request->file('business_logo'); //get the file
            $imgName = time() . '.' . $image->getClientOriginalExtension(); //get the  file extention
            $destinationPath = public_path('/user-uploads/company-logo'); //public path folder dir
            $image->move($destinationPath, $imgName); //mve to destination you mentioned
            $companies->logo = $imgName;
        }

        //if ($request->hasFile('business_logo')) {  //check the file present or not
        //$image2 = $request->file('business_logo'); //get the file
        //$name2 = 'company-'.time() . '.' . $image2->getClientOriginalExtension(); //get the  file extention
        //$destinationPath2 = public_path('/user-uploads/companies/'); //public path folder dir
        //copy($destinationPath.'/'.$imgName , $destinationPath2.$imgName);  //mve to destination you mentioned
        //$companies->logo = url('/user-uploads/companies/' . $imgName);
        //}

        $companies->default1 = 'Y';
        $companies->active = 'Y';
        $companies->save();
        //dd($request);
        //Op Frequency List Type and its Options
        $listType = ListTypes::where('tenant_id', $tenant_id)
            ->where('list_name', 'Op Frequency')
            ->first();
        if (!$listType) {
            $listType = new ListTypes();
            $listType->tenant_id = $tenant_id;
            $listType->list_name = 'Op Frequency';
            $listType->save();
            $OpFreListType = $listType->id;
        } else {
            $OpFreListType = $listType->id;
        }

        $listOption = ListOptions::where('tenant_id', $tenant_id)
            ->where('list_type_id', $OpFreListType)
            ->where('list_option', 'One-time')
            ->first();

        if (!$listOption) {
            $listOption = new ListOptions();
            $listOption->tenant_id = $tenant_id;
            $listOption->list_type_id = $OpFreListType;
            $listOption->list_option = 'One-time';
            $listOption->option_value = '';
            $listOption->save();
        }

        $listOption = ListOptions::where('tenant_id', $tenant_id)
            ->where('list_type_id', $OpFreListType)
            ->where('list_option', 'Weekly')
            ->first();

        if (!$listOption) {
            $listOption = new ListOptions();
            $listOption->tenant_id = $tenant_id;
            $listOption->list_type_id = $OpFreListType;
            $listOption->list_option = 'Weekly';
            $listOption->option_value = '';
            $listOption->save();
        }

        $listOption = ListOptions::where('tenant_id', $tenant_id)
            ->where('list_type_id', $OpFreListType)
            ->where('list_option', 'Fortnightly')
            ->first();

        if (!$listOption) {
            $listOption = new ListOptions();
            $listOption->tenant_id = $tenant_id;
            $listOption->list_type_id = $OpFreListType;
            $listOption->list_option = 'Fortnightly';
            $listOption->option_value = '';
            $listOption->save();
        }

        $listOption = ListOptions::where('tenant_id', $tenant_id)
            ->where('list_type_id', $OpFreListType)
            ->where('list_option', 'Monthly')
            ->first();

        if (!$listOption) {
            $listOption = new ListOptions();
            $listOption->tenant_id = $tenant_id;
            $listOption->list_type_id = $OpFreListType;
            $listOption->list_option = 'Monthly';
            $listOption->option_value = '';
            $listOption->save();
        }

        //Contact Type list Type and its options
        $listType = ListTypes::where('tenant_id', $tenant_id)
            ->where('list_name', 'Contact Type')
            ->first();
        if (!$listType) {
            $listType = new ListTypes();
            $listType->tenant_id = $tenant_id;
            $listType->list_name = 'Contact Type';
            $listType->save();
            $contactTypeListType = $listType->id;
        } else {
            $contactTypeListType = $listType->id;
        }

        $listOption = ListOptions::where('tenant_id', $tenant_id)
            ->where('list_type_id', $contactTypeListType)
            ->where('list_option', 'Office')
            ->first();

        if (!$listOption) {
            $listOption = new ListOptions();
            $listOption->tenant_id = $tenant_id;
            $listOption->list_type_id = $contactTypeListType;
            $listOption->list_option = 'Office';
            $listOption->option_value = '';
            $listOption->save();
        }

        $listOption = ListOptions::where('tenant_id', $tenant_id)
            ->where('list_type_id', $contactTypeListType)
            ->where('list_option', 'Mobile')
            ->first();

        if (!$listOption) {
            $listOption = new ListOptions();
            $listOption->tenant_id = $tenant_id;
            $listOption->list_type_id = $contactTypeListType;
            $listOption->list_option = 'Mobile';
            $listOption->option_value = '';
            $listOption->save();
        }

        $listOption = ListOptions::where('tenant_id', $tenant_id)
            ->where('list_type_id', $contactTypeListType)
            ->where('list_option', 'Home')
            ->first();

        if (!$listOption) {
            $listOption = new ListOptions();
            $listOption->tenant_id = $tenant_id;
            $listOption->list_type_id = $contactTypeListType;
            $listOption->list_option = 'Home';
            $listOption->option_value = '';
            $listOption->save();
        }

        $listOption = ListOptions::where('tenant_id', $tenant_id)
            ->where('list_type_id', $contactTypeListType)
            ->where('list_option', 'Direct')
            ->first();

        if (!$listOption) {
            $listOption = new ListOptions();
            $listOption->tenant_id = $tenant_id;
            $listOption->list_type_id = $contactTypeListType;
            $listOption->list_option = 'Direct';
            $listOption->option_value = '';
            $listOption->save();
        }

        $listOption = ListOptions::where('tenant_id', $tenant_id)
            ->where('list_type_id', $contactTypeListType)
            ->where('list_option', 'Email')
            ->first();

        if (!$listOption) {
            $listOption = new ListOptions();
            $listOption->tenant_id = $tenant_id;
            $listOption->list_type_id = $contactTypeListType;
            $listOption->list_option = 'Email';
            $listOption->option_value = '';
            $listOption->save();
        }

        $listOption = ListOptions::where('tenant_id', $tenant_id)
            ->where('list_type_id', $contactTypeListType)
            ->where('list_option', 'URL')
            ->first();

        if (!$listOption) {
            $listOption = new ListOptions();
            $listOption->tenant_id = $tenant_id;
            $listOption->list_type_id = $contactTypeListType;
            $listOption->list_option = 'URL';
            $listOption->option_value = '';
            $listOption->save();
        }

        $listOption = ListOptions::where('tenant_id', $tenant_id)
            ->where('list_type_id', $contactTypeListType)
            ->where('list_option', 'Other')
            ->first();

        if (!$listOption) {
            $listOption = new ListOptions();
            $listOption->tenant_id = $tenant_id;
            $listOption->list_type_id = $contactTypeListType;
            $listOption->list_option = 'Other';
            $listOption->option_value = '';
            $listOption->save();
        }

        //Job Status list Type and its options
        $listType = ListTypes::where('tenant_id', $tenant_id)
            ->where('list_name', 'Job Status')
            ->first();
        if (!$listType) {
            $listType = new ListTypes();
            $listType->tenant_id = $tenant_id;
            $listType->list_name = 'Job Status';
            $listType->save();
            $jobStatusListType = $listType->id;
        } else {
            $jobStatusListType = $listType->id;
        }

        $listOption = ListOptions::where('tenant_id', $tenant_id)
            ->where('list_type_id', $jobStatusListType)
            ->where('list_option', 'New')
            ->first();

        if (!$listOption) {
            $listOption = new ListOptions();
            $listOption->tenant_id = $tenant_id;
            $listOption->list_type_id = $jobStatusListType;
            $listOption->list_option = 'New';
            $listOption->option_value = '';
            $listOption->save();
        }

        $listOption = ListOptions::where('tenant_id', $tenant_id)
            ->where('list_type_id', $jobStatusListType)
            ->where('list_option', 'Confirmed')
            ->first();

        if (!$listOption) {
            $listOption = new ListOptions();
            $listOption->tenant_id = $tenant_id;
            $listOption->list_type_id = $jobStatusListType;
            $listOption->list_option = 'Confirmed';
            $listOption->option_value = '';
            $listOption->save();
        }

        $listOption = ListOptions::where('tenant_id', $tenant_id)
            ->where('list_type_id', $jobStatusListType)
            ->where('list_option', 'Quoted')
            ->first();

        if (!$listOption) {
            $listOption = new ListOptions();
            $listOption->tenant_id = $tenant_id;
            $listOption->list_type_id = $jobStatusListType;
            $listOption->list_option = 'Quoted';
            $listOption->option_value = '';
            $listOption->save();
        }

        $listOption = ListOptions::where('tenant_id', $tenant_id)
            ->where('list_type_id', $jobStatusListType)
            ->where('list_option', 'Operations')
            ->first();

        if (!$listOption) {
            $listOption = new ListOptions();
            $listOption->tenant_id = $tenant_id;
            $listOption->list_type_id = $jobStatusListType;
            $listOption->list_option = 'Operations';
            $listOption->option_value = '';
            $listOption->save();
        }

        $listOption = ListOptions::where('tenant_id', $tenant_id)
            ->where('list_type_id', $jobStatusListType)
            ->where('list_option', 'Completed')
            ->first();

        if (!$listOption) {
            $listOption = new ListOptions();
            $listOption->tenant_id = $tenant_id;
            $listOption->list_type_id = $jobStatusListType;
            $listOption->list_option = 'Completed';
            $listOption->option_value = '';
            $listOption->save();
        }

        $listOption = ListOptions::where('tenant_id', $tenant_id)
            ->where('list_type_id', $jobStatusListType)
            ->where('list_option', 'Deleted')
            ->first();

        if (!$listOption) {
            $listOption = new ListOptions();
            $listOption->tenant_id = $tenant_id;
            $listOption->list_type_id = $jobStatusListType;
            $listOption->list_option = 'Deleted';
            $listOption->option_value = '';
            $listOption->save();
        }

        //Lead Info list Type and its options
        $listType = ListTypes::where('tenant_id', $tenant_id)
            ->where('list_name', 'Lead Info')
            ->first();
        if (!$listType) {
            $listType = new ListTypes();
            $listType->tenant_id = $tenant_id;
            $listType->list_name = 'Lead Info';
            $listType->save();
            $leadInfoListType = $listType->id;
        } else {
            $leadInfoListType = $listType->id;
        }

        $listOption = ListOptions::where('tenant_id', $tenant_id)
            ->where('list_type_id', $leadInfoListType)
            ->where('list_option', 'Google')
            ->first();

        if (!$listOption) {
            $listOption = new ListOptions();
            $listOption->tenant_id = $tenant_id;
            $listOption->list_type_id = $leadInfoListType;
            $listOption->list_option = 'Google';
            $listOption->option_value = '';
            $listOption->save();
        }

        $listOption = ListOptions::where('tenant_id', $tenant_id)
            ->where('list_type_id', $leadInfoListType)
            ->where('list_option', 'Facebook')
            ->first();

        if (!$listOption) {
            $listOption = new ListOptions();
            $listOption->tenant_id = $tenant_id;
            $listOption->list_type_id = $leadInfoListType;
            $listOption->list_option = 'Facebook';
            $listOption->option_value = '';
            $listOption->save();
        }

        $listOption = ListOptions::where('tenant_id', $tenant_id)
            ->where('list_type_id', $leadInfoListType)
            ->where('list_option', 'Friend')
            ->first();

        if (!$listOption) {
            $listOption = new ListOptions();
            $listOption->tenant_id = $tenant_id;
            $listOption->list_type_id = $leadInfoListType;
            $listOption->list_option = 'Friend';
            $listOption->option_value = '';
            $listOption->save();
        }

        $listOption = ListOptions::where('tenant_id', $tenant_id)
            ->where('list_type_id', $leadInfoListType)
            ->where('list_option', 'Other')
            ->first();

        if (!$listOption) {
            $listOption = new ListOptions();
            $listOption->tenant_id = $tenant_id;
            $listOption->list_type_id = $leadInfoListType;
            $listOption->list_option = '';
            $listOption->option_value = 'Other';
            $listOption->save();
        }

        //Inert CRM Lead Statuses
        $crmLeadStatus = CRMLeadStatuses::where('tenant_id', $tenant_id)
            ->where('lead_status', 'Potential')
            ->first();
        if (!$crmLeadStatus) {
            $crmLeadStatus = new CRMLeadStatuses();
            $crmLeadStatus->tenant_id = $tenant_id;
            $crmLeadStatus->lead_status = 'Potential';
            $crmLeadStatus->sort_order = '1';
            $crmLeadStatus->created_by = '1';
            $crmLeadStatus->save();
        }

        $crmLeadStatus = CRMLeadStatuses::where('tenant_id', $tenant_id)
            ->where('lead_status', 'Qualified')
            ->first();
        if (!$crmLeadStatus) {
            $crmLeadStatus = new CRMLeadStatuses();
            $crmLeadStatus->tenant_id = $tenant_id;
            $crmLeadStatus->lead_status = 'Qualified';
            $crmLeadStatus->sort_order = '2';
            $crmLeadStatus->created_by = '1';
            $crmLeadStatus->save();
        }

        $crmLeadStatus = CRMLeadStatuses::where('tenant_id', $tenant_id)
            ->where('lead_status', 'Customer')
            ->first();
        if (!$crmLeadStatus) {
            $crmLeadStatus = new CRMLeadStatuses();
            $crmLeadStatus->tenant_id = $tenant_id;
            $crmLeadStatus->lead_status = 'Customer';
            $crmLeadStatus->sort_order = '3';
            $crmLeadStatus->created_by = '1';
            $crmLeadStatus->save();
        }

        $crmLeadStatus = CRMLeadStatuses::where('tenant_id', $tenant_id)
            ->where('lead_status', 'Interested')
            ->first();
        if (!$crmLeadStatus) {
            $crmLeadStatus = new CRMLeadStatuses();
            $crmLeadStatus->tenant_id = $tenant_id;
            $crmLeadStatus->lead_status = 'Interested';
            $crmLeadStatus->sort_order = '4';
            $crmLeadStatus->created_by = '1';
            $crmLeadStatus->save();
        }

        $crmLeadStatus = CRMLeadStatuses::where('tenant_id', $tenant_id)
            ->where('lead_status', 'Canceled')
            ->first();
        if (!$crmLeadStatus) {
            $crmLeadStatus = new CRMLeadStatuses();
            $crmLeadStatus->tenant_id = $tenant_id;
            $crmLeadStatus->lead_status = 'Canceled';
            $crmLeadStatus->sort_order = '5';
            $crmLeadStatus->created_by = '1';
            $crmLeadStatus->save();
        }

        $crmLeadStatus = CRMLeadStatuses::where('tenant_id', $tenant_id)
            ->where('lead_status', 'Not Interested')
            ->first();
        if (!$crmLeadStatus) {
            $crmLeadStatus = new CRMLeadStatuses();
            $crmLeadStatus->tenant_id = $tenant_id;
            $crmLeadStatus->lead_status = 'Not Interested';
            $crmLeadStatus->sort_order = '6';
            $crmLeadStatus->created_by = '1';
            $crmLeadStatus->save();
        }

        //Insert CRM Op Pipeline Statuses
        $crmOpPipelineStatus = CRMOpPipelineStatuses::where('tenant_id', $tenant_id)
            ->where('pipeline_status', 'New')
            ->first();
        if (!$crmOpPipelineStatus) {
            $crmOpPipelineStatus = new CRMOpPipelineStatuses();
            $crmOpPipelineStatus->tenant_id = $tenant_id;
            $crmOpPipelineStatus->pipeline_id = '1';
            $crmOpPipelineStatus->pipeline_status = 'New';
            $crmOpPipelineStatus->sort_order = '1';
            $crmOpPipelineStatus->created_by = '1';
            $crmOpPipelineStatus->save();
        }

        $crmOpPipelineStatus = CRMOpPipelineStatuses::where('tenant_id', $tenant_id)
            ->where('pipeline_status', 'Quote Sent')
            ->first();
        if (!$crmOpPipelineStatus) {
            $crmOpPipelineStatus = new CRMOpPipelineStatuses();
            $crmOpPipelineStatus->tenant_id = $tenant_id;
            $crmOpPipelineStatus->pipeline_id = '1';
            $crmOpPipelineStatus->pipeline_status = 'Quote Sent';
            $crmOpPipelineStatus->sort_order = '2';
            $crmOpPipelineStatus->created_by = '1';
            $crmOpPipelineStatus->save();
        }

        $crmOpPipelineStatus = CRMOpPipelineStatuses::where('tenant_id', $tenant_id)
            ->where('pipeline_status', 'Follow up 1')
            ->first();
        if (!$crmOpPipelineStatus) {
            $crmOpPipelineStatus = new CRMOpPipelineStatuses();
            $crmOpPipelineStatus->tenant_id = $tenant_id;
            $crmOpPipelineStatus->pipeline_id = '1';
            $crmOpPipelineStatus->pipeline_status = 'Follow up 1';
            $crmOpPipelineStatus->sort_order = '3';
            $crmOpPipelineStatus->created_by = '1';
            $crmOpPipelineStatus->save();
        }

        $crmOpPipelineStatus = CRMOpPipelineStatuses::where('tenant_id', $tenant_id)
            ->where('pipeline_status', 'Follow up 2')
            ->first();
        if (!$crmOpPipelineStatus) {
            $crmOpPipelineStatus = new CRMOpPipelineStatuses();
            $crmOpPipelineStatus->tenant_id = $tenant_id;
            $crmOpPipelineStatus->pipeline_id = '1';
            $crmOpPipelineStatus->pipeline_status = 'Follow up 2';
            $crmOpPipelineStatus->sort_order = '4';
            $crmOpPipelineStatus->created_by = '1';
            $crmOpPipelineStatus->save();
        }

        $crmOpPipelineStatus = CRMOpPipelineStatuses::where('tenant_id', $tenant_id)
            ->where('pipeline_status', 'Confirmed')
            ->first();
        if (!$crmOpPipelineStatus) {
            $crmOpPipelineStatus = new CRMOpPipelineStatuses();
            $crmOpPipelineStatus->tenant_id = $tenant_id;
            $crmOpPipelineStatus->pipeline_id = '2';
            $crmOpPipelineStatus->pipeline_status = 'Confirmed';
            $crmOpPipelineStatus->sort_order = '5';
            $crmOpPipelineStatus->created_by = '1';
            $crmOpPipelineStatus->save();
        }

        $crmOpPipelineStatus = CRMOpPipelineStatuses::where('tenant_id', $tenant_id)
            ->where('pipeline_status', 'Lost')
            ->first();
        if (!$crmOpPipelineStatus) {
            $crmOpPipelineStatus = new CRMOpPipelineStatuses();
            $crmOpPipelineStatus->tenant_id = $tenant_id;
            $crmOpPipelineStatus->pipeline_id = '3';
            $crmOpPipelineStatus->pipeline_status = 'Lost';
            $crmOpPipelineStatus->sort_order = '6';
            $crmOpPipelineStatus->created_by = '1';
            $crmOpPipelineStatus->save();
        }

        // $tenant_api_details = new TenantApiDetail();
        // $tenant_api_details->tenant_id = $tenant_id;
        // $tenant_api_details->provider = 'PostMarkApp';
        // $tenant_api_details->user = 'moventum';
        // $tenant_api_details->secret = 'Welcome$01';
        // $tenant_api_details->server_id = '5148529';
        // $tenant_api_details->smtp_user = 'bf592896-8c09-4c83-9c16-0ab076f5fda8';
        // $tenant_api_details->smtp_secret = 'bf592896-8c09-4c83-9c16-0ab076f5fda8';
        // $tenant_api_details->from_email = 'email@onexfort.com';
        // $tenant_api_details->to_email = 'email@onexfort.com';
        // $tenant_api_details->url = 'https://account.postmarkapp.com/';
        // $tenant_api_details->save();

        $tenant_googleMaps_ApiDetails = new TenantApiDetail();
        $tenant_googleMaps_ApiDetails->tenant_id = $tenant_id;
        $tenant_googleMaps_ApiDetails->provider = 'GoogleMaps';
        $tenant_googleMaps_ApiDetails->account_key = 'AIzaSyB2SMtaVBlqC5v72gqS716BX8R5oXklaFc';
        $tenant_googleMaps_ApiDetails->save();

        $invoice_settings = new InvoiceSetting();
        $invoice_settings->tenant_id = $tenant_id;
        $invoice_settings->invoice_prefix = 'INV';
        $invoice_settings->template = 'invoice-1';
        $invoice_settings->due_after = 15;
        $invoice_settings->invoice_terms = 'Thank you for your business. Please process this invoice within the due date.';
        $invoice_settings->show_gst = 'yes';
        $invoice_settings->save();

        // Job Moving Pricing Additional 
            $jmpa_model = new JobsMovingPricingAdditional();
            $jmpa_model->tenant_id = $tenant_id;
            $jmpa_model->save();
        // end

        $offline_payment_methods_array = (object) array(
            (object) array(
                'name' => 'Bank Transfer',
                'description' => '<p>Bank Transfer</p>',
                'status' => 'yes',
            ),
            (object) array(
                'name' => 'Cheque',
                'description' => '<p>Cheque</p>',
                'status' => 'yes',
            ),
            (object) array(
                'name' => 'Cash',
                'description' => '<p>Cash</p>',
                'status' => 'yes',
            ),
        );
        foreach ($offline_payment_methods_array as $offline_payment) {
            $offline_payment_methods = OfflinePaymentMethod::where('tenant_id', $tenant_id)
                ->where('name', $offline_payment->name)
                ->first();
            if (!$offline_payment_methods) {
                $offline_payment_methods = new OfflinePaymentMethod();
                $offline_payment_methods->tenant_id = $tenant_id;
                $offline_payment_methods->name = $offline_payment->name;
                $offline_payment_methods->description = $offline_payment->description;
                $offline_payment_methods->status = $offline_payment->status;
                $offline_payment_methods->save();
            }
        }

        //check if GST Registered
        if ($request->gst_registered && $request->gst_registered == '1') {
            $checkTax = Tax::where('tenant_id', $tenant_id)
                ->where('tax_name', 'GST')
                ->first();
            if (!$checkTax) {
                $addTax = new Tax();
                $addTax->tenant_id = $tenant_id;
                $addTax->tax_name = 'GST';
                $addTax->rate_percent = '10';
                $addTax->save();

            }
        }

        //For Moving Module
        if ($getSysModuleName == 'Moving') {

            //Job Type list Type and its options
            $listType = ListTypes::where('tenant_id', $tenant_id)
                ->where('list_name', 'Job Type')
                ->first();
            if (!$listType) {
                $listType = new ListTypes();
                $listType->tenant_id = $tenant_id;
                $listType->list_name = 'Job Type';
                $listType->save();
                $jobTypeListType = $listType->id;
            } else {
                $jobTypeListType = $listType->id;
            }

            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $jobTypeListType)
                ->where('list_option', 'Pickup')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $jobTypeListType;
                $listOption->list_option = 'Pickup';
                $listOption->option_value = '';
                $listOption->save();
            }

            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $jobTypeListType)
                ->where('list_option', 'Storage')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $jobTypeListType;
                $listOption->list_option = 'Storage';
                $listOption->option_value = '';
                $listOption->save();
            }

            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $jobTypeListType)
                ->where('list_option', 'Delivery')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $jobTypeListType;
                $listOption->list_option = 'Delivery';
                $listOption->option_value = '';
                $listOption->save();
            }

            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $jobTypeListType)
                ->where('list_option', 'On Hold')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $jobTypeListType;
                $listOption->list_option = 'On Hold';
                $listOption->option_value = '';
                $listOption->save();
            }

            //Leg Status list Type and its options
            $listType = ListTypes::where('tenant_id', $tenant_id)
                ->where('list_name', 'Leg Status')
                ->first();
            if (!$listType) {
                $listType = new ListTypes();
                $listType->tenant_id = $tenant_id;
                $listType->list_name = 'Leg Status';
                $listType->save();
                $legStatusListType = $listType->id;
            } else {
                $legStatusListType = $listType->id;
            }

            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $legStatusListType)
                ->where('list_option', 'New')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $legStatusListType;
                $listOption->list_option = 'New';
                $listOption->option_value = '';
                $listOption->save();
            }

            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $legStatusListType)
                ->where('list_option', 'Confirmed')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $legStatusListType;
                $listOption->list_option = 'Confirmed';
                $listOption->option_value = '';
                $listOption->save();
            }

            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $legStatusListType)
                ->where('list_option', 'Picked')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $legStatusListType;
                $listOption->list_option = 'Picked';
                $listOption->option_value = '';
                $listOption->save();
            }

            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $legStatusListType)
                ->where('list_option', 'Delivered')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $legStatusListType;
                $listOption->list_option = 'Delivered';
                $listOption->option_value = '';
                $listOption->save();
            }

            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $legStatusListType)
                ->where('list_option', 'Completed')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $legStatusListType;
                $listOption->list_option = 'Completed';
                $listOption->option_value = '';
                $listOption->save();
            }            

            //Payment Status list Type and its options
            $listType = ListTypes::where('tenant_id', $tenant_id)
                ->where('list_name', 'Payment Status')
                ->first();
            if (!$listType) {
                $listType = new ListTypes();
                $listType->tenant_id = $tenant_id;
                $listType->list_name = 'Payment Status';
                $listType->save();
                $paymentStatusListType = $listType->id;
            } else {
                $paymentStatusListType = $listType->id;
            }

            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $paymentStatusListType)
                ->where('list_option', 'Not Paid')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $paymentStatusListType;
                $listOption->list_option = 'Not Paid';
                $listOption->option_value = '';
                $listOption->save();
            }

            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $paymentStatusListType)
                ->where('list_option', 'Deposit')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $paymentStatusListType;
                $listOption->list_option = 'Deposit';
                $listOption->option_value = '';
                $listOption->save();
            }

            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $paymentStatusListType)
                ->where('list_option', 'Fully Paid')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $paymentStatusListType;
                $listOption->list_option = 'Fully Paid';
                $listOption->option_value = '';
                $listOption->save();
            }

            //Price Structure list Type and its options
            $listType = ListTypes::where('tenant_id', $tenant_id)
                ->where('list_name', 'Price Structure')
                ->first();
            if (!$listType) {
                $listType = new ListTypes();
                $listType->tenant_id = $tenant_id;
                $listType->list_name = 'Price Structure';
                $listType->save();
                $priceStructureListType = $listType->id;
            } else {
                $priceStructureListType = $listType->id;
            }

            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $priceStructureListType)
                ->where('list_option', 'Fixed')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $priceStructureListType;
                $listOption->list_option = 'Fixed';
                $listOption->option_value = '';
                $listOption->save();
            }

            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $priceStructureListType)
                ->where('list_option', 'Hourly')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $priceStructureListType;
                $listOption->list_option = 'Hourly';
                $listOption->option_value = '';
                $listOption->save();
            }

            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $priceStructureListType)
                ->where('list_option', 'Other')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $priceStructureListType;
                $listOption->list_option = 'Other';
                $listOption->option_value = '';
                $listOption->save();
            }

            //Vehicle Unavailability list Type and its options
            $listType = ListTypes::where('tenant_id', $tenant_id)
                ->where('list_name', 'Vehicle Unavailability')
                ->first();
            if (!$listType) {
                $listType = new ListTypes();
                $listType->tenant_id = $tenant_id;
                $listType->list_name = 'Vehicle Unavailability';
                $listType->save();
                $vehicleUnavailabilityListType = $listType->id;
            } else {
                $vehicleUnavailabilityListType = $listType->id;
            }

            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $vehicleUnavailabilityListType)
                ->where('list_option', 'Maintenance')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $vehicleUnavailabilityListType;
                $listOption->list_option = 'Maintenance';
                $listOption->option_value = '';
                $listOption->save();
            }
            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $vehicleUnavailabilityListType)
                ->where('list_option', 'Breakdown')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $vehicleUnavailabilityListType;
                $listOption->list_option = 'Breakdown';
                $listOption->option_value = '';
                $listOption->save();
            }
            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $vehicleUnavailabilityListType)
                ->where('list_option', 'Other')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $vehicleUnavailabilityListType;
                $listOption->list_option = 'Other';
                $listOption->option_value = '';
                $listOption->save();
            }

            //People Unavailability list Type and its options
            $listType = ListTypes::where('tenant_id', $tenant_id)
                ->where('list_name', 'People Unavailability')
                ->first();
            if (!$listType) {
                $listType = new ListTypes();
                $listType->tenant_id = $tenant_id;
                $listType->list_name = 'People Unavailability';
                $listType->save();
                $peopleUnavailabilityListType = $listType->id;
            } else {
                $peopleUnavailabilityListType = $listType->id;
            }

            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $peopleUnavailabilityListType)
                ->where('list_option', 'Sick')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $peopleUnavailabilityListType;
                $listOption->list_option = 'Sick';
                $listOption->option_value = '';
                $listOption->save();
            }
            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $peopleUnavailabilityListType)
                ->where('list_option', 'Vacation')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $peopleUnavailabilityListType;
                $listOption->list_option = 'Vacation';
                $listOption->option_value = '';
                $listOption->save();
            }
            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $peopleUnavailabilityListType)
                ->where('list_option', 'Personal')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $peopleUnavailabilityListType;
                $listOption->list_option = 'Personal';
                $listOption->option_value = '';
                $listOption->save();
            }
            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $peopleUnavailabilityListType)
                ->where('list_option', 'Other')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $peopleUnavailabilityListType;
                $listOption->list_option = 'Other';
                $listOption->option_value = '';
                $listOption->save();
            }

            //START::Backloading Status list Type and its options
            $listType = ListTypes::where('tenant_id', $tenant_id)
                ->where('list_name', 'Backloading Status')
                ->first();
            if (!$listType) {
                $listType = new ListTypes();
                $listType->tenant_id = $tenant_id;
                $listType->list_name = 'Backloading Status';
                $listType->save();
                $backloadingStatusListType = $listType->id;
            } else {
                $backloadingStatusListType = $listType->id;
            }

            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $backloadingStatusListType)
                ->where('list_option', 'Scheduled')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $backloadingStatusListType;
                $listOption->list_option = 'Scheduled';
                $listOption->option_value = '';
                $listOption->save();
            }

            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $backloadingStatusListType)
                ->where('list_option', 'In Transit')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $backloadingStatusListType;
                $listOption->list_option = 'In Transit';
                $listOption->option_value = '';
                $listOption->save();
            }

            $listOption = ListOptions::where('tenant_id', $tenant_id)
                ->where('list_type_id', $backloadingStatusListType)
                ->where('list_option', 'Completed')
                ->first();
            if (!$listOption) {
                $listOption = new ListOptions();
                $listOption->tenant_id = $tenant_id;
                $listOption->list_type_id = $backloadingStatusListType;
                $listOption->list_option = 'Completed';
                $listOption->option_value = '';
                $listOption->save();
            }

            //END::Backloading Status >>>>
            

            $moving_inventory_groups_array = (object) array(
                (object) array(
                    'group_id' => 1,
                    'group_name' => 'Bedroom',
                ),
                (object) array(
                    'group_id' => 2,
                    'group_name' => 'Lounge Room',
                ),
                (object) array(
                    'group_id' => 3,
                    'group_name' => 'Dining Room',
                ),
                (object) array(
                    'group_id' => 4,
                    'group_name' => 'Laundry',
                ),
                (object) array(
                    'group_id' => 5,
                    'group_name' => 'Cartons & Bags',
                ),
                (object) array(
                    'group_id' => 6,
                    'group_name' => 'Kitchen',
                ),
                (object) array(
                    'group_id' => 7,
                    'group_name' => 'Garage',
                ),
                (object) array(
                    'group_id' => 8,
                    'group_name' => 'Study',
                ),
                (object) array(
                    'group_id' => 9,
                    'group_name' => 'Sports & Exercise Equipment',
                ),
                (object) array(
                    'group_id' => 10,
                    'group_name' => 'Outdoor Items',
                ),
            );
            foreach ($moving_inventory_groups_array as $moving_group) {
                $moving_inventory_groups = MovingInventoryGroups::where('tenant_id', $tenant_id)
                    ->where('group_id', $moving_group->group_id)
                    ->where('group_name', $moving_group->group_name)
                    ->first();
                if (!$moving_inventory_groups) {
                    $moving_inventory_groups = new MovingInventoryGroups();
                    $moving_inventory_groups->tenant_id = $tenant_id;
                    $moving_inventory_groups->group_id = $moving_group->group_id;
                    $moving_inventory_groups->group_name = $moving_group->group_name;
                    $moving_inventory_groups->save();
                }
            }

            $moving_inventory_definations_array = (object) array(
                (object) array('group_id' => 1, 'item_name' => 'Baby bath', 'cbm' => 0.150, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Bassinet', 'cbm' => 0.227, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Bed - Double ensemble', 'cbm' => 1.281, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Bed - Double slat (inc mattress)', 'cbm' => 1.120, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Bed - Futon', 'cbm' => 1.000, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Bed - King ensemble', 'cbm' => 2.000, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Bed - King slat (inc mattress)', 'cbm' => 1.800, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Bed - Queen ensemble', 'cbm' => 1.600, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Bed - Queen slat (inc mattress)', 'cbm' => 1.400, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Bed - single ensemble', 'cbm' => 0.800, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Bed - Single slat (inc mattress)', 'cbm' => 0.650, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Bed Head Sgl', 'cbm' => 0.600, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Bed Head QS', 'cbm' => 0.900, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Bedroom chair', 'cbm' => 0.283, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Bedside table', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Blanket box', 'cbm' => 0.340, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Bookshelf - large', 'cbm' => 0.700, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Bookshelf - small', 'cbm' => 0.350, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Bunks - lower bunk double', 'cbm' => 1.400, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Bunks - single', 'cbm' => 1.100, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Change table', 'cbm' => 0.340, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Chest of drawers', 'cbm' => 0.400, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Cot', 'cbm' => 0.425, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Desk chair', 'cbm' => 0.150, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Dressing table', 'cbm' => 0.550, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Folding bed', 'cbm' => 0.283, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Glory box', 'cbm' => 0.340, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Guitar', 'cbm' => 0.250, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Lamp - bedside', 'cbm' => 0.057, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Lamp - standard', 'cbm' => 0.170, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Low boy', 'cbm' => 0.400, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Mattress Only - Double', 'cbm' => 0.800, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Mattress Only - King size', 'cbm' => 1.200, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Mattress Only - Queen', 'cbm' => 1.000, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Mattress Only - Single', 'cbm' => 0.600, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Mirror', 'cbm' => 0.200, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Pram', 'cbm' => 0.227, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Student desk', 'cbm' => 0.450, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Tall boy', 'cbm' => 0.400, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Toy Box', 'cbm' => 0.400, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'TV - flat screen', 'cbm' => 0.227, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'TV - portable.', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Wall Artwork', 'cbm' => 0.040, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Wardrobe - 1 door', 'cbm' => 0.800, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Wardrobe - 2 door', 'cbm' => 1.500, 'special_item' => 'No'),
                (object) array('group_id' => 1, 'item_name' => 'Wardrobe - 3 door', 'cbm' => 1.800, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => '2 seat couch', 'cbm' => 1.300, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => '3 seat couch', 'cbm' => 1.600, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => '4 seat couch', 'cbm' => 2.000, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => '5 seat modular couch', 'cbm' => 2.500, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => '6 seat modular couch', 'cbm' => 3.000, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => '7 seat modular couch', 'cbm' => 3.500, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Aquarium - large', 'cbm' => 2.000, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Aquarium - small', 'cbm' => 1.000, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Arm Chair', 'cbm' => 0.750, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Bar', 'cbm' => 1.133, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Bean Bag', 'cbm' => 0.300, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Bookshelf - 1m wide x 1m high', 'cbm' => 0.350, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Bookshelf - 1m wide x 2m high', 'cbm' => 0.700, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Buffet', 'cbm' => 0.850, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Buffet hutch', 'cbm' => 1.250, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'CD/ DVD rack', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Chaise', 'cbm' => 1.000, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'China Cabinet', 'cbm' => 0.800, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Coffee table - large', 'cbm' => 0.283, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Coffee table - small', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Crystal cabinet - large', 'cbm' => 1.100, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Crystal cabinet - small', 'cbm' => 0.550, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'DVD Player/Game console', 'cbm' => 0.057, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Entertainment unit - large', 'cbm' => 1.133, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Entertainment unit - lo line', 'cbm' => 0.425, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Entertainment Unit - Medium', 'cbm' => 0.750, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Fan', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Hall Table', 'cbm' => 0.300, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Hat Stand', 'cbm' => 0.350, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Heater', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Lamp/Side Table', 'cbm' => 0.120, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Nest of Tables', 'cbm' => 0.150, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Ottoman - large', 'cbm' => 0.300, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Ottoman - small', 'cbm' => 0.150, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Pool table (8 foot)', 'cbm' => 3.300, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Rug', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Speakers', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Standing lamp', 'cbm' => 0.200, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Stereo', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Surround sound system', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Table lamp', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'TV - flat screen - large', 'cbm' => 0.600, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'TV - Flat Screen - small', 'cbm' => 0.400, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'TV - portable', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'TV stand', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Upright piano inc stool', 'cbm' => 3.300, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Video', 'cbm' => 0.057, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Wall Art', 'cbm' => 0.040, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Wall unit - 1m wide x 1.8m high', 'cbm' => 1.100, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Wall unit - 2m wide x 1.8m high', 'cbm' => 2.200, 'special_item' => 'No'),
                (object) array('group_id' => 2, 'item_name' => 'Wall unit - 3m wide x 1.8m high', 'cbm' => 3.300, 'special_item' => 'No'),
                (object) array('group_id' => 3, 'item_name' => '4 seat dining table (no chairs)', 'cbm' => 0.708, 'special_item' => 'No'),
                (object) array('group_id' => 3, 'item_name' => '6 seat dining table (no chairs)', 'cbm' => 1.000, 'special_item' => 'No'),
                (object) array('group_id' => 3, 'item_name' => '8 seat dining table (no chairs)', 'cbm' => 1.500, 'special_item' => 'No'),
                (object) array('group_id' => 3, 'item_name' => 'Bench seat ', 'cbm' => 0.500, 'special_item' => 'No'),
                (object) array('group_id' => 3, 'item_name' => 'Dining chair', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 3, 'item_name' => 'Bar stool', 'cbm' => 0.250, 'special_item' => 'No'),
                (object) array('group_id' => 3, 'item_name' => '2 door buffet', 'cbm' => 0.362, 'special_item' => 'No'),
                (object) array('group_id' => 3, 'item_name' => '3 door buffet', 'cbm' => 0.489, 'special_item' => 'No'),
                (object) array('group_id' => 3, 'item_name' => '4 door buffet', 'cbm' => 0.652, 'special_item' => 'No'),
                (object) array('group_id' => 4, 'item_name' => 'Bin', 'cbm' => 0.085, 'special_item' => 'No'),
                (object) array('group_id' => 4, 'item_name' => 'Brooms / mops - 5 per bundle', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 4, 'item_name' => 'Bucket', 'cbm' => 0.100, 'special_item' => 'No'),
                (object) array('group_id' => 4, 'item_name' => 'Clothes rack - foldable', 'cbm' => 0.028, 'special_item' => 'No'),
                (object) array('group_id' => 4, 'item_name' => 'Dryer', 'cbm' => 0.425, 'special_item' => 'No'),
                (object) array('group_id' => 4, 'item_name' => 'Ironing board', 'cbm' => 0.028, 'special_item' => 'No'),
                (object) array('group_id' => 4, 'item_name' => 'Laundry basket', 'cbm' => 0.150, 'special_item' => 'No'),
                (object) array('group_id' => 4, 'item_name' => 'Laundry cupboard - large', 'cbm' => 1.133, 'special_item' => 'No'),
                (object) array('group_id' => 4, 'item_name' => 'Laundry cupboard - small', 'cbm' => 0.567, 'special_item' => 'No'),
                (object) array('group_id' => 4, 'item_name' => 'Sewing cabinet', 'cbm' => 0.566, 'special_item' => 'No'),
                (object) array('group_id' => 4, 'item_name' => 'Sewing machine', 'cbm' => 0.028, 'special_item' => 'No'),
                (object) array('group_id' => 4, 'item_name' => 'Shoe Rack', 'cbm' => 0.200, 'special_item' => 'No'),
                (object) array('group_id' => 4, 'item_name' => 'Vacuum cleaner', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 4, 'item_name' => 'Washing Machine', 'cbm' => 0.566, 'special_item' => 'No'),
                (object) array('group_id' => 5, 'item_name' => 'Backpack', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 5, 'item_name' => 'Book carton', 'cbm' => 0.065, 'special_item' => 'No'),
                (object) array('group_id' => 5, 'item_name' => 'Carton - large', 'cbm' => 0.283, 'special_item' => 'No'),
                (object) array('group_id' => 5, 'item_name' => 'Carton - medium', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 5, 'item_name' => 'Carton - small', 'cbm' => 0.085, 'special_item' => 'No'),
                (object) array('group_id' => 5, 'item_name' => 'Picture carton', 'cbm' => 0.057, 'special_item' => 'No'),
                (object) array('group_id' => 5, 'item_name' => 'Plastic Tubs', 'cbm' => 0.120, 'special_item' => 'No'),
                (object) array('group_id' => 5, 'item_name' => 'Portarobe', 'cbm' => 0.425, 'special_item' => 'No'),
                (object) array('group_id' => 5, 'item_name' => 'Sports bag', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 5, 'item_name' => 'Standard carton', 'cbm' => 0.150, 'special_item' => 'No'),
                (object) array('group_id' => 5, 'item_name' => 'Stripy Bag', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 5, 'item_name' => 'Suitcase', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 6, 'item_name' => 'Bar fridge', 'cbm' => 0.283, 'special_item' => 'No'),
                (object) array('group_id' => 6, 'item_name' => 'Butchers Block', 'cbm' => 0.300, 'special_item' => 'No'),
                (object) array('group_id' => 6, 'item_name' => 'Dishwasher', 'cbm' => 0.566, 'special_item' => 'No'),
                (object) array('group_id' => 6, 'item_name' => 'Freezer - chest', 'cbm' => 0.850, 'special_item' => 'No'),
                (object) array('group_id' => 6, 'item_name' => 'Freezer - upright', 'cbm' => 1.000, 'special_item' => 'No'),
                (object) array('group_id' => 6, 'item_name' => 'Fridge - 1 door', 'cbm' => 1.000, 'special_item' => 'No'),
                (object) array('group_id' => 6, 'item_name' => 'Fridge - 2 door', 'cbm' => 1.560, 'special_item' => 'No'),
                (object) array('group_id' => 6, 'item_name' => 'High chair', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 6, 'item_name' => 'Kitchen chairs', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 6, 'item_name' => 'Kitchen table', 'cbm' => 0.708, 'special_item' => 'No'),
                (object) array('group_id' => 6, 'item_name' => 'Kitchen tidy', 'cbm' => 0.085, 'special_item' => 'No'),
                (object) array('group_id' => 6, 'item_name' => 'Microwave', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 6, 'item_name' => 'Pantry - 1 door', 'cbm' => 0.567, 'special_item' => 'No'),
                (object) array('group_id' => 6, 'item_name' => 'Pantry - 2 door', 'cbm' => 1.333, 'special_item' => 'No'),
                (object) array('group_id' => 6, 'item_name' => 'Rubbish bin', 'cbm' => 0.150, 'special_item' => 'No'),
                (object) array('group_id' => 6, 'item_name' => 'Stool', 'cbm' => 0.180, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Bicycle - Adults', 'cbm' => 0.300, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Bicycle - Childs', 'cbm' => 0.200, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Childs Car Seat', 'cbm' => 0.250, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Compressor - Mobile', 'cbm' => 0.250, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Dog bed - large', 'cbm' => 0.300, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Dog bed - small', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Esky - large', 'cbm' => 0.350, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Esky - small', 'cbm' => 0.150, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Extension ladder', 'cbm' => 0.350, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Garden Hose', 'cbm' => 0.120, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Garden tools - bundle upto 5', 'cbm' => 0.200, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Kennel - large', 'cbm' => 0.708, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Kennel - small', 'cbm' => 0.340, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Lawn mower', 'cbm' => 0.227, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Leaf blower', 'cbm' => 0.150, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Patio heater', 'cbm' => 0.300, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Pressure Washer', 'cbm' => 0.200, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Ride on mower', 'cbm' => 3.000, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Shelves - 1m wide', 'cbm' => 0.680, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Shelves - 2m wide', 'cbm' => 1.360, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Shelves - 3m wide', 'cbm' => 2.040, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Step ladder', 'cbm' => 0.150, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Tent', 'cbm' => 0.300, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Tool box', 'cbm' => 0.120, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Tool chest - large', 'cbm' => 0.650, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Tool chest - small', 'cbm' => 0.200, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Trunk', 'cbm' => 0.400, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Welder', 'cbm' => 0.350, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Wheel barrow', 'cbm' => 0.450, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Wipper snipper', 'cbm' => 0.142, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Work Bench', 'cbm' => 1.500, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Work bench - 1m long', 'cbm' => 0.350, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Work bench - 2m long', 'cbm' => 1.000, 'special_item' => 'No'),
                (object) array('group_id' => 7, 'item_name' => 'Work bench - 3m long', 'cbm' => 1.350, 'special_item' => 'No'),
                (object) array('group_id' => 8, 'item_name' => 'Bookcase - large', 'cbm' => 0.700, 'special_item' => 'No'),
                (object) array('group_id' => 8, 'item_name' => 'Bookcase - small', 'cbm' => 0.350, 'special_item' => 'No'),
                (object) array('group_id' => 8, 'item_name' => 'Computer', 'cbm' => 0.150, 'special_item' => 'No'),
                (object) array('group_id' => 8, 'item_name' => 'Desk', 'cbm' => 1.200, 'special_item' => 'No'),
                (object) array('group_id' => 8, 'item_name' => 'Desk (Flat Pack)', 'cbm' => 0.200, 'special_item' => 'No'),
                (object) array('group_id' => 8, 'item_name' => 'Desk with return', 'cbm' => 1.982, 'special_item' => 'No'),
                (object) array('group_id' => 8, 'item_name' => 'Filing cabinet - 2 drawer', 'cbm' => 0.220, 'special_item' => 'No'),
                (object) array('group_id' => 8, 'item_name' => 'Filing cabinet - 3 drawer', 'cbm' => 0.329, 'special_item' => 'No'),
                (object) array('group_id' => 8, 'item_name' => 'Study Filing cabinet - 4 drawer', 'cbm' => 0.439, 'special_item' => 'No'),
                (object) array('group_id' => 8, 'item_name' => 'Office chair', 'cbm' => 0.227, 'special_item' => 'No'),
                (object) array('group_id' => 8, 'item_name' => 'Printer/ Scanner', 'cbm' => 0.113, 'special_item' => 'No'),
                (object) array('group_id' => 9, 'item_name' => 'Cross Trainer', 'cbm' => 1.500, 'special_item' => 'No'),
                (object) array('group_id' => 9, 'item_name' => 'Treadmill', 'cbm' => 1.500, 'special_item' => 'No'),
                (object) array('group_id' => 9, 'item_name' => 'Treadmill - folding', 'cbm' => 1.000, 'special_item' => 'No'),
                (object) array('group_id' => 9, 'item_name' => 'Golf clubs', 'cbm' => 0.200, 'special_item' => 'No'),
                (object) array('group_id' => 9, 'item_name' => 'Golf club hand buggy', 'cbm' => 0.200, 'special_item' => 'No'),
                (object) array('group_id' => 9, 'item_name' => 'Surf/snow board', 'cbm' => 0.250, 'special_item' => 'No'),
                (object) array('group_id' => 9, 'item_name' => 'Boxing bag', 'cbm' => 0.350, 'special_item' => 'No'),
                (object) array('group_id' => 9, 'item_name' => 'Exercise bike', 'cbm' => 0.450, 'special_item' => 'No'),
                (object) array('group_id' => 9, 'item_name' => 'Multi station gym', 'cbm' => 3.000, 'special_item' => 'No'),
                (object) array('group_id' => 9, 'item_name' => 'Weight bench', 'cbm' => 0.300, 'special_item' => 'No'),
                (object) array('group_id' => 10, 'item_name' => 'BBQ - kettle style', 'cbm' => 0.250, 'special_item' => 'No'),
                (object) array('group_id' => 10, 'item_name' => 'BBQ - trolley 4 burner', 'cbm' => 1.000, 'special_item' => 'No'),
                (object) array('group_id' => 10, 'item_name' => 'BBQ - trolley 4 burner with side shelves', 'cbm' => 1.300, 'special_item' => 'No'),
                (object) array('group_id' => 10, 'item_name' => 'Clam Shell Sandpit', 'cbm' => 0.450, 'special_item' => 'No'),
                (object) array('group_id' => 10, 'item_name' => 'Garden Seat', 'cbm' => 0.600, 'special_item' => 'No'),
                (object) array('group_id' => 10, 'item_name' => 'Outdoor chair - folding', 'cbm' => 0.100, 'special_item' => 'No'),
                (object) array('group_id' => 10, 'item_name' => 'Outdoor dining chair (no arms)', 'cbm' => 0.180, 'special_item' => 'No'),
                (object) array('group_id' => 10, 'item_name' => 'Outdoor dining chair (with arms)', 'cbm' => 0.250, 'special_item' => 'No'),
                (object) array('group_id' => 10, 'item_name' => 'Outdoor heater', 'cbm' => 0.300, 'special_item' => 'No'),
                (object) array('group_id' => 10, 'item_name' => 'Outdoor sofa', 'cbm' => 1.400, 'special_item' => 'No'),
                (object) array('group_id' => 10, 'item_name' => 'Outdoor table - 10 seater (no chairs)', 'cbm' => 2.000, 'special_item' => 'No'),
                (object) array('group_id' => 10, 'item_name' => 'Outdoor table - 2 seater (no chairs)', 'cbm' => 0.250, 'special_item' => 'No'),
                (object) array('group_id' => 10, 'item_name' => 'Outdoor table - 4 seater (no chairs)', 'cbm' => 0.500, 'special_item' => 'No'),
                (object) array('group_id' => 10, 'item_name' => 'Outdoor table - 6 seater (no chairs)', 'cbm' => 1.100, 'special_item' => 'No'),
                (object) array('group_id' => 10, 'item_name' => 'Outdoor table - 8 seater (no chairs)', 'cbm' => 1.450, 'special_item' => 'No'),
                (object) array('group_id' => 10, 'item_name' => 'Outdoor Umbrella', 'cbm' => 0.350, 'special_item' => 'No'),
                (object) array('group_id' => 10, 'item_name' => 'Pots Empty', 'cbm' => 0.120, 'special_item' => 'No'),
                (object) array('group_id' => 10, 'item_name' => 'Sun lounger', 'cbm' => 0.350, 'special_item' => 'No'),
                (object) array('group_id' => 10, 'item_name' => 'Sun lounger - folding', 'cbm' => 0.140, 'special_item' => 'No'),
                (object) array('group_id' => 10, 'item_name' => 'Swing - childs- dismantled', 'cbm' => 0.450, 'special_item' => 'No'),
                (object) array('group_id' => 10, 'item_name' => 'Swing Set', 'cbm' => 1.000, 'special_item' => 'No'),
                (object) array('group_id' => 10, 'item_name' => 'Trampoline', 'cbm' => 3.000, 'special_item' => 'No'),
                (object) array('group_id' => 10, 'item_name' => 'Trampoline - Dismantled', 'cbm' => 1.200, 'special_item' => 'No'),
            );
            foreach ($moving_inventory_definations_array as $moving_defination) {
                $moving_inventory_definations = MovingInventoryDefinitions::where('tenant_id', $tenant_id)
                    ->where('group_id', $moving_defination->group_id)
                    ->where('item_name', $moving_defination->item_name)
                    ->where('cbm', $moving_defination->cbm)
                    ->where('special_item', $moving_defination->special_item)
                    ->first();
                if (!$moving_inventory_definations) {
                    $moving_inventory_definations = new MovingInventoryDefinitions();
                    $moving_inventory_definations->tenant_id = $tenant_id;
                    $moving_inventory_definations->group_id = $moving_defination->group_id;
                    $moving_inventory_definations->item_name = $moving_defination->item_name;
                    $moving_inventory_definations->cbm = $moving_defination->cbm;
                    $moving_inventory_definations->special_item = $moving_defination->special_item;
                    $moving_inventory_definations->save();
                }
            }

            //Add Property Category Options
            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '1')
                ->where('options', 'Flat')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '1';
                $propertyCategoryOption->options = 'Flat';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '1')
                ->where('options', 'House')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '1';
                $propertyCategoryOption->options = 'House';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '1')
                ->where('options', 'Business')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '1';
                $propertyCategoryOption->options = 'Business';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '1')
                ->where('options', 'Storage Facility')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '1';
                $propertyCategoryOption->options = 'Storage Facility';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '2')
                ->where('options', 'Lightly Furnished')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '2';
                $propertyCategoryOption->options = 'Lightly Furnished';
                $propertyCategoryOption->other_value = '0.8';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '2')
                ->where('options', 'Medium Furnished')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '2';
                $propertyCategoryOption->options = 'Medium Furnished';
                $propertyCategoryOption->other_value = '1.0';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '2')
                ->where('options', 'Heavily Furnished')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '2';
                $propertyCategoryOption->options = 'Heavily Furnished';
                $propertyCategoryOption->other_value = '1.2';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '3')
                ->where('options', 'None')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '3';
                $propertyCategoryOption->options = 'None';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '3')
                ->where('options', '1 bedroom')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '3';
                $propertyCategoryOption->options = '1 bedroom';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '3')
                ->where('options', '2 bedrooms')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '3';
                $propertyCategoryOption->options = '2 bedrooms';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '3')
                ->where('options', '3 bedrooms')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '3';
                $propertyCategoryOption->options = '3 bedrooms';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '3')
                ->where('options', '4 bedrooms')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '3';
                $propertyCategoryOption->options = '4 bedrooms';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '3')
                ->where('options', '5 bedrooms')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '3';
                $propertyCategoryOption->options = '5 bedrooms';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '4')
                ->where('options', 'None')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '4';
                $propertyCategoryOption->options = 'None';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '4')
                ->where('options', '1 living area')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '4';
                $propertyCategoryOption->options = '1 living area';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '4')
                ->where('options', '2 living areas')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '4';
                $propertyCategoryOption->options = '2 living areas';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '4')
                ->where('options', '3 living areas')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '4';
                $propertyCategoryOption->options = '3 living areas';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '4')
                ->where('options', '4 living areas')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '4';
                $propertyCategoryOption->options = '4 living areas';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '5')
                ->where('options', 'Garage')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '5';
                $propertyCategoryOption->options = 'Garage';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '5')
                ->where('options', 'Garden shed')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '5';
                $propertyCategoryOption->options = 'Garden shed';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '5')
                ->where('options', 'Fitness room')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '5';
                $propertyCategoryOption->options = 'Fitness room';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '5')
                ->where('options', 'Playroom')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '5';
                $propertyCategoryOption->options = 'Playroom';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '5')
                ->where('options', 'Study')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '5';
                $propertyCategoryOption->options = 'Study';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '5')
                ->where('options', 'Workshop')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '5';
                $propertyCategoryOption->options = 'Workshop';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '5')
                ->where('options', 'Storage room')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '5';
                $propertyCategoryOption->options = 'Storage room';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '5')
                ->where('options', 'Outdoor furniture')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '5';
                $propertyCategoryOption->options = 'Outdoor furniture';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '6')
                ->where('options', 'Piano')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '6';
                $propertyCategoryOption->options = 'Piano';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '6')
                ->where('options', 'Cross trainer')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '6';
                $propertyCategoryOption->options = 'Cross trainer';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

            $propertyCategoryOption = PropertyCategoryOptions::where('tenant_id', $tenant_id)
                ->where('category_id', '6')
                ->where('options', 'Treadmill')
                ->first();
            if (!$propertyCategoryOption) {
                $propertyCategoryOption = new PropertyCategoryOptions();
                $propertyCategoryOption->tenant_id = $tenant_id;
                $propertyCategoryOption->category_id = '6';
                $propertyCategoryOption->options = 'Treadmill';
                $propertyCategoryOption->created_by = '1';
                $propertyCategoryOption->save();
            }

        } // end of moving module

        //For Cleaning Module
        if ($getSysModuleName == 'Cleaning') {

        }

        Session::flash('message', 'Tenant added successfully!');
        Session::flash('alert-class', 'alert-success');

        // Create contact on active campaign via api call
        $this->createActiveCampaignContact($request->first_name, $request->last_name, $request->business_email);
        //end

        // Create PostMarkApp Server via api call
        $this->createPostMarkServer($tenant->id, $tenant->tenant_name, $request->business_email);
        //end

        // Add Demo Data
        $this->addDemoData($tenant->id, $companies);
        //end
        return redirect('/login');

    }

    protected function addDemoData($tenant_id, $company)
    {
        // $tenant_id = 30;
        // $company = Companies::where('id',16)->first();
        $tax = Tax::where('tenant_id', $tenant_id)->first();

        ////////////////////////////////////////////////////////////
        /////...................Demo Data 1....................////
        //////////////////////////////////////////////////////////

        //***************::1. Create Email Template***********//

        $template_data = [
            'tenant_id' => $tenant_id,
            'company_id' => $company->id,
            'from_email' => $company->email,
            'from_email_name' => $company->company_name,
            'email_template_name' => 'Quote for 20 m3 - 2 men - 1 Bedroom',
            'email_subject' => 'Your Quote is Ready',
            'email_body' => '<p><b>Hi {first_name},</b></p><p>Thanks for your quote request.</p><p>Based on the information provided our quote for a 20m3 truck and 2 men is $99.00 per hour (inc GST), + Half hour call out + Half hour travel back. No other hidden fees or charges! Any work after the minimum will be charged in half-hour increments of $49.50.</p><p><span style="background-color: rgb(255, 255, 0);">Click on the following link to fill in your inventory that will help us to give us a more accurate estimate of the move:</span></p><p><span style="color: rgb(51, 51, 51);">{external_inventory_form}</span></p><p>Thanks</p><p>Sales team</p>',
            'active' => 'Y',
            'created_at' => Carbon::now(),
        ];
        EmailTemplates::create($template_data);

        //***************::2. Create a Product Category***********//

        $product_category_data = [
            'tenant_id' => $tenant_id,
            'category_name' => 'Removal Services',
        ];
        $productCategory = ProductCategories::create($product_category_data);

        //***************::3. Create a Product***********//

        $product_data = [
            'tenant_id' => $tenant_id,
            'category_id' => $productCategory->id,
            'tax_id' => $tax->id,
            'product_type' => 'Service',
            'name' => '20 m3 truck with 2 men',
            'price' => 95,
            'description' => '+ $25 per half hour additional man required',
        ];
        $product = Product::create($product_data);

        //***************::4. Create a Lead, Contact, Contact Details***********//

        $lead_data = [
            'tenant_id' => $tenant_id,
            'name' => 'Adam Citizen',
            'lead_status' => 'Potential',
        ];
        $lead = CRMLeads::create($lead_data);

        $contact_data = [
            'tenant_id' => $tenant_id,
            'lead_id' => $lead->id,
            'name' => 'Adam Citizen',
        ];
        $contact = CRMContacts::create($contact_data);

        $contact_email_data = [
            'tenant_id' => $tenant_id,
            'contact_id' => $contact->id,
            'detail_type' => 'Email',
            'detail' => 'adam.citizen@nomail.com',
        ];
        $contactEmail = CRMContactDetail::create($contact_email_data);

        $contact_mobile_data = [
            'tenant_id' => $tenant_id,
            'contact_id' => $contact->id,
            'detail_type' => 'Mobile',
            'detail' => '0400000001',
        ];
        $contactMobile = CRMContactDetail::create($contact_mobile_data);

        //***************::5. Create a new Opportunity in New status & jobs_moving_record***********//

        $opportunity_data = [
            'tenant_id' => $tenant_id,
            'lead_id' => $lead->id,
            'contact_id' => $contact->id,
            'op_type' => 'Moving',
            'op_status' => 'New',
            'est_job_date' => Carbon::now()->addDays(2),
            'confidence' => 50,
            'op_frequency' => 'One-time',
        ];
        $opportunity = CRMOpportunities::create($opportunity_data);

        $job_data = [
            'tenant_id' => $tenant_id,
            'company_id' => $company->id,
            'customer_id' => $lead->id,
            'crm_opportunity_id' => $opportunity->id,
            'job_number' => '1',
            'opportunity' => 'Y',
            'job_type' => 'Moving',
            'job_status' => 'New',
            'pickup_furnishing' => 'Medium Furnished',
            'pickup_property_type' => 'House',
            'pickup_bedrooms' => 2,
            'pickup_suburb' => 'Preston VIC',
            'delivery_suburb' => 'Surrey Hills VIC',
            'no_of_legs' => 0,
            'job_date' => Carbon::now()->addDays(2),
        ];
        $job = JobsMoving::create($job_data);

        //***************::6. Create an Estimate for the opportunity***********//

        $quote_data = [
            'tenant_id' => $tenant_id,
            'crm_opportunity_id' => $opportunity->id,
            'job_id' => $job->job_id,
            'quote_number' => '1',
            'sys_job_type' => 'Moving',
            'quote_date' => Carbon::now(),
        ];
        $quote = Quotes::create($quote_data);

        $quote_item_data = [
            'tenant_id' => $tenant_id,
            'quote_id' => $quote->id,
            'product_id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'type' => $product->product_type,
            'unit_price' => $product->price,
            'quantity' => 1,
            'amount' => ($product->price * 1 * 1.1),
            'tax_id' => $product->tax_id,
        ];
        $quoteItem = QuoteItem::create($quote_item_data);

        //***************::7. Create a Vehicle***********//

        $vehicle_data = [
            'tenant_id' => $tenant_id,
            'vehicle_name' => '4T-Truck1',
            'vehicle_colour' => '#dad2d2',
            'active' => 'Y',
        ];
        $vehicle = Vehicles::create($vehicle_data);

        //***************::8. Create a Driver***********//

        $company_domain = explode("@", $company->email);
        $userEmail = 'tom'.$tenant_id.'@'. $company_domain[1];
        $user_data = [
            'tenant_id' => $tenant_id,
            'name' => 'Tom Driver',
            'email' => $userEmail,
            'password' => Hash::make('blackforest'),
            'mobile' => '0400000001',
        ];
        $user = User::create($user_data);

        $people_data = [
            'tenant_id' => $tenant_id,
            'user_id' => $user->id,
            'employee_number' => '002',
            'first_name' => 'Tom',
            'last_name' => 'Driver',
            'mobile' => '0400000001',
            'is_system_user' => 'Y',
            'sys_job_type' => '2',
        ];
        $pplPeople = PplPeople::create($people_data);

        //***************::10. Insert an incoming email for the Opportunity with Unread status***********//

        $log_data = [
            'tenant_id' => $tenant_id,
            'lead_id' => $lead->id,
            'log_type' => '5',
            'log_from' => 'support@onexfort.com',
            'log_to' => 'adam.citizen@nomail.com',
            'log_subject' => 'This is a sample lead and opportunity, to get you started',
            'log_message' => '<p><b>Hello there!</b></p><p>Congratulations on signing up for Onexfort.&nbsp;</p><p>We have created a sample lead (who will become your customer) and an opportunity (which will become a job) for you. This is the Activity tab, where all communication and activities related to the lead are logged. Feel free to explore the Removals, Inventory and Estimate tabs on this page. We have also created a sample estimate for this opportunity.&nbsp;</p><p>To confirm this booking and convert it to a job, click on the Removals tab and click on the "Confirm Booking" button. This opportunity will then get converted to a job.</p><p>We have also created for you a sample job. To see the job, on the left side main menu, expand the Jobs menu and click on the "List Jobs" submenu. Then click on the job number.</p><p>You will be receiving a series of 8 emails showing you simple steps on getting you started. You can also book an onboarding call, using the following link:&nbsp;<a href="https://calendly.com/onexfort/onboarding" target="_blank">https://calendly.com/onexfort/onboarding</a></p><p>Wishing you great success in scaling your business!</p><p><b>Team, Onexfort</b></p><p>support@onexfort.com</p><p><br></p>',
            'log_date' => Carbon::now(),
            'log_status' => 'unread',
        ];
        $log = CRMActivityLog::create($log_data);

        ////////////////////////////////////////////////////////////
        /////...................Demo Data 2....................////
        //////////////////////////////////////////////////////////
        //***************::9. Create a new Job in New status with current date - invoice, partial payment***********//

        $lead_data2 = [
            'tenant_id' => $tenant_id,
            'name' => 'John Cox',
            'lead_status' => 'Customer',
        ];
        $lead2 = CRMLeads::create($lead_data2);

        $contact_data2 = [
            'tenant_id' => $tenant_id,
            'lead_id' => $lead2->id,
            'name' => 'John Cox',
        ];
        $contact2 = CRMContacts::create($contact_data2);

        $contact_email_data2 = [
            'tenant_id' => $tenant_id,
            'contact_id' => $contact2->id,
            'detail_type' => 'Email',
            'detail' => 'john.cox@nomail.com',
        ];
        $contactEmail2 = CRMContactDetail::create($contact_email_data2);

        $contact_mobile_data2 = [
            'tenant_id' => $tenant_id,
            'contact_id' => $contact2->id,
            'detail_type' => 'Mobile',
            'detail' => '0400000002',
        ];
        $contactMobile2 = CRMContactDetail::create($contact_mobile_data2);

        $opportunity_data2 = [
            'tenant_id' => $tenant_id,
            'lead_id' => $lead2->id,
            'contact_id' => $contact2->id,
            'op_type' => 'Moving',
            'op_status' => 'New',
            'est_job_date' => Carbon::now()->addDays(2),
            'confidence' => 50,
            'op_frequency' => 'One-time',
        ];
        $opportunity2 = CRMOpportunities::create($opportunity_data2);

        $job_data2 = [
            'tenant_id' => $tenant_id,
            'company_id' => $company->id,
            'customer_id' => $lead2->id,
            'crm_opportunity_id' => $opportunity2->id,
            'job_number' => '2',
            'opportunity' => 'N',
            'job_type' => 'Moving',
            'job_status' => 'New',
            'pickup_furnishing' => 'Medium Furnished',
            'pickup_property_type' => 'House',
            'pickup_bedrooms' => 2,
            'pickup_suburb' => 'Springvale VIC',
            'delivery_suburb' => 'Dandenong VIC',
            'no_of_legs' => 0,
            'job_date' => Carbon::now()->addDays(2),
        ];
        $job2 = JobsMoving::create($job_data2);

        $job_leg_data = [
            'tenant_id' => $tenant_id,
            'job_id' => $job2->job_id,
            'leg_number' => '1',
            'job_type' => 'Pickup',
            'leg_status' => 'Awaiting Confirmation',
            'pickup_address' => '34 Springvale Road, Springvale, VIC 3171',
            'drop_off_address' => '1555 Heatherton Road, Dandenong, VIC 3175',
            'pickup_geo_location' => '-37.935970,145.154900',
            'drop_off_geo_location' => '-37.970580,145.220970',
            'est_start_time' => '08:30',
            'est_finish_time' => '11:30',
            'driver_id' => $pplPeople->id,
            'vehicle_id' => $vehicle->id,
            'leg_date' => Carbon::now()->addDays(2),
        ];
        $job_leg = JobsMovingLegs::create($job_leg_data);

        $quote_data2 = [
            'tenant_id' => $tenant_id,
            'crm_opportunity_id' => $opportunity2->id,
            'job_id' => $job2->job_id,
            'quote_number' => '2',
            'sys_job_type' => 'Moving',
            'quote_date' => Carbon::now(),
        ];
        $quote2 = Quotes::create($quote_data2);

        $quote_item_data2 = [
            'tenant_id' => $tenant_id,
            'quote_id' => $quote2->id,
            'product_id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'type' => $product->product_type,
            'unit_price' => $product->price,
            'quantity' => 1,
            'amount' => ($product->price * 1 * 1.1),
            'tax_id' => $product->tax_id,
        ];
        $quoteItem2 = QuoteItem::create($quote_item_data2);

        $invoice_data = [
            'tenant_id' => $tenant_id,
            'job_id' => $job2->job_id,
            'invoice_number' => '2',
            'project_id' => '1',
            'sys_job_type' => 'Moving',
            'discount_type' => 'fixed',
            'status' => 'partial',
            'issue_date' => Carbon::now(),
            'due_date' => Carbon::now()->addDays(7),
        ];
        $invoice = Invoice::create($invoice_data);

        $invoice_item_data = [
            'tenant_id' => $tenant_id,
            'invoice_id' => $invoice->id,
            'product_id' => $product->id,
            'item_name' => $product->name,
            'item_summary' => $product->description,
            'type' => $product->product_type,
            'unit_price' => $product->price,
            'quantity' => 2,
            'amount' => ($product->price * 2 * 1.1),
            'tax_id' => $product->tax_id,
        ];
        $invoice_item = InvoiceItems::create($invoice_item_data);

        $payment_data = [
            'tenant_id' => $tenant_id,
            'invoice_id' => $invoice->id,
            'gateway' => 'Bank Transfer',
            'amount' => 50,
            'paid_on' => Carbon::now(),
        ];
        $payment = Payment::create($payment_data);

        //***************::11. Insert an incoming email for the Job with read status ***********//

        $log2_data = [
            'tenant_id' => $tenant_id,
            'lead_id' => $lead2->id,
            'log_type' => '5',
            'log_from' => 'support@onexfort.com',
            'log_to' => 'john.cox@nomail.com',
            'log_subject' => 'This is a sample job, to get you started',
            'log_message' => '<div><b>Hello there!</b></div><div><br></div><div>Happy to see you exploring Onexfort!</div><div><br></div><div>We have created this sample job for you. This is the Activity tab, where all communication and activities related to the job are logged. Feel free to explore the Operations, Inventory and Invoice tabs on this page.&nbsp;</div><div><br></div><div>The invoice for this job is created using an hourly rate for the job. You will be able to update the Actual hours at the bottom of the Invoice tab and regenerate the invoice. When your drivers start using the Onexfort mobile app, the actual start and finish times of the job (including actual locations) will be automatically logged. The drivers can themselves regenerate the invoice after completing the job and collect balance payments from the customer.</div><div><br></div><div>You can also make payments in the Invoice tab. When you connect your Stripe account (<a href="https://stripe.com/en-au" target="_blank">https://stripe.com/en-au</a>) to Onexfort, you will be able to take payments online directly from your customers and from the drivers mobile app.&nbsp;</div><div><br></div><div>You will be receiving a series of 8 emails showing you simple steps on getting you started. You can also book an onboarding call, using the following link: <a href="https://calendly.com/onexfort/onboarding" target="_blank">https://calendly.com/onexfort/onboarding</a></div><div><br></div><div>Wishing you great success in scaling your business!</div><div><br></div><div><b>Team, Onexfort</b></div><div><br></div><div>support@onexfort.com</div>',
            'log_date' => Carbon::now(),
            'log_status' => 'read',
        ];
        $log2 = CRMActivityLog::create($log2_data);

    }

    protected function createPostMarkServer($tenant_id, $tenant_name, $email)
    {
        $api_key = env('POSTMARK_API_TOKEN');
        $data = [
            'Name' => $tenant_name,
            'Color' => 'Red',
            'SmtpApiActivated' => true,
            'RawEmailEnabled' => true,
            'TrackOpens' => true,
            'InboundHookUrl' => 'https://www.app.onexfort.com/postmarkapp-email-received',
            'OpenHookUrl' => 'https://app.onexfort.com/postmarkapp-email-opened',
            'PostFirstOpenOnly' => true,
        ];

        $headers = array(
            "Accept: application/json",
            "Content-Type: application/json",
            "X-Postmark-Account-Token: {$api_key}",
        );
        $ch = curl_init('https://api.postmarkapp.com/servers');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $curl_error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        // do some checking to make sure it sent
        if ($http_code !== 200) {
            // echo '<pre>';
            // print_r($headers);
            // print_r($response);exit;
            return true;
        } else {
            $result = json_decode($response);

            $tenant_api_details = new TenantApiDetail();
            $tenant_api_details->tenant_id = $tenant_id;
            $tenant_api_details->provider = 'PostMarkApp';
            $tenant_api_details->user = 'moventum';
            $tenant_api_details->secret = 'Welcome$01';
            $tenant_api_details->server_id = $result->ID;
            $tenant_api_details->smtp_user = $result->ApiTokens[0];
            $tenant_api_details->smtp_secret = $result->ApiTokens[0];
            $tenant_api_details->from_email = $email;
            $tenant_api_details->to_email = $email;
            $tenant_api_details->incoming_email = $result->InboundAddress;
            $tenant_api_details->url = 'https://account.postmarkapp.com/';
            $tenant_api_details->save();
            return true;
        }
    }

    protected function createActiveCampaignContact($first_name, $last_name, $email)
    {
        // By default, this sample code is designed to get the result from your ActiveCampaign installation and print out the result
        $url = env('ACTIVE_CAMPAIGN_API_URL');

        $params = array(

            // the API Key can be found on the "Your Settings" page under the "API" tab.
            // replace this with your API Key
            'api_key' => env('ACTIVE_CAMPAIGN_API_KEY'),

            // this is the action that adds a contact
            'api_action' => 'contact_add',

            // define the type of output you wish to get back
            // possible values:
            // - 'xml'  :      you have to write your own XML parser
            // - 'json' :      data is returned in JSON format and can be decoded with
            //                 json_decode() function (included in PHP since 5.2.0)
            // - 'serialize' : data is returned in a serialized format and can be decoded with
            //                 a native unserialize() function
            'api_output' => 'serialize',
        );

        // here we define the data we are posting in order to perform an update
        $post = array(
            'email' => $email,
            'first_name' => $first_name,
            'last_name' => $last_name,
            //'tags' => 'api',
            //'ip4'                    => '127.0.0.1',

            // any custom fields
            //'field[345,0]'           => 'field value', // where 345 is the field ID
            //'field[%PERS_1%,0]'      => 'field value', // using the personalization tag instead (make sure to encode the key)

            // assign to lists:
            'p[2]' => 2, // Free Trial Customers
            'status[2]' => 1, // 1: active, 2: unsubscribed (REPLACE '123' WITH ACTUAL LIST ID, IE: status[5] = 1)
            //'form'          => 1001, // Subscription Form ID, to inherit those redirection settings
            //'noresponders[123]'      => 1, // uncomment to set "do not send any future responders"
            //'sdate[123]'             => '2009-12-07 06:00:00', // Subscribe date for particular list - leave out to use current date/time
            // use the folowing only if status=1
            'instantresponders[2]' => 0, // set to 0 to if you don't want to sent instant autoresponders
            //'lastmessage[123]'       => 1, // uncomment to set "send the last broadcast campaign"

            //'p[]'                    => 345, // some additional lists?
            //'status[345]'            => 1, // some additional lists?
        );

        // This section takes the input fields and converts them to the proper format
        $query = "";
        foreach ($params as $key => $value) {
            $query .= urlencode($key) . '=' . urlencode($value) . '&';
        }

        $query = rtrim($query, '& ');

        // This section takes the input data and converts it to the proper format
        $data = "";
        foreach ($post as $key => $value) {
            $data .= urlencode($key) . '=' . urlencode($value) . '&';
        }

        $data = rtrim($data, '& ');

        // clean up the url
        $url = rtrim($url, '/ ');

        // This sample code uses the CURL library for php to establish a connection,
        // submit your request, and show (print out) the response.
        if (!function_exists('curl_init')) {
            //die('CURL not supported. (introduced in PHP 4.0.2)');
        }

        // If JSON is used, check if json_decode is present (PHP 5.2.0+)
        if ($params['api_output'] == 'json' && !function_exists('json_decode')) {
            //die('JSON not supported. (introduced in PHP 5.2.0)');
        }

        // define a final API request - GET
        $api = $url . '/admin/api.php?' . $query;

        $request = curl_init($api); // initiate curl object
        curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
        curl_setopt($request, CURLOPT_POSTFIELDS, $data); // use HTTP POST to send form data
        //curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment if you get no gateway response and are using HTTPS
        curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);

        $response = curl_exec($request); // execute curl post and store results in $response
        // additional options may be required depending upon your server configuration
        // you can find documentation on curl options at http://www.php.net/curl_setopt
        curl_close($request); // close curl object

        if (!$response) {
            //die('Nothing was returned. Do you have a connection to Email Marketing server?');
        }

        // This line takes the response and breaks it into an array using:
                // JSON decoder
                //$result = json_decode($response);
                // unserializer
                //$result = unserialize($response);
                // XML parser...
                // ...

        // Result info that is always returned
                /*echo 'Result: ' . ($result['result_code'] ? 'SUCCESS' : 'FAILED') . '<br />';
                echo 'Message: ' . $result['result_message'] . '<br />';*/

        // The entire result printed out
                /*echo 'The entire result printed out:<br />';
                echo '<pre>';
                print_r($result);
                echo '</pre>';*/

        // Raw response printed out
                /*echo 'Raw response printed out:<br />';
                echo '<pre>';
                print_r($response);
                echo '</pre>';*/

        // API URL that returned the result
                /*echo 'API URL that returned the result:<br />';
            echo $api;

        echo '<br /><br />POST params:<br />';
        echo '<pre>';
        print_r($post);
        echo '</pre>';*/
    }

    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function dataMigrate()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '3000M');
        $count = 0;
        $d_count = 0;
        $s_count = 0;

        $legs = JobsMovingLegs::all();
        foreach($legs as $leg)
        {
            $count++;
            if($leg->driver_id != null)
            {
                JobsMovingLegsTeam::create([
                    'tenant_id' => $leg->tenant_id,
                    'leg_id' => $leg->id,
                    'people_id' => $leg->driver_id,
                    'driver' => 'Y',
                    'confirmation_status' => $leg->leg_status,
                    'created_at' => $leg->created_at
                ]);
                $d_count++;
            }

            if($leg->offsider_ids != null)
            {
                $all_offsiders = explode(',', $leg->offsider_ids);
                foreach($all_offsiders as $offsider)
                {
                    JobsMovingLegsTeam::create([
                        'tenant_id' => $leg->tenant_id,
                        'leg_id' => $leg->id,
                        'people_id' => $offsider,
                        'driver' => 'N',
                        'confirmation_status' => $leg->leg_status,
                        'created_at' => $leg->created_at
                    ]);
                    $s_count++;
                }
            }
        }

        return '<table>
        <tr><td>Total Legs</td><td><b>'. $count .'</b></td></tr>
        <tr><td>Total Inserted Drivers</td><td><b>'. $d_count .'</b></td></tr>
        <tr><td>Total Inserted Offsiders</td><td><b>'. $s_count .'</b></td></tr>
        </table>';
    }
}
