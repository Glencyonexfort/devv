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
                        <h6 class="card-title">@lang('modules.leaseCleaningQuoteFormSettings.updateTitle')</h6>
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
                                                <input type="text" class="form-control"
                                                    value="{{ $servicing_city->servicing_city ?? '' }}" readonly>
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
                                            <hr>
                                        </div>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>

                            @if (isset($quote_form_setup))
                                <form id="scriptForm" action="" method="get">
                                <input type="hidden" name="city" value="{{ $servicing_city->id }}">
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-12" style="margin-top: 15px;">
                                                <strong>@lang('modules.leaseCleaningQuoteFormSettings.servicing_city'):</strong> &nbsp;&nbsp; {{ $servicing_city->servicing_city ?? '' }}
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12" style="margin-top: 15px;">
                                                <div class="row">                        
                                                    <div class="col-md-6">
                                                        <label>
                                                            Choose company for the external Removals Quote form:
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                    <select name="company" id="company" class="form-control"
                                                        style="display: inline-block;width: fit-content;" required>
                                                        @foreach ($companies as $rs)
                                                            <option @if (isset($slectedCompany) && $slectedCompany == $rs->id)
                                                                selected
                                                        @endif
                                                        value="{{ $rs->id }}">{{ $rs->company_name }}</option>
                                                        @endforeach
                                                        </select>
                                                    </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-left">
                    <div class="row">                        
                        <div class="col-md-6">
                            <label style="margin-top: 15px;"> Discount Offer:</label>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </span>
                                <input id="discount_offer" name="discount_offer" type="number" class="form-control" value="{{ $discount_offer }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-left">
                    <hr>
                    <h3>Hidden Tab</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-left">
                    <button type="button" id="save-form-1" class="btn btn-success m-r-10"><i class="fa fa-code"></i>
                        Generate Script</button>
                    <input type="hidden" name="script1" id="script1" @if (isset($code_script) && !empty($code_script)) value="show" @endif>
                </div>
            </div>
            @if (isset($code_script) && !empty($code_script))
                <input type="hidden" value="{!!  htmlentities($code_script) !!}" id="readyForCopy">
                <div class="row">
                    <div class="col-md-12 mt-3 mb-2">
                        <blockquote>
                            <code>
                                {!! nl2br(htmlentities($code_script)) !!}
                            </code>
                        </blockquote>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" id="copyToClipboard" class="btn btn-primary m-r-10"><i class="fa fa-copy"></i>
                            Copy</button>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-md-12 text-left mt-3">
                    <hr>
                    <h3>Always Protruding</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-left">
                    <button type="button" id="save-form-2" class="btn btn-success m-r-10"><i class="fa fa-code"></i>
                        Generate Script</button>
                    <input type="hidden" name="script2" id="script2" @if (isset($code_script2) && !empty($code_script2)) value="show" @endif>
                    <hr>
                </div>
            </div>
            @if (isset($code_script2) && !empty($code_script2))
                <input type="hidden" value="{!!  htmlentities($code_script2) !!}" id="readyForCopy2">
                <div class="row">
                    <div class="col-md-12 mt-3 mb-2">
                        <blockquote>
                            <code>
                                {!! nl2br(htmlentities($code_script2)) !!}
                            </code>
                        </blockquote>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" id="copyToClipboard2" class="btn btn-primary m-r-10"><i
                                class="fa fa-copy"></i> Copy</button>
                    </div>
                </div>
            @endif
            </form>
            @endif
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
                url: "{{ route('admin.leaseQuoteFormSettings.update', [$quote_form_setup->id]) }}",
                container: '#createTemplate',
                type: "PUT",
                redirect: true,
                data: $('#createTemplate').serialize()
            })
        });

    </script>
    <script>
        function setClipboard(value) {
            var tempInput = document.createElement("input");
            tempInput.style = "position: absolute; left: -1000px; top: -1000px";
            tempInput.value = value;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand("copy");
            document.body.removeChild(tempInput);
        }
        $(document).ready(function() {
            $(document).on('click', '#copyToClipboard', function() {
                setClipboard(document.getElementById("readyForCopy").value);
                $.toast({
                    text: 'Script copied to clipboard.',
                    icon: 'success',
                    position: 'top-right',
                    loader: false,
                    bgColor: '#00c292',
                    textColor: 'white'
                });
            });
            $(document).on('click', '#copyToClipboard2', function() {
                setClipboard(document.getElementById("readyForCopy2").value);
                $.toast({
                    text: 'Script copied to clipboard.',
                    icon: 'success',
                    position: 'top-right',
                    loader: false,
                    bgColor: '#00c292',
                    textColor: 'white'
                });
            });

            $(document).on('click', '#save-form-1', function() {
                $('#script1').val('show');
                $('#scriptForm').submit();
            });
            $(document).on('click', '#save-form-2', function() {
                $('#script2').val('show');
                $('#scriptForm').submit();
            });
        });

    </script>
    {{-- <script src="https://maps.googleapis.com/maps/api/js?key={{$tenant_api_details->account_key ?? ''}}&v=3.exp&libraries=places&region=au"></script>
    <script type="text/javascript">
        function initialize() {
            var options = {
                types: ['(cities)'],
                componentRestrictions: {
                    country: "au"
                }
            };
            var input = document.getElementById('servicing_city');
            var autocomplete = new google.maps.places.Autocomplete(input, options);
            google.maps.event.addListener(autocomplete, 'place_changed', function() {
                var place = autocomplete.getPlace();
                $('#servicing_city_geocode_sw_lat').val(place.geometry.viewport.getSouthWest().lat());
                $('#servicing_city_geocode_sw_lng').val(place.geometry.viewport.getSouthWest().lng());
                $('#servicing_city_geocode_ne_lat').val(place.geometry.viewport.getNorthEast().lat());
                $('#servicing_city_geocode_ne_lng').val(place.geometry.viewport.getNorthEast().lng());
            });
        }
    
        google.maps.event.addDomListener(window, 'load', initialize);
    </script> --}}
@endpush
