<!DOCTYPE html>
<html lang="en">

<head>
    <title>@lang('salary_sheet.employee_payslip')</title>
    <meta charset="utf-8">
</head>
<style>
    table {
        margin: 0 0 40px 0;
        width: 100%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        display: table;
        border-spacing: 0px;
    }

    table,
    td,
    th {
        border: 1px solid #ddd;
    }

    td {
        padding: 3px;
    }

    th {
        padding: 3px;
    }

    .text-center {
        text-align: center;
    }

    .companyAddress {
        width: 367px;
        margin: 0 auto;
    }

    .container {
        padding-right: 15px;
        padding-left: 15px;
        margin-right: auto;
        margin-left: auto;
        width: 95%;
    }

    .row {
        margin-right: -15px;
        margin-left: -15px;
    }

    .col-md-6 {
        width: 49%;
        float: left;
        padding-right: .5%;
        padding-left: .5%;
    }

    .div1 {
        position: relative;
    }

    .div2 {
        position: absolute;
        width: 100%;
        border: 1px solid;
        padding: 30px 12px 0px 12px;
    }

    .col-md-4 {
        width: 33.33333333%;
        float: left;
    }

    .clearFix {
        clear: both;
    }

    .padding {
        margin-bottom: 32px;

    }

</style>

<body>
    <div class="container">
        <div class="row">
            <div class=" companyAddress">
                <div class="headingStyle" style="margin-left: 30px;">
                    @if ($printHeadSetting)
                        {!! $printHeadSetting->description !!}
                    @endif
                </div>
                <h3 style="    margin-left: 65px;"><strong>@lang('salary_sheet.employee_payslip')</strong></h3>
            </div>
            <div class="div1">
                <div class="div2">
                    <div class="clearFix">
                        <div class="col-md-full">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    {{-- <tr>
                                        <h4 class="text-center" style="padding: 2px;"><b>ACS Medical College and
                                                Hospital</b>
                                        </h4>
                                        <h5 class="text-center" style="padding-bottom: 12px;">
                                            Velappanchavadi, Chennai - 600 077.
                                        </h5>
                                    </tr> --}}
                                    @php
                                        $date = $salaryDetails->month_of_salary;
                                        $explode = explode('-', $date);
                                        $yearNum = $explode[0];
                                        $monthNum = $explode[1];
                                        $dateObj = DateTime::createFromFormat('!m', $monthNum);
                                        $monthName = $dateObj->format('F');
                                        // $date = Carbon::createFromFormat('y/m', );
                                    @endphp
                                    {{-- <tr>
                                        <td colspan="5" class="text-center" style="border-bottom: 0px;">
                                            <h4><strong>ACS Medical College and Hospital</strong><br>
                                                <h5>Velappanchavadi, Chennai - 600 077.</h5>
                                            </h4>
                                        </td>
                                    </tr> --}}
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <span>
                                                <h4><b>
                                                        @lang('common.payslip'){{ $monthName . '(' . $yearNum . ')' }}
                                                </h4></b>
                                            </span>
                                        </td>
                                        <td colspan="1" class="text-center" style="border-bottom: 0px;">
                                            <span>
                                                <h4><b>
                                                        {{ $salaryDetails->salary_details_id }}
                                                </h4></b>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="">
                                            @lang('common.employee_name') :
                                        </td>
                                        <td colspan="2" class="text-center">
                                            <span><b>{{ $salaryDetails->first_name }}</b></span>
                                            <span> <b>{{ $salaryDetails->last_name }}</b></span>
                                        </td>
                                        <td colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="1" class="">
                                            @lang('employee.designation') :
                                        </td>
                                        <td colspan="1" class="text-center">
                                            <b>
                                                @if (isset($salaryDetails->designation_name))
                                                    {{ $salaryDetails->designation_name }}
                                                @endif
                                            </b>
                                        </td>
                                        <td colspan="1" class="text-center">
                                            <b>{{ $salaryDetails->employee_id }}</b>
                                        </td>
                                        <td colspan="1" class="text-left">
                                            @lang('employee.department') :
                                        </td>
                                        <td colspan="1" class="text-center">
                                            <b>
                                                @if (isset($salaryDetails->department_name))
                                                    {{ $salaryDetails->department_name }}
                                                @endif
                                            </b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="1" class="col-md-3 text-center"><b>@lang('common.earning')</b>
                                        </td>
                                        <td colspan="1" class=" col-md-3 text-center"><b>@lang('common.amount')</b>
                                        </td>
                                        <td colspan="1" class="col-md-3 text-center">
                                            <b>@lang('common.deduction')</b>
                                        </td>
                                        <td colspan="1" class="col-md-3 text-center"></td>
                                        <td colspan="1" class=" col-md-3 text-center"><b>@lang('common.amount')</b>
                                        </td>

                                    </tr>
                                    <tbody>

                                        <tr>
                                            <td>@lang('salary_sheet.basic_salary') : </td>
                                            <td class="text-center">
                                                {{ number_format($salaryDetails->basic_salary) }}</td>

                                            <td colspan="1" class=" col-md-3 ">TDS/PF:</td>
                                            <td colspan="1" class=" col-md-3 "></td>
                                            <td class=" col-md-3 text-center">0</td>

                                        </tr>

                                        <tr>
                                            <td class="col-md-3 ">@lang('common.arrears') /
                                                <span>@lang('common.lop') </span>
                                            </td>
                                            <td id="demo" class="col-md-3 text-center"> {{ '0' }}</td>
                                            <td colspan="1" class="col-md-3 ">ESI :</td>
                                            <td colspan="1" class=" col-md-3 "></td>
                                            <td class="col-md-3 text-center">0</td>
                                        </tr>
                                        {{-- @if (count($salaryDetailsToAllowance) > 0)
                                        @foreach ($salaryDetailsToAllowance as $allowance)
                                            <tr>
                                                <td>{{ $allowance->allowance_name }}: </td>
                                                <td class="text-center">
                                                    {{ number_format($allowance->amount_of_allowance) }}</td>
                                            </tr>
                                        @endforeach
                                    @endif --}}
                                        {{-- <tr>
                                        <td>@lang('salary_sheet.net_salary') : </td>
                                        <td class="text-center" style="background: #ddd">
                                            {{ number_format($salaryDetails->net_salary) }}</td>
                                    </tr>
                                    <tr>
                                        <td>@lang('salary_sheet.taxable_salary') : </td>
                                        <td class="text-center">
                                            {{ number_format($salaryDetails->taxable_salary) }}</td>
                                    </tr> --}}
                                        {{-- <tr>
                                        <td>@lang('salary_sheet.income_tax_to_pay_for_the_month') : </td>
                                        <td class="text-center"> {{ number_format($salaryDetails->tax) }}</td>
                                    </tr> --}}
                                        @php
                                            $companyTaxDeduction = 0;
                                            $companyTaxDeduction = ($salaryDetails->tax * 70) / 100;
                                            
                                            $employeeTaxDeduction = 0;
                                            $employeeTaxDeduction = ($salaryDetails->tax * 30) / 100;
                                        @endphp
                                        {{-- <tr>
                                        <td>@lang('salary_sheet.company_tax_deduction') : </td>
                                        <td class="text-center">
                                            {{ number_format(round($companyTaxDeduction)) }}</td>
                                    </tr> --}}
                                        <tr>
                                            @if ($salaryDetails->total_overtime_amount != 0)
                                                <td>@lang('salary_sheet.over_time') : </td>
                                                <td class="text-center">
                                                    {{ number_format($salaryDetails->total_overtime_amount) }}</td>
                                            @endif
                                            @if ($salaryDetails->total_overtime_amount == 0)
                                                <td>@lang('salary_sheet.over_time') : </td>
                                                <td class="text-center">
                                                    0</td>
                                            @endif
                                            <td colspan="2" class="col-md-3">@lang('common.ebill_transaction') :
                                            </td>
                                            <td deduction class="text-center">0</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td colspan="2">@lang('common.adv_acc_pro_tax'): </td>
                                            <td class="text-center">
                                                @php
                                                    $totatTax = $employeeTaxDeduction + $companyTaxDeduction;
                                                @endphp
                                                {{ number_format(round($totatTax)) }}
                                        </tr>
                                        {{-- @if (count($salaryDetailsToDeduction) > 0)
                                        @foreach ($salaryDetailsToDeduction as $deduction)
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td>{{ $deduction->deduction_name }} : </td>
                                                <td class="text-center">
                                                    {{ number_format($deduction->amount_of_deduction) }}</td>
                                            </tr>
                                        @endforeach
                                    @endif --}}
                                        {{-- @if ($salaryDetails->total_late_amount != 0)
                                        <tr>
                                            <td>@lang('salary_sheet.late_amount') : </td>
                                            <td class="text-center">
                                                {{ number_format($salaryDetails->total_late_amount) }}</td>
                                        </tr>
                                    @endif --}}
                                        @if ($salaryDetails->total_absence_amount != 0)
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td colspan="1">@lang('common.loss_of_pay') : </td>
                                                <td colspan="1" class=" col-md-3 "></td>

                                                <td class="text-center">
                                                    {{ number_format($salaryDetails->total_absence_amount) }}</td>
                                            </tr>
                                        @endif

                                        <tr>
                                            <td> @lang('common.total_earning') : </td>
                                            <td class="text-center">
                                                @php
                                                    $total_earning = collect([$salaryDetails->basic_salary, $salaryDetails->total_overtime_amount])->sum();
                                                @endphp
                                                {{ number_format($total_earning) }} </td>
                                            <td colspan="2"> @lang('common.total_deduction') : </td>
                                            @php
                                                $total_deduction = 0;
                                                $total_deduction = collect([$salaryDetails->total_deduction])->sum();
                                            @endphp
                                            <td class="text-center">
                                                {{ number_format($total_deduction) }} </td>
                                        </tr>
                                        <th>
                                        <td colspan="1"></td>
                                        @php
                                            $gross_total = $total_earning - $total_deduction;
                                        @endphp
                                        <td colspan="2" class="text-center">
                                            <b>@lang('common.net_amount'):</b>
                                        </td>
                                        <td colspan="1" class="text-center">
                                            <b>{{ round($gross_total) }}</b>
                                        </td>
                                        </th>
                                        {{-- <tr>
                                        <td> @lang('salary_sheet.total_income_tax_deduction_for_the_financial_year')
                                            : </td>
                                        <td class="text-center">
                                            {{ number_format($financialYearTax->totalTax) }} </td>
                                    </tr> --}}

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <footer position="sticky">
                        <div class="clearFix padding">
                            <div class="col-md-4" style="text-align: center;">
                                <strong>@lang('salary_sheet.adminstrator_signature') ...</strong>
                            </div>
                            <div class=" col-md-4" style="text-align: center;">
                                <strong>@lang('common.date') ...</strong>
                            </div>
                            <div class=" col-md-4" style="text-align: center;">
                                <strong>@lang('salary_sheet.employee_signature') ...</strong>
                            </div>
                        </div>
                    </footer>

                </div>
            </div>
        </div>
    </div>

</body>

</html>
