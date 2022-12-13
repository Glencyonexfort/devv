@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ $pageTitle }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <!-- <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> @lang("app.menu.home")</a>
                <a href="{{ route('admin.all-invoices.index') }}" class="breadcrumb-item">@lang("app.menu.invoices")</a>
                <span class="breadcrumb-item active">@lang('app.invoice')</span>
            </div>
        </div> -->
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css"> -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">

    <style>
        .ribbon-wrapper {
            background: #ffffff !important;
        }
        .modal-open .modal {
            opacity: 1 !important;
        }
        .fa-money {
            font-size: 30px !important;
        }
    </style>
@endpush

@section('content')

    <div class="row">

        <div class="col-sm-6 col-xl-3">
            <div class="card card-body bg-blue-400 has-bg-image bg-inverse">
                <div class="media">
                    <div class="media-body">
                        <h3 class="mb-0">{{ ($invoice->currency && $invoice->currency->currency_symbol) ? $invoice->currency->currency_symbol : '$' }} {{ $totalAmount }}</h3>
                        <span class="text-uppercase font-size-xs">@lang('modules.payments.totalAmount')</span>
                    </div>

                    <div class="ml-3 align-self-center">
                        <i class="fa fa-money text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-body has-bg-image bg-success">
                <div class="media">
                    <div class="media-body">
                        <h3 class="mb-0">{{ ($invoice->currency && $invoice->currency->currency_symbol) ? $invoice->currency->currency_symbol : '$' }} @if($invoice->status == 'paid' && $paidAmount == 0){{ $invoice->total }} @else {{ $paidAmount }} @endif</h3>
                        <span class="text-uppercase font-size-xs">@lang('modules.payments.totalPaid')</span>
                    </div>

                    <div class="ml-3 align-self-center">
                        <i class="fa fa-money text-white"></i>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-sm-6 col-xl-3">
            <div class="card card-body has-bg-image bg-danger">
                <div class="media">
                    <div class="media-body">
                        <h3 class="mb-0">{{ ($invoice->currency && $invoice->currency->currency_symbol) ? $invoice->currency->currency_symbol : '$' }} @if($invoice->status == 'paid' && $paidAmount == 0) 0 @else {{ max(($totalAmount-$paidAmount),0) }} @endif</h3>
                        <span class="text-uppercase font-size-xs">@lang('modules.payments.totalDue')</span>
                    </div>

                    <div class="ml-3 align-self-center">
                        <i class="fa fa-money text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                    <i class="fa fa-check"></i> {!! $message !!}
                </div>
                <?php Session::forget('success');?>
            @endif

            @if ($message = Session::get('error'))
                <div class="custom-alerts alert alert-danger fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                    {!! $message !!}
                </div>
                <?php Session::forget('error');?>
            @endif


            <div class="white-box printableArea ribbon-wrapper">
                <!-- <button
                        type="button"
                        onclick="showPayments()"
                        class="btn btn-info pull-right">@lang('app.view') @lang('app.menu.payments')</button> -->
                

                <!-- <button
                        type="button"
                        onclick="addInvoiceItems()"
                        class="btn btn-danger pull-right"
                        style="margin-right:5px;">@lang('modules.payments.addInvoiceItem')</button> -->

                <a
                        href="{{route("admin.list-jobs.view-job", $invoice->job_id)}}"
                        class="btn btn-info pull-right"
                        style="margin-right:5px;">@lang('modules.payments.jobNumber') {{$job_number ? $job_number : ''}}</a> &nbsp;&nbsp;
                <div class="clearfix"></div>
                <div class="ribbon-content ">
                    @if($invoice->status == 'paid')
                        <div class="ribbon ribbon-bookmark ribbon-success">@lang('modules.invoices.paid')</div>
                    @elseif($invoice->status == 'partial')
                        <div class="ribbon ribbon-bookmark ribbon-info">@lang('modules.invoices.partial')</div>
                    @else
                        <div class="ribbon ribbon-bookmark ribbon-danger">@lang('modules.invoices.unpaid')</div>
                    @endif

                    <h3><b>@lang('app.invoice')</b> <span class="pull-right">{{ $invoice->invoice_number }}</span></h3>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">

                            <div class="pull-left">
                                <address>
                                    <h3> &nbsp;<b class="text-danger">{{ ucwords($global->company_name) }}</b></h3>
                                    @if(!is_null($settings))
                                        <p class="text-muted m-l-5">{!! nl2br($global->address) !!}</p>
                                    @endif
                                    @if($invoiceSetting->show_gst == 'yes' && !is_null($invoiceSetting->gst_number))
                                        <p class="text-muted m-l-5"><b>@lang('app.gstIn')
                                                :</b>{{ $invoiceSetting->gst_number }}</p>
                                    @endif
                                </address>
                            </div>
                            <div class="pull-right text-right">
                                <address>
                                    @if(!is_null($customer))
                                        <h3>@lang('app.to'),</h3>
                                        <h4 class="font-bold">{{ ucwords($customer->name) }}</h4>

                                        <p class="text-muted m-l-30">{{ ($job->pickup_address == '')?'':$job->pickup_address.', ' }} {{ ($job->pickup_suburb == '')?'':$job->pickup_suburb.', ' }}
                  {{ ($job->pickup_state == '')?'':$job->pickup_state.', ' }} {{ ($job->pickup_postcode == '')?'':$job->pickup_postcode.', ' }}</p>

                                        @if($crm_contact_email)
                                        <p class="text-muted m-l-30">
                                            {{ $crm_contact_email->detail}}
                                        </p>
                                        @endif
                                        <p class="text-muted m-l-30">
                                            {{ ($crm_contact_phone)?$crm_contact_phone->detail:''}}
                                        </p>

                                        @if($invoiceSetting->show_gst == 'yes' && !is_null($invoice->project->client->client[0]->gst_number))
                                            <p class="m-t-5"><b>@lang('app.gstIn')
                                                    :</b>  {{ $invoice->project->client->client[0]->gst_number }}
                                            </p>
                                        @endif
                                    @endif

                                    <p class="m-t-30"><b>@lang('modules.invoices.invoiceDate') :</b> <i
                                                class="fa fa-calendar"></i> {{ $invoice->issue_date->format($global->date_format) }}
                                    </p>

                                    <p><b>@lang('modules.dashboard.dueDate') :</b> <i
                                                class="fa fa-calendar"></i> {{ $invoice->due_date->format($global->date_format) }}
                                    </p>
                                    @if($invoice->recurring == 'yes')
                                        <p><b class="text-danger">@lang('modules.invoices.billingFrequency') : </b> {{ $invoice->billing_interval . ' '. ucfirst($invoice->billing_frequency) }} ({{ ucfirst($invoice->billing_cycle) }} cycles)</p>
                                    @endif
                                </address>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive m-t-40" style="clear: both;">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>@lang("modules.invoices.item")</th>
                                        <th class="text-right">@lang("modules.invoices.qty")</th>
                                        <th class="text-right">@lang("modules.invoices.unitPrice")</th>
                                        <th class="text-right">@lang("modules.invoices.price")</th>
                                        <th class="text-right">@lang("modules.dashboard.action")</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $count = 0;
                                    $subtotal = 0; ?>
                                    @foreach($invoice->items as $item)

                                        <?php $subtotal += $item->amount; ?> 
                                            <tr>
                                                <td class="text-center">{{ ++$count }}</td>
                                                <td>{{ ucfirst($item->item_name) }}
                                                    @if(!is_null($item->item_summary))
                                                        <p class="font-12">{{ $item->item_summary }}</p>
                                                    @endif
                                                </td>
                                                <td class="text-right">{{ $item->quantity }}</td>
                                                <td class="text-right"> {!! htmlentities(($invoice->currency && $invoice->currency->currency_symbol) ? $invoice->currency->currency_symbol : '$')  !!}{{ $item->unit_price }}

                                                </td>
                                                <td class="text-right"> {!! htmlentities(($invoice->currency && $invoice->currency->currency_symbol) ? $invoice->currency->currency_symbol : '$')  !!}{{ $item->amount }} </td>
                                                <td class="text-right">

                        
                                <a href="javascript:;" data-item-id="{{ $item->id }}" onclick="editInvoiceItems('{{ $item->id }}')" class="btn btn-info"><i class="fa fa-edit"></i></a>

        &nbsp;&nbsp;&nbsp;<a href="javascript:;" data-item-id="{{ $item->id }}" class="btn btn-danger sa-params"><i class="fa fa-times"></i> </a>
                                                </td>
                                            </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="pull-right m-t-30 text-right">
                                <p>@lang("modules.payments.totalAmount")
                                    : {!! htmlentities(($invoice->currency && $invoice->currency->currency_symbol) ? $invoice->currency->currency_symbol : '$')  !!}{{ $subtotal }}
                                    </p>

                                <p>@lang("modules.payments.totalPaid")
                                    : {!! htmlentities(($invoice->currency && $invoice->currency->currency_symbol) ? $invoice->currency->currency_symbol : '$')  !!}{{ $paidAmount }}
                                </p>
                                @foreach($taxes as $key=>$tax)
                                    <p>{{ strtoupper($key) }}
                                        : {!! htmlentities(($invoice->currency && $invoice->currency->currency_symbol) ? $invoice->currency->currency_symbol : '$')  !!}{{ $tax }} </p>
                                @endforeach

                                <p>@lang("modules.payments.totalDue")
                                : {!! htmlentities(($invoice->currency && $invoice->currency->currency_symbol) ? $invoice->currency->currency_symbol : '$')  !!}{{ $subtotal - $paidAmount }}</p>
                                <!-- <hr>
                                <h3><b>@lang("modules.invoices.total")
                                        :</b> {!! htmlentities(($invoice->currency && $invoice->currency->currency_symbol) ? $invoice->currency->currency_symbol : '$')  !!}{{ $invoice->total }}
                                </h3> -->
                            </div>

                            @if(!is_null($invoice->note))
                                <div class="col-md-12">
                                    <p><strong>@lang('app.note')</strong>: {{ $invoice->note }}</p>
                                </div>
                            @endif
                            <div class="clearfix"></div>

                            <hr>


                            <button
                        type="button"
                        onclick="addPayments()"
                        class="btn btn-info pull-left"
                        style="margin-left:5px;margin-top:5px;">@lang('modules.payments.addPayment')</button>
                            <div class="table-responsive">

                                <h1 style="font-weight: bold; color: #fff;background-color:#fb9678!important;text-align: center;margin-top:10px;margin-bottom:0px;padding: 10px;">Payments Received</h1>

                                <table class="table table-bordered table-hover toggle-circle default footable-loaded footable" id="payments-table">
                                    <thead>
                                    <tr>
                                        <th><strong>@lang('modules.invoices.amount')</strong></th>
                                        <th><strong>@lang('modules.payments.paidOn')</strong></th>
                                        <th><strong>@lang('modules.payments.paymentMethod')</strong></th>
                                        <th><strong>@lang('app.notes')</strong></th>
                                        <th><strong>@lang('app.action')</strong></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($invoice_payments->payment as $payment)
                                        <tr>
                                            <td>{!! htmlentities(($invoice->currency && $invoice->currency->currency_symbol) ? $invoice->currency->currency_symbol : '$')  !!}<?php echo $payment->amount;?></td>
                                            <td><?php echo date('d-m-Y',strtotime($payment->paid_on));?></td>
                                            <td><?php echo $payment->gateway;?></td>
                                            <td><?php echo $payment->remarks;?></td>
                                            <td>
                                               <a href="javascript:;" onclick="editInvoicePayment('{{ $payment->id }}')" class="btn btn-info"><i class="fa fa-edit"></i></a>

                                            &nbsp;&nbsp;&nbsp;<a href="javascript:;" data-item-id="{{ $payment->id }}" class="btn btn-danger edit-invoice-payment-params"><i class="fa fa-times"></i> </a>

                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                            <hr>
                            
                            <div class="text-right">

                                <a class="btn btn-default btn-outline"
                                   href="{{ route('admin.all-invoices.download', $invoice->id) }}"> <span><i class="fa fa-file-pdf-o"></i> @lang('modules.invoices.downloadPdf')</span> </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!--{{--Ajax Modal--}}-->
    <div class="modal fade bs-modal-md in" id="addPayment" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
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
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade bs-modal-md in" id="addInvoiceItem" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
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
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade bs-modal-md in" id="paymentDetail" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">
                <form>
                    {{csrf_field()}}
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
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--{{--Ajax Modal Ends--}}-->

@endsection

@push('footer-script')
    <script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
    <script>
        $(function () {
            var table = $('#invoices-table').dataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{{ route('client.invoices.create') }}',
                deferRender: true,
                "order": [[0, "desc"]],
                language: {
                    "url": "<?php echo __("app.datatable") ?>"
                },
                "fnDrawCallback": function (oSettings) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'project_name', name: 'projects.project_name'},
                    {data: 'invoice_number', name: 'invoice_number'},
                    {data: 'currency_symbol', name: 'currencies.currency_symbol'},
                    {data: 'total', name: 'total'},
                    {data: 'issue_date', name: 'issue_date'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });
        });
        // Show Payment detail modal
        function showPayments() {
            var url = '{{route('admin.all-invoices.payment-detail', $invoice->id)}}';
            $.ajaxModal('#paymentDetail', url);
        }
        // Show add Payment modal
        function addPayments() {
            var url = '{{route('admin.all-invoices.add-payment', $invoice->id)}}';
            $.ajaxModal('#addPayment', url);
        }

        // Show add Invoice Item modal
        function addInvoiceItems() {
            var url = '{{route('admin.all-invoices.add-invoice-item', $invoice->id)}}';
            $.ajaxModal('#addInvoiceItem', url);
        }

        // Show edit Invoice Item modal
        function editInvoiceItems(item_id) {
            //alert(item_id);
            var url = '{{route('admin.all-invoices.edit-invoice-item', ":item_id")}}';
            url = url.replace(':item_id', item_id);
            $.ajaxModal('#addInvoiceItem', url);
        }

        // Show edit Invoice Item modal
        function editInvoicePayment(payment_id) {
            var url = '{{route('admin.all-invoices.edit-invoice-payment', ":id")}}';
            url = url.replace(':id', payment_id);
            $.ajaxModal('#addInvoiceItem', url);
        }
    </script>

    <script>
        $(function() {


            $('body').on('click', '.sa-params', function(){
                var id = $(this).data('item-id');
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover the deleted team!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel please!",
                    closeOnConfirm: true,
                    closeOnCancel: true
                }, function(isConfirm){
                    if (isConfirm) {

                        var url = "{{ route('admin.all-invoices.destroy-invoice-item',':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'DELETE',
                            url: url,
                            data: {'_token': token},
                            success: function (response) {
                                if (response.status == "success") {
                                    $.unblockUI();
                                    window.location.reload();
                                }
                            }
                        });
                    }
                });
            });


            $('body').on('click', '.edit-invoice-payment-params', function(){
                var id = $(this).data('item-id');
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover the deleted team!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel please!",
                    closeOnConfirm: true,
                    closeOnCancel: true
                }, function(isConfirm){
                    if (isConfirm) {

                        var url = "{{ route('admin.all-invoices.destroy-invoice-payment',':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'DELETE',
                            url: url,
                            data: {'_token': token},
                            success: function (response) {
                                if (response.status == "success") {
                                    $.unblockUI();
                                    window.location.reload();
                                }
                            }
                        });
                    }
                });
            });



        });

    </script>
@endpush