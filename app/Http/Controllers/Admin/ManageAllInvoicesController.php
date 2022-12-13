<?php
namespace App\Http\Controllers\Admin;
use App\Currency;
use App\Customers;
use App\Estimate;
use App\Helper\Reply;
use App\Http\Requests\InvoiceFileStore;
use App\Http\Requests\Invoices\StoreInvoice;
use App\Invoice;
use App\InvoiceItems;
use App\InvoiceSetting;
use App\JobsMoving;
use App\Notifications\NewInvoice;
use App\Product;
use App\Project;
use App\Proposal;
use App\Setting;
use App\Tax;
use App\User;
use App\Payment;
use App\Lists;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\ProjectMilestone;
use App\CRMLeads;
use App\CRMContactDetail;
use App\CRMContacts;
 
class ManageAllInvoicesController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.invoices');
        $this->pageIcon = 'ti-receipt';
        $this->middleware(function ($request, $next) {
            if (!in_array('invoices', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }
    public function index()
    {
        $this->projects = Project::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $this->clients = User::allClients();
        $this->job_status = Lists::job_status();
        return view('admin.invoices.index', $this->data);
    }
    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function data(Request $request)
    {
        // print_r($request->all());
        // return;
        //DB::enableQueryLog();
        $invoices = Invoice::select('invoices.id', 
            'invoices.sys_job_type', 
            'invoices.invoice_number', 
            'invoices.job_id', 
            'invoices.status', 
            'invoices.due_date', 
            'invoices.issue_date',
            'jobs_moving.job_number',
            'jobs_moving.customer_id as customer_id')
            ->join('jobs_moving', 'jobs_moving.job_id', 'invoices.job_id');
        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate)->toDateString();
            $invoices = $invoices->where(DB::raw('DATE(invoices.`issue_date`)'), '>=', $startDate);
        }
        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->toDateString();
            $invoices = $invoices->where(DB::raw('DATE(invoices.`issue_date`)'), '<=', $endDate);
        }
        if ($request->status != 'all' && !is_null($request->status)) {
            $invoices = $invoices->where('invoices.status', '=', $request->status);
        }
        if ($request->job_status !== null && $request->job_status != 'null' && $request->job_status != '') {
            $job_status = explode(",", $request->job_status);
            $invoices = $invoices->wherein('jobs_moving.job_status', $job_status);
        }
        $invoices = $invoices->where('invoices.tenant_id', '=', auth()->user()->tenant_id);
        $invoices = $invoices->orderBy('invoices.id', 'desc')->get();
        
        //print_r(DB::getQueryLog());
        // dd($invoices);
        return DataTables::of($invoices)
        ->editColumn('sys_job_type', function ($row) {
            return $row->sys_job_type;
        })
        ->editColumn('job_id', function ($row) {
            return $row->job_number;
        })
        ->editColumn('name', function ($row) {
            $customer_name = '';
            if($row->customer_id){
                $customer = CRMLeads::find($row->customer_id);
                if($customer){
                    $customer_name = $customer->name;//first_name.' '.$customer->last_name;
                }
            }
            return $customer_name;
        })
        ->editColumn(
            'due_date',
            function ($row) {
                return $row->due_date->timezone($this->global->timezone)->format($this->global->date_format);
            }
        )
        ->editColumn('total', function ($row) {
            $total = InvoiceItems::where('invoice_id', $row->id)->sum('amount');
            return '$ ' . $total;
        })
        ->editColumn('payment_recieved', function ($row) {
            $payments = Payment::where('invoice_id', $row->id)->sum('amount');
            return '$ ' . $payments;
        })
        ->editColumn('status', function ($row) {
            if ($row->status == 'unpaid') {
                return '<label class="label label-danger">' . strtoupper($row->status) . '</label>';
            } elseif ($row->status == 'paid') {
                return '<label class="label label-success">' . strtoupper($row->status) . '</label>';
            } else {
                return '<label class="label label-info">' . __('modules.invoices.partial') . '</label>';
            }
        })
        ->editColumn(
            'issue_date',
            function ($row) {
                return $row->issue_date->timezone($this->global->timezone)->format($this->global->date_format);
            }
        )
        ->editColumn('invoice_number', function ($row) {
            return '<a href="' . route('admin.all-invoices.show', $row->id) . '">' . ucfirst($row->invoice_number) . '</a>';
        })
            ->rawColumns(['job_id', 'action', 'status', 'invoice_number'])
            ->removeColumn('action')
            ->removeColumn('id')
            ->make(true);
    }
    public function download($id)
    {
        //        header('Content-type: application/pdf');
        $this->invoice = Invoice::findOrFail($id);

        // Download file uploaded
        if ($this->invoice->file_original_name != null) {
            //dd($this->invoice);
            return response()->download(public_path('/invoice-files') . '/' . $this->invoice->file_original_name);
        }

        if ($this->invoice->discount > 0) {
            if ($this->invoice->discount_type == 'percent') {
                $this->discount = (($this->invoice->discount / 100) * $this->invoice->sub_total);
            } else {
                $this->discount = $this->invoice->discount;
            }
        } else {
            $this->discount = 0;
        }
        $taxList = array();
        $items = InvoiceItems::whereNotNull('tax_id')
            ->where('invoice_id', $this->invoice->id)
            ->get();
        foreach ($items as $item) {
            if (!isset($taxList[$item->tax->tax_name . ': ' . $item->tax->rate_percent . '%'])) {
                $taxList[$item->tax->tax_name . ': ' . $item->tax->rate_percent . '%'] = ($item->tax->rate_percent / 100) * $item->amount;
            } else {
                $taxList[$item->tax->tax_name . ': ' . $item->tax->rate_percent . '%'] = $taxList[$item->tax->tax_name . ': ' . $item->tax->rate_percent . '%'] + (($item->tax->rate_percent / 100) * $item->amount);
            }
        }
        $this->taxes = $taxList;
        $this->settings = Setting::findOrFail(1);
        $this->invoiceSetting = InvoiceSetting::first();
        //        return view('invoices.'.$this->invoiceSetting->template, $this->data);
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('invoices.' . $this->invoiceSetting->template, $this->data);
        $filename = $this->invoice->invoice_number;
        //       return $pdf->stream();
        return $pdf->download($filename . '.pdf');
    }
    public function destroy($id)
    {
        Invoice::destroy($id);
        return Reply::success(__('messages.invoiceDeleted'));
    }
    public function create()
    {
        $this->projects = Project::all();
        $this->currencies = Currency::all();
        $this->lastInvoice = Invoice::orderBy('id', 'desc')->first();
        $this->invoiceSetting = InvoiceSetting::first();
        $this->taxes = Tax::all();
        $this->products = Product::all();
        return view('admin.invoices.create', $this->data);
    }
    public function store(StoreInvoice $request)
    {
        $items = $request->input('item_name');
        $itemsSummary = $request->input('item_summary');
        $cost_per_item = $request->input('cost_per_item');
        $quantity = $request->input('quantity');
        $amount = $request->input('amount');
        $tax = $request->input('taxes');
        foreach ($quantity as $qty) {
            if (!is_numeric($qty) && (intval($qty) < 1)) {
                return Reply::error(__('messages.quantityNumber'));
            }
        }
        foreach ($cost_per_item as $rate) {
            if (!is_numeric($rate)) {
                return Reply::error(__('messages.unitPriceNumber'));
            }
        }
        foreach ($amount as $amt) {
            if (!is_numeric($amt)) {
                return Reply::error(__('messages.amountNumber'));
            }
        }
        foreach ($items as $itm) {
            if (is_null($itm)) {
                return Reply::error(__('messages.itemBlank'));
            }
        }
        $invoice = new Invoice();
        $invoice->project_id = $request->project_id;
        $invoice->invoice_number = $request->invoice_number;
        $invoice->issue_date = Carbon::createFromFormat($this->global->date_format, $request->issue_date)->format('Y-m-d');
        $invoice->due_date = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');
        $invoice->sub_total = round($request->sub_total, 2);
        $invoice->discount = round($request->discount_value, 2);
        $invoice->discount_type = $request->discount_type;
        $invoice->total = round($request->total, 2);
        $invoice->currency_id = $request->currency_id;
        $invoice->recurring = $request->recurring_payment;
        $invoice->billing_frequency = $request->recurring_payment == 'yes' ? $request->billing_frequency : null;
        $invoice->billing_interval = $request->recurring_payment == 'yes' ? $request->billing_interval : null;
        $invoice->billing_cycle = $request->recurring_payment == 'yes' ? $request->billing_cycle : null;
        $invoice->note = $request->note;
        $invoice->save();
        foreach ($items as $key => $item) :
            if (!is_null($item)) {
                InvoiceItems::create(
                    [
                        'invoice_id' => $invoice->id,
                        'item_name' => $item,
                        'item_summary' => $itemsSummary[$key],
                        'type' => 'item',
                        'quantity' => $quantity[$key],
                        'unit_price' => round($cost_per_item[$key], 2),
                        'amount' => round($amount[$key], 2),
                        'tax_id' => $tax[$key]
                    ]
                );
            }
        endforeach;
        //set milestone paid if converted milestone to invoice
        if($request->milestone_id != '') {
            $milestone = ProjectMilestone::findOrFail($request->milestone_id);
            $milestone->invoice_created = 1;
            $milestone->invoice_id = $invoice->id;
            $milestone->save();
        }
        //log search
        $this->logSearchEntry($invoice->id, 'Invoice ' . $invoice->invoice_number, 'admin.all-invoices.show');
        if ($invoice->project->client_id != null) {
            // Notify client
            $notifyUser = User::withoutGlobalScope('active')->findOrFail($invoice->project->client_id);
            $notifyUser->notify(new NewInvoice($invoice));
        }
        return Reply::redirect(route('admin.all-invoices.index'), __('messages.invoiceCreated'));
    }
    public function edit($id)
    {
        $this->invoice = Invoice::findOrFail($id);
        $this->projects = Project::all();
        $this->currencies = Currency::all();
        if ($this->invoice->status == 'paid') {
            abort(403);
        }
        $this->taxes = Tax::all();
        $this->products = Product::all();
        return view('admin.invoices.edit', $this->data);
    }
    public function update(StoreInvoice $request, $id)
    {
        $items = $request->input('item_name');
        $itemsSummary = $request->input('item_summary');
        $cost_per_item = $request->input('cost_per_item');
        $quantity = $request->input('quantity');
        $amount = $request->input('amount');
        $tax = $request->input('taxes');
        foreach ($quantity as $qty) {
            if (!is_numeric($qty) && $qty < 1) {
                return Reply::error(__('messages.quantityNumber'));
            }
        }
        foreach ($cost_per_item as $rate) {
            if (!is_numeric($rate)) {
                return Reply::error(__('messages.unitPriceNumber'));
            }
        }
        foreach ($amount as $amt) {
            if (!is_numeric($amt)) {
                return Reply::error(__('messages.amountNumber'));
            }
        }
        foreach ($items as $itm) {
            if (is_null($itm)) {
                return Reply::error(__('messages.itemBlank'));
            }
        }
        $invoice = Invoice::findOrFail($id);
        if ($invoice->status == 'paid') {
            return Reply::error(__('messages.invalidRequest'));
        }
        $invoice->project_id = $request->project_id;
        $invoice->invoice_number = $request->invoice_number;
        $invoice->issue_date = Carbon::createFromFormat($this->global->date_format, $request->issue_date)->format('Y-m-d');
        $invoice->due_date = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');
        $invoice->sub_total = round($request->sub_total, 2);
        $invoice->discount = round($request->discount_value, 2);
        $invoice->discount_type = $request->discount_type;
        $invoice->total = round($request->total, 2);
        $invoice->currency_id = $request->currency_id;
        $invoice->status = $request->status;
        $invoice->recurring = $request->recurring_payment;
        $invoice->billing_frequency = $request->recurring_payment == 'yes' ? $request->billing_frequency : null;
        $invoice->billing_interval = $request->recurring_payment == 'yes' ? $request->billing_interval : null;
        $invoice->billing_cycle = $request->recurring_payment == 'yes' ? $request->billing_cycle : null;
        $invoice->note = $request->note;
        $invoice->save();
        // delete and create new
        InvoiceItems::where('invoice_id', $invoice->id)->delete();
        foreach ($items as $key => $item) :
            InvoiceItems::create(
                [
                    'invoice_id' => $invoice->id,
                    'item_name' => $item,
                    'item_summary' => $itemsSummary[$key],
                    'type' => 'item',
                    'quantity' => $quantity[$key],
                    'unit_price' => round($cost_per_item[$key], 2),
                    'amount' => round($amount[$key], 2),
                ]
            );
        endforeach;
        // Notify client
        $notifyUser = User::withoutGlobalScope('active')->findOrFail($invoice->project->client_id);
        $notifyUser->notify(new NewInvoice($invoice));
        return Reply::redirect(route('admin.all-invoices.index'), __('messages.invoiceUpdated'));
    }
    public function show($id)
    {
        $this->invoice = Invoice::findOrFail($id);
        $this->invoice_payments = Invoice::where('job_id', '=', $this->invoice->job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $this->paidAmount = $this->invoice->getPaidAmount();
        $this->totalAmount = $this->invoice->getTotalAmount();
        $this->job = JobsMoving::where('job_id', '=', $this->invoice->job_id)->where('tenant_id', '=', auth()->user()->tenant_id)->first();
       //echo '<pre>';
        //print_r($this->invoice);
        //exit;
        $jobNumber = 0;
        $nameSpace = '\\App\\';
        $modelName = $nameSpace.'Jobs'.ucfirst($this->invoice->sys_job_type);
        $jobs = $modelName::where('job_id', $this->invoice->job_id)->first();
        $this->customer = CRMLeads::find($jobs->customer_id);

        $this->crm_contacts = CRMContacts::where('lead_id', '=', $jobs->customer_id)->first();
        $this->crm_contact_email = CRMContactDetail::where('contact_id', '=', $this->crm_contacts->id)->where('detail_type', '=', 'Email')->first();
        $this->crm_contact_phone = CRMContactDetail::where('contact_id', '=', $this->crm_contacts->id)->where('detail_type', '=', 'Mobile')->first();

        if($jobs){
            $jobNumber = $jobs->job_number;
        }
        $this->job_number = $jobNumber;
        $this->paidAmount = $this->invoice->getPaidAmount();
        if ($this->invoice->discount > 0) {
            if ($this->invoice->discount_type == 'percent') {
                $this->discount = (($this->invoice->discount / 100) * $this->invoice->sub_total);
            } else {
                $this->discount = $this->invoice->discount;
            }
        } else {
            $this->discount = 0;
        }
        $taxList = array();
        // $items = InvoiceItems::where('invoice_id', $this->invoice->id)->get();
        // foreach ($items as $item) {
        //     if (!isset($taxList[$item->tax->tax_name . ': ' . $item->tax->rate_percent . '%'])) {
        //         $taxList[$item->tax->tax_name . ': ' . $item->tax->rate_percent . '%'] = ($item->tax->rate_percent / 100) * $item->amount;
        //     } else {
        //         $taxList[$item->tax->tax_name . ': ' . $item->tax->rate_percent . '%'] = $taxList[$item->tax->tax_name . ': ' . $item->tax->rate_percent . '%'] + (($item->tax->rate_percent / 100) * $item->amount);
        //     }
        // }
        $this->taxes = $taxList;
        $this->settings = Setting::findOrFail(1);
        $this->invoiceSetting = InvoiceSetting::first();
        return view('admin.invoices.show', $this->data);
    }
    public function convertEstimate($id)
    {
        $this->invoice = Estimate::with('items')->findOrFail($id);
        $this->lastInvoice = Invoice::orderBy('id', 'desc')->first();
        $this->invoiceSetting = InvoiceSetting::first();
        $this->projects = Project::all();
        $this->currencies = Currency::all();
        $this->taxes = Tax::all();
        $this->products = Product::all();
        //        foreach ($this->invoice->items as $items)
        $discount = $this->invoice->items->filter(function ($value, $key) {
            return $value->type == 'discount';
        });
        $tax = $this->invoice->items->filter(function ($value, $key) {
            return $value->type == 'tax';
        });
        $this->totalTax = $tax->sum('amount');
        $this->totalDiscount = $discount->sum('amount');
        return view('admin.invoices.convert_estimate', $this->data);
    }
    public function convertProposal($id)
    {
        $this->invoice = Proposal::findOrFail($id);
        $this->lastInvoice = Invoice::orderBy('id', 'desc')->first();
        $this->invoiceSetting = InvoiceSetting::first();
        $this->projects = Project::all();
        $this->currencies = Currency::all();
        return view('admin.invoices.convert_estimate', $this->data);
    }
    public function addItems(Request $request)
    {
        $this->items = Product::with('tax')->find($request->id);
        $this->taxes = Tax::all();
        $view = view('admin.invoices.add-item', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }
    public function paymentDetail($invoiceID)
    {
        $this->invoice = Invoice::findOrFail($invoiceID);
        return View::make('admin.invoices.payment-detail', $this->data);
    }
    public function addPayment($invoiceID)
    {
        $this->invoice = Invoice::findOrFail($invoiceID);
        $this->lists = Lists::where('list_type', 'Sys Job Type')
            ->where('tenant_id', '=', auth()->user()->tenant_id)
            ->get();
        $job_numbers = Invoice::leftJoin('jobs_moving', 'jobs_moving.job_id', '=', 'invoices.job_id')
            ->select('jobs_moving.job_id', 'jobs_moving.job_number')
            ->where('invoices.status', '<>', 'paid')
            ->where('invoices.sys_job_type', '=', 'Moving')
            ->get();
        $this->payment_gateways = \App\OfflinePaymentMethod::where('tenant_id', auth()->user()->tenant_id)
                ->where('status', 'yes')
                ->get();
        $this->job_numbers = $job_numbers;
        $this->projects = Project::all();
        $this->currencies = Currency::all();
        return View::make('admin.invoices.add-payment', $this->data);
    }
    public function storePayment(Request $request) {
        $payment = new Payment();
        $settings = Setting::where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $payment->invoice_id = $request->invoice_id;
        if($settings) {
            $payment->currency_id = $settings->currency_id;
        }
        $payment->amount = round($request->amount, 2);
        $payment->gateway = $request->gateway;
        $payment->transaction_id = $request->transaction_id;
        $payment->paid_on = Carbon::createFromFormat('d/m/Y H:i', $request->paid_on)->format('Y-m-d H:i:s');
        $payment->tenant_id = auth()->user()->tenant_id;
        $payment->status = 'complete';
        $payment->remarks = $request->remarks;
        $payment->save();
        if($request->has('invoice_id')){
            $invoice = Invoice::find($request->invoice_id);
            if($invoice) {
                $paidAmount = $invoice->getPaidAmount();
                $totalAmount = $invoice->getTotalAmount();
                //dd($paidAmount);
                if (($paidAmount + $request->amount) >= $totalAmount) {
                    $invoice->status = 'paid';
                } else {
                    $invoice->status = 'partial';
                }
                $invoice->save();
            }
        }
        return redirect('/admin/finance/all-invoices/'.$request->invoice_id);
//        return Reply::redirect(route('admin.payments.index'), __('messages.paymentSuccess'));
    }

    public function addInvoiceItem($invoiceID)
    {
        $this->invoice = Invoice::findOrFail($invoiceID);
        return View::make('admin.invoices.add-invoice-item', $this->data);
    }

    public function storeInvoiceItem(Request $request) {
        $invoiceItem = new InvoiceItems();        
        $invoiceItem->invoice_id = $request->invoice_id;
        $invoiceItem->amount = round($request->amount, 2);
        $invoiceItem->item_name = $request->invoice_item;
        $invoiceItem->item_summary = $request->description;
        $invoiceItem->tenant_id = auth()->user()->tenant_id;
        $invoiceItem->quantity = $request->quantity;
        $invoiceItem->unit_price = $request->unit_price;
        $invoiceItem->type = 'item';
        $totalAmount = $request->quantity*$request->unit_price;
        $invoiceItem->amount = round($totalAmount, 2);
        $invoiceItem->save();
        return redirect('/admin/finance/all-invoices/'.$request->invoice_id);
    }

    public function updateInvoiceItem(Request $request) {
        //dd($request->item_id);
        $invoiceItem = InvoiceItems::findOrFail($request->item_id);       
        $invoiceItem->invoice_id = $request->invoice_id;
        $invoiceItem->amount = round($request->amount, 2);
        $invoiceItem->item_name = $request->invoice_item;
        $invoiceItem->item_summary = $request->description;
        $invoiceItem->tenant_id = auth()->user()->tenant_id;
        $invoiceItem->quantity = $request->quantity;
        $invoiceItem->unit_price = $request->unit_price;
        $invoiceItem->type = 'item';
        $totalAmount = $request->quantity*$request->unit_price;
        $invoiceItem->amount = round($totalAmount, 2);
        $invoiceItem->save();
        return redirect('/admin/finance/all-invoices/'.$request->invoice_id);
    }

    public function editInvoiceItem($invoiceItemID)
    {
        
        $this->invoiceItem = InvoiceItems::findOrFail($invoiceItemID);
        return View::make('admin.invoices.edit-invoice-item', $this->data);
    }


    public function editInvoicePayment($invoicePaymentID)
    {
        
        $this->invoicePayment = Payment::findOrFail($invoicePaymentID);        
        $this->invoice = Invoice::findOrFail( $this->invoicePayment->invoice_id);
        $this->lists = Lists::where('list_type', 'Sys Job Type')
            ->where('tenant_id', '=', auth()->user()->tenant_id)
            ->get();
        $job_numbers = Invoice::leftJoin('jobs_moving', 'jobs_moving.job_id', '=', 'invoices.job_id')
            ->select('jobs_moving.job_id', 'jobs_moving.job_number')
            ->where('invoices.status', '<>', 'paid')
            ->where('invoices.sys_job_type', '=', 'Moving')
            ->get();
        $this->payment_gateways = \App\OfflinePaymentMethod::where('tenant_id', auth()->user()->tenant_id)
                ->where('status', 'yes')
                ->get();
        $this->job_numbers = $job_numbers;
        $this->projects = Project::all();
        $this->currencies = Currency::all();
        //dd($this->payment_gateways);
        return View::make('admin.invoices.edit-payment', $this->data);
    }

    public function updateInvoicePayment(Request $request) {
        //dd($request->payment_id);
        $invoicePayment = Payment::findOrFail($request->payment_id);       
        $invoicePayment->gateway = $request->gateway;
        $invoicePayment->amount = round($request->amount, 2);
        $invoicePayment->transaction_id = $request->transaction_id;
        $invoicePayment->remarks = $request->remarks;      
        $invoicePayment->save();
        return redirect('/admin/finance/all-invoices/'.$request->invoice_id);
    }

    public function destroyInvoiceItem($id)
    {
        InvoiceItems::destroy($id);
        return Reply::success(__('messages.invoiceItemDeleted'));
    }

    public function destroyInvoicePayment($id)
    {
        Payment::destroy($id);
        return Reply::success(__('messages.invoicePaymentDeleted'));
    }


    /**
     * @param InvoiceFileStore $request
     * @return array
     */
    public function storeFile(InvoiceFileStore $request)
    {
        $invoiceId = $request->invoice_id;
        $file = $request->file('file');
        $newName = $file->hashName(); // setting hashName name
        // Getting invoice data
        $invoice = Invoice::find($invoiceId);
        if ($invoice != null) {
            if ($invoice->file != null) {
                unlink(storage_path('app/public/invoice-files') . '/' . $invoice->file);
            }
            $file->move(storage_path('app/public/invoice-files'), $newName);
            $invoice->file = $newName;
            $invoice->file_original_name = $file->getClientOriginalName(); // Getting uploading file name;
            $invoice->save();
            return Reply::success(__('messages.fileUploadedSuccessfully'));
        }
        return Reply::error(__('messages.fileUploadIssue'));
    }
    /**
     * @param Request $request
     * @return array
     */
    public function destroyFile(Request $request)
    {
        $invoiceId = $request->invoice_id;
        $invoice = Invoice::find($invoiceId);
        if ($invoice != null) {
            if ($invoice->file != null) {
                unlink(storage_path('app/public/invoice-files') . '/' . $invoice->file);
            }
            $invoice->file = null;
            $invoice->file_original_name = null;
            $invoice->save();
        }
        return Reply::success(__('messages.fileDeleted'));
    }
    /**
     * @param $startDate
     * @param $endDate
     * @param $status
     * @param $projectID
     */
    public function export($startDate, $endDate, $status, $projectID)
    {
        $startDate = Carbon::createFromFormat($this->global->date_format, $startDate)->toDateString();
        $endDate = Carbon::createFromFormat($this->global->date_format, $endDate)->toDateString();
        $invoices = Invoice::join('projects', 'projects.id', '=', 'invoices.project_id')
            ->join('currencies', 'currencies.id', '=', 'invoices.currency_id')
            ->select('invoices.id', 'invoices.invoice_number', 'projects.project_name', 'invoices.total', 'currencies.currency_symbol', 'invoices.status', 'invoices.issue_date');
        if ($startDate !== null && $startDate != 'null' && $startDate != '') {
            $invoices = $invoices->where(DB::raw('DATE(invoices.`issue_date`)'), '>=', $startDate);
        }
        if ($endDate !== null && $endDate != 'null' && $endDate != '') {
            $invoices = $invoices->where(DB::raw('DATE(invoices.`issue_date`)'), '<=', $endDate);
        }
        if ($status != 'all' && !is_null($status)) {
            $invoices = $invoices->where('invoices.status', '=', $status);
        }
        if ($projectID != 'all' && !is_null($projectID)) {
            $invoices = $invoices->where('invoices.project_id', '=', $projectID);
        }
        $attributes =  ['total', 'currency_symbol', 'issue_date'];
        $invoices = $invoices->get()->makeHidden($attributes);
        // Initialize the array which will be passed into the Excel
        // generator.
        $exportArray = [];
        // Define the Excel spreadsheet headers
        $exportArray[] = ['ID', 'InvoiceID', 'Project', 'Status', 'Total', 'Invoice Date'];
        // Convert each member of the returned collection into an array,
        // and append it to the payments array.
        foreach ($invoices as $row) {
            $exportArray[] = $row->toArray();
        }
        // Generate and return the spreadsheet
        Excel::create('invoice', function ($excel) use ($exportArray) {
            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Invoice');
            $excel->setCreator('Worksuite')->setCompany($this->companyName);
            $excel->setDescription('invoice file');
            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function ($sheet) use ($exportArray) {
                $sheet->fromArray($exportArray, null, 'A1', false, false);
                $sheet->row(1, function ($row) {
                    // call row manipulation methods
                    $row->setFont(array(
                        'bold'       =>  true
                    ));
                });
            });
        })->download('xlsx');
    }
    public function convertMilestone($id)
    {
        $this->invoice = ProjectMilestone::findOrFail($id);
        $this->lastInvoice = Invoice::orderBy('id', 'desc')->first();
        $this->invoiceSetting = InvoiceSetting::first();
        $this->projects = Project::all();
        $this->currencies = Currency::all();
        $this->taxes = Tax::all();
        $this->products = Product::all();
        return view('admin.invoices.convert_milestone', $this->data);
    }
}