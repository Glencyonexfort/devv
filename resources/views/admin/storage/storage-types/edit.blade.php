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
                            {{ Form::hidden('id', $storage_type->id) }}
                            <div class="form-body">
                                <h3 class="box-title">Storage Type Detail</h3>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Name</label>
                                            <input type="text" id="name" name="name" class="form-control" placeholder="eg. 20' Container" value="{{ $storage_type->name }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Inside Capacity in cubic meter (m3)</label>
                                            <input type="text" id="inside_cubic_capacity" name="inside_cubic_capacity" value="{{ $storage_type->inside_cubic_capacity }}" class="form-control" style="width: 30%;">
                                        </div>
                                    </div>
                                </div>
                                <!--/row-->

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Max Gross Weight in Kg</label>
                                            <input type="number" id="max_gross_weight_kg" name="max_gross_weight_kg" value="{{ $storage_type->max_gross_weight_kg }}" class="form-control" style="width: 30%;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Tare Weight in Kg</label>
                                            <input type="number" id="tare_weight_kg" name="tare_weight_kg" value="{{ $storage_type->tare_weight_kg }}" class="form-control" style="width: 30%;">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">External Dimensions in meter</label>
                                        </div>
                                    </div>
                                    <div class="row">
										<div class="col-md-3">
											<div class="form-group">
				                                <input type="text" id="ext_length_m" name="ext_length_m" value="{{ $storage_type->ext_length_m }}" class="form-control" placeholder="Length">                                                
			                                </div>
										</div>
                                        <div class="col-md-1" style="padding: 8px 0px 8px 16px;"><i class="icon-cross"></i></div>

										<div class="col-md-3">
											<div class="form-group">
				                                <input type="text" id="ext_width_m" name="ext_width_m" value="{{ $storage_type->ext_width_m }}" class="form-control" placeholder="Width">
			                                </div>
										</div>
                                        <div class="col-md-1" style="padding: 8px 0px 8px 16px;"><i class="icon-cross"></i></div>

                                        <div class="col-md-3">
											<div class="form-group">
				                                <input type="text" id="ext_height_m" name="ext_height_m" value="{{ $storage_type->ext_height_m }}" class="form-control" placeholder="Height">
			                                </div>
										</div>
									</div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Internal Dimensions in meter</label>
                                        </div>
                                    </div>
                                    <div class="row">
										<div class="col-md-3">
											<div class="form-group">
				                                <input type="text" id="int_length_m" name="int_length_m" value="{{ $storage_type->int_length_m }}" class="form-control" placeholder="Length">                                                
			                                </div>
										</div>
                                        <div class="col-md-1" style="padding: 8px 0px 8px 16px;"><i class="icon-cross"></i></div>

										<div class="col-md-3">
											<div class="form-group">
				                                <input type="text" id="int_width_m" name="int_width_m" value="{{ $storage_type->int_width_m }}" class="form-control" placeholder="Width">
			                                </div>
										</div>
                                        <div class="col-md-1" style="padding: 8px 0px 8px 16px;"><i class="icon-cross"></i></div>

                                        <div class="col-md-3">
											<div class="form-group">
				                                <input type="text" id="int_height_m" name="int_height_m" value="{{ $storage_type->int_height_m }}" class="form-control" placeholder="Height">
			                                </div>
										</div>
									</div>
                                </div>    
                                
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-xs-12">
                                        <div class="form-group">
                                            <label class="control-label">Active</label>
                                            <div class="switchery-demo">
                                                <input type="checkbox" id="active" name="active" value="Y" {{ $storage_type->active=='1'?'checked=""':'' }} class="js-switch " data-color="#4caf50"/>
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
                                        <a href="{{ route('admin.storage-types') }}" class="btn btn-light" data-dismiss="modal">Cancel</a>
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
            url: "{{route('admin.storage-types.update')}}",
            container: '#update',
            type: "POST",
            redirect: true,
            data: $('#update').serialize()
        })
    });
</script>
@endpush