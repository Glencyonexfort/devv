<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Lists extends Model {
    protected $table = 'lists';
    public $timestamps = false;
    public static  function job_status() {
        // return Lists::where('list_type', '=', 'Job Status')
        //     ->where('tenant_id', '=', auth()->user()->tenant_id)
        //     ->get();
        return ListTypes::where(['list_types.tenant_id' => auth()->user()->tenant_id, 'list_types.list_name' => 'Job Status'])
        ->join('list_options', 'list_options.list_type_id', '=', 'list_types.id')
        ->select('list_options.list_option as options')
        ->get();            
    }

    public static  function leg_status() {
        return ListTypes::where(['list_types.tenant_id' => auth()->user()->tenant_id, 'list_types.list_name' => 'Leg Status'])
        ->join('list_options', 'list_options.list_type_id', '=', 'list_types.id')
        ->select('list_options.list_option')
        ->get();
    }

    public static  function job_type() {
        return ListTypes::where(['list_types.tenant_id' => auth()->user()->tenant_id, 'list_types.list_name' => 'Job Type'])
        ->join('list_options', 'list_options.list_type_id', '=', 'list_types.id')
        ->select('list_options.list_option as options')
        ->get();
    }

    public static function lead_info_types()
    {
        return DB::table('list_types')->where(['list_types.tenant_id' => auth()->user()->tenant_id, 'list_types.list_name' => 'Lead Info'])
                                                        ->join('list_options', 'list_options.list_type_id', '=', 'list_types.id')
                                                        ->select('list_options.list_option as options', 'list_options.id as id')
                                                        ->get();
    }

    public static  function sys_job_type() {
        return TenantModules::where(['tenant_modules.tenant_id' => auth()->user()->tenant_id])
        ->join('sys_modules', 'tenant_modules.sys_module_id', '=', 'sys_modules.id')
        ->select('sys_modules.sys_job_type as options','sys_modules.id')
        ->get();
    }

    public static  function price_structure() {
        return Lists::where('list_type', '=', 'Price Structure')
            ->where('tenant_id', '=', auth()->user()->tenant_id)
            ->get();
    }

    public static  function payment_status() {
        return Lists::where('list_type', '=', 'Payment Status')
            ->where('tenant_id', '=', auth()->user()->tenant_id)
            ->get();
    }

    public static  function lead_info() {
        return Lists::where('list_type', '=', 'Lead Info')
            ->where('tenant_id', '=', auth()->user()->tenant_id)
            ->get();
    }
}