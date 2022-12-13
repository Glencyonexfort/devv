@extends('layouts.app')
@section('data_grid')
@include('admin.crm-activity.grid')
@endsection
@section('page-title')
<div class="page-header page-header-light view_blade_page_header">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex view_blade_page_padding">
                <h4>
                <i class="icon-envelop2" style="font-size: 28px"></i>
                    <span class="view_blade_page_span_header">{{ $pageTitle }} </span>
            </div>
        </div>
    </div>
@endsection
<style>
    table{
        margin-bottom: 0px!important;
    }
    td,th{
        border-left: 0px!important;
        border-right: 0px!important;
    }
    th{
        background-color: #fff!important;
        color: #000!important;
        border-bottom: 1px solid #999!important;
    }
    td{
        font-size: 13px!important;
        color: #000!important;
    }
    .pagetitle{
        float: left;
        margin: 10px 26px;
        font-size: 18px;
        font-weight: 600;
    }
    .type-icon{
        border-radius: 50%;
        padding: 4px 11px;
        margin-right: 10px;
    }
    .link{
        color: #3781b8;
        font-weight: 500;
    }
    .tablefilter{        
        color: #3781b8;
        font-size: 14px;
        font-weight: 500;
        padding: 4px 10px;
        border-radius: 4px;
        cursor: pointer;
    }
    .tablefilter:hover{
        color: #000;
    }
    .tablefilter.active{
        color: #fff;
        background-color: #3781b8;
    }
    #tablefilter-select{
        padding: 4px 10px;
    font-weight: 500;
    font-size: 14px;
    }
    .dataTables_length label{
        display: flex!important;
        width: 200px!important;
    }
</style>
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12 text-right">
                    <p class="pagetitle">Inbox (<span id="inbox_count">{{ count($activities) }}</span>)</p>
                    <a href="javascript:void(0)" class="pagetitle btn btn-success btn-sm mt-1" id="MarkAsRead" style="color: #fff">Mark as Read</a>
                <div class="justify-content-end" style="margin: 10px 0;display: flex;">
                    <div id="filteration">
                        <span class="tablefilter active mr-2" data-filter="email">Emails</span>
                        <span class="tablefilter mr-2" data-filter="sms">SMS</span>
                        <span class="tablefilter mr-2" data-filter="task">Tasks</span>
                    </div>
                <span class="align-top mr-2">
                    <select id="tablefilter-select">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}"
                                @if($user->id==auth()->user()->id) selected=""
                                @endif
                                >{{ $user->name }}</option>
                        @endforeach
                    </select>
                </span>
            </div>
            </div>
            </div>
            <div class="card">
                <div id="grid_data">
                    {{-- grid data --}}
                    @yield('data_grid')
            </div>
        </div>


    </div>

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
@if($global->locale == 'en')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.{{ $global->locale }}-AU.min.js"></script>
@elseif($global->locale == 'pt-br')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.pt-BR.min.js"></script>
@else
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.{{ $global->locale }}.min.js"></script>
@endif
<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('js/datatable_custom_pagination.js') }}"></script>
<script>

$(document).ready(function() {

    //START:: Get Filtered Data
    $('body').on('click', '.tablefilter', function(e) {
        e.preventDefault();
        var token = "{{ csrf_token() }}";
        var filter = $(this).data('filter');
        var user = $("#tablefilter-select").children("option:selected").val();       
        $('.tablefilter').removeClass('active');
        $(this).addClass('active');
        $.ajax({
            url: "/admin/getActivityData",
            method: 'post',
            data: {'_token': token, 'filter': filter,'user':user},
            dataType: "json",
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            success: function(result) {
                //console.log(result.message);
                if (result.error == 0) {
                    $("#grid_data").html(result.html);
                    $("#inbox_count").text(result.count);
                    $("#allActvities").text(result.allCount);
                    DatatableResponsive.init();
                } else if (result.error == 2) {
                    $("#grid_data").html(result.html);
                    $("#allActvities").text(result.allCount);
                    $("#inbox_count").text(0);
                    //Notification....
                    $.toast({
                        heading: 'Warning',
                        text: result.message,
                        icon: 'warning',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#f7cd62',
                        textColor: 'white'
                    });
                    //..
                }else{
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
    $('body').on('change', '#tablefilter-select', function(e) {
        e.preventDefault();
        var token = "{{ csrf_token() }}";
        var user = $(this).children("option:selected").val();
        var filter = $("#filteration .active").data('filter');
        $.ajax({
            url: "/admin/getActivityData",
            method: 'post',
            data: {'_token': token, 'filter': filter, 'user':user},
            dataType: "json",
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            success: function(result) {
                //console.log(result.message);
                if (result.error == 0) {
                    $("#grid_data").html(result.html);
                    $("#inbox_count").text(result.count);
                    $("#allActvities").text(result.allCount);
                    DatatableResponsive.init();
                } else {
                    $("#grid_data").html(result.html);
                    $("#inbox_count").text(0);
                    $("#allActvities").text(result.allCount);
                     //Notification....
                     $.toast({
                        heading: 'Warning',
                        text: result.message,
                        icon: 'warning',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#f7cd62',
                        textColor: 'white'
                    });
                }
            }
        });
    });
     // All CheckBox will Be Selected
    $("body").off('click', '#toggleAllActivity').on('click', '#toggleAllActivity', function(e) {    
        if(this.checked) 
        {
            // Iterate each checkbox
            $(':checkbox').each(function() {
                this.checked = true;                        
            });
        } 
        else 
        {
            $(':checkbox').each(function() {
                this.checked = false;                       
            });
        }
    });
    $("body").off('click', '.activities-check').on('click', '.activities-check', function(e) {    
        if(!$(this).is(":checked"))
        {
            $('#toggleAllActivity').prop('checked', false);
        }
    });
    // Mark As Read Function
    $("body").off('click', '#MarkAsRead').on('click', '#MarkAsRead', function(e) {
        e.preventDefault();
        var Ids = [];
        var filter = $("#filteration .active").data('filter');
        var user = $("#tablefilter-select").children("option:selected").val();   
        $.each($("input[name='activities']:checked"), function(){
                Ids.push($(this).val());
            });
        if(Ids != '')
        {
            $.ajax({
                type: "GET",
                url: "{{ route('admin.updateActivityDataInIds') }}",
                data: {'Ids': Ids, 'filter': filter, 'user': user},
                dataType: "json",
                beforeSend: function() {
                    $.blockUI();
                },
                complete: function() {
                    $.unblockUI();
                },
                success: function (response) {                    
                    if (response.error == 0) 
                    {
                        $.toast({
                            heading: 'Success',
                            text: response.message,
                            icon: 'success',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#f7cd62',
                            textColor: 'white'
                        });
                        $("#tablefilter-select").trigger("change");
                    } 
                    else 
                    {
                        $.toast({
                            heading: 'Warning',
                            text: response.message,
                            icon: 'warning',
                            position: 'top-right',
                            loader: false,
                            bgColor: '#f7cd62',
                            textColor: 'white'
                        });
                    }
                },
                error: function(error){
                    console.log(error)
                    alert("Something Went Wrong");
                }
            });
        }
        else
        {
            alert("Please select any record!");
        }
    });
    //END:: Get Filtered Data
    $('body').on('click', '.updateActivity', function(e) {
        e.preventDefault();
        var token = "{{ csrf_token() }}";
        var id = $(this).data('id');      
        var type = $(this).data('type');
        if(type=="Task"){
            var title="Mark as Done!";
        }else{
            var title="Mark as Read!";
        }
        swal({
            title: title,
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
        $.ajax({
            url: "/admin/updateActivityData",
            method: 'post',
            data: {'_token': token,'id':id,'type':type},
            dataType: "json",
            beforeSend: function() {
                $.blockUI();
            },
            complete: function() {
                $.unblockUI();
            },
            success: function(result) {
                //console.log(result.message);
                if (result.error == 0) {
                    location.reload();
                    $("#grid_data").html(result.html);
                    $("#inbox_count").text(result.count);
                    $("#allActvities").text(result.allCount);
                    DatatableResponsive.init();
                } else {
                    $("#grid_data").html(result.html);
                    $("#inbox_count").text(result.count);
                    $("#allActvities").text(result.allCount);
                     //Notification....
                    $.toast({
                        heading: 'Warning',
                        text: result.message,
                        icon: 'warning',
                        position: 'top-right',
                        loader: false,
                        bgColor: '#f7cd62',
                        textColor: 'white'
                    });
                }
            }
        });
    }
    });
    });
});
var DatatableResponsive = function() {
// Basic Datatable examples
var _componentDatatableResponsive = function() {
    if (!$().DataTable) {
        console.warn('Warning - datatables.min.js is not loaded.');
        return;
    }

    // Setting datatable defaults
    $.extend( $.fn.dataTable.defaults, {
        autoWidth: false,
        responsive: true,
        order: [],
        columnDefs: [{ 
            orderable: false,
            width: 100,
            targets: [ 5 ]
        }],
        dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
        language: {
            search: '<span>Filter:</span> _INPUT_',
            searchPlaceholder: 'Type to filter...',
            lengthMenu: '<span>Show:</span> _MENU_',
            paginate: { 'first': 'First', 'last': 'Last', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
        }
    });


    // Basic responsive configuration
    $('.datatable-responsive').DataTable({
        responsive: {
            details: {
                type: 'column'
            }
        },
        stateSave: false,
        columnDefs: [
            { 
                orderable: false,
                targets: [0]
            },
            { 
                orderable: false,
                targets: [5]
            },
            { 
                orderable: false,
                targets: [6]
            },
            { 
                orderable: false,
                targets: [7]
            }
        ],
    });

};

// Select2 for length menu styling
var _componentSelect2 = function() {
    if (!$().select2) {
        console.warn('Warning - select2.min.js is not loaded.');
        return;
    }

    // Initialize
    $('.dataTables_length select').select2({
        minimumResultsForSearch: Infinity,
        dropdownAutoWidth: true,
        width: 'auto'
    });
};

return {
    init: function() {
        _componentDatatableResponsive();
        _componentSelect2();
    }
}
}();

document.addEventListener('DOMContentLoaded', function() {
DatatableResponsive.init();
});

</script>
@endpush