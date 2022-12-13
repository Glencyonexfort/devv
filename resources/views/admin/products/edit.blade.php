@extends('layouts.app')
<style>
.help-block{margin-top: 10px;}
</style>
@section('page-title')
    <!-- Page header and Breadcrumb -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><i class="{{ $pageIcon }}"></i> <span class="font-weight-semibold"> {{ $pageTitle }}</span></h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>
        </div>
    </div>
    <!-- /page header and Breadcrumb-->
@endsection

@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
@endpush

@section('content')

    <div class="content">
        <div class="d-md-flex align-items-md-start">
            @include('sections.admin_finance_setting_menu')
            <div style="flex:auto">
                <div class="card">
                    <div class="card-header bg-white header-elements-inline">
                        <h6 class="card-title">@lang('app.update') @lang('app.menu.products')</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">{!! Form::open(['id' => 'updateProduct', 'class' =>
                                'ajax-form']) !!}
                                <input name="_method" value="PUT" type="hidden">
                                <div class="form-body">
                                    <h3 class="box-title">@lang('app.menu.products') @lang('app.details')</h3>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">@lang('app.name')</label>
                                                <input type="text" id="name" name="name" class="form-control"
                                                    value="{{ $product->name }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">@lang('app.price')</label>
                                                <input type="text" id="price" name="price" class="form-control"
                                                    value="{{ $product->price }}">
                                                <span class="help-block"> @lang('messages.productPrice')</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">@lang('modules.invoices.tax') <a
                                                        href="javascript:;" id="tax-settings"><i
                                                            class="ti-settings text-info"></i></a></label>
                                                <select name="tax_id" id="tax_id" class="select2 form-control">
                                                    <option value="">@lang('app.select') @lang('modules.invoices.tax')
                                                    </option>
                                                    @foreach ($taxes as $tax)
                                                        <option @if ($product->tax_id == $tax->id) selected
                                                    @endif value="{{ $tax->id }}">{{ $tax->tax_name }}
                                                    ({{ $tax->rate_percent }}%)</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">@lang('app.description')</label><br/>
                                                {first_name}, {last_name}, {pickup_suburb}, {delivery_suburb}, {pickup_address}, {delivery_address}, {mobile}, {email}, {inventory_list}
                                                <textarea name="description" id="" cols="30" rows="4" class="form-control">{{ $product->description }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Product Category</label>
                                                <select name="category_id" id="category_id" class="select2 form-control">
                                                    <option>select category..</option>
                                                    @foreach ($product_categories as $cat)
                                                        <option value="{{ $cat->id }}" @if ($cat->id == $product->category_id)
                                                            selected=""
                                                    @endif
                                                    >{{ $cat->category_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Product Type</label>
                                                <select name="product_type" id="product_type" class="select2 form-control">
                                                    <option>select type..</option>
                                                    @foreach($product_types as $type)
                                                        @if ($type == 'Item')
                                                            <option value="{{ $type }}" @if ($product->product_type == $type)
                                                                selected=""
                                                            @endif
                                                            >Item - Fixed</option>  
                                                        @endif
                                                        @if ($type == 'Service')
                                                            <option value="{{ $type }}" @if ($product->product_type == $type)
                                                                selected=""
                                                            @endif
                                                            >Service - Hourly</option>  
                                                        @endif
                                                        @if ($type == 'Charge')
                                                            <option value="{{ $type }}" @if ($product->product_type == $type)
                                                                selected=""
                                                            @endif
                                                            >{{ $type }}</option>  
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <?php
                                            $mini_hours_hidden=($product->product_type!="Service")?"hidden":"";
                                        ?>
                                        <div id="hourly_pricing_min_hours_div" class="col-md-6 {{ $mini_hours_hidden }}">
                                            <div class="form-group">                                            
                                                <label class="control-label">Minimum Hours</label>
                                                <input type="number" id="hourly_pricing_min_hours" step="0.01" name="hourly_pricing_min_hours" class="form-control" value="{{ $product->hourly_pricing_min_hours }}">
                                            </div>
                                        </div>
                                        @if ($xero_connected)
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Xero Sale Account</label>
                                                    <select name="xero_account_id" id="xero_account_id"
                                                        class="select2 form-control">
                                                        <option>Select account</option>
                                                        @foreach ($accounts as $account)
                                                            <option value="{{ $account['Code'] }}" @if ($account['Code'] == $product->xero_account_id) selected="" @endif>{{ $account['Code'] . ' - ' . $account['Name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <input type="hidden" name="myob_account_id" id="myob_account_id" value="0" />
                                            @elseif($myob_connected)
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Myob Sale Account</label>
                                                    <select name="myob_account_id" id="myob_account_id" class="select2 form-control">
                                                        <option>Select account</option>
                                                            @if($myob_accounts)
                                                            @foreach($myob_accounts->Items as $account)
                                                                @if($account->Classification!='Income')@continue;@endif
                                                                <option value="{{ $account->UID }}"
                                                                @if($account->UID==$myob_tenant_api_details->account_key)
                                                                selected=""
                                                                @endif
                                                                >{{ $account->DisplayID.' - '.$account->Name }}</option>
                                                            @endforeach
                                                            @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <input type="hidden" name="xero_account_id" id="xero_account_id" value="0" />
                                            @else
                                            <input type="hidden" name="xero_account_id" id="xero_account_id" value="0" />
                                            <input type="hidden" name="myob_account_id" id="myob_account_id" value="0" />
                                            @endif
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="customer_type" class="control-label">Customer Type</label>
                                            <select name="customer_type" id="customer_type"
                                                class="select2 form-control">
                                                @foreach ($customer_types as $type)
                                                    <option value="{{ $type }}" @if ($product->customer_type==$type) selected="" @endif>{{ $type }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div id="customer_id_div" class="col-md-6 @if ($product->customer_type != "Commercial") hidden @endif">
                                        <div class="form-group">
                                            <label class="control-label">Customer (leave blank if applicable for all commercial customers)</label>
                                            <select name="customer_id" id="customer_id" class="form-control">
                                                <option value="0"></option>
                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->id }}" @if($product->customer_id == $customer->id) selected @endif>{{ $customer->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div id="stockable_div" class="col-md-6 @if ($product->product_type != 'Item') hidden @endif">
                                        <div class="form-group">
                                            <input type="checkbox" name="stockable" id="stockable" @if($product->stockable == 'Y') checked @endif>
                                            <label for="Commercial Customer">Stockable</label>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <hr>
                                    <button type="submit" id="save-form" class="btn btn-success"> <i
                                            class="fa fa-check"></i> @lang('app.save')</button>
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-light"
                                        data-dismiss="modal">Cancel</a>
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
    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="taxModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
                </div>
                <div class="modal-body">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn blue">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    {{--Ajax Modal Ends--}}

@endsection

@push('footer-script')
    <script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script>
        $('body').on('change', '#customer_type', function(e){
            if($(this).val()=="Commercial")
            {
                $('#customer_id_div').show(500);
            }
            else
            {
                $('#customer_id_div').hide(500);
            }
        });
        
        $('body').on('change', '#product_type', function(e) {
            e.preventDefault();
            var type = $(this).find(":selected").val();
            if(type=="Service")
            {
                $("#hourly_pricing_min_hours_div").show();
            }
            else
            {
                $("#hourly_pricing_min_hours_div").hide();
            }

            if(type == 'Item')
            {
                $("#stockable_div").show();
            }
            else
            {
                $("#stockable_div").hide();
            }
        });
        $(".select2").select2({
            formatNoMatches: function() {
                return "{{ __('messages.noRecordFound') }}";
            }
        });

        $('#tax-settings').on('click', function(event) {
            event.preventDefault();
            var url = "{{ route('admin.taxes.create') }}";
            $('#modelHeading').html('Manage Project Category');
            $.ajaxModal('#taxModal', url);
        });

        $('#save-form').click(function() {
            $.easyAjax({
                url: "{{ route('admin.products.update', [$product->id]) }}",
                container: '#updateProduct',
                type: "POST",
                redirect: true,
                data: $('#updateProduct').serialize(),
                beforeSend: function() {
                    $.blockUI({
                        message: 'Saving..'
                    });
                },
                complete: function() {
                    $.unblockUI();
                },
            });
        });

    </script>
@endpush
