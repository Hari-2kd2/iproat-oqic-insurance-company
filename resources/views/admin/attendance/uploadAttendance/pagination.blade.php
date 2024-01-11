<div class="border" style="margin: 12px">
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr class="tr_header">
                    <td class="col-md-1" scope="col">S/L</td>
                    <td class="col-md-1" scope="col">Date</td>
                    <td class="col-md-2" scope="col">Name</td>
                    <td class="col-md-1" scope="col">Finger Print Id</td>
                    <td class="col-md-1" scope="col">In Time</td>
                    <td class="col-md-1" scope="col">Out Time</td>
                </tr>
            </thead>
            <tbody>
                @forelse ($attendanceList as $key => $value)
                    @php
                        $name = $value->first_name . ' ' . $value->last_name;
                        $finger_id = $value->finger_print_id;
                        $date = dateConvertDBtoForm($value->date);
                        $in_time = date('H:i:s', strtotime($value->in_time));
                        $out_time = date('H:i:s', strtotime($value->out_time));
                    @endphp
                    <tr>
                        <td>{{ $attendanceList->firstItem() + $key }}</td>
                        <td>{{ $date }}</td>
                        <td>{{ $name }}</td>
                        <td>{{ $finger_id }}</td>
                        <td>{{ $in_time }}</td>
                        <td>{{ $out_time }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">@lang('common.no_data_available') !</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="text-center">
        {{ $attendanceList->links() }}
    </div>
</div>
