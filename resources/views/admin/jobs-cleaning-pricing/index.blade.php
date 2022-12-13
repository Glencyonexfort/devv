@extends('layouts.app')

@section('jobscleaningpricing_grid')
@include('admin.jobs-cleaning-pricing.jobscleaningpricing_grid')
@endsection

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

@section('content')

<div class="content">
    <div class="d-md-flex align-items-md-start">
            @include('sections.cleaning_quote_form_settings_menu')
        <div style="flex:auto">
            <div class="card">
                <div class="card-header bg-white header-elements-inline">
                    <h6 class="card-title">@lang('modules.jobsCleaningPricing.boxTitle')</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            

                            <div class="table-responsive">
                                <table class="table table-bordered" id="jobsCleaningPricing-table">
                                    <thead>
                                        <tr>
                                            <th>@lang('modules.jobsCleaningPricing.bedrooms')</th>
                                            <th>@lang('modules.jobsCleaningPricing.bathrooms')</th>
                                            <th>@lang('modules.jobsCleaningPricing.carpeted')</th>
                                            <th>@lang('modules.jobsCleaningPricing.storey')</th>
                                            <th>@lang('modules.jobsCleaningPricing.tax')</th>
                                            <th>@lang('modules.jobsCleaningPricing.priceExclTax')</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody id="jobscleaningpricing_grid">
                                        @yield('jobscleaningpricing_grid')
                                    </tbody>

                                </table>

                            </div>

                            <button type="button" class="btn alpha-primary text-primary-800 btn-icon add_new_jobs_cleaning_pricing_row"><span class="cursor-pointer"><img src="{{ asset('newassets/img/icon-add.png') }}"></span></button>
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('footer-script')

<script>

    $(".add_new_jobs_cleaning_pricing_row").click(function() {
       var rowCount = $('#jobsCleaningPricing-table tr').length;
       $('#jobsCleaningPricing-table').append('<tr id="tr_jobscleaningpricing'+rowCount+'"><td><input type="number" step="any" min="0" name="bedrooms" value="" id="bedrooms_'+rowCount+'" class="form-control" /></td><td><input type="number" step="any" min="0" name="bathrooms" value="" id="bathrooms_'+rowCount+'" class="form-control" /></td><td> <select name="carpet" id="carpet_'+rowCount+'" class="form-control"><option value="Y">Y</option><option value="N">N</option> </select></td><td> <select name="storey" id="storey_'+rowCount+'" class="form-control"><option value="Y">Y</option><option value="N">N</option> </select></td><td> <select name="tax_id" id="tax_id_'+rowCount+'" class="form-control"> @foreach($taxes as $data)<option value="{{$data->id}}">{{$data->tax_name}}</option> @endforeach </select></td><td><input type="text" name="price" value="" id="price_'+rowCount+'" class="form-control" /></td><td> <button type="submit" id="create_jobsCleaningPricing_btn_'+rowCount+'" data-createrowid="'+rowCount+'" class="btn btn-success m-r-10 btn-sm create_jobsCleaningPricing_btn" style="padding: 6px 6px;">Save</button> <button id="delete_row_'+rowCount+'" type="button" onclick="deleteAddedRow('+rowCount+')" class="btn btn-light btn-sm" style="padding: 6px 6px;">Cancel</button></td></tr>');
    });
    
    $('body').on('click', '.jobsCleaningPricing-edit-btn', function(e) {

        e.preventDefault();
        var localmovesid = $(this).data('localmovesid');
        $("#update_jobsCleaningPricing_form_grid_" + localmovesid).removeClass('hidden');
        $("#display_jobsCleaningPricing_form_grid_" + localmovesid).addClass('hidden');
    });

    $('body').on('click', '.jobsCleaningPricing-cancelUpdate-btn', function(e) {

        e.preventDefault();
        var localmovesid = $(this).data('localmovesid');
        $("#update_jobsCleaningPricing_form_grid_" + localmovesid).addClass('hidden');
        $("#display_jobsCleaningPricing_form_grid_" + localmovesid).removeClass('hidden');
    });


    $('body').on('click', '.create_jobsCleaningPricing_btn', function(e) {

        var createrowid = $(this).data('createrowid');

        var bedrooms        = $('#bedrooms_'+createrowid).val();
        var bathrooms       = $('#bathrooms_'+createrowid).val();
        var carpet             = $('#carpet_'+createrowid).val();
        var storey    = $('#storey_'+createrowid).val();
        var price    = $('#price_'+createrowid).val();
        var tax_id    = $('#tax_id_'+createrowid).val();

        $.ajax({
            url: "/admin/cleaning-settings/ajaxCreateJobsCleaningPricing",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}", "bedrooms":bedrooms, "bathrooms":bathrooms, "carpet":carpet, "storey":storey, "price":price, "tax_id":tax_id},
            dataType: "json",
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            success: function(result) {
              
                if (result.error == 0) {
                    //console.log(result.jobsCleaningPricing_html);
                    $('#jobsCleaningPricing-table tbody').html(result.jobsCleaningPricing_html);
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
                } else {
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
            }
        });
    });

    $('body').on('click', '.update_jobsCleaningPricing_btn', function(e) {
        e.preventDefault();
        var updateid = $(this).data('localmovesid');

        var bedrooms        = $('#bedrooms_'+updateid).val();
        var bathrooms       = $('#bathrooms_'+updateid).val();
        var carpet             = $('#carpet_'+updateid).val();
        var storey    = $('#storey_'+updateid).val();
        var price    = $('#price_'+updateid).val();
        var tax_id    = $('#tax_id_'+updateid).val();

        //alert(group_name);
        $.ajax({
            url: "/admin/cleaning-settings/ajaxUpdateJobsCleaningPricing",
            method: 'post',
            data: { "_token": "{{ csrf_token() }}", "updateid":updateid, "bedrooms":bedrooms, "bathrooms":bathrooms, "carpet":carpet, "storey":storey, "price":price, "tax_id":tax_id},
            dataType: "json",
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            success: function(result) {

                if (result.error == 0) {
                    //console.log(result.jobsCleaningPricing_html);
                    //var lead_id = result.id;
                    $('#jobsCleaningPricing-table tbody').html(result.jobsCleaningPricing_html);
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
                } else {
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
            }
        });
    });


    $('body').on('click', '.jobsCleaningPricing-remove-btn', function() {
        var delete_id = $(this).data('localmovesid');
        swal({
            title: "Are you sure?",
            text: "You want to delete this End of Lease Pricing?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#00c292",
            confirmButtonText: "Yes, Confirm!",
            cancelButtonText: "No, Cancel!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm) {
            if (isConfirm) {

                $.ajax({
                    url: "/admin/cleaning-settings/ajaxDestroyInventorDefinition",
                    method: 'post',
                    data: { "_token": "{{ csrf_token() }}", "id":delete_id },
                    dataType: "json",
                    beforeSend: function() {
                        $.blockUI();
                    },
                    complete: function() {
                        $.unblockUI();
                    },
                    success: function(result) {

                        if (result.error == 2) {
                            swal({
                                title: "Info",
                                text: result.message,
                                type: "info",
                                button: "OK",
                            });
                        } else if (result.error == 0) {
                            
                            $('#jobsCleaningPricing-table tbody').html(result.jobsCleaningPricing_html);
                            //Notification....
                            $.toast({
                                heading: 'Success',
                                text: result.message,
                                icon: 'success',
                                position: 'top-right',
                                loader: false,
                                bgColor: '#00c292',
                                textColor: 'white'
                            });
                        }
                    }
                });
            }
        });
    });


    function deleteAddedRow(row_id)
    {
        $('#tr_jobscleaningpricing'+row_id).remove();
    }

</script>

@endpush