<div class="table-responsive">
    <table id="" class="table table-bordered">
        @php
            $count = null;
        @endphp
        <thead class="tr_header">
            @if (count($results) > 0)
                <tr>
                    <th>{{ 'Summary Leave Report' }}</th>
                </tr>
                <tr>
                    <td>{{ 'Employee ID' }}</td>
                    <td>{{ 'Name' }}</td>
                    <td>{{ 'Department' }}</td>
                    @for ($i = 0; $i < count($leaveTypes) - 2; $i++)
                        <td>#</td>
                    @endfor
                </tr>
                <tr>
                    <td>{{ $results[0]['finger_id'] }}</td>
                    <td>{{ $results[0]['full_name'] }}</td>
                    <td>{{ $results[0]['department_name'] }}</td>
                    @for ($i = 0; $i < count($leaveTypes) - 2; $i++)
                        <td>#</td>
                    @endfor
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <th class="col-md-1 text-center">@lang('common.month')</th>
                    @foreach ($leaveTypes as $leaveType)
                        <th class="col-md-1 text-center">{{ $leaveType->leave_type_name }}</th>
                    @endforeach
                </tr>
        </thead>
        <tbody>
            @forelse  ($results as $value)
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
            @empty
                <tr>
                    <td colspan="5">@lang('common.no_data_available') !</td>
                </tr>
            @endforelse
        </tbody>
        @endif
    </table>
</div>
