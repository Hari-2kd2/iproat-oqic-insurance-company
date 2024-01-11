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
        font-size: 10px;
        border: 1px solid black;
    }

    td {
        font-size: 8px;
        padding: 3px;
    }

    th {
        padding: 3px;
    }

    .present {
        color: #7ace4c;
        font-weight: 700;
    }

    .absence {
        color: #f33155;
        font-weight: 700;
    }

    .leave {
        color: #41b3f9;
        font-weight: 700;
    }

    .bolt {
        font-weight: 700;
    }
</style>

<body style="word-wrap:break-word">
    <div class="printHead">
        <p style="margin-left: 32px;margin-top: 10px"><b>Muster Report</b></p>
    </div>
    <div class="container">
        @php
            $colCount = count($monthToDate) + count($leaveTypes) + 3;
        @endphp
        <b aria-colspan="{{ $colCount }}">Month : </b>{{ $month }}
        <div class="table-responsive" style="font-size: 12px">
            <table id="" class="table table-bordered table-striped table-hover" style="font-size: 12px">
                <thead>
                    <tr>
                        <th>@lang('common.serial')</th>
                        <th>@lang('common.year')</th>
                        <th colspan="0" class="totalCol">@lang('common.month')</th>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th>
                            @if (isset($month))
                                @php
                                    
                                    $exp = explode('-', $month);
                                    echo $exp[0];
                                @endphp
                            @else
                                {{ date('Y') }}
                            @endif
                        </th>
                        <th>{{ $monthName }}</th>
                        <th>#</th>
                        <th>#</th>
                        <th>#</th>
                        <th>#</th>
                        @foreach ($monthToDate as $head)
                            <th>{{ $head['day_name'] }}</th>
                        @endforeach
                        <th>@lang('attendance.day_of_worked')</th>
                        <th>@lang('attendance.public_holiday')</th>
                        @foreach ($leaveTypes as $leaveType)
                            <th>{{ $leaveType->leave_type_name }}</th>
                        @endforeach
                        <th>@lang('attendance.total_paid_days')</th>
                        <th>@lang('attendance.weekly_holiday') </th>
                        <th>@lang('attendance.total_days')</th>

                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#</td>
                        <th>@lang('employee.employee_id')</th>
                        <th>@lang('common.name')</th>
                        <th>@lang('employee.designation')</th>
                        <th>@lang('employee.department')</th>
                        <th>@lang('employee.gender')</th>
                        <th>@lang('employee.status')</th>
                        @foreach ($monthToDate as $head)
                            <th>{{ $head['day'] }}</th>
                        @endforeach
                        <th>#</th>
                        <th>#</th>
                        <th>#</th>
                        @foreach ($leaveTypes as $leaveType)
                            <th>#</th>
                        @endforeach
                        <th>#</th>
                        <th>#</th>
                    </tr>

                    @php
                        $sl = null;
                        $totalPresent = 0;
                        $leaveData = [];
                        $totalCol = 0;
                        $totalWorkHour = 0;
                        $totalWeeklyHoliday = 0;
                        $totalGovtHoliday = 0;
                        $totalAbsent = 0;
                        $totalLeave = 0;
                    @endphp
                    @foreach ($results as $key => $value)
                        <tr>
                            <td>{{ ++$sl }}</td>
                            <td>{{ $value[0]['finger_id'] }}</td>
                            <td>{{ $key }}</td>
                            <td>{{ $value[0]['designation_name'] }}</td>
                            <td>{{ $value[0]['department_name'] }}</td>
                            <td>{{ $value[0]['gender'] }}</td>
                            <td>{{ userStatus($value[0]['status']) }}</td>
                            @foreach ($value as $v)
                                @php
                                    // dd($v);
                                    if ($sl == 1) {
                                        $totalCol++;
                                    }
                                    if ($v['attendance_status'] == 'present') {
                                        $totalPresent++;
                                        $shiftName = $v['shift_name'];
                                    
                                        if ($v['inout_status'] == 'O' && $v['gov_day_worked'] == 'yes') {
                                            echo "<td><span style='color:red ;font-weight:bold'>" . $v['inout_status'] . '' . $shiftName ?? 'NA' . '</span></td>';
                                        } else {
                                            echo "<td><span style='color:#7ace4c ;font-weight:bold'>" . $shiftName ?? 'NA' . '</span></td>';
                                        }
                                    } elseif ($v['attendance_status'] == 'absence') {
                                        $totalAbsent++;
                                        echo "<td><span style='color:#000000 ;font-weight:bold'>AA</span></td>";
                                    } elseif ($v['attendance_status'] == 'leave') {
                                        $totalLeave++;
                                        $leaveData[$key][$v['leave_type']][] = $v['leave_type'];
                                        echo "<td><span style='color:#41b3f9 ;font-weight:bold'>" . $v['leave_type'] ?? 'NA' . '</span></td>';
                                    } elseif ($v['attendance_status'] == 'holiday') {
                                        $totalWeeklyHoliday++;
                                        echo "<td><span style='color:turquoise ;font-weight:bold'>WH</span></td>";
                                    } elseif ($v['attendance_status'] == 'publicHoliday') {
                                        $totalGovtHoliday++;
                                        echo "<td><span style='color: turquoise ;font-weight:bold'>PH</span></td>";
                                    } elseif ($v['attendance_status'] == 'left') {
                                        echo '<td></td>';
                                    } else {
                                        echo '<td></td>';
                                    }
                                @endphp
                            @endforeach
                            <td><span class="bolt">{{ $totalPresent }}</span></td>
                            <td><span class="bolt">{{ $totalGovtHoliday }}</span></td>

                            @foreach ($leaveTypes as $leaveType)
                                <td>
                                    <span class="bolt">
                                        @php
                                            if ($sl == 1) {
                                                $totalCol++;
                                            }
                                            if (isset($leaveData[$key][$leaveType->leave_type_name])) {
                                                $c = count($leaveData[$key][$leaveType->leave_type_name]);
                                            } else {
                                                $c = 0;
                                            }
                                        @endphp
                                        {{ $c }}
                                    </span>
                                </td>
                            @endforeach
                            <td><span class="bolt">{{ $totalPresent + $totalLeave + $totalGovtHoliday }}</span>
                            </td>
                            <td><span class="bolt">{{ $totalWeeklyHoliday }}</span></td>
                            <td><span
                                    class="bolt">{{ $totalPresent + $totalWeeklyHoliday + $totalAbsent + $totalLeave }}</span>
                            </td>
                            @php
                                $totalPresent = 0;
                                $totalWeeklyHoliday = 0;
                                $totalAbsent = 0;
                                $totalLeave = 0;
                                $totalGovtHoliday = 0;
                            @endphp
                        </tr>
                    @endforeach
                    <script>
                        // {!! "$('.totalCol').attr('colspan',$totalCol+3);" !!}
                    </script>
                </tbody>
            </table>
        </div>
