<?php

namespace App\Http\Controllers\Admin;

use App\CRMLeads;
use App\Helper\Reply;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Controllers\Admin\ConnectXeroController;
use App\Http\Controllers\Admin\ConnectMyobController;
use App\Product;
use App\ProductCategories;
use App\Tax;
use App\TenantApiDetail;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use LangleyFoxall\XeroLaravel\OAuth2;
use League\OAuth2\Client\Token\AccessToken;
use LangleyFoxall\XeroLaravel\XeroApp;

class AdminProductController extends AdminBaseController
{
    /**
     * AdminProductController constructor.
     */
    protected $ConnectXeroController;
    protected $ConnectMyobController;
    public function __construct(ConnectXeroController $ConnectXeroController,ConnectMyobController $ConnectMyobController )
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.products');
        $this->pageIcon = 'icon-basket';
        $this->ConnectXeroController = $ConnectXeroController;
        $this->ConnectMyobController = $ConnectMyobController;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->totalProducts = Product::count();
        $this->tenant_api_details = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'Xero'])->first();            
        if($this->tenant_api_details){                
            $token = (array)json_decode($this->tenant_api_details->variable1);   
            $this->ConnectXeroController->refreshAccessTokenIfNecessary($token);
        }
        return view('admin.products.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //$this->taxes = Tax::all();
        $this->xero_connected = false;
        $this->myob_connected = false;
        $this->tenant_api_details = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'Xero'])->first();            
            if($this->tenant_api_details){                
                $token = (array)json_decode($this->tenant_api_details->variable1);                   
                $this->xero = new XeroApp(
                    new AccessToken($token),
                    $this->tenant_api_details->smtp_user
                );
                $this->accounts = $this->xero->accounts()->where('Class','REVENUE')->get();
                $this->xero_connected = true;
            }else{
                $this->myob_tenant_api_details = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'MYOB'])->first(); 
                if($this->myob_tenant_api_details){  
                    $old_token = (array)json_decode($this->myob_tenant_api_details->variable1);
                    $this->ConnectMyobController->refreshAccessToken($old_token['refresh_token']);

                    $myob_api = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'MYOB'])->first();            
                    $access_token_arr = (array)json_decode($myob_api->variable1);
                    $this->myob_accounts = $this->ConnectMyobController->getMyobAccounts($this->myob_tenant_api_details->url ,$access_token_arr['access_token']);
                    $this->myob_connected = true;
                }
            }
        $this->customer_types = ['Both','Residential','Commercial'];  
        $this->product_types = ['Item','Service','Charge'];
        $this->taxes = Tax::where(['tenant_id'=> auth()->user()->tenant_id])->get();
        $this->product_categories = ProductCategories::where(['tenant_id'=> auth()->user()->tenant_id])->get();
        $this->customers = CRMLeads::where('tenant_id', auth()->user()->tenant_id)->where('lead_type', 'Commercial')->get();

        return view('admin.products.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        $product = new Product();
        $product->name = $request->name;
        $product->tenant_id = auth()->user()->tenant_id;
        $product->price = $request->price;
        $product->tax_id = $request->tax_id;
        $product->hourly_pricing_min_hours = $request->hourly_pricing_min_hours;
        $product->product_type = $request->product_type;
        $product->category_id = $request->category_id;
        $product->xero_account_id = $request->xero_account_id;
        
        $product->description = isset($request->description) ? $request->description : '';
        $product->customer_type = $request->customer_type;
        $product->customer_id = isset($request->customer_id) && ($request->customer_id != 0) ? $request->customer_id : NULL;
        $product->stockable = isset($request->stockable) ? 'Y' : 'N';
        $product->save();

        return Reply::redirect(route('admin.products.index'), __('messages.productAdded'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->xero_connected = false;
        $this->myob_connected = false;
        $this->tenant_api_details = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'Xero'])->first();            
            if($this->tenant_api_details){                
                $token = (array)json_decode($this->tenant_api_details->variable1);                   
                $this->xero = new XeroApp(
                    new AccessToken($token),
                    $this->tenant_api_details->smtp_user
                );
                $this->accounts = $this->xero->accounts()->where('Class','REVENUE')->get();
                $this->xero_connected = true;
            }else{
                $this->myob_tenant_api_details = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'MYOB'])->first(); 
                if($this->myob_tenant_api_details){  
                    $old_token = (array)json_decode($this->myob_tenant_api_details->variable1);
                    $this->ConnectMyobController->refreshAccessToken($old_token['refresh_token']);

                    $myob_api = TenantApiDetail::where(['tenant_id' => auth()->user()->tenant_id, 'provider' => 'MYOB'])->first();            
                    $access_token_arr = (array)json_decode($myob_api->variable1);
                    $this->myob_accounts = $this->ConnectMyobController->getMyobAccounts($this->myob_tenant_api_details->url ,$access_token_arr['access_token']);
                    $this->myob_connected = true;
                }
            }
        $this->customer_types = ['Both','Residential','Commercial'];      
        $this->product_types = ['Item','Service','Charge'];    
        $this->product = Product::find($id);
        $this->taxes = Tax::where(['tenant_id'=> auth()->user()->tenant_id])->get();
        //$this->taxes = Tax::all();
        $this->product_categories = ProductCategories::where(['tenant_id'=> auth()->user()->tenant_id])->get();
        $this->customers = CRMLeads::where('tenant_id', auth()->user()->tenant_id)->where('lead_type', 'Commercial')->get();

        return view('admin.products.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->name = $request->name;
        $product->tenant_id = auth()->user()->tenant_id;
        $product->price = $request->price;
        $product->tax_id = $request->tax_id;
        $product->hourly_pricing_min_hours = $request->hourly_pricing_min_hours;
        $product->product_type = $request->product_type;
        $product->category_id = $request->category_id;
        $product->xero_account_id = $request->xero_account_id;
        
        $product->description = isset($request->description) ? $request->description : '';
        $product->customer_type = $request->customer_type;
        $product->customer_id = isset($request->customer_id) && ($request->customer_id != 0)  ? $request->customer_id : NULL;
        $product->stockable = ($request->product_type == 'Item') ? ($request->stockable) ? 'Y' : 'N' : 'N';
        $product->update();

        return Reply::redirect(route('admin.products.index'), __('messages.productUpdated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Product::destroy($id);
        return Reply::success(__('messages.productDeleted'));
    }

    /**
     * @return mixed
     */
    public function data()
    {
        $products = Product::with('tax')
            ->select(
                'products.id',
                'products.name',
                'products.price',
                'products.tax_id',
                'products.product_type',
                'products.category_id',
                'products.customer_type',
                'products.stockable',
                'product_categories.category_name'
                )
            ->where(['products.tenant_id'=> auth()->user()->tenant_id])
            ->leftjoin('product_categories', 'product_categories.id', 'products.category_id')
            ->get();
        
        // dd($products);

        return DataTables::of($products)
            ->addColumn('action', function($row){
                // return '<a href="'.route('admin.products.edit', [$row->id]).'" class="btn btn-info btn-circle"
                //       data-toggle="tooltip" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>

                //       <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                //       data-toggle="tooltip" data-user-id="'.$row->id.'" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a>';
                return '<div class="list-icons float-right">'.
                        '<div class="dropdown">'.
                            '<a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown"><i class="icon-menu"></i></a>'.
                                '<div class="dropdown-menu dropdown-menu-right">'.
                                    '<a href="'.route('admin.products.edit', [$row->id]).'" class="dropdown-item" data-original-title="Edit" ><i class="fa fa-pencil"></i> Edit</a>'.                                                      
                                    '<a href="javascript:;" class="dropdown-item sa-params" data-user-id="'.$row->id.'" data-original-title="Delete" ><i class="fa fa-times"></i> Delete</a>'.                                                      
                                '</div>'.
                        '</div>'.
                    '</div>';
            })
            ->editColumn('category', function ($row) {
                if($row->category_name)
                {
                    return $row->category_name;
                }
                else
                {
                    return 'Null';
                }
            })
            ->editColumn('productType', function ($row) {
                if($row->product_type == 'Item')
                {
                    return 'Item - Fixed';
                }
                else if($row->product_type == 'Service')
                {
                    return 'Service - Hourly';
                }
                else
                {
                    return $row->product_type;
                }
            })
            ->editColumn('name', function ($row) {
                    return ucfirst($row->name);
            })
            ->editColumn('price', function ($row) {
                if (!is_null($row->tax_id)) {
                    return $this->global->currency->currency_symbol.($row->price + ($row->price * ($row->tax->rate_percent/100)));
                } else {
                    return $this->global->currency->currency_symbol.$row->price;
                }
            })
            ->editColumn('customerType', function ($row) {
                return $row->customer_type;
            })
            ->editColumn('stockable', function ($row) {
                return $row->stockable;
            })
            ->rawColumns(['action', 'price'])
            ->make(true);
    }

    public function export()
    {
        $attributes =  ['tax', 'tax_id', 'price'];
        $products = Product::with('tax')
            ->select('id', 'name', 'price', 'tax_id')
            ->where(['tenant_id'=> auth()->user()->tenant_id])
            ->get()->makeHidden($attributes);;

            // Initialize the array which will be passed into the Excel
        // generator.
        $exportArray = [];

        // Define the Excel spreadsheet headers
        $exportArray[] = ['ID', 'Name', 'Price'];

        // Convert each member of the returned collection into an array,
        // and append it to the payments array.
        foreach ($products as $row) {
            $exportArray[] = $row->toArray();
        }

        // Generate and return the spreadsheet
        Excel::create('Product', function($excel) use ($exportArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Product');
            $excel->setCreator('Worksuite')->setCompany($this->companyName);
            $excel->setDescription('Product file');

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
