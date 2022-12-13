<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuoteItem extends Model
{
    protected $table = 'quote_items';

    public $timestamps = false;

    protected $fillable = [
        'tenant_id','quote_id','product_id', 'name', 'type' , 'description','unit_price','quantity','amount','tax_id','created_by','created_date'
    ];

}
