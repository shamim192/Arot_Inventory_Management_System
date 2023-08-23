@extends('layouts.admin-auth')

@section('content')
<div class="login-box-body">
    <p class="login-box-msg">{{ __('Sign in to start your session') }}</p>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">
            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus placeholder="{{ __('Email') }}">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>

            @if ($errors->has('email'))
                <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
            <input id="password" type="password" class="form-control" name="password" required placeholder="{{ __('Password') }}">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>

            @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        </div>

        <div class="row">
            <div class="col-xs-8">
                <div class="checkbox icheck">
                    <label> <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('Remember Me') }}</label>
                </div>
            </div>
            <div class="col-xs-4">
                <button type="submit" class="btn btn-success btn-block btn-flat">{{ __('Sign In') }}</button>
            </div>
        </div>
    </form>

    <a class="btn-link" href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>
</div>
@endsection
