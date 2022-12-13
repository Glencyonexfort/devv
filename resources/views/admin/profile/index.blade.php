@extends('layouts.app')

@section('page-title')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="{{ $pageIcon }} mr-2"></i> <span class="font-weight-semibold">{{ $pageTitle }}</span></h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
    <!-- <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> @lang('app.menu.home')</a>
                <span class="breadcrumb-item active">{{ $pageTitle }}</span>
            </div>
        </div>
    </div> -->
</div>
@endsection

@section('content')


<div class="content">
    <div class="d-md-flex align-items-md-start">
        @include('sections.admin_setting_menu')
        <div style="flex:auto">
            <div class="card">
                <div class="card-header bg-white header-elements-inline">
                    <h6 class="card-title">@lang('modules.profile.updateTitle')</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            {!! Form::open(['id'=>'updateProfile','class'=>'ajax-form','method'=>'PUT']) !!}
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <div class="form-group">
                                            <label>@lang('modules.profile.yourName')</label>
                                            <input type="text" name="name" id="name" class="form-control" value="{{ $userDetail->name }}">
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('modules.profile.yourEmail')</label>
                                            <input type="email" name="email" id="email" class="form-control" value="{{ $userDetail->email }}">
                                        </div>
                                    </div>
                                    <!--/span-->
                                </div>
                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('modules.profile.yourPassword')</label>
                                            <input type="password" name="password" id="password" class="form-control">
                                            <span class="help-block"> @lang('modules.profile.passwordNote')</span>
                                        </div>
                                    </div>
                                    <!--/span-->

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('modules.profile.yourMobileNumber')</label>
                                            <input type="tel" name="mobile" id="mobile" class="form-control" value="{{ $userDetail->mobile }}">
                                        </div>
                                    </div>
                                    <!--/span-->

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('modules.employees.gender')</label>
                                            <select name="gender" id="gender" class="form-control">
                                                <option @if($userDetail->gender == 'male') selected @endif value="male">@lang('app.male')</option>
                                                <option @if($userDetail->gender == 'female') selected @endif value="female">@lang('app.female')</option>
                                                <option @if($userDetail->gender == 'others') selected @endif value="others">@lang('app.others')</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!--/span-->
                                </div>
                                <!--/row-->

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">@lang('modules.profile.yourAddress')</label>
                                            <textarea name="address" id="address" rows="5" class="form-control">@if(!empty($userDetail->employee_details)){{ $userDetail->employee_details->address }}@endif</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label>@lang('modules.profile.profilePicture')</label>

                                        <div class="form-group">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                                    @if(is_null($userDetail->image))
                                                    <img src="http://via.placeholder.com/200x150.png?text=@lang('modules.profile.uploadPicture')" alt="" />
                                                    @else
                                                    <img src="{{ asset('user-uploads/avatar/'.$userDetail->image) }}" alt="" />
                                                    @endif
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"></div>
                                                <div>
                                                    <span class="btn btn-info btn-file">
                                                        <span class="fileinput-new"> @lang('app.selectImage') </span>
                                                        <span class="fileinput-exists"> @lang('app.change') </span>
                                                        <input type="file" name="image" id="image"> </span>
                                                    <a href="javascript:;" class="btn btn-danger fileinput-exists" data-dismiss="fileinput"> @lang('app.remove') </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <hr>
                                        <button type="submit" id="save-form-2" class="btn btn-success m-r-10"><i class="fa fa-check"></i> @lang('app.update')</button>
                                        <button type="reset" class="btn btn-default">@lang('app.reset')</button>
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script>
    $('#save-form-2').click(function() {
        $.easyAjax({
            url: "{{route('member.profile.update', [$userDetail->id])}}",
            container: '#updateProfile',
            type: "POST",
            redirect: true,
            file: (document.getElementById("image").files.length == 0) ? false : true,
            data: $('#updateProfile').serialize(),
            success: function(data) {
                if (data.status == 'success') {
                    window.location.reload();
                }
            }
        })
    });
</script>
@endpush