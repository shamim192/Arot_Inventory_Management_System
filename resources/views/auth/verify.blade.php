@extends('layouts.admin-auth')

@section('content')
<div class="login-box-body">
    <p class="login-box-msg">{{ __('Verify Your Email Address') }}</p>

    @if (session('resent'))
        <div class="alert alert-success">
            {{ __('A fresh verification link has been sent to your email address.') }}
        </div>
    @endif

    <div class="row">
        <div class="col-xs-12 text-justify">
            {{ __('Before proceeding, please check your email for a verification link.') }}
            {{ __('If you did not receive the email') }}, 
            <a href="{{ route('verification.resend') }}" onclick="event.preventDefault(); document.getElementById('resend-form').submit();">{{ __('click here to request another') }}</a>

            <form id="resend-form" class="non-validate" action="{{ route('verification.resend') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>

        <div class="col-xs-12 text-center pt-15">
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-angle-double-left" aria-hidden="true"></i> {{ __('back to login') }}</a>

            <form id="logout-form" class="non-validate" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</div>
@endsection
