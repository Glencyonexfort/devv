<?php

namespace App\Exports;

use App\InvoiceItems;
use App\JobsMoving;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;

class DataExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = DB::table('jobs_moving_legs')
                        ->join('jobs_moving', 'jobs_moving.job_id', '=', 'jobs_moving_legs.job_id') 
                        ->join('customers', 'customers.id', '=', 'jobs_moving.customer_id')
                        // ->join('vehicles', 'vehicles.id', '=', 'jobs_moving.vehicle_id')
                        ->join('invoices', 'invoices.job_id', '=', 'jobs_moving_legs.job_id')
                        ->join('payments', 'payments.invoice_id', '=', 'invoices.id')
                        ->get()
                        ->toArray();

        // dd($data);

        $display[] = array(
                        'Job id',
                        'Job Number', 
                        'Customer Name', 
                        'Customer Mobile', 
                        'Customer Email', 
                        'Job Date', 
                        'Leg No', 
                        'Actual Start Time', 
                        'Actual End Time', 
                        'Pickup Address', 
                        'Delivery Address', 
                        'Vahicle Name',
                        'Driver Name', 
                        'Invoice number',
                        'Invoice Status',
                        'Invoice Total',
                        'Payment Total',
                        'Invoice Item 1',
                        'Invoice Item 1 amount',
                        'Invoice Item 2',
                        'Invoice Item 2 amount',
                        'Invoice Item 3',
                        'Invoice Item 3 amount'
        );
        
        $count = 0 ;
        $id = $data[0]->job_id;
        foreach($data as $job)
        {
            $items = InvoiceItems::where('invoice_id', $job->invoice_id)->take(3)->get();
            if($id == $job->job_id)
            {
                if($count != 3)
                {
                    
                    $display[] = array(
                        'Job id' => $job->job_id,
                        'Job Number' => $job->job_number,
                        'customer Name' => $job->first_name.' '.$job->last_name,
                        'Customer Mobile' => $job->mobile,
                        'Customer Email' => $job->email,
                        'Job Date' => $job->job_date,
                        'Leg No' => $job->leg_number,
                        'Actual Start Time' => $job->actual_start_time,
                        'Actual End Time' => $job->actual_finish_time,
                        'Pickup Address' => $job->pickup_address,
                        'Delivery Address' => $job->delivery_suburb,
                        'Vahicle Name' => null,
                        'Driver Name' => null,
                        'Invoice number' => $job->invoice_number,
                        'Invoice Status' => $job->status,
                        'Invoic Total' => $job->total,
                        'Payment Total' => $job->amount,
                        'Invoice Item 1' => isset($items[0]->item_name) ? $items[0]->item_name : '',
                        'Invoice Item 1 amount' => isset($items[0]->amount) ? $items[0]->amount : '',
                        'Invoice Item 2' => isset($items[1]->item_name) ? $items[1]->item_name : '',
                        'Invoice Item 2 amount' => isset($items[1]->amount) ? $items[1]->amount : '',
                        'Invoice Item 3 ' => isset($items[2]->item_name) ? $items[2]->item_name : '',
                        'Invoice Item 3 amount' => isset($items[2]->amount) ? $items[2]->amount : ''
                    );
                    $count++;
                }
            }
            else
            {
                $display[] = array(
                    'Job id' => $job->job_id,
                    'Job Number' => $job->job_number,
                    'customer Name' => $job->first_name.' '.$job->last_name,
                    'Customer Mobile' => $job->mobile,
                    'Customer Email' => $job->email,
                    'Job Date' => $job->job_date,
                    'Leg No' => $job->leg_number,
                    'Actual Start Time' => $job->actual_start_time,
                    'Actual End Time' => $job->actual_finish_time,
                    'Pickup Address' => $job->pickup_address,
                    'Delivery Address' => $job->delivery_suburb,
                    'Vahicle Name' => null,
                    'Driver Name' => null,
                    'Invoice number' => $job->invoice_number,
                    'Invoice Status' => $job->status,
                    'Invoic Total' => $job->total,
                    'Payment Total' => $job->amount,
                    'Invoice Item 1' => isset($items[0]->item_name) ? $items[0]->item_name : '',
                    'Invoice Item 1 amount' => isset($items[0]->amount) ? $items[0]->amount : '',
                    'Invoice Item 2' => isset($items[1]->item_name) ? $items[1]->item_name : '',
                    'Invoice Item 2 amount' => isset($items[1]->amount) ? $items[1]->amount : '',
                    'Invoice Item 3 ' => isset($items[2]->item_name) ? $items[2]->item_name : '',
                    'Invoice Item 3 amount' => isset($items[2]->amount) ? $items[2]->amount : ''
                );
                $id = $job->job_id;
                $count = 1;
            }
        }

        
        return new Collection([
            $display
        ]);
    }
}
