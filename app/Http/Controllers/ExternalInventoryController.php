<?php
namespace App\Http\Controllers;

use App\CRMActivityLog;
use App\CRMContacts;
use App\CRMLeads;
use App\CRMOpportunities;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Stripe\Stripe;
use App\Invoice;
use App\InvoiceItems;
use App\Payment;
use App\InvoiceSetting;
use App\JobsMoving;
use App\JobsMovingInventory;
use App\MovingInventoryDefinitions;
use App\MovingInventoryGroups;
use App\Quotes;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\File;

class ExternalInventoryController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
    }

    //START::Pay Now Invoice Payment
    public function inventoryForm($params)
    {
        $params = explode('&',base64_decode($params));
        $tenant_id = ltrim($params[0],"tenant_id=");
        $job_id = ltrim($params[1],"job_id=");
        
        
        $this->job = DB::table('jobs_moving')->where(['job_id' => $job_id, 'tenant_id' => $tenant_id])->first();
        $this->company = DB::table('companies')->where(['id' => $this->job->company_id, 'tenant_id' => $tenant_id])->first();
        $this->opportunity = CRMOpportunities::where(['tenant_id' => $tenant_id, 'id' => $this->job->crm_opportunity_id])->first();
        $this->lead = CRMLeads::where(['tenant_id' => $tenant_id, 'id' => $this->opportunity->lead_id])->first();
        $this->inventory_groups = MovingInventoryGroups::where('tenant_id', '=', $tenant_id)->get();
        $this->getInventoryItems = MovingInventoryDefinitions::where('tenant_id', '=', $tenant_id)->get();
        $this->countInvItems = JobsMovingInventory::where('tenant_id', '=', $tenant_id)->where('job_id', $job_id)->where('inventory_id', 'like', '9000%')->count();
        $this->miscllanceous_items = JobsMovingInventory::where('misc_item', 'Y')->where(['job_id' => $this->job->job_id, 'tenant_id' => $tenant_id])->get();                                

        if (File::exists(public_path() . '/user-uploads/company-logo/' . $this->company->logo)) {
            $this->company_logo_exists = true;
        }else{
            $this->company_logo_exists = false;
        }

        return view('external-inventory-form',[
            'company_logo_exists'=>$this->company_logo_exists,
            'company'=>$this->company,
            'job'=>$this->job,       
            'lead'=>$this->lead,   
            'tenant_id'=>$tenant_id,   
            'inventory_groups'=>$this->inventory_groups,
            'getInventoryItems'=>$this->getInventoryItems,
            'countInvItems'=>$this->countInvItems,
            'miscllanceous_items' => $this->miscllanceous_items
        ]);
    }
    //END::Pay Now Invoice Payment

    public function saveInventoryData(Request $request, $job_id)
    {
        try {
            $tenant_id = $request::input('tenant_id');
            $lead_id = $request::input('lead_id');
            $is_modified = 0;
            if ($request::input('calc_data')) {
                $calculator_data = $request::input('calc_data');
                $job_id = $request::input('job_id');
                $explodeData = explode("&", $calculator_data);
                $saveExtraNotes = '';
                foreach ($explodeData as $row) {
                    $explodeRow = explode("=", $row);
                    $updatedata['job_id'] = $job_id;
                    $updatedata['inventory_id'] = $explodeRow[0];
                    $updatedata['quantity'] = $explodeRow[1];
                    // $checkIfSpecialItem = MovingInventoryDefinitions::select('item_name', 'special_notes')->where('id', '=', $explodeRow[0])->where('special_item', '=', 'YES')->first();
                    // $checkIfSpecialItem = $this->db->query("select item_name,special_notes from vbs_inventory_definitions WHERE id='$explodeRow[0]' AND special_item='YES' ")->row();
                    // if ($checkIfSpecialItem) {
                    // $saveExtraNotes .= $checkIfSpecialItem->item_name . ' : ' . $checkIfSpecialItem->special_notes . '\r\n';
                    //UPDATE categories SET code = CONCAT(code, '_standard') WHERE id = 1;
                    //Pool table (8 foot) : Ground floor to ground floor only;
                    // }
                    $table = "job_inventory";
                    $checkIfAlready = JobsMovingInventory::select('id')->where(['job_id' => $job_id, 'tenant_id' => $tenant_id])->where('inventory_id', '=', $explodeRow[0])->first();
                    if ($checkIfAlready) {
                        if($updatedata['quantity']>0){
                            $checkIfAlready->job_id = $updatedata['job_id'];
                            $checkIfAlready->inventory_id = $updatedata['inventory_id'];
                            $checkIfAlready->quantity = $updatedata['quantity'];
                            $checkIfAlready->save();
                            $is_modified = 1;
                        }else{
                            JobsMovingInventory::where('job_id', '=', $job_id)->where(['inventory_id' => $explodeRow[0], 'tenant_id' => $tenant_id])->delete();
                        }
                    } else {
                        $checkIfNoAlready = new JobsMovingInventory();
                        $checkIfNoAlready->job_id = $updatedata['job_id'];
                        $checkIfNoAlready->inventory_id = $updatedata['inventory_id'];
                        $checkIfNoAlready->quantity = $updatedata['quantity'];
                        $checkIfNoAlready->tenant_id = $tenant_id;
                        $checkIfNoAlready->save();
                        
                        // //Add Activity Log
                        // $data['log_message'] = 'Inventory List created by customer';
                        // $data['lead_id'] = $lead_id;
                        // $data['tenant_id'] = $tenant_id;
                        // $data['user_id'] = 0;
                        // $data['log_type'] = 11;
                        // $data['log_date'] = Carbon::now();
                        // $model = CRMActivityLog::create($data);
                    }
                    //echo $explodeRow[0].'<br/>';
                    if (isset($explodeRow[0]) && substr($explodeRow[0], 0, 4) == '9000') {
                        $tmp = explode('_', $explodeRow[0]);
                        if (isset($tmp[1]) && $tmp[1] == 'cbm') {
                            $updateMiscCBM = JobsMovingInventory::where(['job_id' => $job_id, 'tenant_id' => $tenant_id])->where('inventory_id', '=', $tmp[0])->first();
                            $updateMiscCBM->misc_item_cbm = $explodeRow[1];
                            $updateMiscCBM->save();
                        }
                        if (isset($tmp[1]) && $tmp[1] == 'name') {
                            $updateMiscCBM = JobsMovingInventory::where(['job_id' => $job_id, 'tenant_id' => $tenant_id])->where('inventory_id', '=', $tmp[0])->first();
                            $updateMiscCBM->misc_item_name = $explodeRow[1];
                            $updateMiscCBM->save();
                        }
                    }
                }
                if($is_modified==0){
                    //Add Activity Log
                    $data['log_message'] = 'Inventory List Created by customer';
                    $data['lead_id'] = $lead_id;
                    $data['job_id'] = $job_id;
                    $data['tenant_id'] = $tenant_id;
                    $data['user_id'] = 0;
                    $data['log_type'] = 11;
                    $data['log_date'] = Carbon::now();
                    $model = CRMActivityLog::create($data);
                }else{
                    //Add Activity Log
                    $data['log_message'] = 'Inventory List Modified by customer';
                    $data['lead_id'] = $lead_id;
                    $data['job_id'] = $job_id;
                    $data['tenant_id'] = $tenant_id;
                    $data['user_id'] = 0;
                    $data['log_type'] = 14;
                    $data['log_date'] = Carbon::now();
                    $model = CRMActivityLog::create($data);
                }
                $totalCBM = $this->calculate_total_cbm($job_id);

                $job_price_additional = DB::table('jobs_moving_pricing_additional as t1')->select('t1.*')->where(['t1.tenant_id' => $tenant_id])->first();     
                $goods_value_per_cbm = ($job_price_additional)?$job_price_additional->goods_value_per_cbm:0;
                $goods_value = ($totalCBM*$goods_value_per_cbm);

                JobsMoving::where('job_id', $job_id)->update([
                    'total_cbm' => $totalCBM,
                    'goods_value' => $goods_value
                ]);
                $data = array(
                    'totalCBM' => $totalCBM,
                    'special_item_notes' => 'special notes'
                );
                echo json_encode($data);
            } else {
                echo json_encode("0");
            }
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function saveInventoryMiscellanceousData(Request $request, $job_id)
    {
        //try {
            $tenant_id = JobsMovingInventory::where(['job_id' => $job_id, 'inventory_id' => 0])->pluck('tenant_id')->first();
            JobsMovingInventory::where(['job_id' => $job_id, 'inventory_id' => 0])->delete();
            $totalItems = 0;
            for($i = 0; $i < count($request::input('name')); $i++)
            {
                if($request::input('name')[$i] != null && $request::input('cbm')[$i] != null && $request::input('quantity')[$i] != null)
                {
                    $miscllanceousItem = new JobsMovingInventory();
                    $miscllanceousItem->job_id = $job_id;
                    $miscllanceousItem->inventory_id = 0;
                    $miscllanceousItem->quantity = $request::input('quantity')[$i];
                    $miscllanceousItem->misc_item = 'Y';
                    $miscllanceousItem->misc_item_name = $request::input('name')[$i];
                    $miscllanceousItem->misc_item_cbm = $request::input('cbm')[$i]; 
                    $miscllanceousItem->tenant_id = $request::input('tenant_id');
                    $miscllanceousItem->save();
                    $totalItems++;
                }
            }
            $totalCBM = $this->calculate_total_cbm($job_id);
            if($totalItems>0){
                $job_price_additional = DB::table('jobs_moving_pricing_additional as t1')->select('t1.*')->where(['t1.tenant_id' => $tenant_id])->first();     
                $goods_value_per_cbm = ($job_price_additional)?$job_price_additional->goods_value_per_cbm:0;
                $goods_value = ($totalCBM*$goods_value_per_cbm);

                JobsMoving::where('job_id', $job_id)->update([
                    'total_cbm' => $totalCBM,
                    'goods_value' => $goods_value
                ]);
            }
            
            $response['error'] = 0;
            $response['totalCBM'] = $totalCBM;
            $response['totalItems'] = $totalItems;
            $response['special_item_notes'] = 'special notes';
            $response['messgae'] = 'Miscllanceous item has been added.';
            return json_encode($response);

        // } catch (Exception $ex) {
        //     dd($ex->getMessage());
        // }
    }

    public function deleteInventoryData(Request $request)
    {
        try {
            $job_id = $request::input('job_id');
            $inv_id = $request::input('inv_id');
            $tenant_id = JobsMovingInventory::where(['job_id' => $job_id, 'inventory_id' => 0])->pluck('tenant_id')->first();
            if ($inv_id) {
                JobsMovingInventory::where(['job_id' => $job_id])->where('inventory_id', '=', $inv_id)->delete();
                $countInvItems = JobsMovingInventory::where(['job_id' => $job_id])->where('inventory_id', 'like', '9000%')->count();
                $totalCBM = $this->calculate_total_cbm($job_id);

                $job_price_additional = DB::table('jobs_moving_pricing_additional as t1')->select('t1.*')->where(['t1.tenant_id' => $tenant_id])->first();     
                $goods_value_per_cbm = ($job_price_additional)?$job_price_additional->goods_value_per_cbm:0;
                $goods_value = ($totalCBM*$goods_value_per_cbm);

                JobsMoving::where(['job_id' => $job_id])->update([
                    'total_cbm' => $totalCBM,
                    'goods_value' => $goods_value
                ]);
                $details = array(
                    'success' => '1',
                    'countInvItems' => $countInvItems,
                    'totalCBM' => $totalCBM,
                );
                echo json_encode($details);
            } else {
                echo json_encode("0");
            }
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    public function getInventoryDetails($job_id)
    {
        try {
            if ($job_id) {
                $jobInventoryCalculator = DB::select("select vji.*,vid.cbm from jobs_moving_inventory vji LEFT JOIN moving_inventory_definitions vid ON vid.id=vji.inventory_id WHERE job_id = '$job_id' ");
                $inventoryCalcCountbyGroupID = DB::select("select count(vid.group_id) as count,vid.group_id from jobs_moving_inventory vji LEFT JOIN moving_inventory_definitions vid ON vid.id=vji.inventory_id WHERE job_id = '$job_id' group by vid.group_id having count > 0 ");
                $details = array(
                    'inventoryCalc' => $jobInventoryCalculator,
                    'inventoryCalcCountbyGroupID' => $inventoryCalcCountbyGroupID,
                    'totalCBM' => $this->calculate_total_cbm($job_id)
                );
                echo json_encode($details);
            } else {
                echo json_encode("Job_id not posted!");
            }
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    protected function calculate_total_cbm($job_id)
    {
        try {
            $calculateTotalCBM = JobsMovingInventory::select(
                'jobs_moving_inventory.*', 
                'moving_inventory_definitions.cbm'
                )
                ->leftJoin('moving_inventory_definitions', 'moving_inventory_definitions.id', '=', 'jobs_moving_inventory.inventory_id')
                ->where('jobs_moving_inventory.job_id', $job_id)
                ->get();
            $totalCBM = 0;
            
            if ($calculateTotalCBM) {
                foreach ($calculateTotalCBM as $calc) {
                    $qtyExp = $calc->quantity;
                    if($calc->inventory_id != 0) {
                        $cbm = $calc->cbm;
                    } else{
                        $cbm = $calc->misc_item_cbm;   
                    }
                    $cbmQty = floatval($cbm) * floatval($qtyExp);
                    $totalCBM += $cbmQty;
                }
            }
            $floatto2 = number_format((float) $totalCBM, 2, '.', '');
            return $floatto2;
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }
}