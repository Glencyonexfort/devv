<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceItems extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'tenant_id','invoice_id','product_id', 'item_name', 'type' , 'item_summary','unit_price','quantity','amount','tax_id','created_by','created_at'
    ];

    public function tax(){
        return $this->belongsTo(Tax::class, 'tax_id');
    }
}
