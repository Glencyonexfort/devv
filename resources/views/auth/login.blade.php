@extends('layouts.auth')

@section('content')
<style>
    .newfield{
        padding-left: 5rem!important;background-color: #f3f7f8!important;background-image: none!important;border-radius: 6px!important;height: 5rem!important;
    }
    .field-icon{
        font-size: 18px!important;color: #a7a9a6!important;left:1.5rem!important;top: 0.3rem;
    }
</style>
    <form class="form-horizontal form-material" id="loginform" action="{{ route('login') }}" method="POST">
        {{ csrf_field() }}


        @if (session('message'))
            <div class="alert alert-danger m-t-10">
                {{ session('message') }}
            </div>
        @endif
        <div class="form-group form-group-feedback form-group-feedback-left {{ $errors->has('email') ? 'has-error' : '' }}">
            <div class="col-xs-12">
                <input class="form-control newfield" id="email" type="email" name="email" value="{{ old('email') }}" autofocus required="" placeholder="@lang('app.email')">
                @if ($errors->has('email'))
                    <div class="help-block with-errors">{{ $errors->first('email') }}</div>
                @endif

            </div>
            <div class="form-control-feedback form-control-feedback-lg field-icon">
                <i class="fa fa-envelope icon"></i>
            </div>
        </div>
        <div class="form-group form-group-feedback form-group-feedback-left" style="margin-bottom: 10px;!important">
            <div class="col-xs-12">
                <input class="form-control newfield" id="password" type="password" name="password" required="" placeholder="@lang('modules.client.password')">
                @if ($errors->has('password'))
                    <div class="help-block with-errors">{{ $errors->first('password') }}</div>
                @endif
            </div>
            <div class="form-control-feedback form-control-feedback-lg field-icon">
                <i class="fa fa-unlock-alt icon"></i>
            </div>
        </div>
        @if($setting->google_recaptcha)
        <div class="form-group {{ $errors->has('g-recaptcha-response') ? 'has-error' : '' }}">
            <div class="col-xs-12">
                <div class="g-recaptcha"
                     data-sitekey="{{ $setting->google_recaptcha_key }}">
                </div>
                @if ($errors->has('g-recaptcha-response'))
                    <div class="help-block with-errors">{{ $errors->first('g-recaptcha-response') }}</div>
                @endif
            </div>
        </div>
        @endif
        <div class="form-group">
            <div class="col-md-12">
                <div class="checkbox checkbox-primary pull-left p-t-0">
                    <input id="checkbox-signup" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="checkbox-signup" style="font-size: 13px;color: #a7a9a6;"> @lang('app.rememberMe') </label>
                </div>
                <a href="{{ route('password.request') }}"  class="pull-right" style="font-size: 13px;color: #a7a9a6;!important"> @lang('app.forgotPassword') ?</a> </div>
        </div>
        <div class="form-group text-center m-t-20">
            <div class="col-xs-12">
                <button class="btn btn-dark-blue btn-lg btn-block btn-rounded text-uppercase waves-effect waves-light" type="submit" style="height: 5rem;font-size: 14px;background-color: #061871!important;color:#fff">@lang('app.login')</button>
                <p class="margin-top: 5px;">Don’t have an account? <a href="{{ route('registration') }}">Sign up now!</a></p>
            </div>
        </div>

        {{--<div class="form-group m-b-0">--}}
            {{--<div class="col-sm-12 text-center">--}}
                {{--<p>Don't have an account? <a href="{{ route('register') }}" class="text-primary m-l-5"><b>Sign Up</b></a></p>--}}
            {{--</div>--}}
        {{--</div>--}}
    </form>
@endsection
