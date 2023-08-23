<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'sudip.me') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('admin-assets/images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/datetimepicker/jquery.datetimepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/fancybox-3.0/jquery.fancybox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/datatables/dataTables.bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/datatables/export/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/main.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/skins.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/styles.css') }}">
    @yield('styles')
</head>
<body class="hold-transition skin-black sidebar-mini">
    <div class="wrapper">
        <header class="main-header">
            <a href="{{ route('dashboard') }}" class="logo">
                <span class="logo-mini">
                    {{ config('app.name', 'Laravel') }} 
                </span>
                <span class="logo-lg">
                    {{ config('app.name', 'Laravel') }}
                </span>
            </a>

            <nav class="navbar navbar-static-top" role="navigation">
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    {{-- <span class="sr-only">Toggle navigation</span> --}}
                   
                </a>

               

                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">

                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="{{ asset('admin-assets/images/avatar.png') }}" alt="avatar" class="user-image">
                            <span class="hidden-xs">
                                {{ Auth::user()->name }}
                            </span>
                        </a>

                        <ul class="dropdown-menu">

                            <li class="user-header">
                                <img src="{{ asset('admin-assets/images/avatar.png') }}" alt="avatar" class="img-circle">
                                <p>
                                    {{ Auth::user()->name }}
                                    <small>
                                        {{ Auth::user()->mobile }}<br>
                                        {{ Auth::user()->email }}
                                    </small>
                                </p>
                            </li>

                            <li class="user-footer">
                                <div class="pull-left">
                                    <a class="btn btn-info btn-flat" href="{{route('profile')}}">{{ __('lang.My Account') }}</a>
                                </div>
                                <div class="pull-right">
                                    <a class="btn btn-danger btn-flat" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ __('lang.Logout') }}
                                    </a>

                                    <form id="logout-form" class="non-validate" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </li>
                    </ul>
                </div>
            </nav>
        </header>

        <aside class="main-sidebar">
            <section class="sidebar">
                <ul class="sidebar-menu">
                    <li class="{{ Request::routeIs('dashboard') ? 'active' : ''}}">
                        <a href="{{ route('dashboard') }}">
                            <i class="fa fa-dashboard"></i>
                            <span>{{ __('lang.Dashboard') }}</span>
                        </a>
                    </li>
                    
                    <li class="{{ Request::routeIs('invest.*') ? 'active' : ''}}">
                        <a href="{{ route('invest.index') }}">
                            <i class="fa fa-user"></i>
                            <span>{{ __('lang.Invests') }}</span>
                        </a>
                    </li>
                    
                    <li class="{{ Request::routeIs('fund-transfer.*') ? 'active' : ''}}">
                        <a href="{{ route('fund-transfer.index') }}">
                            <i class="fa fa-user"></i>
                            <span>{{ __('lang.Fund Transfers') }}</span>
                        </a>
                    </li>
                    
                   

                    <li class="treeview {{ (Request::routeIs('sale.*') || Request::routeIs('sale-return.*') || Request::routeIs('customer-payment.*')) ? 'active menu-open' : ''}}">
                        <a href="#">
                            <i class="fa fa-upload"></i>
                            <span>{{ __('lang.Sales') }}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{ Request::routeIs('sale.*') ? 'active' : ''}}">
                                <a href="{{ route('sale.index') }}">{{ __('lang.Sale') }}</a>
                            </li>
                            <li class="{{ Request::routeIs('sale-return.*') ? 'active' : ''}}">
                                <a href="{{ route('sale-return.index') }}">{{ __('lang.Sale Return') }}</a>
                            </li>
                            <li class="{{ Request::routeIs('customer-payment.*') ? 'active' : ''}}">
                                <a href="{{ route('customer-payment.index') }}">{{ __('lang.Customer Payment') }}</a>
                            </li>
                        </ul>
                    </li>

                    <li class="treeview {{ (Request::routeIs('stock.*') || Request::routeIs('stock-return.*') || Request::routeIs('supplier-payment.*')) ? 'active menu-open' : ''}}">
                        <a href="#">
                            <i class="fa fa-download"></i>
                            <span>{{ __('lang.Stocks') }}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{ Request::routeIs('stock.*') ? 'active' : ''}}">
                                <a href="{{ route('stock.index') }}">{{ __('lang.Stock In') }}</a>
                            </li>
                            <li class="{{ Request::routeIs('stock-return.*') ? 'active' : ''}}">
                                <a href="{{ route('stock-return.index') }}">{{ __('lang.Stock Return') }}</a>
                            </li>
                            <li class="{{ Request::routeIs('supplier-payment.*') ? 'active' : ''}}">
                                <a href="{{ route('supplier-payment.index') }}">{{ __('lang.Supplier Payment') }}</a>
                            </li>
                        </ul>
                    </li>

                    <li class="treeview {{ (Request::routeIs('report.*')) ? 'active menu-open' : ''}}">
                        <a href="#">
                            <i class="fa fa-cog"></i>
                            <span>{{ __('lang.Reports') }}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{ Request::routeIs('report.product-stock') ? 'active' : ''}}">
                                <a href="{{ route('report.product-stock') }}">{{ __('lang.Product Stock') }}</a>
                            </li>
                            
                           
                            <li class="{{ Request::routeIs('report.bank') ? 'active' : ''}}">
                                <a href="{{ route('report.bank') }}">{{ __('lang.Banks') }}</a>
                            </li>
                            <li class="{{ Request::routeIs('report.supplier') ? 'active' : ''}}">
                                <a href="{{ route('report.supplier') }}">{{ __('lang.Suppliers') }}</a>
                            </li>
                            <li class="{{ Request::routeIs('report.customer') ? 'active' : ''}}">
                                <a href="{{ route('report.customer') }}">{{ __('lang.Customers') }}</a>
                            </li>
                            <li class="{{ Request::routeIs('report.expense') ? 'active' : ''}}">
                                <a href="{{ route('report.expense') }}">{{ __('lang.Expenses') }}</a>
                            </li>
                            <li class="{{ Request::routeIs('report.income') ? 'active' : ''}}">
                                <a href="{{ route('report.income') }}">{{ __('lang.Incomes') }}</a>
                            </li>
                        </ul>
                    </li>


                    {{-- income --}}
                    
                    <li class="treeview {{ (Request::routeIs('income-category.*') || Request::routeIs('income.*')) ? 'active menu-open' : ''}}">
                        <a href="#">
                            <i class="fa fa-money"></i>
                            <span>{{ __('lang.Incomes') }}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{ Request::routeIs('income.*') ? 'active' : ''}}">
                                <a href="{{ route('income.index') }}">{{ __('lang.Income') }}</a>
                            </li>
                            <li class="{{ Request::routeIs('income-category.*') ? 'active' : ''}}">
                                <a href="{{ route('income-category.index') }}">{{ __('lang.Category') }}</a>
                            </li>
                        </ul>
                    </li>


                    {{-- End of income --}}











                    <li class="treeview {{ (Request::routeIs('expense-category.*') || Request::routeIs('expense.*')) ? 'active menu-open' : ''}}">
                        <a href="#">
                            <i class="fa fa-money"></i>
                            <span>{{ __('lang.Expenses') }}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{ Request::routeIs('expense.*') ? 'active' : ''}}">
                                <a href="{{ route('expense.index') }}">{{ __('lang.Expense') }}</a>
                            </li>
                            <li class="{{ Request::routeIs('expense-category.*') ? 'active' : ''}}">
                                <a href="{{ route('expense-category.index') }}">{{ __('lang.Category') }}</a>
                            </li>
                        </ul>
                    </li>

                    <li class="treeview {{ (Request::routeIs('user.*') || Request::routeIs('customer.*') || Request::routeIs('supplier.*')) ? 'active menu-open' : ''}}">
                        <a href="#">
                            <i class="fa fa-user"></i>
                            <span>{{ __('lang.Users') }}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{ Request::routeIs('customer.*') ? 'active' : ''}}">
                                <a href="{{ route('customer.index') }}">{{ __('lang.Customers') }}</a>
                            </li>
                            <li class="{{ Request::routeIs('supplier.*') ? 'active' : ''}}">
                                <a href="{{ route('supplier.index') }}">{{ __('lang.Suppliers') }}</a>
                            </li>

                            @if (Auth::user()->account_type == 'Admin')
                            <li class="{{ Request::routeIs('user.*') ? 'active' : ''}}">
                                <a href="{{ route('user.index') }}">{{ __('lang.Users') }}</a>
                            </li>
                            @endif
                        </ul>
                    </li>

                    <li class="treeview {{ (Request::routeIs('unit.*') || Request::routeIs('bank.*') || Request::routeIs('product.*'))  ? 'active menu-open' : ''}}">
                        <a href="#">
                            <i class="fa fa-cog"></i>
                            <span>{{ __('lang.Setting') }}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{ Request::routeIs('bank.*') ? 'active' : ''}}">
                                <a href="{{ route('bank.index') }}">{{ __('lang.Banks') }}</a>
                            </li>
                            <li class="{{ Request::routeIs('unit.*') ? 'active' : ''}}">
                                <a href="{{ route('unit.index') }}">{{ __('lang.Units') }}</a>
                            </li>
                            <li class="{{ Request::routeIs('product.*') ? 'active' : ''}}">
                                <a href="{{ route('product.index') }}">{{ __('lang.Products') }}</a>
                            </li>
                            
                        </ul>
                    </li>
                </ul>
            </section>
        </aside>

        <div class="content-wrapper">
            @if (session('successMessage'))
            <section class="content-header">
                <div class="alert alert-success text-center" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                {{ session('successMessage') }} <br> <button type="button" class="btn btn-warning" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">Ok</span></button>
                </div>
            </section>
            @endif

            @if (session('errorMessage'))
            <section class="content-header">
                <div class="alert alert-danger text-center" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                {{ session('errorMessage') }} <br> <button type="button" class="btn btn-warning" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">Ok</span></button>
                </div>
            </section>
            @endif

            @yield('content')
        </div>

        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                {{ __('lang.Developed by') }} <a href="#" target="_blank">OSHNI SOFTWARE</a>
            </div>
            <strong>
                {{ __('lang.Copyright') }} &copy; {{ date('Y') }} {{ config('app.name', 'sudip.me') }}.
            </strong> {{ __('lang.All rights reserved') }}
        </footer>
    </div>

    <script> var base_url = '{{ url('') }}'; </script>
    <script src="{{ asset('admin-assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/jquery/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/datetimepicker/jquery.datetimepicker.full.min.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/fancybox-3.0/jquery.fancybox.min.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/validate/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('admin-assets/js/app.min.js') }}"></script>
    @yield('scripts')
</body>
</html>
