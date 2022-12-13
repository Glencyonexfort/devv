<?php

namespace App\Http\Controllers\Admin;

use App\JobsMoving;
use App\TenantApiDetail;
use Illuminate\Http\Request;

class CoverFreightController extends AdminBaseController
{
    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.coverFreight');
        $this->pageIcon = 'icon-shield-check';        
    }

    public function index()
    {
        try{
            $this->coverFreight_connected = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'CoverFreight'])->first();            
            if($this->coverFreight_connected){ 
                $this->tenant_api_details=true;
            }else{
                $this->tenant_api_details=false;
            }
            return view('admin.coverfreight.index', $this->data);
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function disconnect()
    {
        try{
            $tenant_api_details = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'CoverFreight'])->delete();                        
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
        $response = array(
            'status' => 1,
            'message' => 'Disconnected successful.'
        );
        return json_encode($response);
    }

}
