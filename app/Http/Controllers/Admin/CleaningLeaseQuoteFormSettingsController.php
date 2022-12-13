<?php

namespace App\Http\Controllers\Admin;

use App\Companies;
use Illuminate\Http\Request;
use App\Helper\Reply;
use App\JobsCleaningQuoteFormSetup;
use App\JobsCleaningShifts;
use App\JobsCleaningTeamMembers;
use App\JobsCleaningTeams;
use App\JobsCleaningType;
use App\ListTypes;
use App\PplPeople;
use App\ProductCategories;
use App\TenantApiDetail;
use App\TenantServicingCities;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Yajra\DataTables\Facades\DataTables;

class CleaningLeaseQuoteFormSettingsController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.leaseCleaningQuoteFormSettings');
        $this->pageIcon = 'ti-file';
    }

    public function index()
    {
        return view('admin.cleaning-lease-quote-form-settings.index', $this->data);
    }

    public function create()
    {
        $this->servicing_cities = TenantServicingCities::where(['tenant_id' => auth()->user()->tenant_id])
            ->where('deleted', '=', '0')
            ->whereNotIn('id', function ($query) {
                $query->select('servicing_city_id')->from('jobs_cleaning_quote_form_setup')
                    ->where('jobs_cleaning_quote_form_setup.job_type_id', '=', '2')
                    ->where('jobs_cleaning_quote_form_setup.tenant_id', '=', auth()->user()->tenant_id);
            })->get();

        $this->tenant_api_details = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'GoogleMaps'])->first();
        // $this->quote_form_setup  = JobsCleaningQuoteFormSetup::where(['tenant_id' => auth()->user()->tenant_id])->where('job_type_id', '=', '2')->first();
        $this->product_categories = ProductCategories::select('id', 'category_name')->where(['tenant_id' => auth()->user()->tenant_id])->get();
        $this->list_types = ListTypes::select('id', 'list_name')->where(['tenant_id' => auth()->user()->tenant_id])->get();


        return view('admin.cleaning-lease-quote-form-settings.create', $this->data);
    }

    public function store(Request $request)
    {
        $obj = new JobsCleaningQuoteFormSetup();
        $obj->tenant_id = auth()->user()->tenant_id;
        $obj->job_type_id = '2';
        $obj->servicing_city_id = $request->servicing_city_id;
        $obj->servicing_city_geocode_sw_lat = $request->servicing_city_geocode_sw_lat;
        $obj->servicing_city_geocode_sw_lng = $request->servicing_city_geocode_sw_lng;
        $obj->servicing_city_geocode_ne_lat = $request->servicing_city_geocode_ne_lat;
        $obj->servicing_city_geocode_ne_lng = $request->servicing_city_geocode_ne_lng;
        $obj->max_shifts_per_team_per_day = $request->max_shifts_per_team_per_day;
        $obj->min_hours_per_job = $request->min_hours_per_job;
        $obj->max_bedrooms = $request->max_bedrooms;
        $obj->max_bathrooms = $request->max_bathrooms;
        $obj->extras_category_id = $request->extras_category_id;
        $obj->questions_list_type_id = @implode(',', $request->questions_list_type_id);

        $obj->created_by = auth()->user()->id;
        $obj->save();

        return Reply::redirect(route('admin.leaseQuoteFormSettings.index'), __('messages.leaseCleaningQuoteFormSettingsCityCreated'));
    }

    public function edit($id)
    {
        $this->quote_form_setup = JobsCleaningQuoteFormSetup::findOrFail($id);
        $this->servicing_city = TenantServicingCities::where('id', '=', $this->quote_form_setup->servicing_city_id)->first();
        $this->product_categories = ProductCategories::select('id', 'category_name')->where(['tenant_id' => auth()->user()->tenant_id])->get();
        $this->list_types = ListTypes::select('id', 'list_name')->where(['tenant_id' => auth()->user()->tenant_id])->get();

        $request = Input::get();
        $this->code_script = null;
        $this->code_script2 = null;
        $this->show_script = '';
        $this->show_script2 = '';
        $this->discount_offer = 0;
        $this->companies = Companies::where('tenant_id', '=', auth()->user()->tenant_id)->get();

        if (isset($request['company']) && !empty($request['company'])) {
            $company = $request['company'];
            $discount_offer = $request['discount_offer'];
            if (isset($request['script1']) && !empty($request['script1'])) {
                $param = auth()->user()->tenant_id . '&company_id=' . $company . '&mode=close&city=' . $request['city'].'&discount='.$discount_offer;
                $param = base64_encode($param);
                $this->code_script = '<!-- Get end of lease cleaning embed code start -->
                <script src="' . url('/') . '/eccbc87e4b5ce2fe28308fd9f2a7baf3.js?key=' . $param . '"></script>
                <!-- Get end of lease cleaning embed code end -->';
            }

            if (isset($request['script2']) && !empty($request['script2'])) {
                $param = auth()->user()->tenant_id . '&company_id=' . $company . '&mode=open&city=' . $request['city'].'&discount='.$discount_offer;
                $param = base64_encode($param);
                $this->code_script2 = '<!-- Get end of lease cleaning embed code start -->
                <script src="' . url('/') . '/eccbc87e4b5ce2fe28308fd9f2a7baf3.js?key=' . $param . '"></script>
                <!-- Get end of lease cleaning embed code end -->';
            }
            $this->discount_offer = $discount_offer;
        }

        return view('admin.cleaning-lease-quote-form-settings.edit', $this->data);
    }

    public function update(Request $request, $id)
    {
        $obj = JobsCleaningQuoteFormSetup::findOrFail($id);
        $obj->tenant_id = auth()->user()->tenant_id;
        $obj->job_type_id = '2';
        $obj->servicing_city_geocode_sw_lat = $request->servicing_city_geocode_sw_lat;
        $obj->servicing_city_geocode_sw_lng = $request->servicing_city_geocode_sw_lng;
        $obj->servicing_city_geocode_ne_lat = $request->servicing_city_geocode_ne_lat;
        $obj->servicing_city_geocode_ne_lng = $request->servicing_city_geocode_ne_lng;
        $obj->max_shifts_per_team_per_day = $request->max_shifts_per_team_per_day;
        $obj->min_hours_per_job = $request->min_hours_per_job;
        $obj->max_bedrooms = $request->max_bedrooms;
        $obj->max_bathrooms = $request->max_bathrooms;
        $obj->extras_category_id = $request->extras_category_id;
        $obj->questions_list_type_id = @implode(',', $request->questions_list_type_id);

        $obj->updated_at = Carbon::now();
        $obj->updated_by = auth()->user()->id;
        $obj->save();

        return Reply::redirect(route('admin.leaseQuoteFormSettings.index'), __('messages.leaseCleaningQuoteFormSettingsCityUpdated'));
    }

    public function _edit()
    {

        // $this->row = PropertyCategoryOptions::findOrFail($id);
        // $this->property_categories = PropertyCategory::where('active', '=', 'Y')->get();
        // return view('admin.property-category-options.edit', $this->data);

        $this->tenant_api_details = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'GoogleMaps'])->first();
        $this->quote_form_setup  = JobsCleaningQuoteFormSetup::where(['tenant_id' => auth()->user()->tenant_id])->where('job_type_id', '=', '2')->first();
        $this->product_categories = ProductCategories::select('id', 'category_name')->where(['tenant_id' => auth()->user()->tenant_id])->get();
        $this->list_types = ListTypes::select('id', 'list_name')->where(['tenant_id' => auth()->user()->tenant_id])->get();

        $request = Input::get();
        $this->code_script = null;
        $this->code_script2 = null;
        $this->show_script = '';
        $this->show_script2 = '';
        $this->companies = Companies::where('tenant_id', '=', auth()->user()->tenant_id)->get();

        if (isset($request['company']) && !empty($request['company'])) {
            $company = $request['company'];
            if (isset($request['script1']) && !empty($request['script1'])) {
                $param = auth()->user()->tenant_id . '&company_id=' . $company . '&mode=close';
                $param = base64_encode($param);
                $this->code_script = '<!-- Get end of lease cleaning embed code start -->
                <script src="' . url('/') . '/eccbc87e4b5ce2fe28308fd9f2a7baf3.js?key=' . $param . '"></script>
                <!-- Get end of lease cleaning embed code end -->';
            }

            if (isset($request['script2']) && !empty($request['script2'])) {
                $param = auth()->user()->tenant_id . '&company_id=' . $company . '&mode=open';
                $param = base64_encode($param);
                $this->code_script2 = '<!-- Get end of lease cleaning embed code start -->
                <script src="' . url('/') . '/eccbc87e4b5ce2fe28308fd9f2a7baf3.js?key=' . $param . '"></script>
                <!-- Get end of lease cleaning embed code end -->';
            }
        }

        return view('admin.cleaning-lease-quote-form-settings.index', $this->data);
    }

    public function data(Request $request)
    {
        $result = JobsCleaningQuoteFormSetup::select('jobs_cleaning_quote_form_setup.id', 'jobs_cleaning_quote_form_setup.servicing_city_id', 'tenant_servicing_cities.servicing_city')
            ->join('tenant_servicing_cities', 'tenant_servicing_cities.id', '=', 'jobs_cleaning_quote_form_setup.servicing_city_id')
            ->where('jobs_cleaning_quote_form_setup.job_type_id', '=', '2')
            ->where('jobs_cleaning_quote_form_setup.tenant_id', '=', auth()->user()->tenant_id)
            ->where('tenant_servicing_cities.deleted', '=', '0')
            ->orderBy('jobs_cleaning_quote_form_setup.id', 'asc')->get();


        return DataTables::of($result)
            ->addColumn('action', function ($row) {
                return '<div class="btn-group m-r-10">
                            <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info btn-outline  dropdown-toggle waves-effect waves-light" type="button">Action <span class="caret"></span></button>
                            <ul role="menu" class="dropdown-menu">
                              <li><a href="' . route("admin.leaseQuoteFormSettings.edit", $row->id) . '" ><i class="fa fa-pencil"></i> Edit</a></li>
                              <li><a class="sa-params" href="javascript:;" data-row-id="' . $row->id . '"><i class="fa fa-times"></i> Delete</a></li>
                            </ul>
                          </div>
                          ';
            })
            // ->editColumn('property_category', function ($row) {
            //     $category =  PropertyCategory::select('category')->where('id', $row->category_id)->first();
            //     if($category)
            //         return $category->category;
            //     else
            //         return '';
            // })
            ->rawColumns(['action'])
            ->removeColumn('id')
            ->removeColumn('servicing_city_id')
            ->make(true);
    }

    public function destroy($id)
    {
        JobsCleaningQuoteFormSetup::destroy($id);
        return Reply::success(__('messages.leaseCleaningQuoteFormSettingsCityDeleted'));
    }


    public function saveLeaseQuoteFormSettings(Request $request, $id)
    {
        // dd($request);
        if (!empty($id)) {
            $obj = JobsCleaningQuoteFormSetup::findOrFail($id);
        } else {
            $obj = new JobsCleaningQuoteFormSetup();
        }
        $obj->tenant_id = auth()->user()->tenant_id;
        $obj->job_type_id = '2';
        $obj->servicing_city = $request->servicing_city;
        $obj->servicing_city_geocode_sw_lat = $request->servicing_city_geocode_sw_lat;
        $obj->servicing_city_geocode_sw_lng = $request->servicing_city_geocode_sw_lng;
        $obj->servicing_city_geocode_ne_lat = $request->servicing_city_geocode_ne_lat;
        $obj->servicing_city_geocode_ne_lng = $request->servicing_city_geocode_ne_lng;
        $obj->max_shifts_per_team_per_day = $request->max_shifts_per_team_per_day;
        $obj->min_hours_per_job = $request->min_hours_per_job;
        $obj->max_bedrooms = $request->max_bedrooms;
        $obj->max_bathrooms = $request->max_bathrooms;
        $obj->extras_category_id = $request->extras_category_id;
        $obj->questions_list_type_id = @implode(',', $request->questions_list_type_id);

        $obj->updated_at = Carbon::now();
        $obj->updated_by = auth()->user()->id;
        $obj->save();

        return Reply::success(__('messages.leaseCleaningQuoteFormSettingsUpdated'));
    }

    //START:: Cleaning Shifts
    public function cleaningShifts()
    {
        $this->pageTitle = __('app.menu.cleaningShifts');
        $this->cleaning_job_types = JobsCleaningType::all();
        $this->cleaning_shifts = array();
        return view('admin.cleaning-lease-quote-form-settings.cleaning-shifts', $this->data);
    }

    public function ajaxLoadCleaningShifts(Request $request)
    {

        $this->job_type_id = $request->cleaning_job_type;
        $this->cleaning_shifts = JobsCleaningShifts::where(['tenant_id' => auth()->user()->tenant_id, 'job_type_id' => $this->job_type_id])->get();
        $response['html'] = view('admin.cleaning-lease-quote-form-settings.cleaning_shift_grid', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxSaveCleaningShifts(Request $request)
    {

        $this->job_type_id = $request->job_type_id;
        $shift_name = $request->shift_name;
        $shift_display_start_time = $request->shift_display_start_time;

        $model = new JobsCleaningShifts();
        $model->shift_name = $shift_name;
        $model->job_type_id = $this->job_type_id;
        $model->shift_display_start_time = $shift_display_start_time;
        $model->created_by = auth()->user()->id;
        $model->tenant_id = auth()->user()->tenant_id;
        $model->created_at = time();
        $model->save();

        $this->cleaning_shifts = JobsCleaningShifts::where(['tenant_id' => auth()->user()->tenant_id, 'job_type_id' => $this->job_type_id])->get();
        $response['error'] = 0;
        $response['message'] = 'Record has been added';
        $response['html'] = view('admin.cleaning-lease-quote-form-settings.cleaning_shift_grid', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxUpdateCleaningShifts(Request $request)
    {

        $id = $request->id;
        $this->job_type_id = $request->job_type_id;
        $shift_name = $request->shift_name;
        $shift_display_start_time = $request->shift_display_start_time;

        JobsCleaningShifts::where(['id' => $id])
            ->update([
                'shift_name' => $shift_name,
                'shift_display_start_time' => $shift_display_start_time,
                'updated_by' => auth()->user()->id,
                'updated_at' => time()
            ]);

        $this->cleaning_shifts = JobsCleaningShifts::where(['tenant_id' => auth()->user()->tenant_id, 'job_type_id' => $this->job_type_id])->get();
        $response['error'] = 0;
        $response['message'] = 'Record has been updated';
        $response['html'] = view('admin.cleaning-lease-quote-form-settings.cleaning_shift_grid', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxDestroyCleaningShifts(Request $request)
    {

        $id = $request->id;
        $this->job_type_id = $request->job_type_id;
        JobsCleaningShifts::where(['id' => $id])->delete();

        $this->cleaning_shifts = JobsCleaningShifts::where(['tenant_id' => auth()->user()->tenant_id, 'job_type_id' => $this->job_type_id])->get();
        $response['error'] = 0;
        $response['message'] = 'Record has been deleted';
        $response['html'] = view('admin.cleaning-lease-quote-form-settings.cleaning_shift_grid', $this->data)->render();
        return json_encode($response);
    }
    //END:: Cleaning Shifts

    //START:: Cleaning Teams
    public function cleaningTeams()
    {
        $this->pageTitle = __('app.menu.cleaningTeams');
        $this->cleaning_job_types = JobsCleaningType::all();
        $this->cleaning_shifts = array();
        return view('admin.cleaning-lease-quote-form-settings.cleaning-teams', $this->data);
    }

    public function ajaxLoadCleaningTeams(Request $request)
    {

        $this->job_type_id = $request->cleaning_job_type;
        $this->cleaning_teams = JobsCleaningTeams::where(['tenant_id' => auth()->user()->tenant_id, 'job_type_id' => $this->job_type_id])->get();
        $response['html'] = view('admin.cleaning-lease-quote-form-settings.cleaning_team_grid', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxSaveCleaningTeams(Request $request)
    {
        $this->job_type_id = $request->job_type_id;
        $team_name = $request->team_name;
        $team_priority = $request->team_priority;
        $team_colour = $request->team_colour;
        $team_rating = $request->team_rating;
        $active = $request->team_active;

        $model = new JobsCleaningTeams();
        $model->job_type_id = $this->job_type_id;
        $model->team_name = $team_name;
        $model->team_colour = $team_colour;
        $model->team_priority = $team_priority;
        $model->team_rating = $team_rating;
        $model->active = $active;
        $model->created_by = auth()->user()->id;
        $model->tenant_id = auth()->user()->tenant_id;
        $model->created_at = time();
        $model->save();

        $this->cleaning_teams = JobsCleaningTeams::where(['tenant_id' => auth()->user()->tenant_id, 'job_type_id' => $this->job_type_id])->get();
        $response['error'] = 0;
        $response['message'] = 'Record has been added';
        $response['html'] = view('admin.cleaning-lease-quote-form-settings.cleaning_team_grid', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxUpdateCleaningTeams(Request $request)
    {

        $id = $request->id;
        $this->job_type_id = $request->job_type_id;
        $team_name = $request->team_name;
        $team_priority = $request->team_priority;
        $team_colour = $request->team_colour;
        $team_rating = $request->team_rating;
        $active = $request->team_active;

        JobsCleaningTeams::where(['id' => $id])
            ->update([
                'team_name' => $team_name,
                'team_priority' => $team_priority,
                'team_colour' => $team_colour,
                'team_rating' => $team_rating,
                'active' => $active,
                'updated_by' => auth()->user()->id,
                'updated_at' => time()
            ]);

        $this->cleaning_teams = JobsCleaningTeams::where(['tenant_id' => auth()->user()->tenant_id, 'job_type_id' => $this->job_type_id])->get();
        $response['error'] = 0;
        $response['message'] = 'Record has been updated';
        $response['html'] = view('admin.cleaning-lease-quote-form-settings.cleaning_team_grid', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxDestroyCleaningTeams(Request $request)
    {

        $id = $request->id;
        $this->job_type_id = $request->job_type_id;
        JobsCleaningTeams::where(['id' => $id])->delete();

        $this->cleaning_teams = JobsCleaningTeams::where(['tenant_id' => auth()->user()->tenant_id, 'job_type_id' => $this->job_type_id])->get();
        $response['error'] = 0;
        $response['message'] = 'Record has been deleted';
        $response['html'] = view('admin.cleaning-lease-quote-form-settings.cleaning_team_grid', $this->data)->render();
        return json_encode($response);
    }
    //END:: Cleaning Teams

    //START:: Cleaning Team Members
    public function cleaningTeamMembers()
    {
        $this->pageTitle = __('app.menu.cleaningTeamMembers');
        $this->cleaning_job_types = JobsCleaningType::all();
        $this->cleaning_shifts = array();
        return view('admin.cleaning-lease-quote-form-settings.cleaning-team-members', $this->data);
    }

    public function ajaxLoadCleaningTeamMembers(Request $request)
    {

        $this->job_type_id = $request->cleaning_job_type;

        $this->cleaning_team_members = DB::table('jobs_cleaning_team_members as m')
            ->where(['t.job_type_id' => $this->job_type_id, 'm.tenant_id' => auth()->user()->tenant_id])
            ->join('jobs_cleaning_teams as t', 't.id', '=', 'm.team_id')
            ->join('ppl_people as p', 'p.id', '=', 'm.person_id')
            ->select('m.id', 'm.team_id', 'm.person_id', 't.team_name', DB::raw('CONCAT(p.first_name," ",p.last_name) AS person_name'))
            ->get();
        $response['html'] = view('admin.cleaning-lease-quote-form-settings.cleaning_team_member_grid', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxSaveCleaningTeamMembers(Request $request)
    {
        $this->job_type_id = $request->job_type_id;
        $team_id = $request->team_id;
        $person_id = $request->person_id;

        $model = new JobsCleaningTeamMembers();
        $model->team_id = $team_id;
        $model->person_id = $person_id;
        $model->created_by = auth()->user()->id;
        $model->tenant_id = auth()->user()->tenant_id;
        $model->created_at = time();
        $model->save();

        $this->cleaning_team_members = DB::table('jobs_cleaning_team_members as m')
            ->where(['t.job_type_id' => $this->job_type_id, 'm.tenant_id' => auth()->user()->tenant_id])
            ->join('jobs_cleaning_teams as t', 't.id', '=', 'm.team_id')
            ->join('ppl_people as p', 'p.id', '=', 'm.person_id')
            ->select('m.id', 'm.team_id', 'm.person_id', 't.team_name', DB::raw('CONCAT(p.first_name," ",p.last_name) AS person_name'))
            ->get();
        $response['error'] = 0;
        $response['message'] = 'Record has been added';
        $response['html'] = view('admin.cleaning-lease-quote-form-settings.cleaning_team_member_grid', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxUpdateCleaningTeamMembers(Request $request)
    {

        $id = $request->id;
        $this->job_type_id = $request->job_type_id;
        $team_id = $request->team_id;
        $person_id = $request->person_id;

        JobsCleaningTeamMembers::where(['id' => $id])
            ->update([
                'team_id' => $team_id,
                'person_id' => $person_id,
                'updated_by' => auth()->user()->id,
                'updated_at' => time()
            ]);

        $this->cleaning_team_members = DB::table('jobs_cleaning_team_members as m')
            ->where(['t.job_type_id' => $this->job_type_id, 'm.tenant_id' => auth()->user()->tenant_id])
            ->join('jobs_cleaning_teams as t', 't.id', '=', 'm.team_id')
            ->join('ppl_people as p', 'p.id', '=', 'm.person_id')
            ->select('m.id', 'm.team_id', 'm.person_id', 't.team_name', DB::raw('CONCAT(p.first_name," ",p.last_name) AS person_name'))
            ->get();
        $response['error'] = 0;
        $response['message'] = 'Record has been updated';
        $response['html'] = view('admin.cleaning-lease-quote-form-settings.cleaning_team_member_grid', $this->data)->render();
        return json_encode($response);
    }

    public function ajaxDestroyCleaningTeamMembers(Request $request)
    {

        $id = $request->id;
        $this->job_type_id = $request->job_type_id;

        JobsCleaningTeamMembers::where(['id' => $id])->delete();

        $this->cleaning_team_members = DB::table('jobs_cleaning_team_members as m')
            ->where(['t.job_type_id' => $this->job_type_id, 'm.tenant_id' => auth()->user()->tenant_id])
            ->join('jobs_cleaning_teams as t', 't.id', '=', 'm.team_id')
            ->join('ppl_people as p', 'p.id', '=', 'm.person_id')
            ->select('m.id', 'm.team_id', 'm.person_id', 't.team_name', DB::raw('CONCAT(p.first_name," ",p.last_name) AS person_name'))
            ->get();
        $response['error'] = 0;
        $response['message'] = 'Record has been deleted';
        $response['html'] = view('admin.cleaning-lease-quote-form-settings.cleaning_team_member_grid', $this->data)->render();
        return json_encode($response);
    }
    //END:: Cleaning Team Members
}
