{!! Form::open(['url' => '/admin/finance/all-invoices/update-invoice-item'],['id'=>'editInvoiceItem','class'=>'ajax-form','method'=>'POST']) !!}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title"> <i class="fa fa-search"></i>  @lang('modules.payments.editInvoiceItem')</h4>
</div>
<div class="modal-body">
    <div class="form-body">
        <div class="row">
            <input type="hidden" name="invoice_id" id="invoice_id" value="{{$invoiceItem->invoice_id}}">
            <input type="hidden" name="item_id" id="item_id" value="{{$invoiceItem->id}}">


            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('modules.invoices.invoiceItem')</label>
                    <input type="text" required name="invoice_item"  value="{{$invoiceItem->item_name}}" id="invoice_item" class="form-control">
                </div>
            </div>

            <!--/span-->
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('modules.invoices.quantity')</label>
                    <input type="number" value="{{$invoiceItem->quantity}}" name="quantity" id="quantity" class="form-control" min="1">
                </div>
            </div>
            <!--/span-->

            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('modules.invoices.unitPrice')</label>
                    <input type="number" value="{{$invoiceItem->unit_price}}" name="unit_price" id="unit_price" class="form-control" min="0.01" step="0.01">
                   
                </div>
            </div>
            <!--/span-->

            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label">@lang('app.description')</label>
                    <textarea id="description" name="description" class="form-control">{{$invoiceItem->item_summary}}</textarea>
                </div>
            </div>
        </div>
        <!--/row-->
    </div>
</div>
<div class="modal-footer">
    <button type="submit" id="save-form-edit-item" class="btn btn-success"><i class="fa fa-check"></i>
        @lang('app.save')
    </button>
    <button type="reset" class="btn btn-default">@lang('app.reset')</button>
</div>
{!! Form::close() !!}

<script type="text/javascript">
    
    $('#save-form-edit-item').click(function () {
        $.easyAjax({
            url: '{{route('admin.payments.store')}}',
            container: '#editInvoiceItem',
            type: "POST",
            redirect: true,
            data: $('#editInvoiceItem').serialize()
        })
    });
</script>