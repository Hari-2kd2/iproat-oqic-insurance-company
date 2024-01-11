<div class="table-responsive">
    <table id="mustertableData" class="table table-bordered table-striped table-hover"
        style="font-size: 12px;font-weight:400">
        <thead>
            {{-- <tr>
                <td style="font-size: 14px;font-weight:bold" colspan="{{ count($monthToDate) + 6 }}" class="text-center">
                    {{ 'Muster Report  - (' . $start_date . ') ' . ' To ' . ' (' . $end_date . ') .' }}
                </td>
            </tr> --}}
            <tr>
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
            {{-- @php dd($results);@endphp --}}
            @if (count($results) > 0)
                {{ $sl = null }}
                @foreach ($results as $fingerID => $attendance)
                    <tr>
                        <td>{{ ++$sl }}</td>
                        <td>{{ $attendance[0]['branch_name'] }}</td>
                        <td>{{ $fingerID }}</td>
                        <td>{{ $attendance[0]['fullName'] }}</td>
                        <td>{{ $attendance[0]['department_name'] }}</td>

                        <td class="text-left">

                            {{ 'Shift Name' }} <br>

                            {{ 'In Time' }}<br>

                            {{ 'Out Time' }}<br>

                            {{ 'Working.Hrs' }}<br>

                            {{ 'Over Time' }}<br>

                        </td>

                        @foreach ($attendance as $data)
                            {{-- @php array_push(); @endphp --}}
                            @if (strtotime($data['date']) <= strtotime(date('Y-m-d')))
                                <td>
                                    {{ $data['shift_name'] != null ? $data['shift_name'] : 'NA' }}<br>

                                    {{ $data['in_time'] != null ? date('H:i', strtotime($data['in_time'])) : '-:-' }}<br>

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
            @else
                <tr>
                    <td colspan="{{ count($monthToDate) + 6 }}">
                        'No data found...'
                    </td>
                </tr>

            @endif

        </tbody>
    </table>
</div>

@section('page-scripts')
    <script>
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
                a.download = 'AttendanceSummaryReport-' + date + '.xls';
                //triggering the function
                a.click();
                //just in case, prevent default behaviour
                e.preventDefault();
            });
        });
    </script>
@endsection
