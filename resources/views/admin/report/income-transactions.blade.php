@extends('layouts.admin')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active">
                <a>
                    <i class="fa fa-list" aria-hidden="true"></i> {{ __('lang.Income') }} {{ __('lang.Reports') }}
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active">
                <form method="GET" action="{{ route('report.income-transactions') }}" class="form-inline">
                    <div class="box-header text-right">
                        <div class="row">
                           

                            <div class="form-group">
                                <input type="text" class="form-control" name="from" id="datepickerFrom" value="{{ dbDateRetrieve(Request::get('from')) }}" placeholder="From Date">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" name="to" id="datepickerTo" value="{{ dbDateRetrieve(Request::get('to')) }}" placeholder="To Date">
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-info btn-flat">{{ __('lang.Search') }}</button>
                                <a class="btn btn-warning btn-flat" href="{{ route('report.income-transactions') }}">X</a>
                            </div>
                        </div>
                    </div>
                </form>
               

                <div class="box-body table-responsive">
                    <h2 class="text-center" style="margin:0; text-decoration: underline;">{{ $categoryName }}</h2>
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Bank Name</th>
                                <th>Income Date</th>
                                <th>Income Code</th>
                                <th>Income Amount</th>
                                
                            </tr>
                        </thead>
                       
                        <tbody>
                            <?php
                            $totalAmounts = [];
                            ?>
                            @foreach($reports as $income)
                                @if(
                                 !empty($income->BankName) &&
                                    !empty($income->IncomeDate) &&
                                    !empty($income->IncomeCode) &&
                                    !empty($income->IncomeAmount)
                                )
                                    <tr>
                                        <td>{{ $income->BankName }}</td>
                                        <td>{{ $income->IncomeDate }}</td>
                                        <td>{{ $income->IncomeCode }}</td>
                                        <td>{{ $income->IncomeAmount }} {{ env('CURRENCY') }}</td>
                                    </tr>
                                    <?php
                                    $categoryId = $income->category_id;
                                    $totalAmounts[$categoryId] = ($totalAmounts[$categoryId] ?? 0) + $income->IncomeAmount;
                                    ?>
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total Amount</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                
                               
                            </tr>
                            @foreach($totalAmounts as $categoryId => $totalAmount)
                                <tr>
                                    
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>{{ $totalAmount }} {{ env('CURRENCY') }}</td>
                                </tr>
                            @endforeach
                        </tfoot>
                    </table>
                </div>
               
            </div>
        </div>
    </div>
</section>
@endsection
