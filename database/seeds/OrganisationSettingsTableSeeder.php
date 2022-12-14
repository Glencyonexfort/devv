<?php

use Illuminate\Database\Seeder;
use App\Setting;
use App\Currency;


class OrganisationSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $currency = Currency::where('currency_code', 'USD')->first();

        $setting = new Setting();
        $setting->company_name = 'Worksuite';
        $setting->company_email = 'company@email.com';
        $setting->company_phone = '1234567891';
        $setting->address = 'Company address';
        $setting->website = 'www.domain.com';
        $setting->currency_id = $currency->id;
        $setting->timezone = 'Asia/Kolkata';
        $setting->weather_key = '9f7190aeb882036f098ba016003ab300';
        $setting->currency_converter_key = '6c12788708871d0c499d';
        $setting->date_picker_format = 'dd-mm-yyyy';
        $setting->save();


    }

}
