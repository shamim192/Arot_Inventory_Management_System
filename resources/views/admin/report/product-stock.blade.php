@extends('layouts.admin')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active">
                <a>
                    <i class="fa fa-list" aria-hidden="true"></i> {{ __('lang.Product Stock') }} {{ __('lang.Reports') }}
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active">
                <form method="GET" action="{{ route('report.product-stock') }}" class="form-inline">
                    <div class="box-header text-right">
                        <div class="row">
                            <div class="form-group">
                                <select name="unit" class="form-control">
                                    <option value="">Convert Unit</option>
                                    @foreach($units as $u)
                                        <option value="{{ $u->id }}" {{ (Request::get('unit') == $u->id) ? 'selected' : '' }}>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <input type="text" class="form-control" name="q" value="{{ Request::get('q') }}" placeholder="Write your search text...">
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-info btn-flat">{{ __('lang.Search') }}</button>
                                <a class="btn btn-warning btn-flat" href="{{ route('report.product-stock') }}">X</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="box-body table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Unit</th>
                                <th style="text-align: right;">Stock In</th>
                                <th style="text-align: right;">Stock Return</th>
                                <th style="text-align: right;">Sale</th>
                                <th style="text-align: right;">Sale Return</th>
                                <th style="text-align: right;">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $val)
                            <tr>
                                <td><a href="{{ route('report.product-ledger').'?product='.$val->id }}">{{ $val->name }}</a></td>
                                <td>{{ $unit != null ? $unit->name : $val->base_unit }}</td>
                                <td style="text-align: right;">{{ $unit != null ? ($val->stockInQty/$unit->quantity) : $val->stockInQty }}</td>
                                <td style="text-align: right;">({{ $unit != null ? ($val->sReturnQty/$unit->quantity) : $val->sReturnQty }})</td>
                                <td style="text-align: right;">({{ $unit != null ? ($val->saleQty/$unit->quantity) : $val->saleQty }})</td>
                                <td style="text-align: right;">{{ $unit != null ? ($val->cReturnQty/$unit->quantity) : $val->cReturnQty }}</td>
                                <td style="text-align: right;">{{ $unit != null ? ($val->stockQty/$unit->quantity) : $val->stockQty }}</td>
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
