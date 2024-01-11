<!DOCTYPE html>
<html lang="en">

<head>
    <title>Salary Details</title>
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

<body>
    <div class="printHead">
        @if ($printHead)
            {!! $printHead->description !!}
        @endif
        <p style="margin-left: 42px;margin-top: 10px"><b>Salary Details</b></p>
    </div>
    <div class="container">
        <b>@lang('common.month') :</b>{{ $month }},<b>
            <div class="data">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="tr_header ">
                            <tr class="text-capitalize ">
                                <th class="col-md-1" scope="col">S/L</th>
                                <th class="col-md-1" scope="col">Employee Id</th>
                                <th class="col-md-1" scope="col">Month</th>
                                <th class="col-md-1" scope="col">Basic Salary</th>
                                <th class="col-md-1" scope="col">Total Allowence</th>
                                <th class="col-md-1" scope="col">Total Deduction</th>
                                <th class="col-md-1" scope="col">Gross Salary</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($results) > 0)
                                {!! $sl = null !!}
                                @foreach ($results as $value)
                                    @php
                                        $month = $value->month_of_salary;
                                        $employee_id = $value->employee_id;
                                        $basic_salary = $value->basic_salary;
                                        $total_overtime_amount = $value->total_overtime_amount;
                                        $total_deduction = $value->total_deduction;
                                        $gross_salary = $value->gross_salary;
                                    @endphp
                                    <tr>
                                        <td>{{ ++$sl }}</td>
                                        <td>{{ $employee_id }}</td>
                                        <td>{{ $month }}</td>
                                        <td>{{ $basic_salary }}</td>
                                        <td>{{ $total_overtime_amount }}</td>
                                        <td>{{ $total_deduction }}</td>
                                        <td>{{ $gross_salary }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7">@lang('common.no_data_available') !</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

            </div>
    </div>
</body>

</html>
