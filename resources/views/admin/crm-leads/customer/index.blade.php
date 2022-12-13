@extends('layouts.app')
@section('page-title')

@section('customer_detail')
@include('admin.crm-leads.customer.customer_detail')
@endsection

@push('head-script')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
@endpush
<style>
    .attachment-img {
    width: 24px;
    margin-right: 3px;
    }
    .files input {
        outline: 2px dashed #92b0b3;
        outline-offset: -10px;
        -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
        transition: outline-offset .15s ease-in-out, background-color .15s linear;
        padding: 120px 0px 85px 35%;
        text-align: center !important;
        margin: 0;
        width: 100% !important;
    }
    .files input:focus{     outline: 2px dashed #92b0b3;  outline-offset: -10px;
        -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
        transition: outline-offset .15s ease-in-out, background-color .15s linear; border:1px solid #92b0b3;
    }
    .files{ position:relative}
    .files:after {  pointer-events: none;
        pointer-events: none;
        position: absolute;
        top: 35px;
        left: 240px;
        width: 100%;
        content: "\f0ee";
        font-family: FontAwesome;
        display: block;
        margin: 0 auto;
        background-size: 100%;
        background-repeat: no-repeat;
        font-size: 80px;
        opacity: 0.4;
    }
    .color input{ background-color:#f1f1f1;}
    .files:before {
        position: absolute;
        bottom: 0px;
        font-size: 22px;
        left: 0;  pointer-events: none;
        width: 100%;
        right: 0;
        height: 57px;
        content: " or drag it here. ";
        display: block;
        margin: 0 auto;
        color: #999;
        font-weight: 600;
        text-align: center;
    }
    .attachment-link{
        font-size: 16px;
        color: #f44336!important;
        font-weight: 400;
    }
</style>
<div class="page-header page-header-light view_blade_page_header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex view_blade_page_padding">
            <h4>
            <img class="view_blade_page_img_header" src="../../../../../newassets/img/statistics (1)@2x.png">
                <span class="view_blade_page_span_header">{{ $crmlead->lead_type.' Customer' }} </span>
        </div>
    </div>
    <!-- <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> @lang('app.menu.home')</a>
                <a href="content_page_header.html" class="breadcrumb-item">&nbsp;</a>
                <span class="breadcrumb-item active">{{ $pageTitle }}</span>
            </div>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div> -->
</div>
@endsection

@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/tagify-master/dist/tagify.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
    
@endpush

@section('content')
<div class="row">
    <div class="col-12 col-lg-3 px-2">        
        <div class="text-left lead_title">
            <h4 class="font-weight-semibold mb-1 view_blade_1_person_name">{{ $crmlead->name }}
            <p>
                @foreach($crmleadstatuses as $st)
                    @if($st->lead_status == $crmlead->lead_status)
                        {{  $st->lead_status }}
                    @endif
                @endforeach
            </p>
            </h4>            
        </div>

        <div class="card view_blade_4_card">
                <span class="view_blade_4_card_span">
                    <div class="card-header header-elements-inline view_blade_4_card_header">
                        <h6 class="card-title card-title-mg view_blade_4_card_task">Contacts <b id="contactsCount">{{ $totalContacts }}</b></h6>
                        <div class="header-elements">
                            <div class="list-icons">
                                <span class="cursor-pointer add_new_contact_btn">
                                    <img src="{{ asset('newassets/img/icon-add.png') }}">
                                </span>
                            </div>
                        </div>
                    </div>
                </span>

                <div id="add_new_contact_form_grid" class="card-body light-blue-bg p10 hidden body_margin">
                    <form id="add_new_contact_form" class="custom-form" action="#">
                        @csrf
                        {{ Form::hidden('lead_id', $lead_id) }}                        
                        <div class="form-group">
                            <label>Name</label>
                            <input name="name" type="text" class="form-control" placeholder="">
                        </div>

                        <div class="form-group">
                            <label>Title</label>
                            <input name="description" type="text" class="form-control" placeholder="">
                        </div>

                        <div class="form-group">
                            <label>Contact Detail</label>
                            <div class="input-group contact_detail_div mgb-10">
                                <input name="contact_detail[]" type="text" class="form-control contact_detail" placeholder="Phone, email or URL" autocomplete="false">
                                <div class="input-group-append">                                    
                                    <select name="contact_detail_type[]" class="form-control form-control-uniform">
                                        @foreach($contact_types as $data)
                                            <option value="{{ $data->list_option }}">{{ $data->list_option }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-start align-items-center m-t-10">
                            <button type="reset" class="btn btn-light add_new_contact_btn">Cancel</button>
                            <button id="create_contact_btn" type="button" class="btn bg-blue ml-3">Save</button>
                        </div>

                    </form>
                </div>
                <div id="contacts_grid" class="card-body p10 view_blade_4_card_body_contact">
                    @include('admin.crm-leads.customer.contact_grid')
                </div>
            </div>
        </div>

    <!-- Right content -->
    <div class="col-12 col-lg-9 px-2 pt-2">   
        <ul class="nav nav-tabs view_blade_5_navs_tabbs_box_shadow nobackground">
            <li class="nav-item noborder"><a href="#jobs_opportunities_tab" class="nav-link active view_blade_5_navs_tabbs_nav_item_link" data-toggle="tab">Jobs/Opportunities</a></li>

            <li class="nav-item noborder"><a href="#customer_detail_tab" class="nav-link view_blade_5_navs_tabbs_nav_item_link" data-toggle="tab">Customer Details</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="jobs_opportunities_tab">
                <input type="hidden" id="lead_id" name="lead_id" value="{{ $lead_id }}">
                <div class="row">
                    <div class="table-responsive">
                        <h4>Jobs</h4>
                        <table class="table table-bordered table-hover toggle-circle default footable-loaded footable" id="listing-table-jobs" style="">
                            <thead>
                                <tr>
                                    <th>Job #</th>
                                    <th>Name</th>
                                    <th>Created</th>
                                    <th>Job Date</th>
                                    <th>Pickup Suburb</th>
                                    <th>Drop Off Suburb</th>
                                    <th>Job Status</th>
                                    <th>Payment Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="row pt-3" style="border-top: 1px solid #ccc;">
                    <div class="table-responsive">
                        <a href="/admin/crm/view-opportunity/{{ $lead_id }}" class="btn btn-sm bg-blue ml-3 pull-right">New Opportunity</a>
                        <h4>Opportunities</h4>
                        <table class="table table-bordered table-hover toggle-circle default footable-loaded footable" id="listing-table-opportunities" style="">
                            <thead>
                                <tr>
                                    <th>Opp #</th>
                                    <th>Name</th>
                                    <th>Created</th>
                                    <th>Job Date</th>
                                    <th>Pickup Suburb</th>
                                    <th>Drop Off Suburb</th>
                                    <th>Status</th>
                                    <th>Company</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <!-- /timeline -->

            </div>
            <div class="tab-pane" id="customer_detail_tab">
                @yield('customer_detail')
            </div>
        </div>
    </div>
</div>
</div> 
<style>
    .modal {
        background: transparent; // example
        }
</style>
{{-- Copy Job Popup Start --}}
<div id="copy-job-popup" class="modal fade" tabindex="-1">
    <div id="copy-job-popup-inner" class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="border: 1px solid #f0f0f0;padding-bottom:10px">
                <span style="font-size:18px;font-weight: 400;">Copy Job</span>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
        
            <form id="copy-create-job" method="post">
                @csrf
            <div class="modal-body">
                    <div class="form-body">                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label id="copy-job-name" style="font-size:14px">No Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label id="copy-job-email" style="font-size:14px"></label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label id="copy-job-mobile" style="font-size:14px"></label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pickup Address</label>
                                    <input id="copy_job_pickup_address" type="text" name="copy_job_pickup_address" class="form-control copy_job_address_popup" value="" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Drop off Address</label>
                                    <input id="copy_job_drop_off_address" type="text" name="copy_job_drop_off_address" class="form-control copy_job_address_popup" value="">
                                </div>
                            </div>
                            <input class="copy_job_pickup_address_only" name="job_pickup_address_only" type="hidden" value="">
                            <input class="copy_job_pickup_suburb" name="job_pickup_suburb" type="hidden" value="" />
                            <input class="copy_job_pickup_post_code" name="job_pickup_post_code" type="hidden" value=""/>
                            <input class="copy_job_drop_off_address_only" name="job_drop_off_address_only" type="hidden" value="">
                            <input class="copy_job_delivery_suburb" name="job_delivery_suburb" type="hidden" value=""/>
                            <input class="copy_job_drop_off_post_code" name="job_drop_off_post_code" type="hidden" value=""/>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Estimated Job Date</label>
                                    <div class="input-group">
                                        <span class="input-group-prepend">
                                            <span class="input-group-text"><i class="icon-calendar52"></i></span>
                                        </span>
                                        <input name="est_job_date" type="text" class="form-control daterange-single" value="{{ date('d/m/Y') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group companies">
                                    <label style="font-size:14px">Company</label>
                                    <select name="company_id" class="form-control">
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}" @if ($company->id) selected @endif>{{ $company->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-size:14px">Job Type</label>
                                    <select name="op_type" class="form-control op_job_type_field">
                                        <option value="Moving">Moving</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>                    
                    <input type="hidden" id="copy-job-job_id" name="job_id" value="">
                    <input type="hidden" id="copy-job-lead_id" name="lead_id" value="">
            </div>
            <div class="modal-footer" style="background-color: #f5f5f5!important;padding: 10px 20px!important;">
                <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success create-new-job">Copy Job</button>
            
            </div>
            </form>
        </div>
    </div>
</div>
{{-- Copy Job Popup End --}}
{{-- Copy Opportunity Popup Start --}}
<div id="copy-opportunity-popup" class="modal fade" tabindex="-1" style="margin: 10px">
    <div id="copy-opportunity-popup-inner" class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="border: 1px solid #f0f0f0;padding-bottom:10px">
                <span style="font-size:18px;font-weight: 400;">Copy Opportunity</span>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
        
            <form id="copy-create-opportunity" method="post">
                @csrf
            <div class="modal-body">
                    <div class="form-body">                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label id="copy-opportunity-name" style="font-size:14px">No Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label id="copy-opportunity-email" style="font-size:14px"></label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label id="copy-opportunity-mobile" style="font-size:14px"></label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pickup Address</label>
                                    <input id="copy_opportunity_pickup_address" type="text" name="copy_opportunity_pickup_address" class="form-control copy_job_address_popup" value="" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Drop off Address</label>
                                    <input id="copy_opportunity_drop_off_address" type="text" name="copy_opportunity_drop_off_address" class="form-control copy_job_address_popup" value="">
                                </div>
                            </div>
                            <input class="copy_opportunity_pickup_address_only" name="opportunity_pickup_address_only" type="hidden" value="">
                            <input class="copy_opportunity_pickup_suburb" name="opportunity_pickup_suburb" type="hidden" value="" />
                            <input class="copy_opportunity_pickup_post_code" name="opportunity_pickup_post_code" type="hidden" value=""/>
                            <input class="copy_opportunity_drop_off_address_only" name="opportunity_drop_off_address_only" type="hidden" value="">
                            <input class="copy_opportunity_delivery_suburb" name="opportunity_delivery_suburb" type="hidden" value=""/>
                            <input class="copy_opportunity_drop_off_post_code" name="opportunity_drop_off_post_code" type="hidden" value=""/>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Estimated Job Date</label>
                                    <div class="input-group">
                                        <span class="input-group-prepend">
                                            <span class="input-group-text"><i class="icon-calendar52"></i></span>
                                        </span>
                                        <input name="est_job_date" type="text" class="form-control daterange-single" value="{{ date('d/m/Y') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group companies">
                                    <label style="font-size:14px">Company</label>
                                    <select name="company_id" class="form-control">
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-size:14px">Job Type</label>
                                    <select name="op_type" class="form-control op_job_type_field">
                                        <option value="Moving">Moving</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>                    
                    <input type="hidden" id="copy-opportunity-job_id" name="job_id" value="">
                    <input type="hidden" id="copy-opportunity-lead_id" name="lead_id" value="">
                    <input type="hidden" id="copy-opportunity-opportunity_id" name="opportunity_id" value="">
            </div>
            <div class="modal-footer" style="background-color: #f5f5f5!important;padding: 10px 20px!important;">
                <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success create-new-opportunity">Copy Opportunity</button>
            
            </div>
            </form>
        </div>
    </div>
</div>
{{-- Copy Opportunity Popup End --}}
@endsection

@push('footer-script')
        

    <script src="{{ asset('plugins/tagify-master/dist/tagify.js') }}"></script> <!-- Theme JS files -->
    <script src="{{ asset('newassets/global_assets/js/plugins/forms/selects/select2.min.js') }}"></script>
    <script src="{{ asset('newassets/global_assets/js/plugins/editors/summernote/summernote.min.js') }}"></script>
	<script src="{{ asset('newassets/global_assets/js/plugins//forms/styling/uniform.min.js') }}"></script>    
    <script src="{{ asset('newassets/global_assets/js/demo_pages/picker_date.js') }}"></script>
    <script src="{{ asset('js/ajax-requests/ajax-requests.js') }}"></script>
    <script src="{{ asset('js/ajax-requests/ajax-requests-storage.js') }}"></script>    
    <script src="{{ asset('js/ajax-requests/ajax-requests-activity.js') }}"></script>    
    <script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>

    <script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
    <script src="{{ asset('newassets/global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    
    <script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    @if($global->locale == 'en')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.{{ $global->locale }}-AU.min.js"></script>
    @elseif($global->locale == 'pt-br')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.pt-BR.min.js"></script>
    @else
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.{{ $global->locale }}.min.js"></script>
    @endif
    <script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('newassets/global_assets/js/plugins/forms/selects/bootstrap_multiselect.js') }}"></script>
    <script src="{{ asset('newassets/global_assets/js/demo_pages/form_multiselect.js') }}"></script>
    <script>
        $(function() {
        loadTableJobs();
        loadTableOpportunities(); 
        
    });
        function initialize1() {
        var options = {
            fields: ["address_components", "geometry"],
            types: ['address'],
            componentRestrictions: {
                country: "au"
            }
        };
        
            
            var allDepotInputs = document.getElementsByClassName('copy_job_address_popup');
            var autocompletes = [];
            for (var i = 0; i < allDepotInputs.length; i++) {
                // console.log(allDepotInputs[i]);
                var autocomplete = new google.maps.places.Autocomplete(allDepotInputs[i], options);
                autocomplete.inputId = allDepotInputs[i].id;
                autocomplete.addListener('place_changed', fillInFieldsCopyJob);
                autocompletes.push(autocomplete);
            }        
    
        }

        function fillInFieldsCopyJob() {
            var elemId = this.inputId;
            var place = this.getPlace();
            var address = '';
            var full_address = '';
            var suburb = '';
            var postal_code='';
            //Set Postcode field
            $.each(place.address_components, function( key, value ) {
                if(value.types[0]=="street_number"){
                    address=address+' '+value.long_name;
                    full_address=full_address+' '+value.long_name;
                }
                if(value.types[0]=="route"){
                    address=address+' '+value.short_name;
                    full_address=full_address+' '+value.short_name;
                }
                if(value.types[0]=="locality"){
                    // address=address+' '+value.long_name;
                    suburb=value.long_name;
                    full_address=full_address+' '+value.long_name;
                }
                if(value.types[0]=="administrative_area_level_1"){
                    // address=address+' '+value.short_name;
                    suburb=suburb+' '+value.short_name;
                    full_address=full_address+' '+value.short_name;
                }
                if(value.types[0]=="postal_code"){
                    postal_code=value.long_name;
                }
            });

            if(elemId=="copy_job_pickup_address")
            {
                $("#copy_job_pickup_address").val(full_address);
                $(".copy_job_pickup_address_only").val(address);
                $(".copy_job_pickup_suburb").val(suburb);
                $(".copy_job_pickup_post_code").val(postal_code);

            }
            else if(elemId=="copy_job_drop_off_address")
            {
                $('#copy_job_drop_off_address').val(full_address);
                $('.copy_job_drop_off_address_only').val(address);
                $('.copy_job_delivery_suburb').val(suburb);
                $('.copy_job_drop_off_post_code').val(postal_code);
            }
            else if(elemId=="copy_opportunity_pickup_address")
            {
                $("#copy_opportunity_pickup_address").val(full_address);
                $(".copy_opportunity_pickup_address_only").val(address);
                $(".copy_opportunity_pickup_suburb").val(suburb);
                $(".copy_opportunity_pickup_post_code").val(postal_code);
            }
            else if(elemId=="copy_opportunity_drop_off_address")
            {
                $('#copy_opportunity_drop_off_address').val(full_address);
                $('.copy_opportunity_drop_off_address_only').val(address);
                $('.copy_opportunity_delivery_suburb').val(suburb);
                $('.copy_opportunity_drop_off_post_code').val(postal_code);
            }
        }        
    
        google.maps.event.addDomListener(window, 'load', initialize1);  
        document.addEventListener('DOMNodeInserted', function(event) {
            // console.log(event);
    
        });
        $(document).ready(function() {
            $('.summernote').summernote({
            placeholder: 'Hello stand alone ui',
            tabsize: 2,
            height: 120,
            toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
        });
        $('a').each(function(){
            var s = $(this).clone().wrap('<p>').parent().html();
        });
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function() {
            new Switchery($(this)[0], $(this).data());
        });

        $('.summernote').summernote({
            height: 200, // set editor height
            minHeight: null, // set minimum height of editor
            maxHeight: null, // set maximum height of editor
            focus: true, // set focus to editable area after initializing summernote
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']],
            ]

        });
        $('#save-form').click(function() {
            $.easyAjax({
                url: "{{ route('admin.email-templates.store') }}",
                container: '#createTemplate',
                type: "POST",
                redirect: true,
                data: $('#createTemplate').serialize()
            })
        });    
    //(... rest of your JS code)
    $(document).on('change', '#lead_type', function() {
        if($(this).val()=="Commercial"){
            $("#commercial_div").show();
        }else{
            $("#commercial_div").hide();
        }
    });
    $(document).on('click', '#update_customer_detail_btn', function() {
        var url = "{{ route('admin.crm-leads.ajax-save-customer-detail') }}";
        var token = "{{ csrf_token() }}";
        
        $.easyAjax({
            type: 'POST',
            url: url,
            data: $("#customer_detail_form").serialize(),
            success: function(response) {
                if (response.error == 0) {
                    // $("#lead_status_view").toggle();
                    // $("#lead_status_form").toggle();
                    $.toast({
                        heading: 'Updated',
                        text: response.message,
                        icon: 'success',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#00c292',
                        textColor: 'white'
                    });
                }else{
                    //Notification...   
                    $.toast({
                        heading: 'Error',
                        text: 'Something went wrong!',
                        icon: 'error',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#fb9678',
                        textColor: 'white'
                    });
                    //..
                }
            }
        });
    });
    //Delete contact
    $('body').on('click', '.contact-remove-btn', function () {
            var id = $(this).data('contactid');
            var leadid = $(this).data('leadid');
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover the deleted contact!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    // var url = "{{ route('admin.clients.destroy',':id') }}";
                    // url = url.replace(':id', id);
                var token = "{{ csrf_token() }}";
                    $.ajax({
                url: "/admin/crm/crm-leads/ajaxDestroyContact",
                method: 'post',
                data: {'_token': token, 'contact_id': id,'lead_id':leadid},
                dataType: "json",
                beforeSend: function () {
                    $.blockUI();
                },
                complete: function () {
                    $.unblockUI();
                },
                success: function (result) {
                    if (result.error == 0) {
                        $('#contacts_grid').html(result.contact_html);
                        $("#contactsCount").html(result.contact_count);
                        //Notification....
                        $.toast({
                            heading: 'Deleted',
                            text: result.message,
                            icon: 'success',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#00c292',
                            textColor: 'white'
                        });
                        //..
                    }
                }
            });
                }
            });
        });
    //end:: delete contact
    //START:: Start Copy Job
    $('body').on('click', '.create-new-job', function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('admin.crm-leads.ajaxSaveJob') }}",
            method: 'POST',
            data: $('#copy-create-job').serialize(),
            dataType: "json",
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            success: function(result) {
                if (result.error == 0) 
                {
                    location.reload(true);
                    // Notification....
                    $.toast({
                        heading: 'Success',
                        text: result.message,
                        icon: 'success',
                        position: 'top-right',
                        loader: false,        
                        bgColor: '#00c292', 
                        textColor: 'white'
                    });
                    //..
                } 
                else 
                {
                    //Notification....
                    $.toast({
                        heading: 'Error',
                        text: 'Something went wrong!',
                        icon: 'error',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#fb9678',
                        textColor: 'white'
                    });
                    //..
                }
            },
            error: function(xhr) { 
                
            }
        });
    });
    //END:: End Copy Job
    //START:: Start Copy Opportunity
    $('body').on('click', '.create-new-opportunity', function(e) {
        e.preventDefault();
        var lead_id = $(this).data('lead_id');
        var job_id = $(this).data('job_id');
        $.ajax({
            url: "{{ route('admin.crm-leads.ajaxSaveOpportunity') }}",
            method: 'POST',
            data: $('#copy-create-opportunity').serialize(),
            dataType: "json",
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            success: function(result) 
            {
                if (result.error == 0) 
                {
                    location.reload(true);
                    // Notification....
                    $.toast({
                        heading: 'Success',
                        text: result.message,
                        icon: 'success',
                        position: 'top-right',
                        loader: false,        
                        bgColor: '#00c292', 
                        textColor: 'white'
                    });
                    //..
                } 
                else 
                {
                    //Notification....
                    $.toast({
                        heading: 'Error',
                        text: 'Something went wrong!',
                        icon: 'error',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#fb9678',
                        textColor: 'white'
                    });
                    //..
                }
            },
            error: function(xhr) { 
                
            }
        });
    });
    //END:: End Copy Opportunity

    function loadTableJobs() {
            var lead_id = $('#lead_id').val();

            table = $('#listing-table-jobs').dataTable({
                "pageLength": 5,
                searching: false,
                paging: true,
                info: true,
                bLengthChange: false,
                destroy: true,
                //                                    responsive: true,
                processing: true,
                order: [], //Initial no order.
                aaSorting: [],
                serverSide: true,
                scrollX: true,
                ajax: '{!! route('admin.crm-leads.ajaxGetCustomerJobData') !!}?lead_id=' + lead_id,
                language: {
                    "url": "<?php echo __("app.datatable") ?>"
                },
                "fnDrawCallback": function(oSettings) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });

                    var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                    };

                    var count = $('#listing-table-jobs').DataTable().page.info().recordsTotal;
                    $('#totalCount').html(count);

                    var sum = $('#listing-table-jobs').DataTable().column(1).data()
                    .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                    }, 0 );
                    sum = sum.toFixed(2);
                    var nf = new Intl.NumberFormat();
                    //$('#totalVal').html(nf.format(sum));
                },
                columns: [
                    {
                        data: 'job_number',
                        name: 'job_number',
                        width: '6%'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        width: '14%'
                    },
                    {
                        data: 'created',
                        name: 'created',
                        width: '14%'
                    },
                    {
                        data: 'job_date',
                        name: 'job_date',
                        width: '10%'
                    },
                    {
                        data: 'pickup_suburb',
                        name: 'pickup_suburb',
                        width: '12%'
                    },
                    {
                        data: 'drop_off_suburb',
                        name: 'drop_off_suburb',
                        width: '12%'
                    },
                    {
                        data: 'job_status',
                        name: 'job_status',
                        width: '8%'
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status',
                        width: '12%'
                    },
                    { 
                        data: 'action',
                        name: 'action', 
                        width: '4%' 
                    },
                ]
            });
        }
        function loadTableOpportunities() {
            var lead_id = $('#lead_id').val();

            table = $('#listing-table-opportunities').dataTable({
                "pageLength": 5,
                searching: false,
                paging: true,
                info: true,
                bLengthChange: false,
                destroy: true,
                //                                    responsive: true,
                processing: true,
                order: [], //Initial no order.
                aaSorting: [],
                serverSide: true,
                scrollX: true,
                ajax: '{!! route('admin.crm-leads.ajaxGetCustomerOpportunityData') !!}?lead_id=' + lead_id,
                language: {
                    "url": "<?php echo __("app.datatable") ?>"
                },
                "fnDrawCallback": function(oSettings) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });

                    var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                    };

                    var count = $('#listing-table-opportunities').DataTable().page.info().recordsTotal;
                    $('#totalCount').html(count);

                    var sum = $('#listing-table-opportunities').DataTable().column(1).data()
                    .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                    }, 0 );
                    sum = sum.toFixed(2);
                    var nf = new Intl.NumberFormat();
                    //$('#totalVal').html(nf.format(sum));
                },
                columns: [
                    {
                        data: 'job_number',
                        name: 'job_number',
                        width: '6%'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        width: '14%'
                    },
                    {
                        data: 'created',
                        name: 'created',
                        width: '10%'
                    },
                    {
                        data: 'job_date',
                        name: 'job_date',
                        width: '10%'
                    },
                    {
                        data: 'pickup_suburb',
                        name: 'pickup_suburb',
                        width: '12%'
                    },
                    {
                        data: 'drop_off_suburb',
                        name: 'drop_off_suburb',
                        width: '12%'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        width: '6%'
                    },
                    {
                        data: 'company',
                        name: 'company',
                        width: '8%'
                    },
                    { 
                        data: 'action',
                        name: 'action', 
                        width: '4%' 
                    },
                ]
            });
        }
        $('body').on('click', '.copy-job-btn', function() {
            var job_id = $(this).data('job_id');
            var lead_id = $(this).data('lead_id');
            var _token = $('input[name="_token"]').val();
            // alert(job_id);
            // alert(lead_id);
            $.ajax({
                url: "{{ route('admin.crm-leads.ajaxJobPopupData') }}",
                method: 'GET',
                data: {
                    '_token': _token,
                    'job_id': job_id,
                    'lead_id': lead_id,
                },
                dataType: "json",
                beforeSend: function() {
                    $.blockUI();
                },
                complete: function() {
                    $.unblockUI();
                },
                success: function(result) 
                {
                    if (result.error == 0) 
                    {
                        $('#copy-job-popup').modal('show');
                        $("#copy-job-popup-inner").addClass("popup-shadow");
                        $('#copy-job-popup').modal('show');
                        $('#copy-job-popup').css("opacity","1");
                        $('#copy-job-popup').css('top', "100px");
                        $('#copy-job-name').text(result.data.crmlead.name);
                        $('#copy-job-email').text(result.data.email);
                        $('#copy-job-mobile').text(result.data.mobile);
                        $('#copy_job_pickup_address').val(result.data.job.pickup_address+' '+result.data.job.pickup_suburb);
                        $('.copy_job_pickup_address_only').val(result.data.job.pickup_address);
                        $('#copy_job_drop_off_address').val(result.data.job.drop_off_address+' '+result.data.job.delivery_suburb);
                        $('.copy_job_drop_off_address_only').val(result.data.job.drop_off_address);
                        $("div.companies select").val(result.data.job.company_id).change();
                        $('.copy_job_pickup_suburb').val(result.data.job.pickup_suburb);
                        $('.copy_job_pickup_post_code').val(result.data.job.pickup_post_code);
                        $('.copy_job_delivery_suburb').val(result.data.job.delivery_suburb);
                        $('.copy_job_drop_off_post_code').val(result.data.job.drop_off_post_code);
                        $('#copy-job-lead_id').val(result.data.lead_id);
                        $('#copy-job-job_id').val(result.data.job_id);
                    } 
                    else 
                    {
                        //Notification....
                        $.toast({
                            heading: 'Error',
                            text: 'Something went wrong!',
                            icon: 'error',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#fb9678',
                            textColor: 'white'
                        });
                        //..
                    }
                },
                error: function(xhr) { 
                    
                }
            });
        });
        $("body").off('click', '.copy-opportunity-btn').on('click', '.copy-opportunity-btn', function() {
            var job_id = $(this).data('job_id');
            var lead_id = $(this).data('lead_id');
            var opportunity_id = $(this).data('opportunity_id');
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url: "{{ route('admin.crm-leads.ajaxOpportunityPopupData') }}",
                method: 'GET',
                data: {
                    '_token': _token,
                    'job_id': job_id,
                    'lead_id': lead_id,
                    'opportunity_id': opportunity_id,
                },
                dataType: "json",
                beforeSend: function() {
                    $.blockUI();
                },
                complete: function() {
                    $.unblockUI();
                },
                success: function(result) 
                {
                    if (result.error == 0) 
                    {
                        $("#copy-opportunity-popup-inner").addClass("popup-shadow");
                        $('#copy-opportunity-popup').modal('show');
                        $('#copy-opportunity-popup').css("opacity","1");
                        $('#copy-opportunity-popup').css('top', "100px");
                        // update data
                        $('#copy-opportunity-name').text(result.data.crmlead.name);
                        $('#copy-opportunity-email').text(result.data.email);
                        $('#copy-opportunity-mobile').text(result.data.mobile);
                        $('#copy_opportunity_pickup_address').val(result.data.opportunity.pickup_address+' '+result.data.opportunity.pickup_suburb);
                        $('.copy_opportunity_pickup_address_only').val(result.data.opportunity.pickup_address);
                        $('#copy_opportunity_drop_off_address').val(result.data.opportunity.drop_off_address+' '+result.data.opportunity.delivery_suburb);
                        $('.copy_opportunity_drop_off_address_only').val(result.data.opportunity.drop_off_address);
                        $("div.companies select").val(result.data.opportunity.company_id).change();
                        $('.copy_opportunity_pickup_suburb').val(result.data.opportunity.pickup_suburb);
                        $('.copy_opportunity_pickup_post_code').val(result.data.opportunity.pickup_post_code);
                        $('.copy_opportunity_delivery_suburb').val(result.data.opportunity.delivery_suburb);
                        $('.copy_opportunity_drop_off_post_code').val(result.data.opportunity.drop_off_post_code);
                        $('#copy-opportunity-lead_id').val(result.data.lead_id);
                        $('#copy-opportunity-job_id').val(result.data.job_id);
                        $('#copy-opportunity-opportunity_id').val(result.data.opportunity_id);
                    } 
                    else 
                    {
                        //Notification....
                        $.toast({
                            heading: 'Error',
                            text: 'Something went wrong!',
                            icon: 'error',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#fb9678',
                            textColor: 'white'
                        });
                        //..
                    }
                },
                error: function(xhr) { 
                    
                }
            });
        });
</script>
@endpush