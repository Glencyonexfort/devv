<div class="card">
<div class="card-body p10 body_margin">
    <form id="customer_detail_form" class="custom-form" action="#">
        @csrf
        <input name="lead_id" type="hidden" value="{{ $crmlead->id }}"/>
        <input name="customer_detail_id" type="hidden" value="{{ $customer_detail->id }}"/>
        <div class="form-body">                        
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Customer Name</label>
                        <input type="text" name="lead_name" class="form-control" value="{{ $crmlead->name }}"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Customer Type</label>
                        <select id="lead_type" name="lead_type" class="form-control">
                            <option value="Commercial" 
                            @if($crmlead->lead_type == "Commercial")
                                selected=""
                                @endif
                                >Commercial</option>
                            <option value="Residential"
                            @if($crmlead->lead_type == "Residential")
                                selected=""
                                @endif
                                >Residential</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="from-group">
                        <label>Customer Status</label>
                        <select name="lead_status" class="form-control">
                            @foreach($crmleadstatuses as $st)
                            <option value="{{ $st->lead_status }}" {{ $st->lead_status == $crmlead->lead_status ? "selected" : "" }}>{{ $st->lead_status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="billing_address" class="form-control" value="{{ $customer_detail->billing_address }}"/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Suburb</label>
                        <input type="text" name="billing_suburb" class="form-control" value="{{ $customer_detail->billing_suburb }}"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Postcode</label>
                        <input type="text" name="billing_post_code" class="form-control" value="{{ $customer_detail->billing_post_code }}"/>
                    </div>
                </div>
            </div>
                <hr/>
                <div id="commercial_div" class="{{($crmlead->lead_type == 'Commercial')?'':'hidden'}}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Account Number</label>
                            <input type="text" name="account_number" class="form-control" value="{{ $customer_detail->account_number }}"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Invoice Terms</label>
                            <input type="text" name="invoice_terms" class="form-control" value="{{ $customer_detail->invoice_terms }}"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Payment Instructions</label>
                            <textarea name="payment_instructions" rows="5" class="summernote form-control" autocomplete="nope">
                                {{ $customer_detail->payment_instructions }}
                            </textarea>
                        </div>
                    </div>
                </div>
        </div>
        </div>
        
        <div class="d-flex justify-content-start align-items-center m-t-10">
            <button type="button" id="update_customer_detail_btn" class="btn btn-outline btn-success btn-sm">Update</button>
        </div>

    </form>
</div> 
</div>