<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SysCountryStates;
use App\OrganisationSettings;
use App\Helper\Reply;
use App\ProductCategories;

class ManageProductCategoriesController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Product Categories';
        $this->pageIcon = 'icon-list';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ProductCategories()
    {
        $this->organisation_settings = OrganisationSettings::where('tenant_id', '=', auth()->user()->tenant_id)->first();
        $this->productCategories = ProductCategories::where(['product_categories.tenant_id' => auth()->user()->tenant_id])
            ->orderBy('product_categories.category_name', 'asc')->get();

        return view('admin.product-categories.index', $this->data);
    }

    public function ajaxCreateProductCategories(Request $request)
    {
        $model = new ProductCategories();
        $model->tenant_id = auth()->user()->tenant_id;
        $model->category_name = $request->input('category_name');
        $model->created_by = auth()->user()->id;
        $model->created_at = time();
        if ($model->save()) {
            $this->productCategories = ProductCategories::where(['product_categories.tenant_id' => auth()->user()->tenant_id])
            ->orderBy('product_categories.category_name', 'asc');

            $response['error'] = 0;
            $response['message'] = 'Product Category has been added';
            $response['response_html'] = view('admin.product-categories.product_categories_grid')->with(['productCategories' => $this->productCategories])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxUpdateProductCategories(Request $request)
    {
        $product_categories_id = $request->input('product_categories_id');
        $model = ProductCategories::find($product_categories_id);
        $model->category_name = $request->input('category_name');
        $model->updated_by = auth()->user()->id;
        $model->updated_at = time();
        if ($model->save()) {
            $this->productCategories = ProductCategories::where(['product_categories.tenant_id' => auth()->user()->tenant_id])
                ->orderBy('product_categories.category_name', 'asc');

            $response['error'] = 0;
            $response['id'] = $product_categories_id;
            $response['message'] = 'Product Category has been updated';
            $response['response_html'] = view('admin.product-categories.product_categories_grid')->with(['productCategories' => $this->productCategories])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxDestroyProductCategories(Request $request)
    {
        ProductCategories::destroy($request->id);
        $this->productCategories = ProductCategories::where(['product_categories.tenant_id' => auth()->user()->tenant_id])
            ->orderBy('product_categories.category_name', 'asc');
        $response['error'] = 0;
        $response['message'] = 'Product Category has been deleted';
        $response['response_html'] = view('admin.product-categories.product_categories_grid')->with(['productCategories' => $this->productCategories])->render();
        return json_encode($response);
    }
}
