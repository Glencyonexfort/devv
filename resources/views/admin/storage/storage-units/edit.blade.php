@extends('layouts.app')

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
        @include('sections.storage_setting_menu')
        <div style="flex:auto">
            <div class="card">
                <div class="card-header bg-white header-elements-inline">
                    <h6 class="card-title">Edit Storage Type</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            {!! Form::open(['id'=>'update','class'=>'ajax-form','method'=>'POST']) !!}
                            {{ Form::hidden('id', $storage_unit->id) }}
                            <div class="form-body">
                                <h3 class="box-title">Storage Type Detail</h3>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Serial Number</label>
                                            <input type="number" id="serial_number" name="serial_number" class="form-control" value="{{ $storage_unit->serial_number }}" style="width: 30%;">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Unit Name</label>
                                            <input type="text" id="name" name="name" class="form-control" placeholder="eg. 20' Container" value="{{ $storage_unit->name }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Storage Type</label>
                                            <select class="select2 form-control" name="storage_type_id">
                                                @foreach($storage_types as $rs)
                                                <option value="{{ $rs->id }}" @if($rs->id == $storage_unit->storage_type_id)
                                                            selected=""
                                                            @endif>{{ $rs->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div> 

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Manufacturer Serial Number</label>
                                            <input type="text" id="manufacturer_serial_number" name="manufacturer_serial_number" value="{{ $storage_unit->manufacturer_serial_number }}" class="form-control">
                                        </div>
                                    </div>
                                </div>  
                                
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-xs-12">
                                        <div class="form-group">
                                            <label class="control-label">Active</label>
                                            <div class="switchery-demo">
                                                <input type="checkbox" id="active" name="active" value="Y" {{ $storage_unit->active=='1'?'checked=""':'' }} class="js-switch " data-color="#4caf50"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <hr>
                                        <button type="submit" id="save-form" class="btn btn-success"> <i class="fa fa-check"></i> Update</button>
                                        <a href="{{ route('admin.storage-units') }}" class="btn btn-light" data-dismiss="modal">Cancel</a>
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

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script>        
var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    $('.js-switch').each(function() {
        new Switchery($(this)[0], $(this).data());
    });
    // $('.js-switch').on('switchChange', function(e, data) {
    //     alert('d');
    // });
    $(".select2").select2({
        formatNoMatches: function() {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $('#save-form').click(function() {
        $.easyAjax({
            url: "{{route('admin.storage-units.update')}}",
            container: '#update',
            type: "POST",
            redirect: true,
            data: $('#update').serialize()
        })
    });
</script>
@endpush