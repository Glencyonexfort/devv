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
<link rel="stylesheet" href="{{ asset('image-picker/image-picker.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
@endpush

@section('content')
<div class="content">
    <div class="d-md-flex align-items-md-start">
        @include('sections.admin_finance_setting_menu')
        <div style="flex:auto">
            <div class="card">
                <div class="card-header bg-white header-elements-inline">
                    <h6 class="card-title">@lang('modules.invoiceSettings.updateTitle')</h6>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::open(['id'=>'editSettings','class'=>'ajax-form','method'=>'PUT']) !!}
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="invoice_prefix">@lang('modules.invoiceSettings.invoicePrefix')</label>
                                        <input type="text" class="form-control" id="invoice_prefix" name="invoice_prefix" value="{{ $invoiceSetting->invoice_prefix }}">
                                    </div>
                                    {{-- <div class="form-group">
                                        <label for="template">@lang('modules.invoiceSettings.template')</label>
                                        <select name="template" class="image-picker show-labels show-html">
                                            <option data-img-src="{{ asset('invoice-template/1.png') }}" @if($invoiceSetting->template == 'invoice-1') selected @endif
                                                value="invoice-1">Template
                                                1
                                            </option>
                                            <option data-img-src="{{ asset('invoice-template/2.png') }}" @if($invoiceSetting->template == 'invoice-2') selected @endif
                                                value="invoice-2">Template
                                                2
                                            </option>
                                            <option data-img-src="{{ asset('invoice-template/3.png') }}" @if($invoiceSetting->template == 'invoice-3') selected @endif
                                                value="invoice-3">Template
                                                3
                                            </option>
                                            <option data-img-src="{{ asset('invoice-template/4.png') }}" @if($invoiceSetting->template == 'invoice-4') selected @endif
                                                value="invoice-4">Template
                                                4
                                            </option>
                                        </select>

                                    </div> --}}
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="due_after">@lang('modules.invoiceSettings.dueAfter')</label>

                                        <div class="input-group m-t-10">
                                            <input type="number" id="due_after" name="due_after" class="form-control" value="{{ $invoiceSetting->due_after }}">
                                            <span class="input-group-addon">@lang('app.days')</span>
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="gst_number">@lang('app.gstNumber')</label>
                                        <input type="text" id="gst_number" name="gst_number" class="form-control" value="{{ $invoiceSetting->gst_number }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.showGst')</label>
                                        <div class="switchery-demo">
                                            <input type="checkbox" name="show_gst" @if($invoiceSetting->show_gst == 'yes') checked @endif class="js-switch " data-color="#99d683" />
                                        </div>
                                    </div>
                                </div> --}}

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="invoice_terms">@lang('modules.invoiceSettings.invoiceTerms')</label>
                                        <textarea name="invoice_terms" id="invoice_terms" class="summernote form-control" rows="4">{{ $invoiceSetting->invoice_terms }}</textarea>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cc_processing_fee_percent">Credit Card Processing Fee percent</label>
                                        <input type="text" id="cc_processing_fee_percent" name="cc_processing_fee_percent" class="form-control" value="{{ $invoiceSetting->cc_processing_fee_percent }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Credit Card Processing Product Item</label>
                                        <select name="cc_processing_product_id" id="cc_processing_product_id" class="form-control">
                                            <option></option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" @if($product->id == $invoiceSetting->cc_processing_product_id) selected @endif 
                                                    >{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Stripe Pre Authorise Booking Payment</label>
                                        <div class="switchery-demo">
                                            <input type="checkbox" id="stripe_pre_authorise" name="stripe_pre_authorise" value="Y"
                                                {{ $invoiceSetting->stripe_pre_authorise == 'Y' ? 'checked=""' : '' }}
                                                class="js-switch " data-color="#f96262" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Stripe Pre Authorised Opportunity Status</label>
                                        <select name="stripe_pre_authorised_op_status" id="stripe_pre_authorised_op_status" class="form-control">
                                            <option></option>
                                            @foreach($opp_statuses as $status)
                                                <option value="{{ $status->pipeline_status }}" @if($status->pipeline_status == $invoiceSetting->stripe_pre_authorised_op_status) selected @endif 
                                                    >{{ $status->pipeline_status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <hr>
                                    <button type="submit" id="save-form" class="btn btn-success waves-effect waves-light m-r-10"><i class="fa fa-check"></i> @lang('app.update')</button>
                                    <button type="reset" class="btn btn-inverse waves-effect waves-light">@lang('app.reset')</button>
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

<script src="{{ asset('image-picker/image-picker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/switchery/dist/switchery.min.js') }}"></script>
<script src="{{ asset('newassets/global_assets/js/plugins/editors/summernote/summernote.min.js') }}"></script>
    <script>
    //# Summernote editor

var Summernote = function() {
var _componentSummernote = function() {
    if (!$().summernote) {
        console.warn('Warning - summernote.min.js is not loaded.');
        return;
    }
    $('.summernote').summernote({
        height: 100,
        toolbar: [
            ['font', ['bold', 'underline']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol']],
            ['insert', ['link', 'picture']],
            ['view', ['codeview','fullscreen']],
        ],
    });
};

// Uniform
var _componentUniform = function() {
    if (!$().uniform) {
        console.warn('Warning - uniform.min.js is not loaded.');
        return;
    }

    // Styled file input
    $('.note-image-input').uniform({
        fileButtonClass: 'action btn bg-warning-400'
    });
};

return {
    init: function() {
        _componentSummernote();
        _componentUniform();
    }
}
}();
document.addEventListener('DOMContentLoaded', function() {
Summernote.init();
});

    $(".image-picker").imagepicker();
    // Switchery
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    $('.js-switch').each(function() {
        new Switchery($(this)[0], $(this).data());

    });
    $('#save-form').click(function() {
        $.easyAjax({
            url: "{{route('admin.invoice-settings.update', $invoiceSetting->id)}}",
            container: '#editSettings',
            type: "POST",
            redirect: true,
            data: $('#editSettings').serialize(),
            beforeSend : function() {
               $.blockUI({ message: 'Saving..' });
            }, 
            complete: function () {
                 $.unblockUI();
            },
        })
    });
</script>
@endpush