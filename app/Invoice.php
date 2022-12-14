<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    use Notifiable;

    protected $dates = ['issue_date', 'due_date'];
    protected $appends = ['total_amount', 'issue_on'];
    protected $fillable = [
        'tenant_id','job_id', 'invoice_number', 'project_id','sys_job_type','discount_type','status','issue_date','due_date'
    ];
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItems::class, 'invoice_id');
    }

    public function payment()
    {
        return $this->hasMany(Payment::class, 'invoice_id')->orderBy('paid_on', 'desc');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public static function clientInvoices($clientId)
    {
        return Invoice::join('projects', 'projects.id', '=', 'invoices.project_id')
            ->select('projects.project_name', 'invoices.*')
            ->where('projects.client_id', $clientId)
            ->get();
    }

    public function getPaidAmount()
    {
        return (float)Payment::where('invoice_id', $this->id)->sum('amount');
    }

    public function getTotalAmount()
    {
        $record = InvoiceItems::select(DB::raw('SUM(unit_price*quantity) as sub_total, SUM(amount) as amount'))->where('invoice_id', $this->id)->first();
        $sub_total = $record->sub_total;
        $tax_total = $record->amount - $record->sub_total;

        if($this->discount_type=="percent"){
            $sub_total = $sub_total - ($this->discount/100 * $sub_total);            
        }else{
            $sub_total = $sub_total - $this->discount;
        }

        return (float)number_format(($sub_total + $tax_total), 2, '.', ''); 

    }

    public function getTotalApprovalItems()
    {
        return InvoiceItemsForApproval::where('invoice_id', $this->id)->sum('amount');
    }

    public function getTotalAmountAttribute()
    {

        if (!is_null($this->total) && !is_null($this->currency_symbol)) {
            return $this->currency_symbol . $this->total;
        }

        return "";
    }

    public function getIssueOnAttribute()
    {
        if (!is_null($this->issue_date)) {
            return Carbon::parse($this->issue_date)->format('d F, Y');
        }
        return "";
    }
}
