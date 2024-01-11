<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr class="tr_header">
                <th>@lang('common.serial')</th>
                <th>@lang('common.month')</th>
                <th>@lang('employee.photo')</th>
                <th>@lang('common.employee_name')</th>
                <th>@lang('salary_sheet.pay_grade')</th>
                <th>@lang('paygrade.basic_salary')</th>
                <th>@lang('paygrade.gross_salary')</th>
                <th>@lang('common.status')</th>
                <th>Payment Type</th>
                <th>@lang('common.action')</th>
            </tr>
        </thead>
        <tbody>
            @if (count($results) > 0)
                {!! $sl = null !!}
                @foreach ($results as $key => $value)
                    {{-- @if ($value->status == 0) --}}
                    <tr>
                        <td style="width: 100px;">{!! $results->firstItem() + $key !!}</td>
                        <td>
                            @php
                                $monthAndYear = explode('-', $value->month_of_salary);
                                
                                $month = $monthAndYear[1];
                                $dateObj = DateTime::createFromFormat('!m', $month);
                                $monthName = $dateObj->format('F');
                                $year = $monthAndYear[0];
                                
                                $monthAndYearName = $monthName . ' ' . $year;
                                echo $monthAndYearName;
                            @endphp
                        </td>
                        <td>
                            @if ($value->employee->photo != '')
                                <img style=" width: 70px; " src="{!! asset('uploads/employeePhoto/' . $value->employee->photo) !!}" alt="user-img"
                                    class="img-circle">
                            @else
                                <img style=" width: 70px; " src="{!! asset('admin_assets/img/default.png') !!}" alt="user-img"
                                    class="img-circle">
                            @endif
                        </td>
                        <td>
                            @if (isset($value->employee->first_name))
                                {!! $value->employee->first_name !!} {{ $value->employee->last_name }}
                            @endif
                            @if (isset($value->employee->department->department_name))
                                <br>
                                <span class="text-muted">@lang('employee.department') :
                                    {{ $value->employee->department->department_name }}</span>
                            @endif

                        </td>
                        <td>
                            @if (isset($value->employee->payGrade->pay_grade_name))
                                {!! $value->employee->payGrade->pay_grade_name !!}(Monthly)
                            @endif
                            @if (isset($value->employee->hourlySalaries->hourly_grade))
                                {!! $value->employee->hourlySalaries->hourly_grade !!}(Hourly)
                            @endif
                        </td>
                        <td>{!! $value->basic_salary !!}</td>
                        <td>{!! $value->gross_salary !!}</td>
                        @if ($value->status == 0)
                            <td class="text-left">
                                <span class="label label-warning">@lang('salary_sheet.unpaid')</span>
                            </td>
                        @else
                            <td class="text-left">
                                <span class="label label-success">@lang('salary_sheet.paid')</span>
                            </td>
                        @endif
                        <td class="text-left">
                            {{-- @if ($value->payment_method == 'RazorPay')
                                <form action="{!! Url('generateSalarySheet/payment') !!}" method="POST">
                                    <!-- Note that the amount is in paise = 50 INR -->
                                    <!--amount need to be in paisa-->
                                    <script src="https://checkout.razorpay.com/v1/checkout.js" id="makeDigitalPayment" data-key="rzp_test_EzVAI0XNlc8bPq"
                                                                        data-amount="{!! $value->gross_salary * 100 !!}" data-buttontext="RazorPay"
                                                                        data-name="Monthly Salary" data-description="Total Amount"
                                                                        data-phone="{!! $value->employee->phone !!}"
                                                                        data-image="{{ asset('uploads/front/logo.png') }}"
                                                                        data-prefill.name="@if (isset($value->employee->first_name)) {!! $value->employee->first_name !!} {{ $value->employee->last_name }} @endif"
                                                                        data-prefill.email="{!! $value->employee->email !!}" data-theme.color="#41b3f9"></script>
                                    <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                                </form><br> --}}
                            {{-- <a href="https://rzp.io/l/K2FmD811FO"><button class="text-dark">Custom
                                        Page</button></a> --}}
                            {{-- @elseif($value->payment_method != null && $value->payment_method != 'RazorPay')
                                <span class="label label-success">{!! $value->payment_method !!}</span>
                            @else
                                <span class="label label-warning">Null</span>
                            @endif --}}
                            @if ($value->payment_method != null)
                                @if ($value->payment_method == 'RazorPay')
                                    {{-- <form action="{!! Url('generateSalarySheet/payment') !!}" method="POST">
                                        <script src="https://checkout.razorpay.com/v1/checkout.js" id="makeDigitalPayment" data-key="rzp_test_EzVAI0XNlc8bPq"
                                                                                data-amount="{!! $value->gross_salary * 100 !!}" data-buttontext="Pay"
                                                                                data-name="Monthly Salary" data-description="Total Amount"
                                                                                data-phone="{!! $value->employee->phone !!}"
                                                                                data-image="{{ asset('uploads/front/logo.png') }}"
                                                                                data-prefill.name="@if (isset($value->employee->first_name)) {!! $value->employee->first_name !!} {{ $value->employee->last_name }} @endif"
                                                                                data-prefill.email="{!! $value->employee->email !!}" data-theme.color="#41b3f9">
                                        </script>
                                        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                                    </form><br> --}}
                                    <span class="label label-success">{!! $value->payment_method !!}</span>
                                @else
                                    <span class="label label-success">{!! $value->payment_method !!}</span>
                                @endif
                            @else
                                <span class="label label-warning">Null</span>
                            @endif
                        </td>
                        <td style="width: 100px">
                            @if ($value->status == 0)
                                <button class="btn btn-info waves-effect waves-light"
                                    data-salary_details_id="{!! $value->salary_details_id !!}"
                                    data-monthAndYearName="{!! $monthAndYearName !!}"
                                    data-basic_salary="{!! $value->basic_salary !!}"
                                    data-gross_salary="{!! $value->gross_salary !!}"
                                    data-total_allowance="{!! $value->total_allowance !!}"
                                    data-total_deduction="{!! $value->total_deduction !!}"
                                    data-total_advance_deduction="{!! $value->total_advance_deduction !!}"
                                    data-employee_name="@if (isset($value->employee->first_name)) {!! $value->employee->first_name !!} {{ $value->employee->last_name }} @endif"
                                    data-toggle="modal" data-target="#responsive-modal"><span>@lang('salary_sheet.make_payment')</span>
                                </button>
                            @else
                                <a href="{{ url('generateSalarySheet/generatePayslip', $value->salary_details_id) }}"
                                    target="_blank"><button
                                        class="btn btn-success  waves-effect waves-light"><span>@lang('salary_sheet.generate_payslip')</span>
                                    </button></a>
                            @endif
                        </td>

                    </tr>

                    {{-- @endif --}}
                @endforeach
            @else
                <tr>
                    <td colspan="10">@lang('common.no_data_available') !</td>
                </tr>
            @endif
        </tbody>
    </table>
    @if (count($results) > 0)
        <div class="text-center">
            {{ $results->links() }}
        </div>
    @endif
</div>
