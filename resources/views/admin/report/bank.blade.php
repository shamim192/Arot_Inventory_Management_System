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
                <form method="GET" action="{{ route('report.bank') }}" class="form-inline">
                    <div class="box-header text-right">
                        <div class="row">
                            <div class="form-group">
                                <input type="text" class="form-control" name="q" value="{{ Request::get('q') }}" placeholder="Write your search text...">
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-info btn-flat">{{ __('lang.Search') }}</button>
                                <a class="btn btn-warning btn-flat" href="{{ route('report.bank') }}">X</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="box-body table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Bank</th>
                                <th>Branch</th>
                                <th>Account Number</th>
                                <th style="text-align: right;">In Amount</th>
                                <th style="text-align: right;">Out Amount</th>
                                <th style="text-align: right;">Balance Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $val)
                            <tr>
                                <td><a href="{{ route('report.bank-transactions').'?bank='.$val->id }}">{{ $val->name }}</a></td>
                                <td>{{ $val->branch }}</td>
                                <td>{{ $val->account_number }}</td>
                                <td style="text-align: right;">{{ $val->inAmount }} {{ env('CURRENCY') }}</td>
                                <td style="text-align: right;">{{ $val->outAmount }} {{ env('CURRENCY') }}</td>
                                <td style="text-align: right;">{{ $val->balanceAmount }} {{ env('CURRENCY') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-sm-4 pagi-msg">{!! pagiMsg($reports) !!}</div>

                    <div class="col-sm-4 text-center">
                        {{ $reports->appends(Request::except('page'))->links() }}
                    </div>

                    <div class="col-sm-4">
                        <div class="pagi-limit-box">
                            <div class="input-group pagi-limit-box-body">
                                <span class="input-group-addon">Show:</span>

                                <select class="form-control pagi-limit" name="limit">
                                    @foreach(paginations() as $pag)
                                        <option value="{{ qUrl(['limit' => $pag]) }}" {{ ($pag == Request::get('limit')) ? 'selected' : '' }}>{{ $pag }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
