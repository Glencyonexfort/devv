<?php

namespace App\Http\Controllers\Admin;

use App\Currency;
use App\Helper\Reply;
use App\Http\Requests\Settings\UpdateOrganisationSettings;
use App\Setting;
use App\Traits\CurrencyExchange;
use Carbon\Carbon;

use Illuminate\Http\Request;

class OrganisationSettingsController extends AdminBaseController
{

    use CurrencyExchange;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.accountSettings');
        $this->tutorialUrl = 'https://www.youtube.com/watch?v=nO9KT5EVAJM';
        $this->pageIcon = 'ti-settings';
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
        $this->currencies = Currency::all();
        $this->dateObject = Carbon::now();

        return view('admin.settings.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrganisationSettings $request, $id)
    {
        config(['filesystems.default' => 'local']);
        
        $setting = Setting::where('tenant_id', auth()->user()->tenant_id)->first();
        $setting->company_name = $request->input('company_name');
        $setting->company_email = $request->input('company_email');
        $setting->company_phone = $request->input('company_phone');
        $setting->website = $request->input('website');
        $setting->address = $request->input('address');
        //$setting->currency_id = $request->input('currency_id');
        $setting->timezone = $request->input('timezone');
        $setting->locale = $request->input('locale');
        $setting->date_format = $request->input('date_format');
        $setting->time_format = $request->input('time_format');
        $setting->week_starts = $request->input('week_starts');
        $setting->app_debug = $request->has('app_debug') && $request->input('app_debug') == 'on' ? 1 : 0;
        $setting->google_recaptcha = $request->has('google_recaptcha') && $request->input('google_recaptcha') == 'on' ? 1 : 0;
        $setting->google_recaptcha_key = $request->input('google_recaptcha_key');
        $setting->google_recaptcha_secret = $request->input('google_recaptcha_secret');
        $setting->weather_key = $request->input('weather_key');

        switch ($setting->date_format) {
            case 'd-m-Y':
                $setting->date_picker_format = 'dd-mm-yyyy';
                break;
            case 'm-d-Y':
                $setting->date_picker_format = 'mm-dd-yyyy';
                break;
            case 'Y-m-d':
                $setting->date_picker_format = 'yyyy-mm-dd';
                break;
            case 'd.m.Y':
                $setting->date_picker_format = 'dd.mm.yyyy';
                break;
            case 'm.d.Y':
                $setting->date_picker_format = 'mm.dd.yyyy';
                break;
            case 'Y.m.d':
                $setting->date_picker_format = 'yyyy.mm.dd';
                break;
            case 'd/m/Y':
                $setting->date_picker_format = 'dd/mm/yyyy';
                break;
            case 'm/d/Y':
                $setting->date_picker_format = 'mm/dd/yyyy';
                break;
            case 'Y/m/d':
                $setting->date_picker_format = 'yyyy/mm/dd';
                break;
            case 'd-M-Y':
                $setting->date_picker_format = 'dd-M-yyyy';
                break;
            case 'd/M/Y':
                $setting->date_picker_format = 'dd/M/yyyy';
                break;
            case 'd.M.Y':
                $setting->date_picker_format = 'dd.M.yyyy';
                break;
            case 'd-M-Y':
                $setting->date_picker_format = 'dd-M-yyyy';
                break;
            case 'd M Y':
                $setting->date_picker_format = 'dd M yyyy';
                break;
            case 'd F, Y':
                $setting->date_picker_format = 'dd MM, yyyy';
                break;
            case 'D/M/Y':
                $setting->date_picker_format = 'D/M/yyyy';
                break;
            case 'D.M.Y':
                $setting->date_picker_format = 'D.M.yyyy';
                break;
            case 'D-M-Y':
                $setting->date_picker_format = 'D-M-yyyy';
                break;
            case 'D M Y':
                $setting->date_picker_format = 'D M yyyy';
                break;
            case 'd D M Y':
                $setting->date_picker_format = 'dd D M yyyy';
                break;
            case 'D d M Y':
                $setting->date_picker_format = 'D dd M yyyy';
                break;
            case 'dS M Y':
                $setting->date_picker_format = 'dd M yyyy';
                break;
            
            default:
                $setting->date_picker_format = 'mm/dd/yyyy';
            break;
        }

        if ($request->hasFile('logo')) {
            $setting->logo = $request->logo->hashName();
            $request->logo->store('user-uploads/app-logo');
        }
        $setting->last_updated_by = $this->user->id;

        if ($request->hasFile('login_background')) {
            $request->login_background->storeAs('user-uploads', 'login-background.jpg');
            $setting->login_background = 'login-background.jpg';
        }
        $setting->save();

        //$this->updateExchangeRates();

        return Reply::redirect(route('admin.settings.index'));

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

    public function changeLanguage(Request $request)
    {
        $setting = Setting::first();
        $setting->locale = $request->input('lang');

        $setting->last_updated_by = $this->user->id;
        $setting->save();

        return Reply::success('Language changed successfully.');
    }
}
