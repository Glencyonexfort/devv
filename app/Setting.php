<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'organisation_settings';
    protected $appends = ['logo_url','login_background_url'];

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function getLogoUrlAttribute()
    {
        if (is_null($this->logo)) {
            return asset('worksuite-logo.png');
        }

        return asset('user-uploads/app-logo/' . $this->logo);
    }

    public function getLoginBackgroundUrlAttribute()
    {
        return asset('login-background.jpg');
    }
}
