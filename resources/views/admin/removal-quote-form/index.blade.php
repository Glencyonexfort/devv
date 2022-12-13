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
        @php
        echo ($jobs_moving_auto_quoting->tenant_id);
        @endphp
        <div class="d-md-flex align-items-md-start">
            @include('sections.removal_setting_menu')
            <div style="flex:auto">
                <div class="card">
                    <div class="card-header bg-white header-elements-inline">
                        <h6 class="card-title">Removals Quote Form</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                {!! Form::open(['id' => 'saveFormSettings', 'class' => 'ajax-form', 'method' => 'POST']) !!}
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <div class="form-group">
                                                <label>Redirect to Thank You page after form submission</label>
                                                <div class="form-check form-check-switchery">
                                                    <label class="form-check-label">
                                                        <input value="Y" type="checkbox"name="quote_form_redirect_after_submit" id="quote_form_redirect_after_submit" @if (isset($jobs_moving_auto_quoting) && $jobs_moving_auto_quoting->quote_form_redirect_after_submit == 'Y') checked @endif class="js-switch form-check-input-switchery-danger" data-fouc data-color="#f96262">
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row quote_form_redirect_url_box">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Redirect URL after for submission</label>
                                                <input type="text" name="quote_form_redirect_url"
                                                    id="quote_form_redirect_url" class="form-control"
                                                    value="{{ $jobs_moving_auto_quoting->quote_form_redirect_url ?? '' }}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <hr>
                                            <button type="submit" id="save-form-0" class="btn btn-success m-r-10"><i
                                                    class="fa fa-check"></i> @lang('app.save')</button>
                                            <!-- <button type="reset" class="btn btn-default">@lang('app.reset')</button> -->
                                            <hr>
                                        </div>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                            <div class="col-sm-12 col-xs-12">
                                <form id="scriptForm" action="" method="get">
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-12" style="margin-top: 15px;">
                                                <div class="form-group">
                                                    Choose company for the external Removals Quote form:
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
                                    <div class="row">
                                        <div class="col-md-12 text-left">
                                            <h3>Hidden Tab</h3>
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-left">
                                            <button type="button" id="save-form-1" class="btn btn-success m-r-10"><i
                                                    class="fa fa-code"></i> Generate Script</button>
                                            <input type="hidden" name="script1" id="script1" @if (isset($code_script) && !empty($code_script)) value="show"
                                            @endif>
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
                                                <button type="button" id="copyToClipboard" class="btn btn-primary m-r-10"><i
                                                        class="fa fa-copy"></i> Copy</button>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-12 text-left mt-3">
                                            <h3>Always Protruding</h3>
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-left">
                                            <button type="button" id="save-form-2" class="btn btn-success m-r-10"><i
                                                    class="fa fa-code"></i> Generate Script</button>
                                            <input type="hidden" name="script2" id="script2" @if (isset($code_script2) && !empty($code_script2)) value="show"
                                            @endif>
                                        </div>
                                    </div>
                                    @if (isset($code_script2) && !empty($code_script2))
                                        <input type="hidden" value="{!!  htmlentities($code_script2) !!}"
                                            id="readyForCopy2">
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
                                                <button type="button" id="copyToClipboard2"
                                                    class="btn btn-primary m-r-10"><i class="fa fa-copy"></i> Copy</button>
                                            </div>
                                        </div>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // if (isset($jobs_moving_auto_quoting)) {
        // $url = '/admin/moving-settings/saveAutoQuoteData/' . $jobs_moving_auto_quoting->id;
        // } else {
        // $url = '/admin/moving-settings/createAutoQuoteData/0';
        // }
        //dd($url);
        ?>
    </div>
@endsection

@push('footer-script')
    <?php if (isset($jobs_moving_auto_quoting)) {
    $url = '/admin/moving-settings/saveRemovalQuoteFormSettings/' . $jobs_moving_auto_quoting->id;
    } else {
    $url = '/admin/moving-settings/saveRemovalQuoteFormSettings/0';
    } ?>
    <script>
        $('#save-form-0').click(function() {
            $.easyAjax({
                url: "{{ $url }}",
                container: '#saveFormSettings',
                type: "POST",
                redirect: true,
                data: $('#saveFormSettings').serialize(),
                success: function(data) {
                    if (data.status == 'success') {
                        window.location.reload();
                    }
                }
            })
        });

    </script>

    <script src="{{ asset('plugins/bower_components/switchery/dist/switchery.min.js') }}"></script>
    <script>
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function() {
            new Switchery($(this)[0], $(this).data());
        });

        $(".js-switch").change(function() {
            var $this = $(this);
            toggleRedirectField();
        });

        function toggleRedirectField() {
            if ($('#quote_form_redirect_after_submit').is(':checked')) {
                $('.quote_form_redirect_url_box').show();
                $('#quote_form_redirect_url').prop('required', true);
            } else {
                $('.quote_form_redirect_url_box').hide();
                $('#quote_form_redirect_url').prop('required', false);
            }
        }

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
            toggleRedirectField();
        });

    </script>
@endpush
