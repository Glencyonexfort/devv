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

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">

@endpush

@section('content')

<div class="content">
    <div class="d-md-flex align-items-md-start">
        @include('sections.cleaning_quote_form_settings_menu')
        <div style="flex:auto">
            <div class="card">
                <div class="card-header bg-white header-elements-inline">
                    <h6 class="card-title">@lang('modules.generalCleaningQuoteFormSettings.boxTitle')</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            {!! Form::open(['id'=>'saveFormSettings','class'=>'ajax-form','method'=>'POST']) !!}
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.generalCleaningQuoteFormSettings.service_product_category')</label>
                                            <select name="services_category_id" id="services_category_id" class="form-control" required>
                                                @foreach($product_categories as $rs)
                                                <option @if(isset($quote_form_setup) && $quote_form_setup->services_category_id == $rs->id) selected @endif value="{{ $rs->id }}">{{ $rs->category_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.generalCleaningQuoteFormSettings.minimum_cleaners')</label>
                                            <input type="number" name="min_cleaners" id="min_cleaners" class="form-control" value="{{ $quote_form_setup->min_cleaners ?? ''}}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.generalCleaningQuoteFormSettings.maximum_cleaners')</label>
                                            <input type="number" name="max_cleaners" id="max_cleaners" class="form-control" value="{{ $quote_form_setup->max_cleaners ?? ''}}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.generalCleaningQuoteFormSettings.minimum_hours')</label>
                                            <input type="number" name="min_hours" id="min_hours" class="form-control" value="{{ $quote_form_setup->min_hours ?? ''}}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.generalCleaningQuoteFormSettings.maximum_hours')</label>
                                            <input type="number" name="max_hours" id="max_hours" class="form-control" value="{{ $quote_form_setup->max_hours ?? ''}}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.generalCleaningQuoteFormSettings.cleaning_frequency')</label>
                                            <select name="time_selector_list_type_id" id="time_selector_list_type_id" class="form-control">
                                                @foreach($list_types as $rs)
                                                <option @if(isset($quote_form_setup) && $quote_form_setup->time_selector_list_type_id == $rs->id) selected @endif value="{{ $rs->id }}">{{ $rs->list_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.generalCleaningQuoteFormSettings.extra_product_category')</label>
                                            <select name="extras_category_id" id="extras_category_id" class="form-control">
                                                @foreach($product_categories as $rs)
                                                <option @if(isset($quote_form_setup) && $quote_form_setup->extras_category_id == $rs->id) selected @endif value="{{ $rs->id }}">{{ $rs->category_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                $questions_list_type_id_ary = array();
                                if(isset($quote_form_setup->questions_list_type_id)){
                                    $questions_list_type_id_ary = @explode(',', $quote_form_setup->questions_list_type_id);
                                }
                                ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.generalCleaningQuoteFormSettings.questions_list')</label>
                                            <select name="questions_list_type_id[]" id="questions_list_type_id" class="form-control" multiple required>
                                                @foreach($list_types as $rs)
                                                <option value="{{ $rs->id }}" @if(in_array($rs->id, $questions_list_type_id_ary)) selected @endif >{{ $rs->list_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <hr>
                                        <button type="submit" id="save-form-2" class="btn btn-success m-r-10"><i class="fa fa-check"></i> @lang('app.save')</button>
                                        <!-- <button type="reset" class="btn btn-default">@lang('app.reset')</button> -->
                                        <hr>
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                        <form id="scriptForm" action="" method="get">
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-12" style="margin-top: 15px;">
                                        <div class="form-group">
                                            Choose company for the external Removals Quote form:
                                            <select name="company" id="company" class="form-control" style="display: inline-block;width: fit-content;" required>
                                                @foreach($companies as $rs)
                                                <option @if(isset($slectedCompany) && $slectedCompany==$rs->id) selected @endif value="{{ $rs->id }}">{{ $rs->company_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-left">
                                    <h3>Hidden Tab</h3>
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-left">
                                    <button type="button" id="save-form-1" class="btn btn-success m-r-10"><i class="fa fa-code"></i> Generate Script</button>
                                    <input type="hidden" name="script1" id="script1"  @if(isset($code_script) && !empty($code_script)) value="show" @endif>
                                </div>
                            </div>
                            @if(isset($code_script) && !empty($code_script))
                            <input type="hidden" value="{!! htmlentities($code_script) !!}" id="readyForCopy">
                            <div class="row">
                                <div class="col-md-12 mt-3 mb-2">
                                    <blockquote>
                                        <code>
                                            {!! nl2br(htmlentities($code_script)) !!}
                                        </code>
                                    </blockquote>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" id="copyToClipboard" class="btn btn-primary m-r-10"><i class="fa fa-copy"></i> Copy</button>
                                </div>
                            </div>
                            @endif
                            <div class="row">
                                <div class="col-md-12 text-left mt-3">
                                    <h3>Always Protruding</h3>
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-left">
                                    <button type="button" id="save-form-2" class="btn btn-success m-r-10"><i class="fa fa-code"></i> Generate Script</button>
                                    <input type="hidden" name="script2" id="script2" @if(isset($code_script2) && !empty($code_script2)) value="show" @endif>
                                </div>
                            </div>
                            @if(isset($code_script2) && !empty($code_script2))
                            <input type="hidden" value="{!! htmlentities($code_script2) !!}" id="readyForCopy2">
                            <div class="row">
                                <div class="col-md-12 mt-3 mb-2">
                                    <blockquote>
                                        <code>
                                            {!! nl2br(htmlentities($code_script2)) !!}
                                        </code>
                                    </blockquote>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" id="copyToClipboard2" class="btn btn-primary m-r-10"><i class="fa fa-copy"></i> Copy</button>
                                </div>
                            </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('footer-script')
<?php
if (isset($quote_form_setup)) {
    $url = '/admin/cleaning-settings/saveGeneralQuoteFormSettings/' . $quote_form_setup->id;
} else {
    $url = '/admin/cleaning-settings/saveGeneralQuoteFormSettings/0';
}
?>
<script>
    $('#save-form-2').click(function() {
        $.easyAjax({
            url: "{{ $url }}",
            container: '#saveFormSettings',
            type: "POST",
            redirect: true,
            data: $('#saveFormSettings').serialize(),
            success: function(data) {
                if (data.status == 'success') {
                    window.location.reload();
                }
            }
        })
    });
</script>
<script>
    function setClipboard(value) {
        var tempInput = document.createElement("input");
        tempInput.style = "position: absolute; left: -1000px; top: -1000px";
        tempInput.value = value;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
    }
    $(document).ready(function() {
        $(document).on('click', '#copyToClipboard', function() {
            setClipboard(document.getElementById("readyForCopy").value);
            $.toast({
                text: 'Script copied to clipboard.',
                icon: 'success',
                position: 'top-right',
                loader: false,
                bgColor: '#00c292',
                textColor: 'white'
            });
        });
        $(document).on('click', '#copyToClipboard2', function() {
            setClipboard(document.getElementById("readyForCopy2").value);
            $.toast({
                text: 'Script copied to clipboard.',
                icon: 'success',
                position: 'top-right',
                loader: false,
                bgColor: '#00c292',
                textColor: 'white'
            });
        });

        $(document).on('click', '#save-form-1', function() {
            $('#script1').val('show');
            $('#scriptForm').submit();
        });
        $(document).on('click', '#save-form-2', function() {
            $('#script2').val('show');
            $('#scriptForm').submit();
        });
    });
</script>
@endpush