@extends('admin.master')
@section('content')
@section('title')
    @lang('approve_overtime.approval_list')
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <a href="{{ route('approveOvertime.create') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                    class="fa fa-plus-circle" aria-hidden="true"></i> @lang('approve_overtime.add_overtime_approval')</a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i>@yield('title')</div>
                    <div class="panel-body">
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif

                        <div class="row"
                            style="border: 1px solid #EFEEEF; border-radius:4px;margin:2px;padding:20px 0 0 0;margin-bottom:6px;">
                            <p class="border" style="margin-left:30px">
                                <span><i class="fa fa-upload"></i></span>
                                <span style="margin-left: 4px"><b>Upload Document Here (.xlsx).</b></span>
                            </p>
                            <div class="col-md-8">
                                <form action="{{ route('approveOvertime.import') }}" method="post"
                                    enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="col-md-4">
                                        <input type="file" name="select_file"
                                            class="form-control custom-file-upload">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <input class="form-control dateField" style="height: 32px;background:#fff"
                                            required readonly placeholder="@lang('common.date')" id="date"
                                            name="date"
                                            value="@if (isset($date)) {{ $date }}@else {{ dateConvertDBtoForm(date('Y-m-d')) }} @endif">
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-success btn-sm" style="margin-top: 1px;"
                                            type="submit"><span><i class="fa fa-upload" aria-hidden="true"></i></span>
                                            Upload</button>
                                    </div>
                                </form>
                            </div>
                            <div class="row col-md-4 pull-right">
                                <form action="{{ route('templates.approveOvertimeTemplate') }}" method="get"
                                    enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="col-md-4 col-sm-0"></div>
                                    <div class="col-md-4 form-group">
                                        <input class="form-control dateField" style="height: 32px;background:#fff"
                                            required readonly placeholder="@lang('common.date')" id="date"
                                            name="date"
                                            value="@if (isset($date)) {{ $date }}@else {{ dateConvertDBtoForm(date('Y-m-d')) }} @endif"
                                            required>
                                    </div>
                                    <button class="col-md-4 btn btn-info btn-sm waves-effect waves-light" type="submit"
                                        style="margin-top: 2px;width:100px;">
                                        <i class="fa fa-download" style="margin-right: 2px;"
                                            aria-hidden="true"></i><span>
                                            Template</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="row">
                            <div id="searchBox">
                                {{ Form::open(['route' => 'approveOvertime.index', 'id' => 'approveOvertime', 'method' => 'POST']) }}
                                <div class="form-group">
                                    <div class="col-sm-1"></div>
                                    <div class="col-md-3">
                                        <div class="form-group branchName">
                                            <label class="control-label" for="email">@lang('common.branch')<span
                                                    class="validateRq">*</span></label>
                                            <select class="form-control branch_id select2 required" required
                                                name="branch_id">
                                                @foreach ($branchList as $key => $value)
                                                    <option value="{{ $key }}"
                                                        {{ $key == $branch_id ? 'selected' : '' }}>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group departmentName">
                                            <label class="control-label" for="email">@lang('common.department')<span
                                                    class="validateRq"></span></label>
                                            <select class="form-control department_id select2"
                                                name="department_id">
                                                @foreach ($departmentList as $key => $value)
                                                    <option value="{{ $key }}"  {{ $key == $department_id ? 'selected' : '' }}>
                                                        {{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="control-label" for="date">@lang('common.date')<span
                                                    class="validateRq">*</span>:</label>
                                            <input type="text" class="form-control dateField required"
                                                style="height: 35px;" readonly placeholder="@lang('common.date')"
                                                id="date" name="date"
                                                value="@if (isset($date)) {{ $date }}@else {{ dateConvertDBtoForm(date('Y-m-d')) }} @endif"
                                                required>
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <input type="submit" id="filter" style="margin-top: 25px;"
                                                class="btn btn-info" value="@lang('common.filter')">
                                        </div>
                                    </div>

                                </div>
                                {{ Form::close() }}

                            </div>
                        </div>
                        <br>
                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        {{-- <th >@lang('approve_overtime.branch')</th> --}}
                                        <th>@lang('approve_overtime.employee_id')</th>
                                        <th>@lang('approve_overtime.employee_name')</th>
                                        <th>@lang('approve_overtime.date')</th>
                                        <th>@lang('approve_overtime.actual_overtime')</th>
                                        <th>@lang('approve_overtime.approved_over_time')</th>
                                        <th>@lang('approve_overtime.remark')</th>
                                        <th>@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @foreach ($results as $value)
                                        <td style="width: 50px;">{!! ++$sl !!}</td>
                                        {{-- <td style="width: 50px;">{!! $value->branch->branch_name !!}</td> --}}
                                        <td style="width: 50px;">{!! $value->finger_print_id !!}</td>
                                        <td style="width: 50px;">{!! $value->employee->first_name . ' ' . $value->employee->last_name !!}</td>
                                        <td style="width: 50px;">{!! DateConvertDBToFOrm($value->date) !!}</td>
                                        <td style="width: 50px;">{!! $value->actual_overtime !!}</td>
                                        <td style="width: 50px;">{!! $value->approved_overtime !!}</td>
                                        <td style="width: 50px;">{!! $value->remark !!}</td>
                                        <td style="width: 100px;">
                                            <a href="{!! route('approveOvertime.edit', $value->approve_over_time_id) !!}" class="btn btn-success btn-xs btnColor">
                                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                            </a>
                                            <a href="{!! route('approveOvertime.delete', $value->approve_over_time_id) !!}" data-token="{!! csrf_token() !!}"
                                                data-id="{!! $value->approve_over_time_id !!}"
                                                class="delete btn btn-danger btn-xs deleteBtn btnColor"><i
                                                    class="fa fa-trash-o" aria-hidden="true"></i></a>
                                        </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
