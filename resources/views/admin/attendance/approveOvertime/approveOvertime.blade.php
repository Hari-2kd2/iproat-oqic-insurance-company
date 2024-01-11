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

    input[type=checkbox] {
        accent-color: #3F739A;
        margin-bottom: 0px;
        align-items: center;
        align-content: center;
        vertical-align: -2px;
        /* box-shadow: inset 0 0 0.12vw 0.12vw #3F739A; */
        box-shadow: inset 0 0 0.125vw 0.1vw rgba(0, 0, 0, 0.322);
        /* position: absolute; */
        /* visibility: hidden; */
    }

    input[type=checkbox]:checked {
        accent-color: #3F739A;
        margin-bottom: 0px;
        align-items: center;
        align-content: center;
        vertical-align: -2px;
        color: rgba(0, 0, 0, 0.322);
        box-shadow: inset 0 0 0.125vw 0.125vw #3F739A;
        /* position: absolute; */
        /* visibility: hidden; */
    }

    /*
    
    input[type=checkbox]~label {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 20px;
        background: rgb(255, 0, 45);
        border-radius: 2.5vw;
        border: 1px inset rgba(0, 0, 0, 0.5);
        box-shadow: inset 0 0 0.125vw 0.125vw rgba(0, 0, 0, 0.55);
        cursor: pointer;
        transition: background 0.25s ease-out;
    }

    input[type=checkbox]:after {
        position: absolute;
        content: '';
        top: 5%;
        left: 5%;
        height: 90%;
        width: 45%;
        background: rgba(255, 225, 225, 1);
        border-radius: 50%;
        box-shadow: 2px 0 3px 3px rgba(0, 0, 0, 0.25), inset 0 0 0.72vw 0.5vw rgba(55, 55, 55, 0.25);
        transition: left 0.25s linear, background 0.25s linear, box-shadow 0.25s linear;
    }

    input[type=checkbox]~span {
        position: relative;
        left: 100%;
        line-height: 2vw;
        margin-left: 1em;
        white-space: nowrap;
    }

    input[type=checkbox]:checked~label {
        background: rgb(0, 255, 100);
    }

    input[type=checkbox]:checked~label:after {
        position: absolute;
        background: rgba(255, 255, 255, 1);
        content: '';
        top: 5%;
        left: 50%;
        border-radius: 50%;
        box-shadow: -2px 0 3px 3px rgba(0, 0, 0, 0.25), inset 0 0 0.72vw 0.2vw rgba(0, 0, 0, 0.25);
    } */

    .switch-control-label {
        color: rgba(255, 255, 255, 1);
    }

    .switch .switch-control-input:checked~.switch-control-label::after {
        background-color: #fff;
        transform: translateX(1.75rem) !important;
    }

    .switch .switch-control-label::before {
        width: 50px !important;
    }

    table .subtable tr td {
        margin: 0;
        padding: 0;
    }
</style>
<script>
    jQuery(function() {
        $("#overtimeApproval").validate();
    });
</script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
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
                        <div class="row">
                            <div id="searchBox">
                                {{ Form::open([
                                    'route' => 'overtimeApproval.overtimeApproval',
                                    'id' => 'overtimeApproval',
                                ]) }}
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-md-2 col-sm-2">
                                        <div class="form-group">
                                            <label class="control-label" for="branch_id">@lang('common.branch'):<span
                                                    class="validateRq">*</span></label>
                                            <select name="branch_id" class="form-control branch_id select2 required"
                                                required>
                                                <option value="">--- @lang('common.please_select') ---</option>
                                                @foreach ($branchList as $value)
                                                    <option value="{{ $value->branch_id }}"
                                                        @if ($value->branch_id == $branch_id) {{ 'selected' }} @endif>
                                                        {{ $value->branch_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-2">
                                        <div class="form-group">
                                            <label class="control-label" for="employee_id">@lang('common.employee'):</label>
                                            <select name="employee_id" class="form-control employee_id  select2">
                                                <option value="">--- @lang('common.all') ---</option>
                                                @foreach ($employeeList as $value)
                                                    <option value="{{ $value->employee_id }}"
                                                        @if ($value->employee_id == $employee_id) {{ 'selected' }} @endif>
                                                        {{ $value->first_name . ' ' . $value->last_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-2">
                                        <label class="control-label" for="email">@lang('common.from_date')<span
                                                class="validateRq">*</span></label>
                                        <div class="form-group">
                                            <input type="text" id="from_date" class="form-control dateField required"
                                                readonly placeholder="@lang('common.from_date')" name="from_date"
                                                value="@if (isset($from_date)) {{ $from_date }}@else {{ dateConvertDBtoForm(date('Y-m-01')) }} @endif">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-2">
                                        <label class="control-label" for="email">@lang('common.to_date')<span
                                                class="validateRq">*</span></label>
                                        <div class="form-group">
                                            <input type="text" class="form-control dateField required" readonly
                                                placeholder="@lang('common.to_date')" name="to_date" id="to_date"
                                                value="@if (isset($to_date)) {{ $to_date }}@else {{ dateConvertDBtoForm(date('Y-m-t', strtotime(date('Y-m-01')))) }} @endif">
                                        </div>
                                    </div>
                                    <div class="col-md-1 col-sm-1">
                                        <div class="form-group">
                                            <input type="submit" id="filter" style="margin-top: 28px;width:100px"
                                                class="btn btn-info" value="@lang('common.filter')">
                                        </div>
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                        <hr>
                        <div class="table-responsive">

                            {{ Form::open(['route' => 'overtimeApproval.changeOvertimeStatus', 'enctype' => 'multipart/form-data', 'id' => 'changeOvertimeStatus', 'class' => 'form-horizontal']) }}

                            <table id="myTable" class="table table-bordered table-hover"
                                style="font-size: 12px;font-weight:400">
                                <thead>
                                    <tr class="tr_header" style="background: #ECF0F5">
                                        <th class="text-center" style="width: 32px;">@lang('common.serial')</th>
                                        <th class="text-center" style="width: 100px">@lang('common.contractor')</th>
                                        <th class="text-center" style="width: 100px">@lang('common.employee_id')</th>
                                        <th class="text-center" style="width: 100px">@lang('common.name')</th>
                                        <th class="text-center" style="width: 100px">@lang('common.department')</th>
                                        <th class="text-center" style="width: 100px">@lang('common.in_out_shift')</th>
                                        @foreach ($monthToDate as $head)
                                            <th style="width: 80px" class="text-center">{{ $head['day'] }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    {{ $sl = null }}
                                    @foreach ($results as $fingerID => $attendance)
                                        <tr>

                                            <td>{{ ++$sl }}</td>
                                            <td>{{ $attendance[0]['branch_name'] }}</td>
                                            <td>{{ $fingerID }}</td>
                                            <td>{{ $attendance[0]['fullName'] }}</td>
                                            <td>{{ $attendance[0]['department_name'] }}</td>

                                            <td class="text-center">
                                                <table style="width: 100px;margin-bottom: -6px;background:#ECF0F5"
                                                    class="table subtable">
                                                    <tr>
                                                        <td>{{ 'Shift Name' }}</td>
                                                    </tr>
                                                    {{-- <tr>
                                                        <td>{{ 'In Time' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ 'Out Time' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ 'Working.Hrs' }}</td>
                                                    </tr> --}}
                                                    <tr>
                                                        <td>{{ 'Over Time' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="height: 30px">{{ ' O.T Approval' }}</td>
                                                    </tr>
                                                </table>
                                            </td>

                                            @foreach ($attendance as $data)
                                                <td>
                                                    <table class="text-center table subtable"
                                                        style="width: 100px;margin-bottom: 0px;background: 
                                                            @if ($data['attendance_status'] == 'holiday') {{ '#ECF0F5' }} 
                                                            @elseif($data['over_time'] != null && $data['attendance_status'] == 'present') {{ '#ECF0F5' }} 
                                                            @else {{ '#ECF0F5' }} @endif">
                                                        <tr>
                                                            @if ($data['attendance_status'] == 'holiday')
                                                                <td>{{ $data['shift_name'] != null ? $data['shift_name'] : 'PH' }}
                                                                </td>
                                                            @elseif($data['attendance_status'] == 'absence')
                                                                <td>{{ $data['shift_name'] != null ? $data['shift_name'] : 'AA' }}
                                                                </td>
                                                            @else
                                                                <td>{{ $data['shift_name'] != null ? $data['shift_name'] : 'NA' }}
                                                                </td>
                                                            @endif
                                                        </tr>
                                                        {{-- <tr>
                                                            <td>
                                                                {{ $data['in_time'] != null ? date('H:i', strtotime($data['in_time'])) : '-:-' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                {{ $data['out_time'] != null ? date('H:i', strtotime($data['out_time'])) : '-:-' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                {{ $data['working_time'] != null ? date('H:i', strtotime($data['working_time'])) : '-:-' }}
                                                            </td>
                                                        </tr> --}}
                                                        <tr>
                                                            <td>
                                                                {{ $data['over_time'] != null ? date('H:i', strtotime($data['over_time'])) : '-:-' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                @if ($data['over_time'] != null)
                                                                    <div class="switch"
                                                                        style="padding:3px 8px 0px 8px;background:
                                                                            @if ($data['over_time_status'] == App\Lib\Enumerations\OvertimeStatus::$OT_FOUND_AND_APPROVED) {{ '#90CB8E' }}  @else {{ '#F39B9A' }} @endif">
                                                                        <div for="employee_attendance_id"
                                                                            class="switch-control-label">
                                                                            <input
                                                                                data-id="{{ $data['employee_attendance_id'] }}"
                                                                                value="{{ $data['date'] }}"
                                                                                type="text"
                                                                                name="attendance_date[]"
                                                                                id="attendance_date" hidden>
                                                                            <input
                                                                                data-id="{{ $data['employee_attendance_id'] }}"
                                                                                value="{{ $data['employee_attendance_id'] }}"
                                                                                name="approve_attendance[]"
                                                                                tabindex="-1" id="approve_attendance"
                                                                                class="form-check-input switch-control-input approve_attendance approve_attendance{{ $data['employee_attendance_id'] }}"
                                                                                type="checkbox"
                                                                                {{ $data['over_time_status'] == App\Lib\Enumerations\OvertimeStatus::$OT_FOUND_AND_APPROVED ? 'checked' : '' }}>
                                                                            <label>
                                                                                <span> {{ 'YES' }}</span>
                                                                            </label>
                                                                            {{ ' | ' }}
                                                                            <input
                                                                                data-id="{{ $data['employee_attendance_id'] }}"
                                                                                value="{{ $data['employee_attendance_id'] }}"
                                                                                name="reject_overtime[]"
                                                                                tabindex="-1" id="reject_overtime"
                                                                                class="form-check-input switch-control-input reject_overtime reject_overtime{{ $data['employee_attendance_id'] }}"
                                                                                type="checkbox"
                                                                                {{ $data['over_time_status'] == App\Lib\Enumerations\OvertimeStatus::$OT_DIS_APPROVED ? 'checked' : '' }}>
                                                                            <label>
                                                                                <span>{{ 'NO' }}</span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <div
                                                                        style="padding:6px 8px 0px 8px;margin-bottom: -12px;height:30px;background:#ACE5EE;color:#fff">
                                                                        {{ 'NA' }}
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </table>

                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if (count($results) > 0)
                                <div class="text-left" style="margin-bottom: 24px;margin-top: 24px">
                                    <div style="font-size: 14px;font-weight:bold;"
                                        colspan="{{ count($monthToDate) + 6 }}" class="text-left">
                                        <button type="submit" class="btn btn-info btn_style"><i
                                                class="fa fa-check"></i>
                                            @lang('common.save')</button>
                                    </div>
                                </div>
                            @endif

                            {{ Form::close() }}

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
    $(function() {


        $(document).on('change', '.branch_id', function(event) {
            $(".employee_id")[0].selectedIndex = 0;
        });

        $(document).on('change', '.employee_id', function(event) {
            $(".branch_id")[0].selectedIndex = 0;
        });

        $(".branch_id").bind("click", function() {
            $(".employee_id")[0].selectedIndex = 0;
        });

        $(".employee_id").bind("click", function() {
            $(".branch_id")[0].selectedIndex = 0;
        });

        $('.approve_attendance').click(function() {
            var id = $(this).attr('data-id');
            var checked = $('.approve_attendance' + id).is(':checked');
            $('.reject_overtime' + id).prop('checked', false);
        });

        $('.reject_overtime').click(function() {
            var id = $(this).attr('data-id');
            var checked = $('.reject_overtime' + id).is(':checked');
            $('.approve_attendance' + id).prop('checked', false);
        });

        // $('label').click(function() {
        //     var checked = $('input', this).is(':checked');
        //     $('span', this).text(checked ? 'Yes' : 'No');
        // });



        $('.employee_attendance_id').change(function(e) {
            e.preventDefault();
            var id = $('.employee_attendance_id').val();
        });

    });
</script>
@endsection
