@extends('layouts.admin')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li {{ (isset($list)) ? 'class=active' : '' }}>
                <a href="{{ route('stock-return.index').qString() }}">
                    <i class="fa fa-list" aria-hidden="true"></i> Return {{ __('lang.List') }}
                </a>
            </li>

            <li {{ (isset($create)) ? 'class=active' : '' }}>
                <a href="{{ route('stock-return.create').qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> {{ __('lang.Add') }} Return
                </a>
            </li>

            @if (isset($edit))
            <li class="active">
                <a href="#">
                    <i class="fa fa-edit" aria-hidden="true"></i> {{ __('lang.Edit') }} Return
                </a>
            </li>
            @endif

            @if (isset($show))
            <li class="active">
                <a href="#">
                    <i class="fa fa-list-alt" aria-hidden="true"></i> Return  {{ __('lang.Details') }}
                </a>
            </li>
            @endif
        </ul>

        <div class="tab-content">
            @if(isset($show))
            <div class="tab-pane active">
                <div class="box-body table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width:170px;">Supplier</th>
                            <th style="width:10px;">:</th>
                            <td>{{ $data->supplier != null ? $data->supplier->name : ''}}</td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <th>:</th>
                            <td>{{ dateFormat($data->date) }}</td>
                        </tr>
                        <tr>
                            <th>Stockin Date</th>
                            <th>:</th>
                            <td>{{ dateFormat($data->stock->date) }}</td>
                        </tr>
                        <tr>
                            <th>Stockin Challan Number</th>
                            <th>:</th>
                            <td>{{ $data->stock->challan_number }}</td>
                        </tr>
                        <tr>
                            <th>Stockin Challan Date</th>
                            <th>:</th>
                            <td>{{ dateFormat($data->stock->challan_date) }}</td>
                        </tr>
                    </table>
                    
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Unit</th>
                                <th style="text-align: right;">Quantity</th>
                                <th style="text-align: right;">Unit Price</th>
                                <th style="text-align: right;">Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data->items as $key => $item)
                            <tr>
                                <td>{{ $item->product != null ? $item->product->name : '-' }}</td>
                                <td>{{ $item->unit != null ? $item->unit->name : '-' }}</td>
                                <td style="text-align: right;">{{ $item->quantity }}</td>
                                <td style="text-align: right;">{{ $item->unit_price }}</td>
                                <td style="text-align: right;">{{ $item->amount }}</td>
                            </tr>
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <th style="text-align: right;" colspan="2">Total Quantity :</th>
                                <th style="text-align: right;">{{ $data->items->sum('quantity') }}</th>
                                <th style="text-align: right;">Sub Total Amount:</th>
                                <th style="text-align: right;">{{ numberFormat($data->items->sum('amount')) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @elseif(isset($edit) || isset($create))
            <div class="tab-pane active">
                <div class="box-body">
                    <form method="POST" action="{{ isset($edit) ? route('stock-return.update', $edit) : route('stock-return.store') }}{{ qString() }}" id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                        @csrf

                        @if (isset($edit))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label class="control-label col-sm-3 required">Supplier :</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" name="supplier_id" id="supplier_id" required onchange="getStockIn()">
                                            <option value="">Select Supplier</option>
                                            @php ($supplier_id = old('supplier_id', isset($data) ? $data->supplier_id : ''))
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" {{ ($supplier_id == $supplier->id) ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                        
                                        @if ($errors->has('supplier_id'))
                                            <span class="help-block">{{ $errors->first('supplier_id') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('stock_id') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Stock In :</label>
                                    <div class="col-md-9">
                                        <select class="form-control" name="stock_id" id="stock_id" onchange="stockItems()" required>
                                            <option value="">Select Stock In</option>
                                            @if(isset($stocks))
                                                @php ($stock_id = old('stock_id', isset($data) ? $data->stock_id : ''))
                                                @foreach($stocks as $stock)
                                                    <option value="{{ $stock->id }}" {{ ($stock_id == $stock->id) ? 'selected' : '' }}>{{ $stock->challan_number ?? 'N/A' }} / {{ $stock->date }}</option>
                                                @endforeach
                                            @endif
                                        </select>

                                        @if ($errors->has('stock_id'))
                                            <span class="help-block">{{ $errors->first('stock_id') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Date :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control datepicker" name="date" value="{{ old('date', isset($data) ? dbDateRetrieve($data->date) : date('d-m-Y')) }}" required>

                                        @if ($errors->has('date'))
                                            <span class="help-block">{{ $errors->first('date') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box-body table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Unit</th>
                                        <th>U.P.</th>
                                        <th>Stockin Qty</th>
                                        <th>Returned Qty</th>
                                        <th>Rem. Qty</th>
                                        <th class="required">Returning Qty</th>
                                    </tr>
                                </thead>
                                <tbody id="responseHtml">
                                    @foreach($returnItems as $i => $item)
                                    <tr>
                                        <td style="width: 20%;">
                                            <input type="hidden" name="stock_return_item_id[]" value="{{ $item->id }}">
                                            <input type="hidden" name="stock_item_id[]" value="{{ $item->stock_item_id }}">
                                            <input type="hidden" name="product_id[]" value="{{ $item->product_id }}">
                                            <input type="hidden" name="unit_id[]" value="{{ $item->unit_id }}">
                                            <input type="text" class="form-control" value="{{ $item->product->name }}" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" value="{{ $item->unit->name }}" readonly>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" name="unit_price[]" value="{{ $item->unit_price }}" readonly>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" value="{{ $item->stock_quantity }}" readonly>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" value="{{ $item->returned_quantity }}" readonly>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" name="remain_quantity[]" id="remain_quantity{{ $i }}" value="{{ $item->remain_quantity }}" readonly>
                                        </td>
                                        <td>
                                            <input type="number" step="any" min="1" class="form-control" name="quantity[]" id="quantity{{ $i }}" onkeyUp="chkQty({{ $i }})" value="{{ $item->quantity }}">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-right"><strong>Total Quantity :</strong></td>
                                        <td class="text-right">
                                            <input type="text" class="form-control" readonly id="total_quantity" value="{{isset($edit) ? $returnItems->sum('quantity') : ''}}">
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="form-group">
                            <div class="text-center">
                                <button type="submit" class="btn btn-success btn-flat">{{ isset($edit) ? __('lang.Update') : __('lang.Create') }}</button>
                                <button type="reset" class="btn btn-warning btn-flat">{{ __('lang.Clear') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @elseif (isset($list))
            <div class="tab-pane active">
                <form method="GET" action="{{ route('stock-return.index') }}" class="form-inline">
                    <div class="box-header text-right">
                        <div class="row">
                            <div class="form-group">
                                <input type="text" class="form-control" name="from" id="datepickerFrom" value="{{ dbDateRetrieve(Request::get('from')) }}" placeholder="From Date">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" name="to" id="datepickerTo" value="{{ dbDateRetrieve(Request::get('to')) }}" placeholder="To Date">
                            </div>

                            <div class="form-group">
                                <select class="form-control" name="supplier">
                                    <option value="">Any Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ (Request::get('supplier') == $supplier->id) ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-info btn-flat">{{ __('lang.Search') }}</button>
                                <a class="btn btn-warning btn-flat" href="{{ route('stock-return.index') }}">X</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="box-body table-responsive">
                    <table class="table table-bordered table-hover dataTable">
                        <thead>
                            <tr>
                                <th>Supplier</th>
                                <th>Date</th>
                                <th>Challan Number</th>
                                <th>Challan Date</th>
                                <th class="text-right">Quantity</th>
                                <th class="text-right">Amount</th>
                                <th class="col-action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockReturns as $val)
                            <tr>
                                <td>{{ isset($val->supplier) ? $val->supplier->name : ''}}</td>
                                <td>{{ dateFormat($val->date) }}</td>
                                <td>{{ $val->stock->challan_number ?? 'N/A' }}</td>
                                <td>{{ dateFormat($val->stock->challan_date) }}</td>
                                <td class="text-right">{{ $val->items->sum('quantity') }} </td>
                                <td class="text-right">{{ $val->items->sum('amount') }} </td>
                                <td>
                                    <div class="dropdown">
                                        <a class="btn btn-default btn-flat btn-xs dropdown-toggle" type="button" data-toggle="dropdown">Action <span class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li><a href="{{ route('stock-return.show', $val->id).qString() }}"><i class="fa fa-eye"></i> Show</a></li>
                                            <li><a href="{{ route('stock-return.print', $val->id).qString() }}"><i class="fa fa-print"></i> Print</a></li>
                                            <li><a href="{{ route('stock-return.edit', $val->id).qString() }}"><i class="fa fa-eye"></i> Edit</a></li>
                                            <li><a onclick="deleted('{{ route('stock-return.destroy', $val->id).qString() }}')"><i class="fa fa-close"></i> Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-sm-4 pagi-msg">{!! pagiMsg($stockReturns) !!}</div>

                    <div class="col-sm-4 text-center">
                        {{ $stockReturns->appends(Request::except('page'))->links() }}
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
            @endif
        </div>
    </div>
</section>
@endsection

@section('scripts')
    <script>
        function getStockIn() {
            var supplierId = $('#supplier_id').val();
            $.ajax({
                type: "POST",
                dataType: 'JSON',
                url: "{{ route('supplier-wise-stock-ajax') }}",
                data: { 'supplier_id': supplierId },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    var options = '<option value="">Select Stock In</option>';
                    if (res.status) {
                        res.stocks.forEach((item, i) => {
                            options += '<option value="' + item.id + '">' + item.challan_number +' / '+ item.date+ '</option>';
                        });
                    }
                    $('#stock_id').html(options);
                }
            });
        }

        function stockItems() {
            var stockId = $('#stock_id').val();
            $.ajax({
                type: "POST",
                url: "{{ route('stock-item-ajax') }}",
                data: {
                    'stock_id': stockId,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    var html = '';
                    if (res.status) {
                        res.items.forEach((item, i) => {
                            html += `<tr>
                                <td style="width: 20%;">
                                    <input type="hidden" name="stock_return_item_id[]" value="${item.id}">
                                    <input type="hidden" name="stock_item_id[]" value="${item.stock_item_id}">
                                    <input type="hidden" name="product_id[]" value="${item.product_id}">
                                    <input type="hidden" name="unit_id[]" value="${item.unit_id}">
                                    <input type="text" class="form-control" value="${item.product.name}" readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control" value="${item.unit.name}" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="unit_price[]" value="${item.unit_price}" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control" value="${item.stock_quantity}" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control" value="${item.returned_quantity}" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="remain_quantity[]" id="remain_quantity${i}" value="${item.remain_quantity}" readonly>
                                </td>
                                <td>
                                    <input type="number" step="any" min="1" class="form-control" name="quantity[]" id="quantity${i}" onkeyUp="chkQty(${i})">
                                </td>
                            </tr>`;
                        });
                    }
                    $('#responseHtml').html(html);
                }
            });
        }

        function chkQty(key) {
            var remQty = Number($('#remain_quantity'+key).val());

            var quantity = Number($('#quantity'+key).val());
            if (isNaN(quantity)) {
                $('#quantity'+key).val('');
                $('#quantity'+key).focus();
                alerts('Please Provide Valid Price!');
            }

            if (quantity > remQty) {
                $('#quantity'+key).val('');
                $('#quantity'+key).focus();
                alerts('You are not allowed to input greater than remaining quantity!');
            }

            var totalQuantity = 0;
            $("input[id^='quantity']").each(function () {
                totalQuantity += +$(this).val();
            });
            $('#total_quantity').val(totalQuantity);
        }
    </script>
@endsection
