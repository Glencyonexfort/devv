<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\LanguageSetting;
use Illuminate\Http\Request;

class LanguageSettingsController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.language').' '.__('app.menu.settings');
        $this->pageIcon = 'icon-settings';
    }

    public function index()
    {
        $this->languages = LanguageSetting::all();
        return view('admin.language-settings.index', $this->data);
    }

    public function update(Request $request,$id)
    {
        $setting = LanguageSetting::findOrFail($request->id);
        $setting->status = $request->status;
        $setting->save();
        session(['language_setting' => \App\LanguageSetting::where('status', 'enabled')->get()]);

        return Reply::success(__('messages.settingsUpdated'));
    }
}
