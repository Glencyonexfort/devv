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
    <style>
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

        .files input:focus {
            outline: 2px dashed #92b0b3;
            outline-offset: -10px;
            -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
            transition: outline-offset .15s ease-in-out, background-color .15s linear;
            border: 1px solid #92b0b3;
        }

        .files {
            position: relative
        }

        .files:after {
            pointer-events: none;
            position: absolute;
            top: 60px;
            left: 0;
            width: 50px;
            right: 0;
            height: 56px;
            content: "";
            background-image: url(https://image.flaticon.com/icons/png/128/109/109612.png);
            display: block;
            margin: 0 auto;
            background-size: 100%;
            background-repeat: no-repeat;
        }

        .color input {
            background-color: #f1f1f1;
        }

        .files:before {
            position: absolute;
            bottom: 10px;
            left: 0;
            pointer-events: none;
            width: 100%;
            right: 0;
            height: 57px;
            content: " or drag it here. ";
            display: block;
            margin: 0 auto;
            color: #2ea591;
            font-weight: 600;
            text-transform: capitalize;
            text-align: center;
        }

        .attachment-img {
            width: 24px;
            margin-right: 3px;
        }

    </style>
@endpush

@section('content')

    <div class="content">
        <div class="d-md-flex align-items-md-start">
            @include('sections.removal_setting_menu')
            <div style="flex:auto">
                <div class="card">
                    <div class="card-header bg-white header-elements-inline">
                        <h6 class="card-title">@lang('modules.propertyCategoryOptions.updateTitle')</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">

                                {!! Form::open(['id' => 'updateTemplate', 'method' => 'PUT', 'files' => true, 'url' =>
                                'admin/moving-settings/property-category-options/' . $row->id]) !!}
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <div class="form-group">
                                                <label>@lang('modules.propertyCategoryOptions.property_category_option')</label>
                                                <input type="text" name="options" id="options" value="{{ $row->options }}"
                                                    class="form-control" autocomplete="nope" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('modules.propertyCategoryOptions.property_cateogry')</label>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <select class="select2 form-control"
                                                            data-placeholder="Choose Category" name="category_id">
                                                            @foreach ($property_categories as $rs)
                                                                <option value="{{ $rs->id }}" @if ($rs->id == $row->category_id)
                                                                    selected=""
                                                            @endif
                                                            >{{ $rs->category }}</option>
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
                                                <input type="number" step="any" min="0" name="m3_value" id="m3_value"
                                                    value="{{ $row->m3_value }}" class="form-control" autocomplete="nope"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <div class="form-group">
                                                <label>@lang('modules.propertyCategoryOptions.other_value')</label>
                                                <input type="number" step="any" min="0" name="other_value" id="other_value"
                                                    value="{{ $row->other_value }}" class="form-control" autocomplete="nope"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-xs-12">
                                            <div class="form-group">
                                                <label
                                                    class="control-label">@lang('modules.propertyCategoryOptions.active')</label>
                                                <div class="switchery-demo">
                                                    <input type="checkbox" id="active" id="active"
                                                        {{ $row->active == 'Y' ? 'checked=""' : '' }} name="active"
                                                        value="Y" class="js-switch " data-color="#f96262"  />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <hr>
                                            <button type="submit" id="save-form" class="btn btn-success"><i
                                                    class="fa fa-check"></i> @lang('app.update')</button>
                                            <a href="{{ route('admin.property-category-options.index') }}"
                                                class="btn btn-default">@lang('app.back')</a>
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
    <script src="{{ asset('plugins/tagify-master/dist/tagify.js') }}"></script>
    <script src="{{ asset('bootstrap/jquery.bootstrap-touchspin.js') }}"></script>
    <script src="{{ asset('plugins/tagify-master/dist/tagify.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/switchery/dist/switchery.min.js') }}"></script>
    <script>
        $('.summernote').summernote({
            height: 200, // set editor height
            minHeight: null, // set minimum height of editor
            maxHeight: null, // set maximum height of editor
            focus: false // set focus to editable area after initializing summernote
        });
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function() {
            new Switchery($(this)[0], $(this).data());
        });
        //$('#save-form').click(function () {
        //    $.easyAjax({
        //        url: '{{ route('admin.property-category-options.update', [$row->id]) }}',
        //        container: '#updateTemplate',
        //        type: "POST",
        //        redirect: true,
        //        processData: false,
        //        contentType: false,
        //        data: $('#updateTemplate').serialize()
        //    })
        //});

    </script>
@endpush
