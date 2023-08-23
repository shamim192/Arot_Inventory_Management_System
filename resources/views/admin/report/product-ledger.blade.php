@extends('layouts.admin')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active">
                <a>
                    <i class="fa fa-list" aria-hidden="true"></i> {{ __('lang.Product Ledger') }} {{ __('lang.Reports') }}
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active">
                <form method="GET" action="{{ route('report.product-ledger') }}" class="form-inline">
                    <div class="box-header text-right">
                        <div class="row">
                            <div class="form-group">
                                <select name="product" class="form-control" required>
                                    <option value="">Select Product</option>
                                    @foreach($products as $pro)
                                        <option value="{{ $pro->id }}" {{ (Request::get('product') == $pro->id) ? 'selected' : '' }}>{{ $pro->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <select name="unit" class="form-control">
                                    <option value="">Convert Unit</option>
                                    @foreach($units as $u)
                                        <option value="{{ $u->id }}" {{ (Request::get('unit') == $u->id) ? 'selected' : '' }}>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-info btn-flat">{{ __('lang.Search') }}</button>
                                <a class="btn btn-warning btn-flat" href="{{ route('report.product-ledger') }}">X</a>
                            </div>
                        </div>
                    </div>
                </form>

                @if(isset($product))
                <div class="box-body table-responsive">
                    <h2 class="text-center" style="margin:0; text-decoration: underline;">{{ $product->name }}</h2>
                    @if(!empty($unit))
                        <h3 class="text-center" style="margin:0; text-decoration: underline;">Unit: {{ $unit->name }}</h3>
                    @endif

                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th style="text-align: right;">Quantity</th>
                                <th style="text-align: right;">Stock</th>
                                {{-- <th style="text-align: right;">Stock In</th>
                                <th style="text-align: right;">Stock Return</th>
                                <th style="text-align: right;">Sale</th>
                                <th style="text-align: right;">Sale Return</th>
                                <th style="text-align: right;">Stock</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @php($stockQty = 0)
                            @foreach($reports as $val)
                            @if($val->type == 'Stock Return' || $val->type == 'Sale')
                                @php($stockQty -= $val->actual_quantity)
                            @else
                                @php($stockQty += $val->actual_quantity)
                            @endif
                            <tr>
                                <td><a href="{{ route($val->route, $val->rowId) }}" target="_blank">{{ $val->rowId }}</td>
                                <td>{{ dateFormat($val->created_at) }}</td>
                                <td>{{ $val->type }}</td>

                                <td style="text-align: right;">
                                    {{ amountByUnit($val->actual_quantity, $product->base_unit, $unit) }}
                                </td>
                                <td style="text-align: right;">
                                    {{ amountByUnit($stockQty, $product->base_unit, $unit) }}
                                </td>

                                {{-- <td style="text-align: right;">
                                    @if($val->type == 'Stock In') 
                                        {{ $unit != null ? ($val->actual_quantity/$unit->quantity) : $val->actual_quantity }}
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    @if($val->type == 'Stock Return') 
                                        ({{ $unit != null ? ($val->actual_quantity/$unit->quantity) : $val->actual_quantity }})
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    @if($val->type == 'Sale') 
                                        ({{ $unit != null ? ($val->actual_quantity/$unit->quantity) : $val->actual_quantity }})
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    @if($val->type == 'Sale Return') 
                                        {{ $unit != null ? ($val->actual_quantity/$unit->quantity) : $val->actual_quantity }}
                                    @endif
                                </td>
                                <td style="text-align: right;">{{ $unit != null ? ($stockQty/$unit->quantity) : $stockQty }}</td> --}}
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <h2 class="text-center" style="padding: 50px 0;">Select a product first!</h2>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
