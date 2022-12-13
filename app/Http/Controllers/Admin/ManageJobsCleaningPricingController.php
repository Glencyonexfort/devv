<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\JobsCleaningPricing;
use App\Helper\Reply;
use App\Tax;

class ManageJobsCleaningPricingController extends AdminBaseController
{
   public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.endOfLeasePricing');
        $this->pageIcon = 'ti-file';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->taxes =  Tax::where(['tenant_id'=> auth()->user()->tenant_id])->get();
        $this->jobsCleaningPricing = JobsCleaningPricing::select('taxes.tax_name', 'jobs_cleaning_pricing.id', 'jobs_cleaning_pricing.tax_id','jobs_cleaning_pricing.bedrooms', 'jobs_cleaning_pricing.bathrooms', 'jobs_cleaning_pricing.carpet', 'jobs_cleaning_pricing.storey', 'jobs_cleaning_pricing.price')
                        ->join('taxes', 'taxes.id', '=', 'jobs_cleaning_pricing.tax_id')
                        ->where(['jobs_cleaning_pricing.tenant_id'=>auth()->user()->tenant_id])
                        ->orderBy('jobs_cleaning_pricing.bedrooms', 'asc')
                        ->orderBy('jobs_cleaning_pricing.bathrooms', 'asc')
                        ->orderBy('jobs_cleaning_pricing.carpet', 'asc')
                        ->orderBy('jobs_cleaning_pricing.storey', 'asc')
                        ->get();

        //dd(count($this->jobsCleaningPricing));

        return view('admin.jobs-cleaning-pricing.index', $this->data);
    }

    public function ajaxCreateJobsCleaningPricing(Request $request)
    {

        $model = new JobsCleaningPricing();
        $model->tenant_id      = auth()->user()->tenant_id;        
        $model->bedrooms      = $request->input('bedrooms');
        $model->bathrooms       = $request->input('bathrooms');
        $model->carpet       = $request->input('carpet');
        $model->storey       = $request->input('storey');
        $model->tax_id       = $request->input('tax_id');
        $model->price       = $request->input('price');
        //print_r($model);exit;
        if ($model->save()) {
            
            $taxes = Tax::where(['tenant_id'=> auth()->user()->tenant_id])->get();

            $jobsCleaningPricing = JobsCleaningPricing::select('taxes.tax_name', 'jobs_cleaning_pricing.id', 'jobs_cleaning_pricing.tax_id','jobs_cleaning_pricing.bedrooms', 'jobs_cleaning_pricing.bathrooms', 'jobs_cleaning_pricing.carpet', 'jobs_cleaning_pricing.storey', 'jobs_cleaning_pricing.price')
                                                        ->join('taxes', 'taxes.id', '=', 'jobs_cleaning_pricing.tax_id')
                                                        ->where(['jobs_cleaning_pricing.tenant_id'=>auth()->user()->tenant_id])
                                                        ->orderBy('jobs_cleaning_pricing.bedrooms', 'asc')
                                                        ->orderBy('jobs_cleaning_pricing.bathrooms', 'asc')
                                                        ->orderBy('jobs_cleaning_pricing.carpet', 'asc')
                                                        ->orderBy('jobs_cleaning_pricing.storey', 'asc')
                                                        ->get();


            $response['error'] = 0;
            //$response['id'] = $local_moves_id;
            $response['message'] = 'End of Lease Pricing has been added';
            $response['jobsCleaningPricing_html'] = view('admin.jobs-cleaning-pricing.jobscleaningpricing_grid')->with(['taxes' => $taxes, 'jobsCleaningPricing' => $jobsCleaningPricing])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxUpdateJobsCleaningPricing(Request $request)
    {
        $updateid = $request->input('updateid');
        $model = JobsCleaningPricing::find($updateid);
        $model->bedrooms      = $request->input('bedrooms');
        $model->bathrooms       = $request->input('bathrooms');
        $model->carpet       = $request->input('carpet');
        $model->storey       = $request->input('storey');
        $model->tax_id       = $request->input('tax_id');
        $model->price       = $request->input('price');
        $model->updated_at = time();
        //print_r($model);exit;
        if ($model->save()) {
            $taxes = Tax::where(['tenant_id'=> auth()->user()->tenant_id])->get();

            $jobsCleaningPricing = JobsCleaningPricing::select('taxes.tax_name', 'jobs_cleaning_pricing.id', 'jobs_cleaning_pricing.tax_id','jobs_cleaning_pricing.bedrooms', 'jobs_cleaning_pricing.bathrooms', 'jobs_cleaning_pricing.carpet', 'jobs_cleaning_pricing.storey', 'jobs_cleaning_pricing.price')
                                    ->join('taxes', 'taxes.id', '=', 'jobs_cleaning_pricing.tax_id')
                                    ->where(['jobs_cleaning_pricing.tenant_id'=>auth()->user()->tenant_id])
                                    ->orderBy('jobs_cleaning_pricing.bedrooms', 'asc')
                                    ->orderBy('jobs_cleaning_pricing.bathrooms', 'asc')
                                    ->orderBy('jobs_cleaning_pricing.carpet', 'asc')
                                    ->orderBy('jobs_cleaning_pricing.storey', 'asc')
            ->get();

            $response['error'] = 0;
            $response['id'] = $updateid;
            $response['message'] = 'End of Lease Pricing has been updated';
            $response['jobsCleaningPricing_html'] = view('admin.jobs-cleaning-pricing.jobscleaningpricing_grid')->with(['taxes' => $taxes, 'jobsCleaningPricing' => $jobsCleaningPricing])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxDestroyInventorDefinition(Request $request)
    {
        JobsCleaningPricing::destroy($request->id);
        
        $taxes = Tax::where(['tenant_id'=> auth()->user()->tenant_id])->get();

        $jobsCleaningPricing = JobsCleaningPricing::select('taxes.tax_name', 'jobs_cleaning_pricing.id', 'jobs_cleaning_pricing.tax_id','jobs_cleaning_pricing.bedrooms', 'jobs_cleaning_pricing.bathrooms', 'jobs_cleaning_pricing.carpet', 'jobs_cleaning_pricing.storey', 'jobs_cleaning_pricing.price')
                                ->join('taxes', 'taxes.id', '=', 'jobs_cleaning_pricing.tax_id')
                                ->where(['jobs_cleaning_pricing.tenant_id'=>auth()->user()->tenant_id])
                                ->orderBy('jobs_cleaning_pricing.bedrooms', 'asc')
                                ->orderBy('jobs_cleaning_pricing.bathrooms', 'asc')
                                ->orderBy('jobs_cleaning_pricing.carpet', 'asc')
                                ->orderBy('jobs_cleaning_pricing.storey', 'asc')
                                ->get();

        $response['error'] = 0;
        $response['message'] = 'End of Lease Pricing has been deleted';
        $response['jobsCleaningPricing_html'] = view('admin.jobs-cleaning-pricing.jobscleaningpricing_grid')->with(['taxes' => $taxes, 'jobsCleaningPricing' => $jobsCleaningPricing])->render();
        return json_encode($response);
    }
    
}
