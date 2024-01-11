<div style="margin-right: 10px;margin-left: 10px">
    <div class="">
        <div class="col-md-9"></div>
        <div class="text-right">
            <input type="button" id="tableexport" class="btn btn-success" value="Export Monthly Report"
                style="margin-bottom: 12px;width:200px;margin-right: 24px;margin-top: -12px" />

        </div>
    </div>
    <div id="btableData" class="table-responsive">
        <table class="table table-bordered">
            <tdead>
                <tr class="tr_header">
                    <td class="col-md-1" scope="col">S/L</td>
                    <td class="col-md-1" scope="col">Employee Id</td>
                    <td class="col-md-1" scope="col">Month</td>
                    <td class="col-md-1" scope="col">Basic Salary</td>
                    <td class="col-md-1" scope="col">Total Allowence</td>
                    <td class="col-md-1" scope="col">Total Deduction</td>
                    <td class="col-md-1" scope="col">Gross Salary</td>
                </tr>
            </tdead>
            <tbody>
                @if (count($results) > 0)
                    @foreach ($results as $key => $value)
                        @php
                            $month = $value->month_of_salary;
                            $employee_id = $value->employee_id;
                            $basic_salary = $value->basic_salary;
                            $total_overtime_amount = $value->total_overtime_amount;
                            $total_deduction = $value->total_deduction;
                            $gross_salary = $value->gross_salary;
                        @endphp
                        <tr>
                            <td>{{ $results->firstItem() + $key }}</td>
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
    {{-- <div class="text-center">
        {{ $results->links() }}
    </div> --}}
</div>

<script>
    $("#tableexport").click(function(e) {
        window.open('data:application/vnd.ms-excel,' + encodeURIComponent($('#btableData').html()));
        e.preventDefault();
    });
</script>
