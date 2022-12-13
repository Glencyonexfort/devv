<?php

namespace App\Http\Controllers;

use App\Invoice;
use Illuminate\Support\Facades\Auth;
use App\InvoiceItems;
use App\InvoiceSetting;
use App\OfflinePaymentMethod;
use App\PaymentGatewayCredentials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helper\Reply;
use App\Setting;

class HomeController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function checkAuthLogin()
    {
        if (Auth::check()) {
            return json_encode([
                'status'=>200,
                'message' => 'Authorized user'
            ]); 
        }else{
            return json_encode([
                'status'=>401,
                'message' => 'Unauthorized user'
            ]);
        }
    }

    public function login()
    {
        // Test database connection
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            return redirect('install');
        }

        return redirect(route('login'));
    }

    public function updateDatabase()
    {
        \Illuminate\Support\Facades\Artisan::call('migrate', array('--force' => true));

        return 'Database updated successfully. <a href="' . route('login') . '">Click here to Login</a>';
    }

    public function invoice($id)
    {
        $this->pageTitle = __('app.menu.clients');
        $this->pageIcon = 'icon-people';

        $this->invoice = Invoice::where(['tenant_id' => auth()->user()->tenant_id])->whereRaw('md5(id) = ?', $id)->firstOrFail();
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

        $items = InvoiceItems::whereNotNull('tax_id')
                ->where(['invoice_id' => $this->invoice->id, 'tenant_id' => auth()->user()->tenant_id])
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
        $this->credentials = PaymentGatewayCredentials::first();
        $this->methods = OfflinePaymentMethod::activeMethod();
        $this->invoiceSetting = InvoiceSetting::where(['tenant_id' => auth()->user()->tenant_id])->first();

        return view('invoice', [
            'companyName' => $this->settings->company_name,
            'pageTitle' => $this->pageTitle,
            'pageIcon' => $this->pageIcon,
            'global' => $this->settings,
            'setting' => $this->settings,
            'settings' => $this->settings,
            'invoice' => $this->invoice,
            'paidAmount' => $this->paidAmount,
            'discount' => $this->discount,
            'credentials' => $this->credentials,
            'taxes' => $this->taxes,
            'methods' => $this->methods,
            'invoiceSetting' => $this->invoiceSetting,
        ]);
    }

    public function processInboundEmailDelivery()
    {
        $insertdata['log_details'] = 'Delivered';
        $insertdata['date'] = date('Y-m-d H:i:s');
    }

    public function processInboundEmailOpened()
    {
        try {
            $response = json_decode(file_get_contents('php://input'), true);
            if ($response) {
                $job_id = $response['Tag'];
                $jobLogId = \App\JobsMovingLogs::where(['job_id' => $job_id, 'tenant_id' => auth()->user()->tenant_id])
                        ->where('log_type_id', 5)
                        ->first();
                if ($jobLogId) {
                    $time = date('Y-m-d H:i:s');
                    $jobLogId->log_details = 'Email opened at ' . $time;
                    $jobLogId->log_type_id = 4;
                    $jobLogId->save();
                    $msg = 'done';
                } else {
                    $msg = 'No Log ID found.';
                }
            } else {
                $msg = 'No JSON posted.';
            }
            echo json_encode($msg);
            exit;
        } catch (\Exception $ex) {
            echo json_encode($ex->getMessage());
            exit;
        }
    }

    public function processInboundEmailReceived()
    {
        try {
            $response = json_decode(file_get_contents('php://input'), true);
            if ($response) {
                $job_id = session('job_id');
                $jobLogId = \App\JobsMovingLogs::where(['job_id' => $job_id, 'tenant_id' => auth()->user()->tenant_id])
                        ->where('log_type_id', 3)
                        ->first();
                if ($jobLogId) {
                    $time = date('Y-m-d H:i:s');
                    $jobLogId->log_details = 'Email recieved at ' . $time;
                    $jobLogId->log_type_id = 5;
                    $jobLogId->save();
                    $msg = 'done';
                } else {
                    $msg = 'No Log ID found.';
                }
            } else {
                $msg = 'No JSON posted.';
            }
            echo json_encode($msg);
            exit;
        } catch (\Exception $ex) {
            echo json_encode($ex->getMessage());
            exit;
        }
    }

}

// Todo::remove this controller
