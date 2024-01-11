@extends('admin.master')
@section('content')
@section('title')
    @lang('leave.leave_summary_report')
@endsection
<style>
    .employeeName {
        position: relative;
    }

    #employee_id-error {
        position: absolute;
        top: 66px;
        left: 0;
        width: 100%he;
        width: 100%;
        height: 100%;
    }

    .grid-container {
        display: grid;
        grid-template-columns: auto auto auto auto;
        grid-gap: 10px;
        background: #EDF1F5;
    }

    .grid-container>div {
        background-color: rgba(255, 255, 255, 0.8);
        text-align: center;
        font-size: 30px;
    }

    .item1 {
        grid-row: 1;
    }
</style>
<script>
    jQuery(function() {
        $("#leaveReport").validate();
    });
</script>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>

    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i>@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="row">
                            <div id="searchBox">
                                {{ Form::open(['route' => 'summaryReport.summaryReport', 'id' => 'leaveReport']) }}
                                <div class="col-md-1"></div>
                                <div class="col-md-3">
                                    <div class="form-group employeeName">
                                        <label class="control-label" for="email">@lang('common.employee_name')<span
                                                class="validateRq">*</span></label>
                                        <select class="form-control employee_id select2 required" required
                                            name="employee_id">
                                            <option value="">---- @lang('common.please_select') ----</option>
                                            @foreach ($employeeList as $value)
                                                <option value="{{ $value->employee_id }}"
                                                    @if ($value->employee_id == $employee_id) {{ 'selected' }} @endif>
                                                    {{ $value->first_name }} {{ $value->last_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="email">@lang('common.from_month')<span
                                            class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control monthField required" readonly
                                            placeholder="@lang('common.from_date')" name="from_date"
                                            value="@if (isset($from_date)) {{ $from_date }}@else {{ (date('Y-01')) }} @endif">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label class="control-label" for="email">@lang('common.to_month')<span
                                            class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control monthField required" readonly
                                            placeholder="@lang('common.to_date')" name="to_date"
                                            value="@if (isset($to_date)) {{ $to_date }}@else {{ (date('Y-m')) }} @endif">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="submit" id="filter" style="margin-top: 25px; width: 100px;"
                                            class="btn btn-info" value="@lang('common.filter')">
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                        <hr>
                        @if (count($results) > 0)
                            <h4 class="text-right">
                                <a class="btn btn-success  btn-sm" style="color: #fff"
                                    href="{{ URL('downloadSummaryReport/?employee_id=' . $employee_id . '&from_date=' . $from_date . '&to_date=' . $to_date) }}"><i
                                        class="fa fa-download fa-lg" aria-hidden="true"></i> @lang('common.download')</a>
                            </h4>
                        @endif
                        <div class="table-responsive" style="font-size: 12px">
                            <table id="myDataTable" class="table table-bordered">
                                {{-- @if (count($results) > 0)
                                    <div class="grid-container table-bordered text-center"
                                        style="padding-top:12px;font-weight:500;color:#666666"
                                        style="background:#EDF1F5">
                                        <p>{{ 'Employee ID : ' . $results[0]['finger_id'] }}</p>
                                        <p>{{ 'Name : ' . $results[0]['full_name'] }}</p>
                                        <p>{{ 'Department : ' . $results[0]['department_name'] }}</p>
                                    </div>
                                @endif --}}
                                @php
                                    $count = null;
                                @endphp
                                <thead class="tr_header">
                                    @if (count($results) > 0)
                                        <tr>
                                            <th class="text-center">{{ 'Employee ID' }}</th>
                                            <th class="text-center">{{ 'Name' }}</th>
                                            <th class="text-center">{{ 'Department' }}</th>
                                            @for ($i = 0; $i < count($leaveTypes) - 2; $i++)
                                                <th class="text-center">#</th>
                                            @endfor
                                        </tr>
                                        <tr>
                                            <th class="text-center">{{ $results[0]['finger_id'] }}</th>
                                            <th class="text-center">{{ $results[0]['full_name'] }}</th>
                                            <th class="text-center">{{ $results[0]['department_name'] }}</th>
                                            @for ($i = 0; $i < count($leaveTypes) - 2; $i++)
                                                <th class="text-center">#</th>
                                            @endfor
                                        </tr>
                                    @endif
                                    <tr>
                                        <th class="col-md-1 text-center">@lang('common.month')</th>
                                        @foreach ($leaveTypes as $leaveType)
                                            <th class="col-md-1 text-center">{{ $leaveType->leave_type_name }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                        @foreach ($results as $value)
                                            <tr>
                                                <td class="col-md-1 text-center">{{ $value['month_name'] }}</td>
                                                @foreach ($value['leaveType'] as $key => $noOfDays)
                                                    @if ($noOfDays != '')
                                                        <td class="col-md-1 text-center">{{ $noOfDays }}</td>
                                                    @else
                                                        <td class="col-md-1 text-center">{{ '0' }}</td>
                                                    @endif
                                                @endforeach
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
