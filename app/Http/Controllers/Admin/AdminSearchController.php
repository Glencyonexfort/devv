<?php

namespace App\Http\Controllers\Admin;

use App\UniversalSearch;
use Illuminate\Http\Request;

class AdminSearchController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Search results';
        $this->pageIcon = 'icon-magnifier';
    }

    public function store(Request $request)
    {
        $key = $request->search_key;

        if(trim($key) == ''){
            return redirect()->back();
        }

        return redirect(route('admin.search.show', $key));
    }

    public function show($key)
    {
        $this->searchResults = UniversalSearch::where('title', 'like', '%'.$key.'%')->where(['tenant_id' => auth()->user()->tenant_id])->get();
        $this->searchKey = $key;
        return view('admin.search.show', $this->data);
    }
}
