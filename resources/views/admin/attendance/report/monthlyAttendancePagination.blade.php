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
        font-weight: 500;
    }

    td {
        padding: 5px;
    }

    th {
        padding: 5px;
    }
</style>
<div class="container">
    <table class="table table-bordered" style="font-size: 12px;">
        <thead class="tr_header">
            <tr>
                <th colspan="6">@lang('attendance.monthly_attendance_report')</th>
            </tr>
            <tr>
                <td colspan="6"></td>
            </tr>
            <tr>
                <td colspan="2"><b>@lang('common.name') &nbsp;</b></td>
                <td colspan="2"><b>@lang('employee.department') &nbsp;</b></td>
                <td colspan="1"><b>@lang('common.from_date') &nbsp;</b></td>
                <td colspan="1"><b>@lang('common.to_date') &nbsp;</b></td>

            </tr>
            <tr>
                <td colspan="2">{{ $employee_name }}</td>
                <td colspan="2">{{ $department_name }}</td>
                <td>{{ $from_date }}</td>
                <td>{{ $to_date }}</td>
            </tr>
            <tr>
                <td colspan="6"></td>
            </tr>
            <tr>
                <th style="width:100px;"><b>@lang('common.serial')&nbsp;</b></th>
                <th><b>@lang('common.date')&nbsp;</b></th>
                <th><b>@lang('attendance.in_time')&nbsp;</b></th>
                <th><b>@lang('attendance.out_time')&nbsp;</b></th>
                <th><b>@lang('attendance.working_time')&nbsp;</b></th>
                <th><b>@lang('common.status')&nbsp;</b></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalPresent = 0;
            $totalHoliday = 0;
            $totalAbsence = 0;
            $totalLeave = 0;
            $totalLate = 0;
            $totalHour = 0;
            $totalMinit = 0;
            $total_working_hour = 0;
            ?>
            {{ $serial = null }}
            @foreach ($results as $key => $value)
            <tr>
                <td>{{ ++$serial }}</td>
                <td>{{ $value['date'] }}</td>
                <td>

                    @if ($value['in_time'] != '')
                    {{ $value['in_time'] }}
                    @else
                    {{ '--' }}
                    @endif
                </td>
                <td>
                    @if ($value['out_time'] != '')
                    {{ $value['out_time'] }}
                    @else
                    {{ '--' }}
                    @endif
                </td>

                <td>

                    @if ($value['working_time'] == '')
                    {{ '--' }}
                    @else
                    @if ($value['working_time'] != '00:00:00')
                    <?php
                    $d = date('H:i', strtotime($value['working_time']));
                    $hour_minit = explode(':', $d);
                    $totalHour += $hour_minit[0];
                    $totalMinit += $hour_minit[1];
                    $totalPresent++;
                    ?>
                    {{ $d }}
                    @else
                    {{ 'One Time Punch' }}
                    @endif
                        @if($value['permission_status'] == 1)
                            {{'Permission:'.date('H:i', strtotime($value['permission_from_time'])).'-'.date('H:i', strtotime($value['permission_to_time']))}}
                        @endif
                    @endif

                </td>
                <td>
                    @if ($value['action'] == 'Absence')
                    {{ __('common.absence') }}
                    <?php $totalAbsence += 1; ?>
                    @elseif ($value['action'] == 'Leave')
                    {{ __('common.leave') }}
                    <?php $totalLeave += 1; ?>
                    @elseif ($value['action'] == 'Holiday')
                    {{ 'Holiday' }}
                    <?php $totalHoliday += 1; ?>
                    @elseif ($value['action'] == 'Present')
                    {{ __('common.present') }}
                    @endif
                </td>
            </tr>
            <?php
            $totalPresent = $value['total_present'];
            $total_working_hour = ($totalHour * 60 + $totalMinit) / 60;
            ?>
            @endforeach
            <tr>
                <td colspan="6"></td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="3"><b>@lang('attendance.total_working_days'): &nbsp;</b></td>
                <td><b>{{ $serial }}</b>
                    @lang('common.days')
                </td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="3"><b>@lang('attendance.total_present'): &nbsp;</b></td>
                <td><b>{{ $totalPresent / 2 }}</b>
                    @lang('common.days')
                </td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="3"><b>Total Holiday: &nbsp;</b></td>
                <td><b>{{ $totalHoliday }}</b>
                    @lang('common.days')
                </td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="3"><b>Total Leave: &nbsp;</b></td>
                <td><b>{{ $totalLeave }}</b>
                    @lang('common.days')
                </td>
            </tr>
            <tr>
                <td colspan="4"></td>
                <td><b>Total Absent: &nbsp;</b></td>
                <td><b>{{ round($totalAbsence) }}</b>
                    @lang('common.days')</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="3"><b>@lang('attendance.actual_working_hour'): &nbsp;</b></td>
                <td><b>{{ round($total_working_hour) }}</b>
                    @lang('common.hours')</td>
            </tr>
        </tbody>
    </table>
</div>