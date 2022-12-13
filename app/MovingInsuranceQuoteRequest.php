<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class MovingInsuranceQuoteRequest extends Model
{
    use Notifiable;

    protected $table = 'moving_insurance_quote_requests';
    protected $primaryKey  = 'id';
    public $timestamps = false;
}
