@extends('layouts.admin')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active">
                <a>
                    <i class="fa fa-list" aria-hidden="true"></i> {{ __('lang.Banks') }} {{ __('lang.Reports') }}
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active">
                <form method="GET" action="{{ route('report.bank-transactions') }}" class="form-inline">
                    <div class="box-header text-right">
                        <div class="row">
                            <div class="form-group">
                                <select name="bank" class="form-control" required>
                                    <option value="">Select Bank</option>
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}" {{ (Request::get('bank') == $bank->id) ? 'selected' : '' }}>{{ $bank->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <input type="text" class="form-control" name="from" id="datepickerFrom" value="{{ dbDateRetrieve(Request::get('from')) }}" placeholder="From Date">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" name="to" id="datepickerTo" value="{{ dbDateRetrieve(Request::get('to')) }}" placeholder="To Date">
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-info btn-flat">{{ __('lang.Search') }}</button>
                                <a class="btn btn-warning btn-flat" href="{{ route('report.bank-transactions') }}">X</a>
                            </div>
                        </div>
                    </div>
                </form>

                @if(isset($data))
                <div class="box-body table-responsive">
                    <h2 class="text-center" style="margin:0; text-decoration: underline;">{{ $data->name }}</h2>
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Event</th>
                                <th>Note</th>
                                <th style="text-align: right;">In Amount</th>
                                <th style="text-align: right;">Out Amount</th>
                                <th style="text-align: right;">Balance Amount</th>
                            </tr>
                        </thead>
                       
                        <tbody>
                            @php($balance = 0)
                            @foreach($reports as $val)
                            @if($val->type == 'Out')
                                @php($balance -= $val->amount)
                            @else
                                @php($balance += $val->amount)
                            @endif
                            <tr>
                                <td>{{ dateFormat($val->datetime, 1) }}</td>
                                <td>{{ $val->flag }}</td>
                                <td>{{ $val->note }}</td>
                                <td style="text-align: right;">{{ $val->type == 'In' ? $val->amount.' '.env('CURRENCY') : '' }}</td>
                                <td style="text-align: right;">{{ $val->type == 'Out' ? $val->amount.' '.env('CURRENCY') : '' }}</td>
                                <td style="text-align: right;">{{ $balance.' '.env('CURRENCY') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <h2 class="text-center" style="padding: 50px 0;">Select a bank first!</h2>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
