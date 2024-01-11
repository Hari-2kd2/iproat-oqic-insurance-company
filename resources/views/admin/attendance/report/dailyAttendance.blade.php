@extends('admin.master')
@section('content')
@section('title')
    @lang('attendance.daily_attendance')
@endsection
<script>
    jQuery(function() {
        $("#dailyAttendanceReport").validate();
    });
</script>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
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
                                    'route' => 'dailyAttendance.dailyAttendance',
                                    'id' => 'dailyAttendanceReport',
                                    'class' => 'form-horizontal',
                                ]) }}

                                @php
                                    $listStatus = [
                                        '1' => 'Present',
                                        '9' => 'Missing in Punch',
                                        '8' => 'Missing out punch',
                                        '10' => 'Less Hours',
                                        '2' => 'Absent',
                                        '3' => 'Leave',
                                        '4' => 'Holiday',
                                        '11' => 'Comp Off',
                                    ];
                                    $listEarlyLateInStatus = [
                                        '1' => 'Early In',
                                        '3' => 'Late In',
                                    ];
                                    $listEarlyLateOutStatus = [
                                        '2' => 'Early Out',
                                        '4' => 'Late Out',
                                    ];

                                @endphp

                                <div class="row">

                                    <div class="col-md-2"></div>

                                    <div class="col-md-2">
                                        <label class="control-label" for="department_id">@lang('common.branch'):</label>
                                        <select name="branch_name" class="form-control branch_id  select2">
                                            <option value="">--- @lang('common.all') ---</option>
                                            @foreach ($branchList as $value)
                                                <option value="{{ $value->branch_name }}"
                                                    @if ($value->branch_name == $branch_name) {{ 'selected' }} @endif>
                                                    {{ $value->branch_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="control-label" for="email">@lang('common.status'):</label>
                                        <select name="attendance_status"
                                            class="form-control attendance_status  select2">
                                            <option value="">--- @lang('common.all') ---</option>
                                            @foreach ($listStatus as $key => $value)
                                                <option value="{{ $key }}"
                                                    @if ($key == $attendance_status) {{ 'selected' }} @endif>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="control-label" for="email">@lang('common.early_late_in_status'):</label>
                                        <select name="early_late_in_status"
                                            class="form-control early_late_in_status  select2">
                                            <option value="">--- @lang('common.all') ---</option>
                                            @foreach ($listEarlyLateInStatus as $key => $value)
                                                <option value="{{ $key }}"
                                                    @if ($key == $early_late_in_status) {{ 'selected' }} @endif>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="control-label" for="email">@lang('common.early_late_out_status'):</label>
                                        <select name="early_late_out_status"
                                            class="form-control early_late_out_status  select2">
                                            <option value="">--- @lang('common.please_select') ---</option>
                                            @foreach ($listEarlyLateOutStatus as $key => $value)
                                                <option value="{{ $key }}"
                                                    @if ($key == $early_late_out_status) {{ 'selected' }} @endif>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-2">
                                        <label class="control-label" for="email">@lang('common.date')<span
                                                class="validateRq">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control dateField required" readonly
                                                placeholder="@lang('common.date')" name="date"
                                                value="@if (isset($date)) {{ $date }}@else {{ dateConvertDBtoForm(date('Y-m-d')) }} @endif">
                                        </div>
                                    </div>

                                    <div class="row justify-content-center">
                                        <div class="col-md-2">
                                            <input type="submit" id="filter" style="margin-top: 28px;"
                                                class="btn btn-info btn-md" value="@lang('common.filter')">
                                        </div>
                                    </div>
                                </div>

                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>

                    <hr style="margin: 0 12px;height:12px;">

                    <div id="btableData" style="margin: 0 12px;">
                        <div class="table-responsive">
                            <table id="myDataTable" class="table table-bordered" style="font-size: 12px;">
                                <thead class="tr_header bg-title">
                                    <tr>
                                        <th style="width:50px;">@lang('common.serial')</th>
                                        <th style="font-size:12px;">@lang('common.date')</th>
                                        <th style="font-size:12px;">@lang('common.employee_name')</th>
                                        <th style="font-size:12px;">@lang('common.id')</th>
                                        <th style="font-size:12px;">@lang('attendance.department')</th>
                                        <th style="font-size:12px;">@lang('attendance.shift')</th>
                                        <th style="font-size:12px;">@lang('attendance.in_time')</th>
                                        <th style="font-size:12px;">@lang('attendance.out_time')</th>
                                        <th style="font-size:12px;">@lang('attendance.duration')</th>
                                        <th style="font-size:12px;">@lang('attendance.early_in')</th>
                                        <th style="font-size:12px;">@lang('attendance.late_in')</th>
                                        <th style="font-size:12px;">@lang('attendance.early_out')</th>
                                        <th style="font-size:12px;">@lang('attendance.late_out')</th>
                                        <th style="font-size:12px;">@lang('attendance.over_time')</th>
                                        <th style="font-size:12px;width:auto;">@lang('attendance.history_of_records')</th>
                                        <th style="font-size:12px;">@lang('attendance.status')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    {{ $sl = null }}
                                    @foreach ($results as $key => $value)
                                        @php
                                            $zero = '00:00';
                                            $isHoliday = false;
                                            $holidayDate = '';
                                        @endphp
                                        <tr>
                                            <td style="font-size:12px;">{{ ++$sl }}</td>
                                            <td style="font-size:12px;">{{ $value->date }}</td>
                                            <td style="font-size:12px;">{{ $value->fullName }}</td>
                                            <td style="font-size:12px;">{{ $value->finger_print_id }}</td>
                                            <td style="font-size:12px;">{{ $value->department_name }}</td>
                                            <td style="font-size:12px;">{{ $value->shift_name ?? 'N/A' }}</td>
                                            <td style="font-size:12px;">
                                                @php
                                                    if ($value->in_time != '') {
                                                        echo $value->in_time;
                                                    } else {
                                                        echo $zero;
                                                    }
                                                @endphp
                                            </td>
                                            <td style="font-size:12px;">
                                                @php
                                                    if ($value->out_time != '') {
                                                        echo $value->out_time;
                                                    } else {
                                                        echo $zero;
                                                    }
                                                @endphp
                                            </td>
                                            <td style="font-size:12px;">
                                                @php
                                                    if ($value->working_time != null) {
                                                        echo date('H:i', strtotime($value->working_time));
                                                    } else {
                                                        echo $zero;
                                                    }
                                                @endphp
                                                <br />
                                            </td>
                                            <td style="font-size:12px;">
                                                @php
                                                    if ($value->early_by != null) {
                                                        echo "<span style='color:#7ace4c ;font-weight:bold'>" . date('H:i', strtotime($value->early_by)) . '</span>';
                                                    } else {
                                                        echo $zero;
                                                    }
                                                @endphp
                                            </td>
                                            <td style="font-size:12px;">
                                                @php
                                                    if ($value->late_by != null) {
                                                        echo "<span style='color:red ;font-weight:bold'>" . date('H:i', strtotime($value->late_by)) . '</span>';
                                                    } else {
                                                        echo $zero;
                                                    }
                                                @endphp
                                            </td>
                                            <td style="font-size:12px;">
                                                @php
                                                    if ($value->early_out != null) {
                                                        echo "<span style='color:red ;font-weight:bold'>" . date('H:i', strtotime($value->early_out)) . '</span>';
                                                    } else {
                                                        echo $zero;
                                                    }
                                                @endphp
                                            </td>
                                            <td style="font-size:12px;">
                                                @php
                                                    if ($value->late_out != null) {
                                                        echo "<span style='color:#7ace4c ;font-weight:bold'>" . date('H:i', strtotime($value->late_out)) . '</span>';
                                                    } else {
                                                        echo $zero;
                                                    }
                                                @endphp
                                            </td>
                                            <td class="text-center" style="font-size:12px;">
                                                @php
                                                    if (isset($value->over_time) && $value->over_time_status != null) {
                                                        echo 'OT Hr: ' . date('H:i', strtotime($value->over_time)) . '<br>' . 'Status: ' . ($value->over_time_status == 1 ? 'Approved' : 'Not Approved');
                                                    } else {
                                                        echo 'OT Hr: ' . ($value->over_time ? date('H:i', strtotime($value->over_time)) : '-') . '<br>' . 'Status: ' . '-';
                                                    }
                                                @endphp
                                            </td>
                                            <td style="font-size:12px;">
                                                @php
                                                    if ($value->in_out_time != null) {
                                                        echo $value->in_out_time;
                                                    } else {
                                                        echo $zero;
                                                    }
                                                @endphp
                                            </td>

                                            <td style="font-size:12px;">
                                                <?php
                                                echo attStatus($value->attendance_status);
                                                ?>
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
</div>
@endsection

@section('page_scripts')
<script>
    $(document).ready(function() {
        $("#excelexport").click(function(e) {
            //getting values of current time for generating the file name
            var dt = new Date();
            var day = dt.getDate();
            var month = dt.getMonth() + 1;
            var year = dt.getFullYear();
            var hour = dt.getHours();
            var mins = dt.getMinutes();
            var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
            //creating a temporary HTML link element (they support setting file names)
            var a = document.createElement('a');
            //getting data from our div that contains the HTML table
            var data_type = 'data:application/vnd.ms-excel';
            var table_div = document.getElementById('btableData');
            var table_html = table_div.outerHTML.replace(/ /g, '%20');
            a.href = data_type + ', ' + table_html;
            //setting the file name
            a.download = 'attendance_details_' + postfix + '.xls';
            //triggering the function
            a.click();
            //just in case, prevent default behaviour
            e.preventDefault();
        });


    });
</script>
@endsection
