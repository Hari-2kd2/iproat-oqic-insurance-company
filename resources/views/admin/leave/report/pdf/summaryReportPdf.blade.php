<!DOCTYPE html>
<html lang="en">

<head>
    <title>@lang('leave.leave_summary_report')</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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

<body>
    <div class="printHead">
        @if ($printHead)
            {!! $printHead->description !!}
        @endif
        <br>
        <br>
    </div>
    <div class="container">
        <b>@lang('common.name') : </b>{{ $employee_name }},<b>@lang('employee.department') :
        </b>{{ $department_name }}<b>,@lang('common.from_date') : </b>{{ $form_date }} , <b>@lang('common.to_date') :
        </b>{{ $to_date }}
        <div class="table-responsive">
            <table id="" class="table table-bordered">
                @php
                    $count = null;
                @endphp
                <thead class="tr_header">
                    <tr>
                        <th class="col-md-1 text-center">@lang('common.month')</th>
                        @foreach ($leaveTypes as $leaveType)
                            <th class="col-md-1 text-center">{{ $leaveType->leave_type_name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($results as $value)
					@php
						dd($results);
					@endphp
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>
