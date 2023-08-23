<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{ asset('admin-assets/images/favicon.ico') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'sudip.me') }}</title>

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/bootstrap/css/bootstrap.min.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/font-awesome/css/font-awesome.min.css') }}">

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('admin-assets/css/main.min.css') }}">

    <!-- iCheck for checkbox/radio -->
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/iCheck/square/blue.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/styles.css') }}">
</head>
<body class="hold-transition login-page skin-black">
    <div class="login-box">
        <div class="login-logo">
            <a>{{ config('app.name', 'sudip.me') }}</a>
        </div>
        @if (session('successMessage'))
            <section class="content-header">
                <div class="alert alert-success" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    {{ session('successMessage') }}
                </div>
            </section>
        @endif

        @if (session('errorMessage'))
            <section class="content-header">
                <div class="alert alert-danger" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    {{ session('errorMessage') }}
                </div>
            </section>
        @endif
        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('admin-assets/plugins/jquery/jquery.min.js') }}"></script>

    <!-- Bootstrap 3.3.7 -->
    <script src="{{ asset('admin-assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>

    <!-- iCheck for checkbox/radio -->
    <script src="{{ asset('admin-assets/plugins/iCheck/icheck.min.js') }}"></script>

    <!-- Form validate -->
    <script src="{{ asset('admin-assets/plugins/validate/jquery.validate.min.js') }}"></script>
    <script>
        $('form').validate({
            errorElement: "em",

            errorPlacement: function ( error, element ) {
                // Add the `help-block` class to the error element
                error.addClass( "help-block" );

                if ( element.prop( "type" ) === "checkbox" ) {
                    error.insertAfter( element.parent( "label" ) );
                } else {
                    error.insertAfter( element );
                }
            },
            highlight: function ( element, errorClass, validClass ) {
                $( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
            },
            unhighlight: function (element, errorClass, validClass) {
                $( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
            }
        });
    </script>
</body>
</html>
