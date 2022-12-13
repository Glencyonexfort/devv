<?php
namespace App\Http\Controllers\Admin;
use App\Currency;
use App\Helper\Reply;
use App\Http\Requests\Payments\ImportPayment;
use App\Http\Requests\Payments\StorePayment;
use App\Http\Requests\Payments\UpdatePayments;
use App\Invoice;
use App\Lists;
use App\Payment;
use App\Project;
use App\Setting;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
class ManagePaymentsController extends AdminBaseController {
    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.payments');
        $this->pageIcon = 'fa fa-money';
        $this->middleware(function ($request, $next) {
            if (!in_array('payments', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }
    public function index() {
        $this->projects = Project::all();
        $this->clients = User::allClients();
        return view('admin.payments.index', $this->data);
    }
    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function data(Request $request) {
        $payments = Payment::leftJoin('projects', 'projects.id', '=', 'payments.project_id')
            ->join('currencies', 'currencies.id', '=', 'payments.currency_id')
            ->select('payments.id', 'payments.project_id', 'projects.project_name', 'payments.amount', 'currencies.currency_symbol', 'currencies.currency_code', 'payments.status', 'payments.paid_on', 'payments.remarks')
            ->where('payments.tenant_id', '=', auth()->user()->tenant_id);
        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate)->toDateString();
            $payments = $payments->where(DB::raw('DATE(payments.`paid_on`)'), '>=', $startDate);
        }
        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->toDateString();
            $payments = $payments->where(DB::raw('DATE(payments.`paid_on`)'), '<=', $endDate);
        }
        if ($request->status != 'all' && !is_null($request->status)) {
            $payments = $payments->where('payments.status', '=', $request->status);
        }
        if ($request->project != 'all' && !is_null($request->project)) {
            $payments = $payments->where('payments.project_id', '=', $request->project);
        }
        if ($request->client != 'all' && !is_null($request->client)) {
            $payments = $payments->where('projects.client_id', '=', $request->client);
        }
        $payments = $payments->orderBy('payments.id', 'desc')->get();
        return DataTables::of($payments)
            ->addColumn('action', function ($row) {
                return '<a href="' . route("admin.payments.edit", $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-info btn-circle"><i class="fa fa-pencil"></i></a>
                        &nbsp;&nbsp;<a href="javascript:;" data-toggle="tooltip" data-original-title="Delete" data-payment-id="' . $row->id . '" class="btn btn-danger btn-circle sa-params"><i class="fa fa-times"></i></a>';
            })
            ->editColumn('remarks', function($row) {
                return ucfirst($row->remarks);
            })
            ->editColumn('project_id', function($row) {
                if ($row->project_id != null) {
                    return '<a href="' . route('admin.projects.show', $row->project_id) . '">' . ucfirst($row->project_name) . '</a>';
                } else {
                    return '--';
                }
            })
            ->editColumn('status', function ($row) {
                if ($row->status == 'pending') {
                    return '<label class="label label-warning">' . strtoupper($row->status) . '</label>';
                } else {
                    return '<label class="label label-success">' . strtoupper($row->status) . '</label>';
                }
            })
            ->editColumn('amount', function ($row) {
                return $row->currency_symbol . number_format((float) $row->amount, 2, '.', '') . ' (' . $row->currency_code . ')';
            })
            ->editColumn(
                'paid_on', function ($row) {
                if (!is_null($row->paid_on)) {
                    return $row->paid_on->format($this->global->date_format . ' ' . $this->global->time_format);
                }
            }
            )
            ->rawColumns(['action', 'status', 'project_id'])
            ->removeColumn('invoice_id')
            ->removeColumn('currency_symbol')
            ->removeColumn('currency_code')
            ->removeColumn('project_name')
            ->make(true);
    }
    public function create() {
        $this->lists = Lists::where('list_type', 'Sys Job Type')
            ->where('tenant_id', '=', auth()->user()->tenant_id)
            ->get();
        $job_numbers = Invoice::leftJoin('jobs_moving', 'jobs_moving.job_id', '=', 'invoices.job_id')
            ->select('jobs_moving.job_id', 'jobs_moving.job_number')
            ->where('invoices.status', '<>', 'paid')
            ->where('invoices.sys_job_type', '=', 'Moving')
            ->get();
        $this->job_numbers = $job_numbers;
        $this->projects = Project::all();
        $this->currencies = Currency::all();
        return view('admin.payments.create', $this->data);
    }
    public function store(StorePayment $request) {
        $payment = new Payment();
        $settings = Setting::where('tenant_id', '=', auth()->user()->tenant_id)->first();
        if($request->has('invoice_id')){
            $invoice = Invoice::find($request->invoice_id);
            if($invoice) {
                $payment->invoice_id = $invoice->id;
                $invoice->job_id = $request->job_number;
                $invoice->sys_job_type = $request->system_job_type;
                $paidAmount = $invoice->getPaidAmount();
                if (($paidAmount + $request->amount) >= $invoice->total) {
                    $invoice->status = 'paid';
                } else {
                    $invoice->status = 'partial';
                }
                $invoice->save();
            }
        }
        if($settings) {
            $payment->currency_id = $settings->currency_id;
        }
        $payment->amount = round($request->amount, 2);
        $payment->gateway = $request->gateway;
        $payment->transaction_id = $request->transaction_id;
        $payment->paid_on = Carbon::createFromFormat('d/m/Y H:i', $request->paid_on)->format('Y-m-d H:i:s');
        $payment->tenant_id = auth()->user()->tenant_id;
        $payment->remarks = $request->remarks;
        $payment->save();
        return Reply::redirect(route('admin.payments.index'), __('messages.paymentSuccess'));
    }
    public function destroy($id) {
        Payment::destroy($id);
        return Reply::success(__('messages.paymentDeleted'));
    }
    public function edit($id) {
        $this->lists = Lists::where('list_type', 'Sys Job Type')
            ->where('tenant_id', '=', auth()->user()->tenant_id)
            ->get();
        $job_numbers = Invoice::leftJoin('jobs_moving', 'jobs_moving.job_id', '=', 'invoices.job_id')
            ->select('jobs_moving.job_id', 'jobs_moving.job_number')
            ->where('invoices.status', '<>', 'paid')
            ->where('invoices.sys_job_type', '=', 'Moving')
            ->get();
        $this->job_numbers = $job_numbers;
        $this->currencies = Currency::all();
        $payment = Payment::findOrFail($id);
        $this->payment = $payment;
        $this->invoice = Invoice::findOrFail($payment->invoice_id);
        return view('admin.payments.edit', $this->data);
    }
    public function update(UpdatePayments $request, $id) {
        $payment = Payment::findOrFail($id);
        $invoice = Invoice::find($payment->invoice_id);
        if($invoice) {
            $invoice->job_id = $request->job_number;
            $invoice->sys_job_type = $request->system_job_type;
            $paidAmount = $invoice->getPaidAmount();
            if (($paidAmount + $request->amount) >= $invoice->total) {
                $invoice->status = 'paid';
            } else {
                $invoice->status = 'partial';
            }
            $invoice->save();
        }
        $settings = Setting::where('tenant_id', '=', auth()->user()->tenant_id)->first();
        if($settings) {
            $payment->currency_id = $settings->currency_id;
        }
        $payment->amount = round($request->amount, 2);
        $payment->gateway = $request->gateway;
        $payment->transaction_id = $request->transaction_id;
        $payment->paid_on = Carbon::createFromFormat('d/m/Y H:i', $request->paid_on)->format('Y-m-d H:i:s');
        $payment->status = 'complete';
        $payment->remarks = $request->remarks;
        $payment->save();
        return Reply::redirect(route('admin.payments.index'), __('messages.paymentSuccess'));
    }
    public function payInvoice($invoiceId) {
        $this->invoice = Invoice::findOrFail($invoiceId);
        $this->paidAmount = $this->invoice->getPaidAmount();
        if ($this->invoice->status == 'paid') {
            return "Invoice already paid";
        }
        return view('admin.payments.pay-invoice', $this->data);
    }
    public function importExcel(ImportPayment $request) {
        if ($request->hasFile('import_file')) {
            $path = $request->file('import_file')->getRealPath();
            $data = Excel::load($path)->get();
            if ($data->count()) {
                foreach ($data as $key => $value) {
                    if ($request->currency_character) {
                        $amount = substr($value->amount, 1);
                    } else {
                        $amount = substr($value->amount, 0);
                    }
                    $amount = str_replace(',', '', $amount);
                    $amount = str_replace(' ', '', $amount);
                    $arr[] = [
                        'paid_on' => Carbon::createFromFormat($this->global->date_format, $value->date)->format('Y-m-d'),
                        'amount' => $amount,
                        'currency_id' => $this->global->currency_id,
                        'tenant_id' => auth()->user()->tenant_id,
                        'status' => 'complete'
                    ];
                }
                if (!empty($arr)) {
                    DB::table('payments')->insert($arr);
                }
            }
        }
        return Reply::redirect(route('admin.payments.index'), __('messages.importSuccess'));
    }
    public function downloadSample() {
        return response()->download(public_path() . '/payment-sample.csv');
    }
    /**
     * @param $startDate
     * @param $endDate
     * @param $status
     */
    public function export($startDate, $endDate, $status, $project) {
        $payments = Payment::leftJoin('projects', 'projects.id', '=', 'payments.project_id')
            ->join('currencies', 'currencies.id', '=', 'payments.currency_id')
            ->select('payments.id', 'projects.project_name', 'payments.amount', 'currencies.currency_symbol', 'currencies.currency_code', 'payments.status', 'payments.paid_on', 'payments.remarks')
            ->where('payments.tenant_id', '=', auth()->user()->tenant_id);
        if ($startDate !== null && $startDate != 'null' && $startDate != '') {
            $payments = $payments->where(DB::raw('DATE(payments.`paid_on`)'), '>=', $startDate);
        }
        if ($endDate !== null && $endDate != 'null' && $endDate != '') {
            $payments = $payments->where(DB::raw('DATE(payments.`paid_on`)'), '<=', $endDate);
        }
        if ($status != 'all' && !is_null($status)) {
            $payments = $payments->where('payments.status', '=', $status);
        }
        if ($project != 'all' && !is_null($project)) {
            $payments = $payments->where('payments.project_id', '=', $project);
        }
        $attributes = ['amount', 'currency_symbol', 'paid_on'];
        $payments = $payments->orderBy('payments.id', 'desc')->get()->makeHidden($attributes);
        // Initialize the array which will be passed into the Excel
        // generator.
        $exportArray = [];
        // Define the Excel spreadsheet headers
        $exportArray[] = ['ID', 'Project', 'Currency Code', 'Status', 'Remark', 'Amount', 'Paid On'];
        // Convert each member of the returned collection into an array,
        // and append it to the payments array.
        foreach ($payments as $row) {
            $exportArray[] = $row->toArray();
        }
        // Generate and return the spreadsheet
        Excel::create('payment', function($excel) use ($exportArray) {
            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Payment');
            $excel->setCreator('Worksuite')->setCompany($this->companyName);
            $excel->setDescription('payment file');
            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($exportArray) {
                $sheet->fromArray($exportArray, null, 'A1', false, false);
                $sheet->row(1, function($row) {
                    // call row manipulation methods
                    $row->setFont(array(
                        'bold' => true
                    ));
                });
            });
        })->download('xlsx');
    }
}