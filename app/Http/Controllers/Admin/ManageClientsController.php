<?php

namespace App\Http\Controllers\Admin;

use App\ClientDetails;
use App\Customers;
use App\Helper\Reply;
use App\Http\Requests\Admin\Client\StoreClientRequest;
use App\Http\Requests\Admin\Client\UpdateClientRequest;
use App\Invoice;
use App\JobsMoving;
use App\Lead;
use App\Notifications\NewUser;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ManageClientsController extends AdminBaseController
{

    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.clients');
        $this->pageIcon = 'icon-people';
        $this->middleware(function ($request, $next) {
            if (!in_array('clients', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->clients = Customers::where('tenant_id', '=', auth()->user()->tenant_id)->get();
        $this->totalClients = count($this->clients);

        return view('admin.clients.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($leadID = null)
    {
        if ($leadID) {
            $this->leadDetail = Lead::findOrFail($leadID);
        }

        $client = new ClientDetails();
        $this->fields = $client->getCustomFieldGroupsWithFields()->fields;
        return view('admin.clients.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClientRequest $request)
    {
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->mobile = $request->input('mobile');
        $user->login = $request->input('login');
        $user->save();

        if ($user->id) {
            $client = new ClientDetails();
            $client->user_id = $user->id;
            $client->company_name = $request->company_name;
            $client->address = $request->address;
            $client->website = $request->website;
            $client->note = $request->note;
            $client->skype = $request->skype;
            $client->facebook = $request->facebook;
            $client->twitter = $request->twitter;
            $client->linkedin = $request->linkedin;
            $client->gst_number = $request->gst_number;
            $client->save();
        }

        // To add custom fields data
        if ($request->get('custom_fields_data')) {
            $client->updateCustomFieldData($request->get('custom_fields_data'));
        }

        $user->attachRole(3);


        if ($this->emailSetting[0]->send_email == 'yes') {
            //send welcome email notification
            $user->notify(new NewUser($request->input('password')));
        }

        //log search
        $this->logSearchEntry($user->id, $user->name, 'admin.clients.edit');
        $this->logSearchEntry($user->id, $user->email, 'admin.clients.edit');
        if (!is_null($client->company_name)) {
            $this->logSearchEntry($user->id, $client->company_name, 'admin.clients.edit');
        }

        if ($request->has('lead')) {
            $lead = Lead::findOrFail($request->lead);
            $lead->client_id = $user->id;
            $lead->save();

            return Reply::redirect(route('admin.leads.index'), __('messages.leadClientChangeSuccess'));
        }

        return Reply::redirect(route('admin.clients.index'));
    }

    public function storeCustomer(Request $request)
    {
        $customer = new Customers();
        $customer->tenant_id = auth()->user()->tenant_id;
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->alt_phone = $request->alt_phone;
        $customer->mobile = $request->mobile;
        $customer->notes = $request->note;
        $customer->save();

        return Reply::redirect(route('admin.clients.index'));
    }

    public function updateCustomer(Request $request, $id)
    {
        $customer = Customers::find($id);
        if ($customer) {
            $customer->tenant_id = auth()->user()->tenant_id;
            $customer->first_name = $request->first_name;
            $customer->last_name = $request->last_name;
            $customer->email = $request->email;
            $customer->phone = $request->phone;
            $customer->alt_phone = $request->alt_phone;
            $customer->mobile = $request->mobile;
            $customer->notes = $request->note;
            $customer->save();
        }

        return Reply::redirect(route('admin.clients.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->client = User::withoutGlobalScope('active')->findOrFail($id);
        $this->customer = Customers::find($id);
        return view('admin.clients.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->userDetail = User::withoutGlobalScope('active')->findOrFail($id);
        $this->clientDetail = ClientDetails::where('user_id', '=', $this->userDetail->id)->first();
        $this->customerDetail = Customers::find($id);

        if (!is_null($this->clientDetail)) {
            $this->clientDetail = $this->clientDetail->withCustomFields();
            $this->fields = $this->clientDetail->getCustomFieldGroupsWithFields()->fields;
        }

        return view('admin.clients.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClientRequest $request, $id)
    {
        $user = User::withoutGlobalScope('active')->findOrFail($id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        if ($request->password != '') {
            $user->password = Hash::make($request->input('password'));
        }
        $user->mobile = $request->input('mobile');
        $user->status = $request->input('status');
        $user->login = $request->login;
        $user->save();

        $client = ClientDetails::where('user_id', '=', $user->id)->first();
        if (empty($client)) {
            $client = new ClientDetails();
            $client->user_id = $user->id;
        }
        $client->company_name = $request->company_name;
        $client->address      = $request->address;
        $client->website      = $request->website;
        $client->note         = $request->note;
        $client->skype        = $request->skype;
        $client->facebook     = $request->facebook;
        $client->twitter      = $request->twitter;
        $client->linkedin     = $request->linkedin;
        $client->gst_number   = $request->gst_number;
        $client->save();

        // To add custom fields data
        if ($request->get('custom_fields_data')) {
            $client->updateCustomFieldData($request->get('custom_fields_data'));
        }
        return Reply::redirect(route('admin.clients.index'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Customers::destroy($id);
        return Reply::success(__('messages.customerDeleted'));
    }

    public function data(Request $request)
    {

        $users = User::withoutGlobalScope('active')->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->leftJoin('client_details', 'users.id', '=', 'client_details.user_id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'client_details.company_name', 'users.email', 'users.created_at', 'users.status')
            ->where('roles.name', 'client');

        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate)->toDateString();
            $users = $users->where(DB::raw('DATE(users.`created_at`)'), '>=', $startDate);
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->toDateString();
            $users = $users->where(DB::raw('DATE(users.`created_at`)'), '<=', $endDate);
        }

        if ($request->status != 'all' && $request->status != '') {
            $users = $users->where('users.status', $request->status);
        }

        if ($request->client != 'all' && $request->client != '') {
            $users = $users->where('users.id', $request->client);
        }

            $users = $users->get();

        $customers = Customers::where('tenant_id', '=', auth()->user()->tenant_id)->get();

        return DataTables::of($customers)
            ->addColumn('action', function($row){
                return '<a href="'.route('admin.clients.edit', [$row->id]).'" class="btn btn-info btn-circle"
                      data-toggle="tooltip" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>

                      <a href="' . route('admin.clients.projects', [$row->id]) . '" class="btn btn-success btn-circle"
                      data-toggle="tooltip" data-original-title="View Customer Details"><i class="fa fa-search" aria-hidden="true"></i></a>

                      <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-user-id="'.$row->id.'" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a>';
            })
            ->editColumn(
                'first_name',
                function ($row) {
                    return ucfirst($row->first_name);
                }
            )
            ->editColumn(
                'last_name',
                function ($row) {
                    return ucfirst($row->last_name);
                }
            )
            ->editColumn(
                'email',
                function ($row) {
                    return $row->email;
                }
            )
            ->editColumn(
                'phone',
                function ($row) {
                    return $row->phone;
                }
            )
            ->editColumn(
                'mobile',
                function ($row) {
                    return $row->mobile;
                }
            )
            ->make(true);
    }

    public function showProjects($id) {
        $this->client = User::withoutGlobalScope('active')->findOrFail($id);
        $this->clientDetail = ClientDetails::where('user_id', '=', $this->client->id)->first();

        if(!is_null($this->clientDetail)){
            $this->clientDetail = $this->clientDetail->withCustomFields();
            $this->fields = $this->clientDetail->getCustomFieldGroupsWithFields()->fields;
        }

        $this->customer = Customers::findOrFail($id);
        $this->jobs = JobsMoving::where('customer_id', $id)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->get();


        return view('admin.clients.projects', $this->data);
    }

    public function showInvoices($id) {
        $this->client = User::withoutGlobalScope('active')->findOrFail($id);
        $this->clientDetail = ClientDetails::where('user_id', '=', $this->client->id)->first();

        if(!is_null($this->clientDetail)){
            $this->clientDetail = $this->clientDetail->withCustomFields();
            $this->fields = $this->clientDetail->getCustomFieldGroupsWithFields()->fields;
        }

        $this->invoices = Invoice::join('projects', 'projects.id', '=', 'invoices.project_id')
            ->join('currencies', 'currencies.id', '=', 'invoices.currency_id')
            ->join('users', 'users.id', '=', 'projects.client_id')
            ->select('invoices.invoice_number', 'invoices.total', 'currencies.currency_symbol', 'invoices.issue_date', 'invoices.id')
            ->where('projects.client_id', $id)
            ->get();

        $this->customer = Customers::findOrFail($id);

        return view('admin.clients.invoices', $this->data);
    }

    public function export($status, $client) {
//        $rows = User::join('role_user', 'role_user.user_id', '=', 'users.id')
//            ->withoutGlobalScope('active')
//            ->join('roles', 'roles.id', '=', 'role_user.role_id')
//            ->where('roles.name', 'client')
//            ->leftJoin('client_details', 'users.id', '=', 'client_details.user_id')
//            ->select(
//                'users.id',
//                'users.name',
//                'users.email',
//                'users.mobile',
//                'client_details.company_name',
//                'client_details.address',
//                'client_details.website',
//                'users.created_at'
//            );
//
//            if($status != 'all' && $status != ''){
//                $rows = $rows->where('users.status', $status);
//            }
//
//            if($client != 'all' && $client != ''){
//                $rows = $rows->where('users.id', $client);
//            }
//
//            $rows = $rows->get();

        $rows = Customers::select(
            'id',
            'first_name',
            'last_name',
            'email',
            'mobile',
            'phone',
            'alt_phone',
            'notes'
        )->get();

        // Initialize the array which will be passed into the Excel
        // generator.
        $exportArray = [];

        // Define the Excel spreadsheet headers
        $exportArray[] = ['ID', 'First Name', 'Last Name','Email','Mobile','Phone', 'Alternate Phone', 'Notes'];

        // Convert each member of the returned collection into an array,
        // and append it to the payments array.
        foreach ($rows as $row) {
            $exportArray[] = $row->toArray();
        }

        // Generate and return the spreadsheet
        Excel::create('customers', function($excel) use ($exportArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Customers');
//            $excel->setCreator('Worksuite')->setCompany($this->email);
            $excel->setDescription('customers file');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($exportArray) {
                $sheet->fromArray($exportArray, null, 'A1', false, false);

                $sheet->row(1, function($row) {

                    // call row manipulation methods
                    $row->setFont(array(
                        'bold'       =>  true
                    ));

                });

            });



        })->download('xlsx');
    }

}
