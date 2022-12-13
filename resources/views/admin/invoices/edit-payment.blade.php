<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datetime-picker/datetimepicker.css') }}">
{!! Form::open(['url' => '/admin/finance/all-invoices/update-invoice-payment'],['id'=>'updatePayment','class'=>'ajax-form','method'=>'POST']) !!}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title"> <i class="fa fa-search"></i>  @lang('modules.payments.editPayment')</h4>
</div>
<div class="modal-body">
    <div class="form-body">
        <div class="row">
            <input type="hidden" name="invoice_id" id="invoice_id" value="{{$invoicePayment->invoice_id}}">
            <input type="hidden" name="payment_id" id="payment_id" value="{{$invoicePayment->id}}">

            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label">@lang('modules.payments.paymentDate')</label>
                    <input type="text" class="form-control paid_on" name="paid_on" id="paid_on" value="{{ $invoicePayment->paid_on->format('d/m/Y') }}">
                </div>
            </div>

            <!--/span-->
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('modules.invoices.amount')</label>
                    <input type="text" name="amount" id="amount" value="{{$invoicePayment->amount}}" class="form-control">
                </div>
            </div>
            <!--/span-->

            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('modules.payments.paymentGateway')</label>
                    <select class="form-control" name="gateway" id="gateway">
                        @foreach($payment_gateways as $gateway)
                        <option <?php if($gateway->name==$invoicePayment->gateway) echo "selected"; ?> value="{{$gateway->name}}">{{$gateway->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!--/span-->

            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('modules.payments.transactionId')</label>
                    <input type="text" name="transaction_id" id="transaction_id" value="{{$invoicePayment->transaction_id}}" class="form-control">
                </div>
            </div>
            <!--/span-->

            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label">@lang('app.remark')</label>
                    <textarea id="remarks" name="remarks" class="form-control">{{$invoicePayment->remarks}}</textarea>
                </div>
            </div>
        </div>
        <!--/row-->
    </div>
</div>
<div class="modal-footer">
    <button type="submit" id="save-form-edit-payment" class="btn btn-success"><i class="fa fa-check"></i>
        @lang('app.save')
    </button>
    <button type="reset" class="btn btn-default">@lang('app.reset')</button>
</div>
{!! Form::close() !!}
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/switchery/dist/switchery.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('plugins/datetime-picker/datetimepicker.js') }}"></script>
<script type="text/javascript">
    jQuery('#paid_on').datetimepicker({
        format: 'D/M/Y hh:mm',
    });
    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });
    $('#save-form-edit-payment').click(function () {
        $.easyAjax({
            url: '{{route('admin.payments.store')}}',
            container: '#updatePayment',
            type: "POST",
            redirect: true,
            data: $('#updatePayment').serialize()
        })
    });
</script>