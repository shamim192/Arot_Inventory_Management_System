@extends('layouts.admin')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active">
                <a>
                    <i class="fa fa-list" aria-hidden="true"></i> {{ __('lang.Customers') }} {{ __('lang.Reports') }}
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active">
                <form method="GET" action="{{ route('report.customer-transactions') }}" class="form-inline">
                    <div class="box-header text-right">
                        <div class="row">
                            <div class="form-group">
                                <select class="form-control" name="customer">
                                    <option value="">Any Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ (Request::get('customer') == $customer->id) ? 'selected' : '' }}>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- <div class="form-group">
                                <input type="text" class="form-control" name="from" id="datepickerFrom" value="{{ dbDateRetrieve(Request::get('from')) }}" placeholder="From Date">
                            </div> --}}
                            <div class="form-group">
                                <input type="text" class="form-control" name="to" id="datepickerTo" value="{{ dbDateRetrieve(Request::get('to')) }}" placeholder="As On Date">
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-info btn-flat">{{ __('lang.Search') }}</button>
                                <a class="btn btn-warning btn-flat" href="{{ route('report.customer-transactions') }}">X</a>
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
                                <th style="text-align: right;">Amount</th>
                                <th style="text-align: right;">Balance Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($balance = 0)
                            @if ($data->previous_due > 0)
                                @php($balance += $data->previous_due)
                                <tr>
                                    <td>{{ dateFormat($data->created_at) }}</td>
                                    <td><a href="{{ route('customer.show', $data->id) }}" target="_blank">Opening Balance</a></td>
                                    <td style="text-align: right;">{{ $data->previous_due.' '.env('CURRENCY') }}</td>
                                    <td style="text-align: right;">{{ $balance.' '.env('CURRENCY') }}</td>
                                </tr>
                            @endif

                            @foreach($reports as $val)
                            @if($val->type == 'Sale Return' || $val->type == 'Payment In')
                                @php($balance -= $val->amount)
                            @else
                                @php($balance += $val->amount)
                            @endif
                            <tr>
                                <td>{{ dateFormat($val->date) }}</td>
                                <td><a href="{{ route($val->route, $val->id) }}" target="_blank">{{ $val->type }}</a></td>
                                <td style="text-align: right;">{{ $val->amount.' '.env('CURRENCY') }}</td>
                                <td style="text-align: right;">{{ $balance.' '.env('CURRENCY') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <h2 class="text-center" style="padding: 50px 0;">Select a customer first!</h2>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
