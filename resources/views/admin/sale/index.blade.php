@extends('layouts.admin')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li {{ (isset($list)) ? 'class=active' : '' }}>
                <a href="{{ route('sale.index').qString() }}">
                    <i class="fa fa-list" aria-hidden="true"></i> Sale {{ __('lang.List') }}
                </a>
            </li>

            <li {{ (isset($create)) ? 'class=active' : '' }}>
                <a href="{{ route('sale.create').qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> {{ __('lang.Add') }} Sale
                </a>
            </li>

            @if (isset($edit))
            <li class="active">
                <a href="#">
                    <i class="fa fa-edit" aria-hidden="true"></i> {{ __('lang.Edit') }} Sale
                </a>
            </li>
            @endif

            @if (isset($show))
            <li class="active">
                <a href="#">
                    <i class="fa fa-list-alt" aria-hidden="true"></i> Sale  {{ __('lang.Details') }}
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
                            <th style="width:120px;">Customer</th>
                            <th style="width:10px;">:</th>
                            <td>{{ $data->customer != null ? $data->customer->name : ''}}</td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <th>:</th>
                            <td>{{ dateFormat($data->date) }}</td>
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
                            <tr>
                                <th style="text-align: right;" colspan="4"><strong>Vat({{ $data->vat_percent }}%) :</strong></th>
                                <th style="text-align: right;">{{ $data->vat_amount }}</th>
                            </tr>
                            <tr>
                                <th style="text-align: right;" colspan="4"><strong>Discount Amount :</strong></th>
                                <th style="text-align: right;">{{ $data->discount_amount }}</th>
                            </tr>
                            <tr>
                                <th style="text-align: right;" colspan="4"><strong>Total Amount :</strong></th>
                                <th style="text-align: right;">{{ $data->total_amount }}</th>
                            </tr>
                            <tr>
                                <th style="text-align: right;" colspan="4"><strong>Paid Amount :</strong></th>
                                <th style="text-align: right;">{{ $data->paid_amount }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @elseif(isset($edit) || isset($create))
            <div class="tab-pane active">
                <div class="box-body">
                    <form method="POST" action="{{ isset($edit) ? route('sale.update', $edit) : route('sale.store') }}{{ qString() }}" id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                        @csrf

                        @if (isset($edit))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label class="control-label col-sm-3 required">Customer :</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" name="customer_id" required>
                                            <option value="">Select Customer</option>
                                            @php ($customer_id = old('customer_id', isset($data) ? $data->customer_id : ''))
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" {{ ($customer_id == $customer->id) ? 'selected' : '' }}>{{ $customer->name }}</option>
                                            @endforeach
                                        </select>
                                        
                                        @if ($errors->has('customer_id'))
                                            <span class="help-block">{{ $errors->first('customer_id') }}</span>
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
                                        <th></th>
                                        <th class="required">Product</th>
                                        <th class="required">Unit</th>
                                        <th class="required">Quantity</th>
                                        <th class="required">Unit Price</th>
                                        <th class="required">Total Price</th>
                                    </tr>
                                </thead>
                                <tbody id="responseHtml">
                                    @foreach($items as $key => $item)
                                    <tr class="subRow" id="row{{$key}}">
                                        <input type="hidden" name="sale_item_id[]" value="{{ $item->id }}">
                                        <td>
                                            @if($key == 0)
                                                <a class="btn btn-success btn-flat" onclick="addRow({{ $key }})"><i class="fa fa-plus"></i></a>
                                            @else
                                                <a class="btn btn-danger btn-flat" onclick="removeRow({{ $key }})"><i class="fa fa-minus"></i></a>
                                            @endif
                                        </td>
                                        <td>
                                            <select name="product_id[]" id="product_id{{ $key }}" class="form-control" required onchange="checkProduct({{ $key }})">
                                                <option value="">Select Product</option>
                                                @foreach($products as $product)
                                                    <option data-stock="{{ $product->stockQty }}" value="{{ $product->id }}" {{ ($item->product_id == $product->id) ? 'selected' : '' }}>{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="unit_id[]" id="unit_id{{ $key }}" class="form-control" required onchange="checkStock({{ $key }})">
                                                <option value="">Select Unit</option>
                                                @foreach($units as $unit)
                                                    <option data-unit_qty="{{ $unit->quantity }}" value="{{ $unit->id }}" {{ ($item->unit_id == $unit->id) ? 'selected' : '' }}>{{ $unit->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="any" min="1" class="form-control" name="quantity[]" id="quantity{{ $key }}" value="{{ $item->quantity }}" onchange="chkItemPrice({{ $key }})" onkeyup="chkItemPrice({{ $key }})" required>
                                        </td>
                                        <td>
                                            <input type="number" step="any" min="0" class="form-control" name="unit_price[]" id="unit_price{{ $key }}" value="{{  $item->unit_price }}" onkeyup="chkItemPrice({{ $key }})" required>
                                        </td>
                                        <td>
                                            <input type="number" step="any" min="0" class="form-control" name="amount[]" id="amount{{ $key }}" value="{{ $item->amount }}" readonly>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td class="text-right" colspan="3"><strong>Total Quantity :</strong></td>
                                        <td class="text-right"><input type="text" class="form-control" readonly name="total_quantity" id="total_quantity" value="{{isset($edit) ? $items->sum('quantity') : ''}}"></td>

                                        <td class="text-right"><strong>Sub Total Amount :</strong></td>
                                        <td class="text-right"><input type="text" class="form-control" readonly name="subtotal_amount" id="subtotal_amount" value="{{isset($edit) ? numberFormat($items->sum('amount')) : ''}}"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-right" colspan="5"><strong>Tax({{ env('VAT_PERCENT') }}%) :</strong></td>
                                        <td class="text-right"><input type="text" class="form-control" readonly name="vat_amount" id="vat_amount" value="{{ old('vat_amount', isset($data) ? $data->vat_amount : '') }}"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-right" colspan="5"><strong>Discount Amount :</strong></td>
                                        <td class="text-right"><input type="text" class="form-control" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', isset($data) ? $data->discount_amount : '') }}" onkeyup="chkAmount('discount_amount')"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-right" colspan="5"><strong>Total Amount :</strong></td>
                                        <td class="text-right"><input type="text" class="form-control" readonly name="total_amount" id="total_amount" value="{{ old('total_amount', isset($data) ? $data->total_amount : '') }}"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-right" colspan="5"><strong>Paid Amount :</strong></td>
                                        <td class="text-right"><input type="text" class="form-control" name="paid_amount" id="paid_amount" value="{{ old('paid_amount', isset($data) ? $data->paid_amount : '') }}" onkeyup="chkPaid()"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-right" colspan="5"><strong>Bank :</strong></td>
                                        <td class="text-right">
                                            <select name="bank_id" id="bank_id" class="form-control">
                                                <option value="">Select Bank</option>
                                                @php ($bank_id = old('bank_id', isset($data) ? $data->bank_id : ''))
                                                @foreach($banks as $bank)
                                                    <option value="{{ $bank->id }}" {{ ($bank_id == $bank->id) ? 'selected' : '' }}>{{ $bank->name }}</option>
                                                @endforeach
                                            </select>

                                            @if ($errors->has('bank_id'))
                                                <span class="help-block">{{ $errors->first('bank_id') }}</span>
                                            @endif
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
                <form method="GET" action="{{ route('sale.index') }}" class="form-inline">
                    <div class="box-header text-right">
                        <div class="row">
                            <div class="form-group">
                                <input type="text" class="form-control" name="from" id="datepickerFrom" value="{{ dbDateRetrieve(Request::get('from')) }}" placeholder="From Date">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" name="to" id="datepickerTo" value="{{ dbDateRetrieve(Request::get('to')) }}" placeholder="To Date">
                            </div>

                            <div class="form-group">
                                <select class="form-control" name="customer">
                                    <option value="">Any Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ (Request::get('customer') == $customer->id) ? 'selected' : '' }}>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-info btn-flat">{{ __('lang.Search') }}</button>
                                <a class="btn btn-warning btn-flat" href="{{ route('sale.index') }}">X</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="box-body table-responsive">
                    <table class="table table-bordered table-hover dataTable">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Date</th>
                                <th class="text-right">Quantity</th>
                                <th class="text-right">Amount</th>
                                <th class="col-action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $val)
                            <tr>
                                <td>{{ isset($val->customer) ? $val->customer->name : ''}}</td>
                                <td>{{ dateFormat($val->date) }}</td>
                                <td class="text-right">{{ $val->total_quantity }} </td>
                                <td class="text-right">{{ numberFormat($val->total_amount) }} </td>
                                <td>
                                    <div class="dropdown">
                                        <a class="btn btn-default btn-flat btn-xs dropdown-toggle" type="button" data-toggle="dropdown">Action <span class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li><a href="{{ route('sale.show', $val->id).qString() }}"><i class="fa fa-eye"></i> Show</a></li>
                                            <li><a href="{{ route('sale.print', $val->id).qString() }}"><i class="fa fa-print"></i> Print</a></li>
                                            <li><a href="{{ route('sale.edit', $val->id).qString() }}"><i class="fa fa-eye"></i> Edit</a></li>
                                            <li><a onclick="deleted('{{ route('sale.destroy', $val->id).qString() }}')"><i class="fa fa-close"></i> Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-sm-4 pagi-msg">{!! pagiMsg($sales) !!}</div>

                    <div class="col-sm-4 text-center">
                        {{ $sales->appends(Request::except('page'))->links() }}
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
        function addRow(key) {
            var newKey = $("tr[id^='row']").length;
            var productOptions = $('#product_id' + key).html();
            var unitOptions = $('#unit_id' + key).html();

            var html = `<tr class="subRow" id="row` + newKey + `">
                <input type="hidden" name="sale_item_id[]" value="0">
                <td><a class="btn btn-danger btn-flat" onclick="removeRow(` + newKey + `)"><i class="fa fa-minus"></i></a></td>
                <td>
                    <select name="product_id[]" id="product_id` + newKey + `" class="form-control" required onchange="checkProduct(` + newKey + `)">` + productOptions + `</select>
                </td>
                <td>
                    <select name="unit_id[]" id="unit_id` + newKey + `" class="form-control" required onchange="checkStock(` + newKey + `)">` + unitOptions + `</select>
                </td>
                <td>
                    <input type="number" step="any" min="1" class="form-control" name="quantity[]" id="quantity` + newKey + `" onchange="chkItemPrice(` + newKey + `)" onkeyup="chkItemPrice(` + newKey + `)" required>
                </td>
                <td>
                    <input type="number" step="any" min="0" class="form-control" name="unit_price[]" id="unit_price` + newKey + `" onkeyup="chkItemPrice(` + newKey + `)" required>
                </td>
                <td>
                    <input type="number" step="any" min="0" class="form-control" name="amount[]" id="amount` + newKey + `" readonly>
                </td>
            </tr>`;
            $('#responseHtml').append(html);
            $('#product_id' + newKey).val('');
        }

        function removeRow(key) {
            $('#row' + key).remove();
        }
        
        function checkProduct(key) {
            var product_id = $('#product_id' + key).val();

            var rowId = $(".subRow").length;
            for (var x = 0; x < rowId; x++) {
                if (x != key) {
                    if ($('#product_id' + x).val() == product_id) {
                        $('#product_id' + key).val('');
                        alerts('This Product Already Entered In This Sale.');
                        return false;
                    }
                }
            }

            checkStock(key);
        }

        function checkStock(key) {
            var quantity = Number($('#quantity' + key).val());

            var stock = Number($('#product_id' + key).find(':selected').data('stock'));
            var unitQty = Number($('#unit_id' + key).find(':selected').data('unit_qty'));

            if (unitQty != null && stock != null) {
                var unitStock = Number(stock / unitQty);
                if (unitStock < quantity) {
                    $('#quantity' + key).val('');
                    $('#quantity' + key).focus();
                    alerts('Stock quantity not exist!');
                }
            }
        }

        function chkItemPrice(key) {
            var quantity = Number($('#quantity' + key).val());
            if (isNaN(quantity)) {
                $('#quantity' + key).val('');
                $('#quantity' + key).focus();
                alerts('Please Provide Valid Quantity!');
            }

            var unit_price = Number($('#unit_price' + key).val());
            if (isNaN(unit_price)) {
                $('#unit_price' + key).val('');
                $('#unit_price' + key).focus();
                alerts('Please Provide Valid Price!');
            }

            var amount = (quantity * unit_price);
            $('#amount' + key).val(amount.toFixed(2));

            checkStock(key);
            totalCal();
        }
        
        function chkAmount(field) {
            var fieldAmount = Number($('#' + field).val());
            if (isNaN(fieldAmount)) {
                $('#' + field).val('');
                $('#' + field).focus();
                alerts('Please Provide Valid Amount!');
            }
            
            totalCal();
        }

        function totalCal() {
            var quantity = 0;
            $("input[id^='quantity']").each(function () {
                quantity += +$(this).val();
            });
            $('#total_quantity').val(quantity);

            var subTotal = 0;
            $("input[id^='amount']").each(function () {
                subTotal += +Number($(this).val());
            });
            $('#subtotal_amount').val(subTotal.toFixed(2));
            
            var vatAmount = 0;
            var vatPercent = '{{ env('VAT_PERCENT') }}';
            if (vatPercent > 0) {
                vatAmount = ((subTotal * vatPercent) / 100);
            }
            $('#vat_amount').val(vatAmount.toFixed(2));

            var discountAmount = Number($('#discount_amount').val());

            var total = ((subTotal + vatAmount) - discountAmount);
            $('#total_amount').val(total.toFixed(2));

        }

        function chkPaid() {
            var paidAmount = Number($('#paid_amount').val());
            if (isNaN(paidAmount)) {
                $('#paid_amount').val('');
                $('#paid_amount').focus();
                alert('Please Provide Valid Amount!');
            }

            // var totalAmount = Number($('#total_amount').val());
            // if (paidAmount > totalAmount) {
            //     $('#paid_amount').val('');
            //     $('#paid_amount').focus();
            //     alert('You Can\'t Paid greater than Total Amount!');
            // }

            if (paidAmount > 0) {
                $('#bank_id').prop('required', true);
            } else {
                $('#bank_id').prop('required', false);
            }
        }
    </script>
@endsection
