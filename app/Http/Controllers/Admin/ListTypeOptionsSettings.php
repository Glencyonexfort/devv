<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ListTypes;
use App\ListOptions;
use App\Helper\Reply;


class ListTypeOptionsSettings extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.listTypesAndOptions');
        $this->pageIcon = 'icon-list3';        
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->listTypes = ListTypes::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('list_name', 'asc')->get();

        /*$listOptions = ListOptions::select(
                        'list_options.id','list_options.list_option','list_options.option_value', 'list_types.list_name')
                        ->leftjoin('list_types', 'list_types.id', '=', 'list_options.list_type_id')
                        ->where(['list_options.tenant_id'=> auth()->user()->tenant_id])
                        ->orderBy('list_types.list_name', 'asc')
                        ->orderBy('list_options.list_option', 'asc')
                        ->get();*/
        //dd($listOptions);
        /*$this->ListOptions = ListOptions::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('list_option', 'asc')->get();*/

        return view('admin.list-type-and-options.index', $this->data);
    }


    public function ajaxLoadListOptions(Request $request)
    {
        
        $list_type_val = $request->input('list_type_val');

        if ($list_type_val) {

            $listOptions = ListOptions::select(
                        'list_options.id','list_options.list_option','list_options.option_value', 'list_types.list_name','list_options.list_type_id')
                        ->leftjoin('list_types', 'list_types.id', '=', 'list_options.list_type_id')
                        ->where(['list_options.tenant_id'=> auth()->user()->tenant_id, 'list_options.list_type_id'=> $list_type_val])
                        ->orderBy('list_types.list_name', 'asc')
                        ->orderBy('list_options.list_option', 'asc')
                        ->get();

            $listTypes = ListTypes::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('list_name', 'asc')->get();
            
            $response['error'] = 0;
            $response['message'] = 'List Type has been added';
            $response['listOptions_html'] = view('admin.list-type-and-options.listOptions_grid')->with(['listOptions' => $listOptions, 'listTypes' => $listTypes])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxCreateListOption(Request $request)
    {
        $model = new ListOptions();
        $model->tenant_id = auth()->user()->tenant_id;
        $model->list_type_id = $request->input('list_type_id');
        $model->list_option = $request->input('list_option');
        $model->created_by = auth()->user()->id;
        $model->created_at = time();
        //print_r($model);exit;
        if ($model->save()) {
            $listTypes = ListTypes::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('list_name', 'asc')->get();

            $listOptions = ListOptions::select(
                        'list_options.id','list_options.list_option','list_options.option_value', 'list_types.list_name', 'list_options.list_type_id')
                        ->leftjoin('list_types', 'list_types.id', '=', 'list_options.list_type_id')
                        ->where(['list_options.tenant_id'=> auth()->user()->tenant_id, 'list_options.list_type_id'=> $request->input('list_type_id')])
                        ->orderBy('list_types.list_name', 'asc')
                        ->orderBy('list_options.list_option', 'asc')
                        ->get();

            $response['error'] = 0;
            $response['message'] = 'List Option has been added';
            $response['select_list_type'] = $request->input('list_type_id');
            $response['listOptions_html'] = view('admin.list-type-and-options.listOptions_grid')->with(['listOptions' => $listOptions, 'listTypes' => $listTypes])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxUpdateListOption(Request $request)
    {
        $update_id = $request->input('update_id');
        $model = ListOptions::find($update_id);
        $model->list_type_id = $request->input('list_type_id');
        $model->list_option  = $request->input('list_option');
        $model->updated_by   = auth()->user()->id;
        $model->updated_at   = time();
        //print_r($model);exit;
        if ($model->save()) {

            $listOptions = ListOptions::select(
                        'list_options.id','list_options.list_option','list_options.option_value', 'list_types.list_name', 'list_options.list_type_id')
                        ->leftjoin('list_types', 'list_types.id', '=', 'list_options.list_type_id')
                        ->where(['list_options.tenant_id'=> auth()->user()->tenant_id, 'list_options.list_type_id'=> $request->input('selected_list_id')])
                        ->orderBy('list_types.list_name', 'asc')
                        ->orderBy('list_options.list_option', 'asc')
                        ->get();

            $listTypes = ListTypes::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('list_name', 'asc')->get();

            $response['error'] = 0;
            $response['id'] = $request->input('list_type_id');
            $response['message'] = 'List Option has been updated';
            $response['listOptions_html'] = view('admin.list-type-and-options.listOptions_grid')->with(['listOptions' => $listOptions, 'listTypes' => $listTypes])->render();

            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }

    public function ajaxDestroyListOption(Request $request)
    {
        ListOptions::destroy($request->id);
        $listTypes = ListTypes::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('list_name', 'asc')->get();
        $listOptions = ListOptions::select(
                        'list_options.id','list_options.list_option','list_options.option_value', 'list_types.list_name', 'list_options.list_type_id')
                        ->leftjoin('list_types', 'list_types.id', '=', 'list_options.list_type_id')
                        ->where(['list_options.tenant_id'=> auth()->user()->tenant_id, 'list_options.list_type_id'=> $request->input('selected_list_id')])
                        ->orderBy('list_types.list_name', 'asc')
                        ->orderBy('list_options.list_option', 'asc')
                        ->get();
        $response['error'] = 0;
        $response['message'] = 'List Option has been deleted';
        $response['listOptions_html'] = view('admin.list-type-and-options.listOptions_grid')->with(['listOptions' => $listOptions, 'listTypes' => $listTypes])->render();
        return json_encode($response);
    }



    public function ajaxCreateListType(Request $request)
    {
        $model = new ListTypes();
        $model->tenant_id = auth()->user()->tenant_id;
        $model->list_name = $request->input('list_name');
        $model->created_by = auth()->user()->id;
        $model->created_at = time();
        //print_r($model);exit;
        if ($model->save()) {
            $listTypes = ListTypes::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('list_name', 'asc')->get();

            $response['error'] = 0;
            $response['message'] = 'List Type has been added';
            $response['listType_html'] = view('admin.list-type-and-options.listtype_grid')->with(['listTypes' => $listTypes])->render();
             $response['chooseListType_html'] = view('admin.list-type-and-options.chooseListType_grid')->with(['listTypes' => $listTypes])->render();
            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }


    public function ajaxUpdateListType(Request $request)
    {
        $list_type_id = $request->input('list_type_id');
        $model = ListTypes::find($list_type_id);
        $model->list_name = $request->input('list_name');
        $model->updated_by = auth()->user()->id;
        $model->updated_at = time();
        //print_r($model);exit;
        if ($model->save()) {
            $listTypes = ListTypes::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('list_name', 'asc')->get();

            $response['error'] = 0;
            $response['id'] = $list_type_id;
            $response['message'] = 'List Type has been updated';
            $response['listType_html'] = view('admin.list-type-and-options.listtype_grid')->with(['listTypes' => $listTypes])->render();

            $response['chooseListType_html'] = view('admin.list-type-and-options.chooseListType_grid')->with(['listTypes' => $listTypes])->render();

            return json_encode($response);
        } else {
            $response['error'] = 1;
        }
    }


    public function ajaxDestroyListType(Request $request)
    {
        ListTypes::destroy($request->id);
        $listTypes = ListTypes::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('list_name', 'asc')->get();
        $response['error'] = 0;
        $response['message'] = 'List Type has been deleted';
        $response['listType_html'] = view('admin.list-type-and-options.listtype_grid')->with(['listTypes' => $listTypes])->render();
        $response['chooseListType_html'] = view('admin.list-type-and-options.chooseListType_grid')->with(['listTypes' => $listTypes])->render();
        return json_encode($response);
    }

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ajaxGetListTypes()
    {
        $listTypes = ListTypes::where(['tenant_id'=> auth()->user()->tenant_id])->orderBy('list_name', 'asc')->get();
        $response = '';
        foreach ($listTypes as $key) {
            $response.= '<option value="'.$key->id.'">'.$key->list_name.'</option>';
        }
        return json_encode($response);
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
