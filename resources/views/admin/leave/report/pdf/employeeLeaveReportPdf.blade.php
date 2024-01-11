<!DOCTYPE html>
<html lang="en">

<head>
    <title>@lang('leave.employee_leave_report')</title>
    <meta charset="utf-8">
</head>
<style>
    table {
        margin: 0 0 40px 0;
        width: 100%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        display: table;
        border-collapse: collapse;
    }

    .printHead {
        width: 35%;
        margin: 0 auto;
    }

    table,
    td,
    th {
        border: 1px solid black;
    }

    td {
        padding: 5px;
    }

    th {
        padding: 5px;
    }
</style>

<body style="font-size: 12px">
    <div class="printHead">
        @if ($printHead)
            {!! $printHead->description !!}
        @endif
        <br>
        <br>
    </div>
    <div class="container">
        <div>
            <p class="text-center" style="font-size: 18px"><b>Leave Report</b></p>
        </div>
        <div class="table-responsive">
            <table id="" class="table table-bordered">
                <thead class="tr_header">
                    <tr>
                        <th style="width:50px;">@lang('common.serial')</th>
                        <th>@lang('leave.employee')</th>
                        <th>@lang('leave.employee_id')</th>
                        <th>@lang('leave.leave_type')</th>
                        <th>@lang('leave.applied_date')</th>
                        <th>@lang('leave.request_duration')</th>
                        <th>@lang('leave.approve_by')</th>
                        <th>@lang('leave.approve_date')</th>
                        <th>@lang('leave.purpose')</th>
                        <th>@lang('leave.number_of_day')</th>
                    </tr>
                </thead>
                <tbody>
                    {{ $sl = null }}
                    @foreach ($results as $value)
                        <tr>
                            <td>{{ ++$sl }}</td>
                            <td>{{ $value->employee->first_name . ' ' . $value->employee->last_name }}
                            </td>
                            <td>{{ $value->employee->finger_id }}</td>
                            <td>
                                @if ($value->leaveType->leave_type_name)
                                    {{ $value->leaveType->leave_type_name }}
                                @endif
                            </td>
                            <td>{{ dateConvertDBtoForm($value->application_date) }}</td>
                            <td>{{ dateConvertDBtoForm($value->application_from_date) }}
                                <b>to</b>
                                {{ dateConvertDBtoForm($value->application_to_date) }}
                            </td>
                            <td>
                                @if ($value->approveBy->first_name)
                                    {{ $value->approveBy->first_name }}
                                    {{ $value->approveBy->last_name }}
                                @endif
                            </td>
                            <td>{{ dateConvertDBtoForm($value->approve_date) }}</td>
                            <td width="200px;word-wrap: break-word">{{ $value->purpose }}</td>
                            <td>{{ $value->number_of_day }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>
