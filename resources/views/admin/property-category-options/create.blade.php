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
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">
@endpush

@section('content')
    <div class="content">
        <div class="d-md-flex align-items-md-start">
            @include('sections.removal_setting_menu')
            <div style="flex:auto">
                <div class="card">
                    <div class="card-header bg-white header-elements-inline">
                        <h6 class="card-title">@lang('modules.propertyCategoryOptions.createTitle')</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                {!! Form::open(['id' => 'createTemplate', 'class' => 'ajax-form', 'method' => 'POST']) !!}
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <div class="form-group">
                                                <label>@lang('modules.propertyCategoryOptions.property_category_option')</label>
                                                <input type="text" name="options" id="options" class="form-control" autocomplete="nope" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('modules.propertyCategoryOptions.property_cateogry')</label>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <select class="select2 form-control" data-placeholder="Choose Category" name="category_id">
                                                            @foreach ($property_categories as $rs)
                                                                <option value="{{ $rs->id }}">{{ ($rs->category) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <div class="form-group">
                                                <label>@lang('modules.propertyCategoryOptions.m3_value')</label>
                                                <input type="number" step="any" min="0" name="m3_value" id="m3_value" class="form-control" autocomplete="nope" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <div class="form-group">
                                                <label>@lang('modules.propertyCategoryOptions.other_value')</label>
                                                <input type="number" step="any" min="0" name="other_value" id="other_value" class="form-control" autocomplete="nope" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-xs-12">
                                            <div class="form-group">
                                                <label class="control-label">@lang('modules.propertyCategoryOptions.active')</label>
                                                <div class="switchery-demo">
                                                    <input type="checkbox" id="active" id="active" checked name="active" value="Y" class="js-switch " data-color="#f96262" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <hr>
                                            <button type="submit" id="save-form" class="btn btn-success"> <i
                                                    class="fa fa-check"></i> @lang('app.save')</button>
                                            <a href="{{ route('admin.property-category-options.index') }}"
                                                class="btn btn-default">@lang('app.cancel')</a>
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
    <script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/switchery/dist/switchery.min.js') }}"></script>
    <script>
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function() {
            new Switchery($(this)[0], $(this).data());
        });
        $('.summernote').summernote({
            height: 200, // set editor height
            minHeight: null, // set minimum height of editor
            maxHeight: null, // set maximum height of editor
            focus: false // set focus to editable area after initializing summernote
        });

        $('#save-form').click(function() {
            $.easyAjax({
                url: "{{ route('admin.property-category-options.store') }}",
                container: '#createTemplate',
                type: "POST",
                redirect: true,
                data: $('#createTemplate').serialize()
            })
        });

    </script>
@endpush
