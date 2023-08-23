@extends('layouts.admin')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li {{ (isset($list)) ? 'class=active' : '' }}>
                <a href="{{ route('customer-payment.index').qString() }}">
                    <i class="fa fa-list" aria-hidden="true"></i> Customer Payment {{ __('lang.List') }}
                </a>
            </li>

            <li {{ (isset($create)) ? 'class=active' : '' }}>
                <a href="{{ route('customer-payment.create').qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> {{ __('lang.Add') }} Customer Payment
                </a>
            </li>

            @if (isset($edit))
            <li class="active">
                <a href="#">
                    <i class="fa fa-edit" aria-hidden="true"></i> {{ __('lang.Edit') }} Customer Payment
                </a>
            </li>
            @endif

            @if (isset($show))
            <li class="active">
                <a href="#">
                    <i class="fa fa-list-alt" aria-hidden="true"></i> Customer Payment  {{ __('lang.Details') }}
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
                            <td>{{ $data->customer != null ? $data->customer->name : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Bank</th>
                            <th>:</th>
                            <td>{{ $data->bank != null ? $data->bank->name : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Type</th>
                            <th>:</th>
                            <td>{{ $data->type }}</td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <th>:</th>
                            <td>{{ dateFormat($data->date) }}</td>
                        </tr>
                        <tr>
                            <th>Note</th>
                            <th>:</th>
                            <td>{!! nl2br($data->note) !!}</td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <th>:</th>
                            <td>{{ $data->amount }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @elseif(isset($edit) || isset($create))
            <div class="tab-pane active">
                <div class="box-body">
                    <form method="POST" action="{{ isset($edit) ? route('customer-payment.update', $edit) : route('customer-payment.store') }}{{ qString() }}" id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
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

                                <div class="form-group{{ $errors->has('bank_id') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Bank:</label>
                                    <div class="col-sm-9">
                                        <select name="bank_id" class="form-control" required>
                                            <option value="">Select Bank</option>
                                            @php ($bank_id = old('bank_id', isset($data) ? $data->bank_id : ''))
                                            @foreach($banks as $bank)
                                                <option value="{{ $bank->id }}" {{ ($bank_id == $bank->id) ? 'selected' : '' }}>{{ $bank->name }}</option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('bank_id'))
                                            <span class="help-block">{{ $errors->first('bank_id') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Type:</label>
                                    <div class="col-sm-9">
                                        <select name="type" class="form-control" required>
                                            @php ($type = old('type', isset($data) ? $data->type : ''))
                                            @foreach(['In', 'Out'] as $sts)
                                                <option value="{{ $sts }}" {{ ($type == $sts) ? 'selected' : '' }}>{{ $sts }}</option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('type'))
                                            <span class="help-block">{{ $errors->first('type') }}</span>
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

                                <div class="form-group{{ $errors->has('note') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3">Note :</label>
                                    <div class="col-sm-9">
                                        <textarea type="text" class="form-control" name="note" rows="4">{{ old('note', isset($data) ? $data->note : '') }}</textarea>
                                        @if ($errors->has('note'))
                                            <span class="help-block">{{ $errors->first('note') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Amount:</label>
                                    <div class="col-sm-9">
                                        <input type="number" step="any" min="0" class="form-control" name="amount" value="{{ old('amount', isset($data) ? $data->amount : '') }}" required>

                                        @if ($errors->has('amount'))
                                            <span class="help-block">{{ $errors->first('amount') }}</span>
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
                <form method="GET" action="{{ route('customer-payment.index') }}" class="form-inline">
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

                            <div class="form-group">
                                <select name="bank" class="form-control">
                                    <option value="">Any Bank</option>
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
                                <input type="text" class="form-control" name="q" value="{{ Request::get('q') }}" placeholder="Write your search text...">
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-info btn-flat">{{ __('lang.Search') }}</button>
                                <a class="btn btn-warning btn-flat" href="{{ route('customer-payment.index') }}">X</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="box-body table-responsive">
                    <table class="table table-bordered table-hover dataTable">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Bank</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Note</th>
                                <th>Amount</th>
                                <th class="col-action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $val)
                            <tr>
                                <td>{{ $val->customer != null ? $val->customer->name : '-' }}</td>
                                <td>{{ $val->bank != null ? $val->bank->name : '-' }}</td>
                                <td>{{ $val->type }}</td>
                                <td>{{ dateFormat($val->date) }}</td>
                                <td>{{ $val->note }}</td>
                                <td>{{ $val->amount }}</td>
                                <td>
                                    <div class="dropdown">
                                        <a class="btn btn-default btn-flat btn-xs dropdown-toggle" type="button" data-toggle="dropdown">Action <span class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li><a href="{{ route('customer-payment.show', $val->id).qString() }}"><i class="fa fa-eye"></i> Show</a></li>
                                            <li><a href="{{ route('customer-payment.print', $val->id).qString() }}"><i class="fa fa-print"></i> Print</a></li>
                                            <li><a href="{{ route('customer-payment.edit', $val->id).qString() }}"><i class="fa fa-eye"></i> Edit</a></li>
                                            <li><a onclick="deleted('{{ route('customer-payment.destroy', $val->id).qString() }}')"><i class="fa fa-close"></i> Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-sm-4 pagi-msg">{!! pagiMsg($payments) !!}</div>

                    <div class="col-sm-4 text-center">
                        {{ $payments->appends(Request::except('page'))->links() }}
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
