{!! Form::open(['url' => '/admin/finance/all-invoices/store-invoice-item'],['id'=>'createInvoiceItem','class'=>'ajax-form','method'=>'POST']) !!}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title"> <i class="fa fa-search"></i>  @lang('modules.payments.addInvoiceItem')</h4>
</div>
<div class="modal-body">
    <div class="form-body">
        <div class="row">
            <input type="hidden" name="invoice_id" id="invoice_id" value="{{$invoice->id}}">


            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('modules.invoices.invoiceItem')</label>
                    <input type="text" required name="invoice_item" id="invoice_item" class="form-control">
                </div>
            </div>

            <!--/span-->
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('modules.invoices.quantity')</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" min="1">
                </div>
            </div>
            <!--/span-->

            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('modules.invoices.unitPrice')</label>
                    <input type="number" name="unit_price" id="unit_price" class="form-control" min="0.01" step="0.01">
                   
                </div>
            </div>
            <!--/span-->

            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label">@lang('app.description')</label>
                    <textarea id="description" name="description" class="form-control"></textarea>
                </div>
            </div>
        </div>
        <!--/row-->
    </div>
</div>
<div class="modal-footer">
    <button type="submit" id="save-form-2" class="btn btn-success"><i class="fa fa-check"></i>
        @lang('app.save')
    </button>
    <button type="reset" class="btn btn-default">@lang('app.reset')</button>
</div>
{!! Form::close() !!}

<script type="text/javascript">
    
    $('#save-form-2').click(function () {
        $.easyAjax({
            url: '{{route('admin.payments.store')}}',
            container: '#createInvoiceItem',
            type: "POST",
            redirect: true,
            data: $('#createInvoiceItem').serialize()
        })
    });
</script>