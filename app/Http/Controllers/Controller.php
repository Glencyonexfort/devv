<?php

namespace App\Http\Controllers;

use App\Setting;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;

class Controller extends BaseController {

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    public function __construct() {

//        $this->global = (object) array();

ini_set('memory_limit', '2048M');

        $this->middleware(function ($request, $next) {
            if (auth()->user()) {
                $this->global = Setting::where('tenant_id', auth()->user()->tenant_id)->first();
                config(['froiden_envato.allow_users_id' => true]);
            }else{
                $this->global = Setting::first();
            }
            App::setLocale($this->global->locale);
            Carbon::setLocale($this->global->locale);
            setlocale(LC_TIME, $this->global->locale . '_' . strtoupper($this->global->locale));
            if (config('app.env') !== 'development') {
                config(['app.debug' => $this->global->app_debug]);
            }
            //$this->checkMigrateStatus();
            return $next($request);
        });
    }

    public function checkMigrateStatus() {
        $status = Artisan::call('migrate:check');
        if ($status) {
            Artisan::call('migrate', array('--force' => true)); //migrate database
            Artisan::call('optimize'); //migrate database
        }
    }

}
