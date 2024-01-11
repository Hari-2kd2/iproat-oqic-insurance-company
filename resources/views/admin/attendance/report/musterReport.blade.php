@extends('admin.master')
@section('content')
@section('title')
    @lang('attendance.attendance_muster_report')
@endsection
<style>
    .present {
        color: #7ace4c;
        font-weight: 700;
        cursor: pointer;
    }

    .absence {
        color: #f33155;
        font-weight: 700;
        cursor: pointer;
    }

    .leave {
        color: #41b3f9;
        font-weight: 700;
        cursor: pointer;
    }

    .bolt {
        font-weight: 700;
    }
</style>
<script>
    jQuery(function() {
        $("#attendanceMusterReport").validate();
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
                                {{ Form::open([
                                    'route' => 'attendanceMusterReport.attendanceMusterReport',
                                    'id' => 'attendanceMusterReport',
                                ]) }}
                                <br>
                                <div class="row" style="margin:0 5% 0 5%">
                                    <div class="col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label" for="branch_id">@lang('common.branch'):</label>
                                            <select name="branch_id" class="form-control branch_id  select2">
                                                <option value="">--- @lang('common.all') ---</option>
                                                @foreach ($branchList as $value)
                                                    <option value="{{ $value->branch_id }}"
                                                        @if ($value->branch_id == $branch_id) {{ 'selected' }} @endif>
                                                        {{ $value->branch_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label" for="department_id">@lang('common.department'):</label>
                                            <select name="department_id" class="form-control department_id  select2">
                                                <option value="">--- @lang('common.all') ---</option>
                                                @foreach ($departmentList as $value)
                                                    <option value="{{ $value->department_id }}"
                                                        @if ($value->department_id == $department_id) {{ 'selected' }} @endif>
                                                        {{ $value->department_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label" for="employee_id">@lang('common.employee'):</label>
                                            <select name="employee_id" class="form-control employee_id  select2">
                                                <option value="">--- @lang('common.all') ---</option>
                                                @foreach ($employeeList as $value)
                                                    <option value="{{ $value->employee_id }}"
                                                        @if ($value->employee_id == $employee_id) {{ 'selected' }} @endif>
                                                        {{ $value->first_name . ' ' . $value->last_name . '(' . $value->finger_id . ')' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label" for="email">@lang('common.from_date')<span
                                                    class="validateRq">*</span></label>
                                            <input type="text" class="form-control dateField required" readonly
                                                placeholder="@lang('common.from_date')" name="from_date"
                                                value="@if (isset($from_date)) {{ $from_date }}@else {{ dateConvertDBtoForm(date('Y-m-01')) }} @endif">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label" for="email">@lang('common.to_date')<span
                                                    class="validateRq">*</span></label>
                                            <input type="text" class="form-control dateField required" readonly
                                                placeholder="@lang('common.to_date')" name="to_date"
                                                value="@if (isset($to_date)) {{ $to_date }}@else {{ dateConvertDBtoForm(date('Y-m-t', strtotime(date('Y-m-01')))) }} @endif">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-2">
                                        <div class="form-group">
                                            <input type="submit" id="filter" style="margin-top: 28px;width:100px"
                                                class="btn btn-instagram" value="@lang('common.filter')">
                                        </div>
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                        <hr>
                        @if (count($results) > 0 && $results != '')
                            <h4 class="text-right">
                                <a class="btn btn-success" style="color: #fff"
                                    href="{{ URL('downloadMusterAttendanceExcel/?employee_id=' . $employee_id . '&from_date=' . $from_date . '&to_date=' . $to_date . '&department_id=' . $department_id . '&branch_id=' . $branch_id) }}">
                                    Export CSV</a>
                            </h4>
                        @endif
                        <div class="table-responsive">
                            <table id="mustertableData" class="table table-bordered table-hover"
                                style="font-size: 12px;font-weight:400">
                                <thead>
                                    {{-- <tr class="tr_header">
                                        <th style="font-size: 13px;font-weight:500;"
                                            colspan="{{ count($monthToDate) + 6 }}" class="text-center">
                                            {{ 'Attendance Summary Report  - ' . $start_date . ' ' . ' to ' . ' ' . $end_date }}
                                        </th>
                                    </tr> --}}
                                    <tr class="tr_header">
                                        <th style="width: 32px">@lang('common.serial')</th>
                                        <th style="width: 100px">@lang('common.branch')</th>
                                        <th style="width: 100px">@lang('common.employee_id')</th>
                                        <th style="width: 100px">@lang('common.name')</th>
                                        <th style="width: 100px">@lang('common.department')</th>
                                        <th style="width: 100px">@lang('common.in_out_shift')</th>
                                        @foreach ($monthToDate as $head)
                                            <th>{{ $head['day'] }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    {{ $sl = null }}
                                    @foreach ($results as $fingerID => $attendance)
                                        <tr rowspan="5">

                                            <td>{{ ++$sl }}</td>
                                            <td>{{ $attendance[0]['branch_name'] }}</td>
                                            <td>{{ $fingerID }}</td>
                                            <td>{{ $attendance[0]['fullName'] }}</td>
                                            <td>{{ $attendance[0]['department_name'] }}</td>
                                            <td>
                                                {{ 'Shift Name' }}
                                                <br>
                                                {{ 'In Time' }}
                                                <br>
                                                {{ 'Out Time' }}
                                                <br>
                                                {{ 'Working.Hrs' }}
                                                <br>
                                                {{ 'Over Time' }}
                                                <br>
                                            </td>

                                            @foreach ($attendance as $data)
                                                @if (strtotime($data['date']) <= strtotime(date('Y-m-d')))
                                                    <td>
                                                        {{ $data['shift_name'] != null ? $data['shift_name'] : 'NA' }}
                                                        <br>
                                                        {{ $data['in_time'] != null ? date('H:i', strtotime($data['in_time'])) : '-:-' }}
                                                        <br>
                                                        {{ $data['out_time'] != null ? date('H:i', strtotime($data['out_time'])) : '-:-' }}
                                                        <br>
                                                        {{ $data['working_time'] != null ? date('H:i', strtotime($data['working_time'])) : '-:-' }}
                                                        <br>
                                                        {{ $data['over_time'] != null ? date('H:i', strtotime($data['over_time'])) : '-:-' }}
                                                        <br>
                                                    </td>
                                                @else
                                                    <td></td>
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

@section('page_scripts')
<script type="text/javascript">
    $('#mustertableData').DataTable({
        "ordering": false,
    });

    $(document).ready(function() {
        $("#musterexcelexport").click(function(e) {
            //getting values of current time for generating the file name
            var dt = new Date();
            var day = dt.getDate();
            var month = dt.getMonth() + 1;
            var year = dt.getFullYear();
            var hour = dt.getHours();
            var mins = dt.getMinutes();
            var date = day + "." + month + "." + year;
            var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
            //creating a temporary HTML link element (they support setting file names)
            var a = document.createElement('a');
            //getting data from our div that contains the HTML table
            var data_type = 'data:application/vnd.ms-excel';
            var table_div = document.getElementById('mustertableData');
            var table_html = table_div.outerHTML.replace(/ /g, '%20');
            a.href = data_type + ', ' + table_html;
            //setting the file name
            a.download = 'SummaryReport-' + year + month + day + hour + mins + '.xls';
            //triggering the function
            a.click();
            //just in case, prevent default behaviour
            e.preventDefault();
        });


    });
</script>
@endsection
