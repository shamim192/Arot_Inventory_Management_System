@extends('layouts.admin')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li {{ (isset($list)) ? 'class=active' : '' }}>
                <a href="{{ route('warehouse-transfer.index').qString() }}">
                    <i class="fa fa-list" aria-hidden="true"></i> Warehouse Transfer {{ __('lang.List') }}
                </a>
            </li>

            <li {{ (isset($create)) ? 'class=active' : '' }}>
                <a href="{{ route('warehouse-transfer.create').qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> {{ __('lang.Add') }} Warehouse Transfer
                </a>
            </li>

            @if (isset($edit))
            <li class="active">
                <a href="#">
                    <i class="fa fa-edit" aria-hidden="true"></i> {{ __('lang.Edit') }} Warehouse Transfer
                </a>
            </li>
            @endif

            @if (isset($show))
            <li class="active">
                <a href="#">
                    <i class="fa fa-list-alt" aria-hidden="true"></i> Warehouse Transfer  {{ __('lang.Details') }}
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
                            <th style="width:150px;">Date</th>
                            <th style="width:10px;">:</th>
                            <td>{{ dateFormat($data->date) }}</td>
                        </tr>
                        <tr>
                            <th>Warehouse (From)</th>
                            <th>:</th>
                            <td>{{ $data->from != null ? $data->from->name : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Warehouse (To)</th>
                            <th>:</th>
                            <td>{{ $data->to != null ? $data->to->name : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Product</th>
                            <th>:</th>
                            <td>{{ $data->product != null ? $data->product->name : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Unit</th>
                            <th>:</th>
                            <td>{{ $data->unit != null ? $data->unit->name : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Quantity</th>
                            <th>:</th>
                            <td>{{ $data->quantity }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @elseif(isset($edit) || isset($create))
            <div class="tab-pane active">
                <div class="box-body">
                    <form method="POST" action="{{ isset($edit) ? route('warehouse-transfer.update', $edit) : route('warehouse-transfer.store') }}{{ qString() }}" id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                        @csrf

                        @if (isset($edit))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group{{ $errors->has('from_warehouse_id') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Warehouse (From):</label>
                                    <div class="col-sm-9">
                                        <select name="from_warehouse_id" class="form-control" required>
                                            <option value="">Select Warehouse</option>
                                            @php ($from_warehouse_id = old('from_warehouse_id', isset($data) ? $data->from_warehouse_id : ''))
                                            @foreach($warehouses as $warehouse)
                                                <option value="{{ $warehouse->id }}" {{ ($from_warehouse_id == $warehouse->id) ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('from_warehouse_id'))
                                            <span class="help-block">{{ $errors->first('from_warehouse_id') }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="form-group{{ $errors->has('to_warehouse_id') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Warehouse (To):</label>
                                    <div class="col-sm-9">
                                        <select name="to_warehouse_id" class="form-control" required>
                                            <option value="">Select Warehouse</option>
                                            @php ($to_warehouse_id = old('to_warehouse_id', isset($data) ? $data->to_warehouse_id : ''))
                                            @foreach($warehouses as $warehouse)
                                                <option value="{{ $warehouse->id }}" {{ ($to_warehouse_id == $warehouse->id) ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('to_warehouse_id'))
                                            <span class="help-block">{{ $errors->first('to_warehouse_id') }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="form-group{{ $errors->has('product_id') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Product:</label>
                                    <div class="col-sm-9">
                                        <select name="product_id" class="form-control" required>
                                            <option value="">Select Product</option>
                                            @php ($product_id = old('product_id', isset($data) ? $data->product_id : ''))
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" {{ ($product_id == $product->id) ? 'selected' : '' }}>{{ $product->name }}</option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('product_id'))
                                            <span class="help-block">{{ $errors->first('product_id') }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="form-group{{ $errors->has('unit_id') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Unit:</label>
                                    <div class="col-sm-9">
                                        <select name="unit_id" class="form-control" required>
                                            <option value="">Select Unit</option>
                                            @php ($unit_id = old('unit_id', isset($data) ? $data->unit_id : ''))
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}" {{ ($unit_id == $unit->id) ? 'selected' : '' }}>{{ $unit->name }}</option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('unit_id'))
                                            <span class="help-block">{{ $errors->first('unit_id') }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Date:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control datepicker" name="date" value="{{ old('date', isset($data) ? dbDateRetrieve($data->date) : '') }}" required>

                                        @if ($errors->has('date'))
                                            <span class="help-block">{{ $errors->first('date') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('quantity') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Quantity:</label>
                                    <div class="col-sm-9">
                                        <input type="number" step="any" min="0" class="form-control" name="quantity" value="{{ old('quantity', isset($data) ? $data->quantity : '') }}" required>

                                        @if ($errors->has('quantity'))
                                            <span class="help-block">{{ $errors->first('quantity') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-3 text-center">
                                        <button type="submit" class="btn btn-success btn-flat">{{ isset($edit) ? __('lang.Update') : __('lang.Create') }}</button>
                                        <button type="reset" class="btn btn-warning btn-flat">{{ __('lang.Clear') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @elseif (isset($list))
            <div class="tab-pane active">
                <form method="GET" action="{{ route('warehouse-transfer.index') }}" class="form-inline">
                    <div class="box-header text-right">
                        <div class="row">
                            <div class="form-group">
                                <select name="warehouse" class="form-control">
                                    <option value="">Any Warehouse</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ (Request::get('warehouse') == $warehouse->id) ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <select name="product" class="form-control">
                                    <option value="">Any Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ (Request::get('product') == $product->id) ? 'selected' : '' }}>{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <select name="unit" class="form-control">
                                    <option value="">Any Unit</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ (Request::get('unit') == $unit->id) ? 'selected' : '' }}>{{ $unit->name }}</option>
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
                                <a class="btn btn-warning btn-flat" href="{{ route('warehouse-transfer.index') }}">X</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="box-body table-responsive">
                    <table class="table table-bordered table-hover dataTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Warehouse (From)</th>
                                <th>Warehouse (To)</th>
                                <th>Product</th>
                                <th>Unit</th>
                                <th>Quantity</th>
                                <th class="col-action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fundTransfers as $val)
                            <tr>
                                <td>{{ dateFormat($val->date) }}</td>
                                <td>{{ $val->from != null ? $val->from->name : '-' }}</td>
                                <td>{{ $val->to != null ? $val->to->name : '-' }}</td>
                                <td>{{ $val->product != null ? $val->product->name : '-' }}</td>
                                <td>{{ $val->unit != null ? $val->unit->name : '-' }}</td>
                                <td>{{ $val->quantity }}</td>
                                <td>
                                    <div class="dropdown">
                                        <a class="btn btn-default btn-flat btn-xs dropdown-toggle" type="button" data-toggle="dropdown">Action <span class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li><a href="{{ route('warehouse-transfer.show', $val->id).qString() }}"><i class="fa fa-eye"></i> Show</a></li>
                                            <li><a href="{{ route('warehouse-transfer.edit', $val->id).qString() }}"><i class="fa fa-eye"></i> Edit</a></li>
                                            <li><a onclick="deleted('{{ route('warehouse-transfer.destroy', $val->id).qString() }}')"><i class="fa fa-close"></i> Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-sm-4 pagi-msg">{!! pagiMsg($fundTransfers) !!}</div>

                    <div class="col-sm-4 text-center">
                        {{ $fundTransfers->appends(Request::except('page'))->links() }}
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
