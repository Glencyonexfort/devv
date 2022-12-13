@extends('layouts.app')

@section('page-title')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="{{ $pageIcon }} mr-2"></i> <span class="font-weight-semibold">{{ $pageTitle }}</span></h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
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
                    <h6 class="card-title">Cleaning Shifts</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            {!! Form::open(['id'=>'loadData','class'=>'ajax-form','method'=>'POST']) !!}
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Choose Cleaning Job Type</label>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">                                            
                                            <select name="cleaning_job_type" id="cleaning_job_type" class="form-control">
                                                <option selected>Please select.. </option>
                                                @foreach($cleaning_job_types as $data)
                                                <option value="{{ $data->id }}">{{ $data->job_type_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                            <div id="cleaning_shift_table">                                
                            </div>                                                                                  
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
    $(document).ready(function() {
    $('body').on('click', '#add_cleaningshift_btn', function(e) {
        e.preventDefault();
        $(this).hide();
        $("#new_line").css("display", "table-row");
    });    
    $('body').on('click', '#cancel_cleaningshift_btn', function(e) {        
        e.preventDefault();
        $("#add_cleaningshift_btn").show();
        $("#new_line").hide();
    });
    $('body').on('click', '.cancel_cleaningshift_btn', function(e) {
        var id = $(this).data('id');
        e.preventDefault();
        $("#edit_line_"+id).hide();
        $("#row_line_"+id).css("display", "table-row");
    });
    $('body').on('click', '.edit_cleaningshift_btn', function(e) {
        var id = $(this).data('id');
        e.preventDefault();
        $("#row_line_"+id).hide();
        $("#edit_line_"+id).css("display", "table-row");        
    });
    $('body').on('change', '#cleaning_job_type', function(e) {
        e.preventDefault();
        $.easyAjax({
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            url: "/admin/cleaning-settings/ajaxLoadCleaningShifts",
            container: '#loadData',
            type: "POST",
            data: $('#loadData').serialize(),
            success: function(result) {
                $("#cleaning_job_type").css("background-color", "#dceffc");
                $('#cleaning_shift_table').html(result.html);
            }
        })
    });
    // Add New Shift data
    $('body').on('click', '.save_cleaningshift_btn', function(e) {
        e.preventDefault();
        var token = "{{ csrf_token() }}";
        var job_type_id= $("#job_type_id").val();
        var shift_name= $("#cleaning_shift_name").val();
        var shift_display_start_time= $("#cleaning_shift_display_start_time").val();
        $.easyAjax({
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            url: "/admin/cleaning-settings/ajaxSaveCleaningShifts",
            type: "POST",
            data: {'_token':token,'job_type_id':job_type_id,'shift_name':shift_name,'shift_display_start_time':shift_display_start_time},
            success: function(result) {
                if(result.error==0){
                    $('#cleaning_shift_table').html(result.html);
                }else{
                    $.toast({
                        heading: 'Error',
                        text: 'Something went wrong!',
                        icon: 'error',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#fb9678',
                        textColor: 'white'
                    });
                }
            }
        })
    });
    //update Shift data
    $('body').on('click', '.update_cleaningshift_btn', function(e) {
        e.preventDefault();
        var token = "{{ csrf_token() }}";
        var id= $(this).data('id');
        var job_type_id= $("#job_type_id").val();
        var shift_name= $("#cleaning_shift_name_"+id).val();
        var shift_display_start_time= $("#cleaning_shift_display_start_time_"+id).val();

        $.easyAjax({
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            url: "/admin/cleaning-settings/ajaxUpdateCleaningShifts",
            type: "POST",
            data: {'_token':token,'id':id,'job_type_id':job_type_id,'shift_name':shift_name,'shift_display_start_time':shift_display_start_time},
            success: function(result) {
                $('#cleaning_shift_table').html(result.html);
            }
        })
    });
    //delete Shift data
    $('body').on('click', '.delete_cleaningshift_btn', function(e) {
        e.preventDefault();
        var token = "{{ csrf_token() }}";
        var id= $(this).data('id');
        var job_type_id= $("#job_type_id").val();
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted item!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
            $.easyAjax({
                beforeSend: function() {
                    $.blockUI();
                },
                complete: function() {
                    $.unblockUI();
                },
                url: "/admin/cleaning-settings/ajaxDestroyCleaningShifts",
                type: "POST",
                data: {'_token':token,'id':id,'job_type_id':job_type_id},
                success: function(result) {
                    $('#cleaning_shift_table').html(result.html);
                }
            });
        }
    });
    });
});
</script>
@endpush