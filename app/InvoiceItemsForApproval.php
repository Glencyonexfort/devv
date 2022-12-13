<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceItemsForApproval extends Model
{
    protected $table = 'invoice_items_for_approval';
    protected $guarded = ['id'];
    protected $fillable = ['tenant_id','invoice_id','product_id','item_name','item_summary','type','quantity','unit_price','amount','tax_id','approved'];

}
