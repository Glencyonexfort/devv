@extends('layouts.app')
<style>
    .note-group-select-from-files {
       display: none;
    }
</style>
@section('page-title')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="{{ $pageIcon }} mr-2"></i> <span class="font-weight-semibold">{{ $pageTitle }}</span></h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
</div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">   
@endpush

@section('content')

<div class="row" style="padding-top:25px">
    <div class="col-md-12">

        <div class="panel panel-inverse">
            <div class="panel-heading"> @lang('modules.peopleOperations.updateEmployee')</div>
            <div class="panel-wrapper" aria-expanded="true">
                <div class="panel-body card">
                    {!! Form::open(['id'=>'updateEmployee','class'=>'ajax-form','method'=>'PUT']) !!}
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6 ">
                                <div class="form-group">
                                    <label>@lang('modules.peopleOperations.first_name')</label>
                                    <input type="text" name="first_name" id="first_name" value="{{ $employee->first_name }}" class="form-control" autocomplete="nope">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('modules.peopleOperations.last_name')</label>
                                    <input type="text" name="last_name" id="last_name" value="{{ $employee->last_name }}" class="form-control" autocomplete="nope">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom:15px;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('modules.peopleOperations.employeeNumber')</label>
                                    <input type="text" name="employee_number" id="employee_number" value="{{ $employee->employee_number }}" class="form-control" autocomplete="nope">
                                </div>
                            </div>
                            <div class="col-md-6 ">
                                <div class="form-group">
                                    <label>@lang('modules.peopleOperations.mobile')</label>
                                    <input type="text" name="mobile" id="mobile" value="{{ $employee->mobile }}" class="form-control" autocomplete="nope">
                                </div>
                            </div>

                            <div class="col-md-6 ">

                                <div class="form-group">
                                            <label>@lang('modules.peopleOperations.is_this_employee_system_user')</label>
                                            <div class="form-check form-check-switchery">
                                                <label class="form-check-label">
                                                    <input value="Y" type="checkbox" name="is_system_user" id="is_system_user" @if(isset($employee) && $employee->is_system_user == 'Y') checked @endif class="js-switch class="js-switch form-check-input-switchery-danger" data-fouc data-color="#4caf50">
                                                </label>
                                            </div>
                                           
                                </div>
                            </div>
                        </div><br/>
                        <div class="row" id="user-profile-section" @if(isset($employee) && $employee->is_system_user == 'N') style="display:none;" @endif>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('modules.peopleOperations.email_used_for_login')</label>
                                    <input type="email" name="email" id="email" value="{{ $employee->email }}" class="form-control" autocomplete="nope">
                                </div>
                            </div>

                            <div class="col-md-6">
                                
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                            <!-- <div class="col-md-4 pull-left">
                                                <label>@lang('modules.peopleOperations.password') </label>
                                            </div>
                                            <div class="col-md-4 pull-left">
                                                <input type="checkbox" class="pull-left" style="margin-right: 4px;margin-top: 3px;" onclick="showPassword()"> Show Password
                                            </div> -->
                                            <label class="col-md-12" style="padding-left: 0px;margin-bottom: 0px;"><span class="pull-left">@lang('modules.peopleOperations.password')</span> 
                                                <span class="pull-right"> <input type="checkbox" style="margin-right: 4px;margin-top: 3px;" onclick="showPassword()"> Show Password </span></label>
                                            <input type="password" name="password" id="password" class="form-control">
                                            <span class="help-block"> @lang('modules.profile.passwordNote')</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                            <label>@lang('modules.peopleOperations.confirmPassword')</label>
                                            <input type="password" name="confirm_password" id="confirm_password" class="form-control">
                                            <span class="help-block"> @lang('modules.profile.passwordNote')</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('modules.peopleOperations.system_role')</label>
                                    <select name="role_id" id="role_id" class="form-control">
                                        @foreach($roles as $role)
                                            <option data-name="{{ $role->name }}" @if(isset($employee) && $employee->role_id == $role->id) selected @endif  value="{{ $role->id }}">{{ $role->display_name }}</option>
                                        @endforeach
                                                
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('modules.peopleOperations.system_job_type')</label>
                                    <select name="sys_job_type" id="sys_job_type" class="form-control">
                                        <option value=""></option>
                                        @foreach($sysModules as $module)
                                            <option @if(isset($employee) && $employee->sys_job_type == $module->id) selected @endif  value="{{ $module->id }}">{{ $module->options }}</option>
                                        @endforeach
                                                
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>@lang('modules.peopleOperations.email_signature')</label>
                                        <textarea name="email_signature" id="email_signature" class="summernote">{{ $employee->email_signature }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6" style="margin-bottom:12px;">

                                <div class="form-group">
                                            <label>@lang('modules.peopleOperations.active')</label>
                                            <div class="form-check form-check-switchery">
                                                <label class="form-check-label">
                                                    <input value="active" type="checkbox" name="status" @if(isset($employee) && $employee->status == 'active') checked @endif class="js-switch class="js-switch form-check-input-switchery-danger" data-fouc data-color="#4caf50">
                                                </label>
                                            </div>
                                           
                                </div>
                            </div>
                        </div>

                    </div><br/>
                    <div class="form-actions">
                        <button type="submit" id="save-form" class="btn btn-success"><i
                                class="fa fa-check"></i> @lang('app.update')</button>
                        <a href="{{ route('admin.list-employees.index') }}" class="btn btn-default">@lang('app.back')</a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('footer-script')
<script src="{{ asset('plugins/bower_components/switchery/dist/switchery.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script>
    $('.summernote').summernote({
        height: 200, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
        focus: false // set focus to editable area after initializing summernote
    });
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    $('.js-switch').each(function() {
        new Switchery($(this)[0], $(this).data());
    });

function showPassword() {
  var x = document.getElementById("password");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}

$('#save-form').click(function () {
    $.easyAjax({
        url: '{{route('admin.list-employees.update', [$employee->id])}}',
        container: '#updateEmployee',
        type: "POST",
        redirect: true,
        data: $('#updateEmployee').serialize()
    })
});

$("#is_system_user").change(function() {
        if(this.checked) {
            $("#user-profile-section").show("1000");
        } else {
            $("#user-profile-section").hide("1000");
        }
    });
</script>
@endpush

