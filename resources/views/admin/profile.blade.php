@extends('layouts.admin')

@section('content')
<section class="content-header">
    <h1>Profile Settings</h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-sm-6">
            <div class="box">
                <div class="box-body">
                    <h4 class="box-title">Account  {{ __('lang.Details') }}</h4>
                    <form method="POST" action="{{ route('profile') }}">
                        @csrf

                        <div class="form-group">
                            <label>Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" placeholder="Full Name" value="{{ Auth::user()->name }}" required>

                            @if ($errors->has('name'))
                                <span class="help-block">{{ $errors->first('name') }}</span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>Mobile <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">+88</span>
                                <input type="text" class="form-control" name="mobile" placeholder="01712xxxxxx" value="{{ Auth::user()->mobile }}" required pattern="[0-9]{11}" title="11 Digit mobile number without country code">
                            </div>

                            @if ($errors->has('mobile'))
                                <span class="help-block">{{ $errors->first('mobile') }}</span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" placeholder="Email address" value="{{ Auth::user()->email }}" required>

                            @if ($errors->has('email'))
                                <span class="help-block">{{ $errors->first('email') }}</span>
                            @endif
                        </div>

                        <button class="btn btn-success btn-flat btn-block" type="submit">Save change</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6">
            <div class="box">
                <div class="box-body">
                    <h4 class="box-title">Change password</h4>
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf

                        <div class="form-group">
                            <label>Current password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="current_password" required>

                            @error('current_password')
                                <span class="help-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>New password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" required>

                            @error('password')
                                <span class="help-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Confirm new password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password_confirmation" required>
                        </div>

                        <button class="btn btn-success btn-flat btn-block" type="submit">Change password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
