<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class MovingInsuranceQuoteResponse extends Model
{
    use Notifiable;

    protected $table = 'moving_insurance_quote_responses';
    protected $primaryKey  = 'id';
    public $timestamps = false;
}
