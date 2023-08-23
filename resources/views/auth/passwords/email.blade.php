@extends('layouts.admin-auth')

@section('content')
<div class="login-box-body">
    <p class="login-box-msg">{{ __('Reset Password') }}</p>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="{{ __('E-Mail Address') }}" required>

            @if ($errors->has('email'))
                <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>

        <div class="row">
            <div class="col-xs-12">
                <button type="submit" class="btn btn-success btn-block btn-flat">
                    {{ __('Send Password Reset Link') }}
                </button>
            </div>

            <div class="col-xs-12 text-center pt-15">
                <a href="{{ route('login') }}"><i class="fa fa-angle-double-left" aria-hidden="true"></i> {{ __('back to login') }}</a>
            </div>
        </div>
    </form>
</div>
@endsection
