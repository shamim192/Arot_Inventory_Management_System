@extends('layouts.admin')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li {{ (isset($list)) ? 'class=active' : '' }}>
                <a href="{{ route('user.index').qString() }}">
                    <i class="fa fa-list" aria-hidden="true"></i> User {{ __('lang.List') }}
                </a>
            </li>

            <li {{ (isset($create)) ? 'class=active' : '' }}>
                <a href="{{ route('user.create').qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> {{ __('lang.Add') }} User
                </a>
            </li>

            @if (isset($edit))
            <li class="active">
                <a href="#">
                    <i class="fa fa-edit" aria-hidden="true"></i> {{ __('lang.Edit') }} User
                </a>
            </li>
            @endif

            @if (isset($show))
            <li class="active">
                <a href="#">
                    <i class="fa fa-list-alt" aria-hidden="true"></i> User  {{ __('lang.Details') }}
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
                            <th style="width:120px;">Name</th>
                            <th style="width:10px;">:</th>
                            <td>{{ $data->name }}</td>
                        </tr>
                        <tr>
                            <th>Mobile</th>
                            <th>:</th>
                            <td>{{ $data->mobile }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <th>:</th>
                            <td>{{ $data->email }}</td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <th>:</th>
                            <td>{{ $data->account_type }}</td>
                        </tr>

                        {{-- @if($data->account_type == 'Warehouse Manager')
                        <tr>
                            <th>Warehouse</th>
                            <th>:</th>
                            <td>{{ $data->warehouse !=null ? $data->warehouse->name : '-' }}</td>
                        </tr>
                        @endif --}}

                        <tr>
                            <th>Status</th>
                            <th>:</th>
                            <td>{{ $data->status }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @elseif(isset($edit) || isset($create))
            <div class="tab-pane active">
                <div class="box-body">
                    <form method="POST" action="{{ isset($edit) ? route('user.update', $edit) : route('user.store') }}{{ qString() }}" id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                        @csrf

                        @if (isset($edit))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Name:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="name" value="{{ old('name', isset($data) ? $data->name : '') }}" required>

                                        @if ($errors->has('name'))
                                            <span class="help-block">{{ $errors->first('name') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Mobile:</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <span class="input-group-addon">+88</span>
                                            <input type="text" class="form-control" name="mobile" value="{{ old('mobile', isset($data) ? $data->mobile : '') }}" required pattern="[0-9]{11}" title="11 Digit mobile number without country code">
                                        </div>

                                        @if ($errors->has('mobile'))
                                            <span class="help-block">{{ $errors->first('mobile') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Email:</label>
                                    <div class="col-sm-9">
                                        <input type="email" class="form-control" name="email" value="{{ old('email', isset($data) ? $data->email : '') }}" required>

                                        @if ($errors->has('email'))
                                            <span class="help-block">{{ $errors->first('email') }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 {!! isset($create) ? 'required' : '' !!}">Password:</label>
                                    <div class="col-sm-9">
                                        <input type="password" class="form-control" name="password" {{ isset($create) ? 'required' : '' }}>
        
                                        @if ($errors->has('password'))
                                            <span class="help-block">{{ $errors->first('password') }}</span>
                                        @endif
                                    </div>
                                </div>
        
                                <div class="form-group">
                                    <label class="control-label col-sm-3 {!! isset($create) ? 'required' : '' !!}">Confirm password:</label>
                                    <div class="col-sm-9">
                                        <input type="password" class="form-control" name="password_confirmation" {{ isset($create) ? 'required' : '' }}>
                                    </div>
                                </div>
                                
                                <div class="form-group{{ $errors->has('account_type') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Role</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" name="account_type" id="account_type" onchange="chkRole()" required>
                                            @php ($account_type = old('account_type', isset($data) ? $data->account_type : ''))
                                            @foreach(['Admin', 'Staff'] as $rl)
                                                <option value="{{ $rl }}" {{ ($rl==$account_type)?'selected':''}}>{{ $rl }}</option>
                                            @endforeach
                                        </select>
            
                                        @if ($errors->has('account_type'))
                                            <span class="help-block">{{ $errors->first('account_type') }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                              

                                <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Status:</label>
                                    <div class="col-sm-9">
                                        <select name="status" class="form-control select2" required>
                                            @php ($status = old('status', isset($data) ? $data->status : ''))
                                            @foreach(['Active', 'Deactivated'] as $sts)
                                                <option value="{{ $sts }}" {{ ($status == $sts) ? 'selected' : '' }}>{{ $sts }}</option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('status'))
                                            <span class="help-block">{{ $errors->first('status') }}</span>
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
                <form method="GET" action="{{ route('user.index') }}" class="form-inline">
                    <div class="box-header text-right">
                        <div class="row">
                            <div class="form-group">
                                <select class="form-control" name="account_type">
                                    @foreach(['Admin', 'Staff'] as $rl)
                                        <option value="{{ $rl }}" {{ (Request::get('account_type') == $rl) ? 'selected' : '' }}>{{ $rl }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <select name="status" class="form-control">
                                    <option value="">Any Status</option>
                                    @foreach(['Active', 'Deactivated'] as $sts)
                                        <option value="{{ $sts }}" {{ (Request::get('status') == $sts) ? 'selected' : '' }}>{{ $sts }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <input type="text" class="form-control" name="q" value="{{ Request::get('q') }}" placeholder="Write your search text...">
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-info btn-flat">{{ __('lang.Search') }}</button>
                                <a class="btn btn-warning btn-flat" href="{{ route('user.index') }}">X</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="box-body table-responsive">
                    <table class="table table-bordered table-hover dataTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>Role</th>                               
                                <th>Status</th>
                                <th class="col-action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $val)
                            <tr>
                                <td>{{ $val->name }}</td>
                                <td>{{ $val->mobile }}</td>
                                <td>{{ $val->email }}</td>
                                <td>{{ $val->account_type }}</td>
                                <td>{{ $val->status }}</td>
                                <td>
                                    <div class="dropdown">
                                        <a class="btn btn-default btn-flat btn-xs dropdown-toggle" type="button" data-toggle="dropdown">Action <span class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li><a href="{{ route('user.show', $val->id).qString() }}"><i class="fa fa-eye"></i> Show</a></li>
                                            <li><a href="{{ route('user.edit', $val->id).qString() }}"><i class="fa fa-eye"></i> Edit</a></li>
                                            <li><a onclick="deleted('{{ route('user.destroy', $val->id).qString() }}')"><i class="fa fa-close"></i> Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-sm-4 pagi-msg">{!! pagiMsg($users) !!}</div>

                    <div class="col-sm-4 text-center">
                        {{ $users->appends(Request::except('page'))->links() }}
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
    function chkRole() {
        var role = $('#account_type').val();
        if (role == 'Warehouse Manager') {
            $('#warehouseBox').show();
            $('#warehouse_id').prop('required', true);
        } else {
            $('#warehouseBox').hide();
            $('#warehouse_id').prop('required', false);
        }
    }
</script>
@endsection