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
                        <h6 class="card-title">@lang('modules.autoQuoteCleaning.boxTitle')</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                {!! Form::open(['id' => 'updateAutoQuote', 'class' => 'ajax-form', 'method' => 'POST']) !!}
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <div class="form-group">
                                                <label>@lang('modules.autoQuoteCleaning.autoQuoteEnabled')</label>
                                                <div class="form-check form-check-switchery">
                                                    <label class="form-check-label">
                                                        <input value="Y" type="checkbox" name="auto_quote_enabled" @if (isset($autoQuoting) && $autoQuoting->auto_quote_enabled == 'Y')
                                                        checked @endif class="js-switch class="js-switch
                                                        form-check-input-switchery-danger" data-fouc data-color="#f96262">
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('modules.autoQuoteCleaning.sendQuoteEmailToCustomer')</label>

                                                <div class="form-check form-check-switchery">
                                                    <label class="form-check-label">
                                                        <input value="Y" name="send_auto_quote_email_to_customer"
                                                            type="checkbox" @if (isset($autoQuoting) && $autoQuoting->send_auto_quote_email_to_customer == 'Y')
                                                        checked @endif class="js-switch
                                                        form-check-input-switchery-danger" data-fouc data-color="#f96262">
                                                    </label>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mt-3">
                                            <div class="form-group">
                                                <label>@lang('modules.autoQuoteCleaning.sendQuoteSmsToCustomer')</label>
                                                <div class="form-check form-check-switchery">
                                                    <label class="form-check-label">
                                                        <input value="Y" name="send_auto_quote_sms_to_customer"
                                                            type="checkbox" @if (isset($autoQuoting) && $autoQuoting->send_auto_quote_sms_to_customer == 'Y')
                                                        checked @endif class="js-switch
                                                        form-check-input-switchery-danger" data-fouc data-color="#f96262">
                                                    </label>
                                                </div>
                                            </div>
                                        </div>                                        
                                        <div class="col-md-6 mt-3">
                                            <div class="form-group">
                                                <label>@lang('modules.autoQuoteCleaning.quote_sms_template')</label>
                                                <select name="quote_sms_template_id" id="quote_sms_template_id"
                                                    class="form-control">
                                                    @foreach ($sms_templates as $rs)
                                                        <option @if (isset($autoQuoting) && $autoQuoting->quote_sms_template_id == $rs->id)
                                                            selected
                                                    @endif
                                                    value="{{ $rs->id }}">{{ $rs->sms_template_name }}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6" style="margin-top: 15px;">
                                            <div class="form-group">
                                                <label>@lang('modules.autoQuoteCleaning.initialOpportunityStatus')</label>
                                                <select name="initial_op_status_id" id="initial_op_status_id"
                                                    class="form-control">
                                                    @foreach ($pipelineStatuses as $status)
                                                        <option @if (isset($autoQuoting) && $autoQuoting->initial_op_status_id == $status->id)
                                                            selected
                                                    @endif
                                                    value="{{ $status->id }}">{{ $status->pipeline_status }}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('modules.autoQuoteCleaning.successfullyQuotedOpportunityStatus')</label>
                                                <select name="quoted_op_status_id" id="quoted_op_status_id"
                                                    class="form-control">
                                                    @foreach ($pipelineStatuses as $status)
                                                        <option @if (isset($autoQuoting) && $autoQuoting->quoted_op_status_id == $status->id)
                                                            selected
                                                    @endif
                                                    value="{{ $status->id }}">{{ $status->pipeline_status }}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                        <!--/span-->

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('modules.autoQuoteCleaning.quotingFailedOpportunityStatus')</label>
                                                <select name="failed_op_status_id" id="failed_op_status_id"
                                                    class="form-control">
                                                    @foreach ($pipelineStatuses as $status)
                                                        <option @if (isset($autoQuoting) && $autoQuoting->failed_op_status_id == $status->id)
                                                            selected
                                                    @endif
                                                    value="{{ $status->id }}">{{ $status->pipeline_status }}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                        <!--/span-->

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('modules.autoQuoteCleaning.quoteEmailTemplate')</label>
                                                <select name="quote_email_template_id" id="quote_email_template_id"
                                                    class="form-control">
                                                    @foreach ($emailTemplates as $template)
                                                        <option @if (isset($autoQuoting) && $autoQuoting->quote_email_template_id == $template->id)
                                                            selected
                                                    @endif
                                                    value="{{ $template->id }}">{{ $template->email_template_name }}
                                                    </option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('modules.autoQuoteCleaning.failureEmailTemplate')</label>
                                                <select name="fail_email_template_id" id="fail_email_template_id"
                                                    class="form-control">
                                                    @foreach ($emailTemplates as $template)
                                                        <option @if (isset($autoQuoting) && $autoQuoting->fail_email_template_id == $template->id)
                                                            selected
                                                    @endif
                                                    value="{{ $template->id }}">{{ $template->email_template_name }}
                                                    </option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('modules.autoQuoteCleaning.taxUsedInQuote')</label>
                                                <select name="tax_id_for_quote" id="tax_id_for_quote" class="form-control">
                                                    @foreach ($taxes as $tax)
                                                        <option @if (isset($autoQuoting) && $autoQuoting->tax_id_for_quote == $tax->id)
                                                            selected
                                                    @endif value="{{ $tax->id }}">{{ $tax->tax_name }} -
                                                    {{ $tax->rate_percent }}%</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('modules.autoQuoteCleaning.sendFailureEmailTo')</label>
                                                <select name="send_quote_fail_email_to" id="send_quote_fail_email_to"
                                                    class="form-control">
                                                    @foreach ($users as $usr)
                                                        <option @if (isset($autoQuoting) && $autoQuoting->send_quote_fail_email_to == $usr->email)
                                                            selected
                                                    @endif value="{{ $usr->email }}">{{ $usr->name }}
                                                    ({{ $usr->email }})</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Quote Line Item Product</label>
                                                <select name="quote_line_item_product_id" id="quote_line_item_product_id"
                                                    class="form-control">
                                                    <option></option>
                                                    @foreach ($products as $product)
                                                        <option @if (isset($autoQuoting) && $autoQuoting->quote_line_item_product_id == $product->id)
                                                            selected
                                                    @endif value="{{ $product->id }}">{{ $product->name }}
                                                    </option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>

                                        {{-- <div class="col-md-6 ">
                                            <div class="form-group">
                                                <label>Redirect to Inventory Form After Quote Payment</label>
                                                <div class="form-check form-check-switchery">
                                                    <label class="form-check-label">
                                                        <input value="Y" type="checkbox"
                                                            name="redirect_to_inven_form_after_quote_payment" @if (isset($autoQuoting) && $autoQuoting->redirect_to_inven_form_after_quote_payment == 'Y')
                                                        checked @endif class="js-switch class="js-switch
                                                        form-check-input-switchery-danger" data-fouc data-color="#f96262">
                                                    </label>
                                                </div>
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <hr>
                                            <button type="submit" id="save-form-2" class="btn btn-success m-r-10"><i
                                                    class="fa fa-check"></i> @lang('app.save')</button>
                                            <!-- <button type="reset" class="btn btn-default">@lang('app.reset')</button> -->
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

        <?php if (isset($autoQuoting)) {
        $url = '/admin/cleaning-settings/saveAutoQuoteData/' . $autoQuoting->id;
        } else {
        $url = '/admin/cleaning-settings/createAutoQuoteData/0';
        }
        //dd($url);
        ?>
    </div>
@endsection

@push('footer-script')

    <script src="{{ asset('plugins/bower_components/switchery/dist/switchery.min.js') }}"></script>

    <script>
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function() {
            new Switchery($(this)[0], $(this).data());
        });

        $('#save-form-2').click(function() {
            $.easyAjax({
                url: "{{ $url }}",
                container: '#updateAutoQuote',
                type: "POST",
                redirect: true,
                data: $('#updateAutoQuote').serialize(),
                success: function(data) {
                    if (data.error == 1) {
                        swal({
                            title: "Error",
                            text: data.message,
                            type: "error",
                            button: "OK",
                        });
                    } else {
                        window.location.reload();
                    }
                },
                beforeSend: function() {
                    $.blockUI({
                        message: 'Saving..'
                    });
                },
                complete: function() {
                    $.unblockUI();
                },
            })
        });

    </script>
@endpush
