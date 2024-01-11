<style>
    .uppercase {
        text-transform: uppercase !important;
        font-size: 13px;
    }
</style>

<div class="table-responsive">
    <table id="myTable" class="table table-hover table-bordered manage-u-table">
        <thead class="uppercase">
            <tr class="tr_header">
                <th>#</th>
                <th>@lang('dashboard.photo')</th>
                <th>@lang('common.id')</th>
                <th>@lang('common.name')</th>
                {{-- <th>@lang('dashboard.date')</th> --}}
                <th>@lang('dashboard.in_time')</th>
                <th>@lang('dashboard.out_time')</th>
                {{-- <th>Location</th> --}}
                <th>@lang('common.view')</th>

            </tr>
        </thead>
        <tbody>
            {{-- @if (count($attendanceData) > 0) --}}

            {{ $dailyAttendanceSl = null }}

            @foreach ($attendanceData as $key => $dailyAttendance)
                <tr class="{!! $dailyAttendance->employee_id !!}">
                    <td>{{ ++$dailyAttendanceSl }}</td>
                    <td>
                        @if ($dailyAttendance->photo != '')
                            <img width="32" height="32"  src="{!! asset('uploads/employeePhoto/' . $dailyAttendance->photo) !!}" alt="user-img" class="img-circle">
                        @else
                            <img width="32" height="32"  src="{!! asset('admin_assets/img/default.png') !!}" alt="user-img" class="img-circle">
                        @endif
                    </td>
                    <td>{{ $dailyAttendance->finger_id }} </td>
                    @if ($dailyAttendance->fullName)
                        <td>{{ $dailyAttendance->fullName }}
                            <br /><span class="text-muted">{{ $dailyAttendance->department_name }}</span>
                        </td>
                    @else
                        <td>{{ $dailyAttendance->first_name . ' ' . $dailyAttendance->last_name }}
                            <br /><span class="text-muted">{{ $dailyAttendance->department_name }}</span>
                        </td>
                    @endif

                    {{-- <td>{{ $dailyAttendance->date }} </td> --}}
                    <td>{{ $dailyAttendance->in_time }} </td>
                    <td>
                        <?php
                        if ($dailyAttendance->out_time != '') {
                            echo $dailyAttendance->out_time;
                        } else {
                            echo '--';
                        }
                        ?>
                    </td>

                    {{-- <td>
                            @if ($dailyAttendance->status == 'O')
                                {{ 'Outside' }}
                            @elseif($dailyAttendance->status == 'I')
                                {{ 'Inside' }}
                            @else
                                {{ 'NA' }}
                            @endif
                        </td> --}}

                    <td class="" style="width: 100px;">
                        <a href="{!! route('mobileAttendance.mobileAttendanceReport', [
                            'employee_id' => $dailyAttendance->employee_id,
                            'date' => $dailyAttendance->date,
                        ]) !!}" data-token="{!! csrf_token() !!}"
                            data-id="{!! $dailyAttendance->employee_id !!}" class="btn btn-success btn-xs btnColor"><i
                                class="fa fa-eye" aria-hidden="true"></i></a>
                    </td>

                    {{-- <a href="{!! url('mobileAttendanceReport/?employee_id=' . $dailyAttendance->employee_id . 'date=' . $dailyAttendance->date) !!}" data-token="{!! csrf_token() !!}" --}}

                </tr>
            @endforeach
            {{-- @else
                <tr>
                    <td colspan="8">@lang('common.no_data_available')</td>
                </tr>
            @endif --}}
        </tbody>
    </table>
</div>
