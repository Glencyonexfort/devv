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
            @include('sections.cleaning_quote_form_settings_menu')
            <div style="flex:auto">
                <div class="card">
                    <div class="card-header bg-white header-elements-inline">
                        <h6 class="card-title">@lang('modules.leaseCleaningQuoteFormSettings.createTitle')</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                {!! Form::open(['id' => 'createTemplate', 'class' => 'ajax-form', 'method' => 'POST']) !!}
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('modules.leaseCleaningQuoteFormSettings.servicing_city')</label>
                                                <select class="select2 form-control" data-placeholder="Choose Category"
                                                    name="servicing_city_id" required>
                                                    @foreach ($servicing_cities as $rs)
                                                        <option value="{{ $rs->id }}">{{ $rs->servicing_city }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('modules.leaseCleaningQuoteFormSettings.servicing_city_geocode_sw_lat')</label>
                                                <input type="text" name="servicing_city_geocode_sw_lat"
                                                    id="servicing_city_geocode_sw_lat" class="form-control"
                                                    value="{{ $quote_form_setup->servicing_city_geocode_sw_lat ?? '' }}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('modules.leaseCleaningQuoteFormSettings.servicing_city_geocode_sw_lng')</label>
                                                <input type="text" name="servicing_city_geocode_sw_lng"
                                                    id="servicing_city_geocode_sw_lng" class="form-control"
                                                    value="{{ $quote_form_setup->servicing_city_geocode_sw_lng ?? '' }}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('modules.leaseCleaningQuoteFormSettings.servicing_city_geocode_ne_lat')</label>
                                                <input type="text" name="servicing_city_geocode_ne_lat"
                                                    id="servicing_city_geocode_ne_lat" class="form-control"
                                                    value="{{ $quote_form_setup->servicing_city_geocode_ne_lat ?? '' }}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('modules.leaseCleaningQuoteFormSettings.servicing_city_geocode_ne_lng')</label>
                                                <input type="text" name="servicing_city_geocode_ne_lng"
                                                    id="servicing_city_geocode_ne_lng" class="form-control"
                                                    value="{{ $quote_form_setup->servicing_city_geocode_ne_lng ?? '' }}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('modules.leaseCleaningQuoteFormSettings.maximum_shifts_per_team_per_day')</label>
                                                <input type="number" name="max_shifts_per_team_per_day"
                                                    id="max_shifts_per_team_per_day" class="form-control"
                                                    value="{{ $quote_form_setup->max_shifts_per_team_per_day ?? '' }}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('modules.leaseCleaningQuoteFormSettings.minimum_hours_per_jobs')</label>
                                                <input type="number" name="min_hours_per_job" id="min_hours_per_job"
                                                    class="form-control"
                                                    value="{{ $quote_form_setup->min_hours_per_job ?? '' }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('modules.leaseCleaningQuoteFormSettings.maximum_bedrooms_per_house')</label>
                                                <input type="number" name="max_bedrooms" id="max_bedrooms"
                                                    class="form-control" value="{{ $quote_form_setup->max_bedrooms ?? '' }}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('modules.leaseCleaningQuoteFormSettings.maximum_bathrooms_per_house')</label>
                                                <input type="number" name="max_bathrooms" id="max_bathrooms"
                                                    class="form-control"
                                                    value="{{ $quote_form_setup->max_bathrooms ?? '' }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('modules.leaseCleaningQuoteFormSettings.extra_product_category')</label>
                                                <select name="extras_category_id" id="extras_category_id"
                                                    class="form-control">
                                                    @foreach ($product_categories as $rs)
                                                        <option @if (isset($quote_form_setup) && $quote_form_setup->extras_category_id == $rs->id)
                                                            selected
                                                    @endif value="{{ $rs->id }}">{{ $rs->category_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    $questions_list_type_id_ary = [];
                                    if (isset($quote_form_setup->questions_list_type_id)) {
                                    $questions_list_type_id_ary = @explode(',', $quote_form_setup->questions_list_type_id);
                                    }
                                    ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('modules.leaseCleaningQuoteFormSettings.questions_list')</label>
                                                <select name="questions_list_type_id[]" id="questions_list_type_id"
                                                    class="form-control" multiple required>
                                                    @foreach ($list_types as $rs)
                                                        <option value="{{ $rs->id }}" @if (in_array($rs->id, $questions_list_type_id_ary))
                                                            selected
                                                    @endif >{{ $rs->list_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <hr>
                                            <button type="submit" id="save-form" class="btn btn-success m-r-10"><i
                                                    class="fa fa-check"></i> @lang('app.save')</button>
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
    <script>
        $('#createTemplate').on('submit', function() {
            $.easyAjax({
                url: "{{ route('admin.leaseQuoteFormSettings.store') }}",
                container: '#createTemplate',
                type: "POST",
                redirect: true,
                data: $('#createTemplate').serialize()
            })
        });
    </script>
@endpush
