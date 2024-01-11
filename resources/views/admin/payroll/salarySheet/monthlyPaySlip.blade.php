@extends('admin.master')
@section('content')
@section('title')
    @lang('salary_sheet.employee_payslip')
@endsection
<style>
    .table>tbody>tr>td {
        padding: 5px 7px;
    }

</style>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>

            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>
                    @lang('salary_sheet.employee_payslip')</div>
                <div class="col-md-12 text-right">
                    <h4 style="">
                        <a class="btn btn-success" style="color: #fff"
                            href="{{ URL('downloadPayslip/' . $paySlipId) }}"><i class="fa fa-download fa-lg"
                                aria-hidden="true"></i> @lang('common.download') PDF</a>
                    </h4>
                </div>

                <div class="row" style="margin-top: 25px">

                    <div class="col-md-12 text-center">

                        <h3><strong> @lang('salary_sheet.employee_payslip') </strong>
                        </h3>
                        {{-- <div class="row">
                                <div class="col-md-5 text-center" style="font-weight: 500;">
                                    <b>Arrears / Ref.of.LOP:</b> <input type="number" id="myText" value="0"><button
                                        onclick="myFunction()">Set</button>
                                </div>
                            </div> --}}
                    </div>
                </div>

                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body" style="    padding: 18px 49px;">
                        <div class="row" style="border: 1px solid #ddd;padding: 26px 9px">
                            <div class="col-md-full">
                                <table class="table table-bordered">
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
                                                        @lang('common.payslip')
                                                        <span>{{ $monthName . '(' . $yearNum . ')' }}</span>
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
                                        <td colspan="1" class="">
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
                                                <span>@lang('common.lop')<span> / @lang('common.refund')</span></span>
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
                                        <td colspan=""></td>
                                        @php
                                            $gross_total = $total_earning - $total_deduction;
                                        @endphp
                                        <td colspan="2" class="text-center" style="background: #ddd; padding: 12px;">
                                            <b>@lang('common.net_amount'):</b>
                                        </td>
                                        <td colspan="1" class="text-center" style="background: #ddd; padding: 12px;">
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
                                <script>
                                    function myFunction() {
                                        var x = document.getElementById("myText").value;
                                        document.getElementById("demo").innerHTML = x;
                                    }
                                </script>
                            </div>
                            <div class="row">
                                <div class="col-md-6"></div>
                                <div class="col-md-6"></div>
                            </div>
                            <div class="col-md-4">
                                <p style="font-weight: 500;">@lang('salary_sheet.adminstrator_signature') ....</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <p style="font-weight: 500;"> @lang('common.date') .... </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <p style="font-weight: 500;"> @lang('salary_sheet.employee_signature') .... </p>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
