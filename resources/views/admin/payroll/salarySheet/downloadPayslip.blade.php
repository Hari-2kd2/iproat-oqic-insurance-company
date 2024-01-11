@extends('admin.master')
@section('content')
@section('title')
    @lang('salary_sheet.download_payslip')
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-1">
                                <label for="exampleInput"
                                    style="padding-top: 8px;">@lang('common.month') :</label>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::text('month', '', $attributes = ['class' => 'form-control monthField', 'id' => 'month', 'placeholder' => __('common.month'), 'readonly' => 'readonly']) !!}
                                </div>
                            </div>
                            {{-- <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">@lang('common.status')</label>
                                    {{ Form::select('status',['' => '---- ' . __('common.please_select') . ' ----','0' => __('salary_sheet.unpaid'),'1' => __('salary_sheet.paid')],'',['class' => 'form-control status select2 required']) }}
                                </div>
                            </div> --}}
                        </div>
                        <br>
                        <div class="data">
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
                                            {{-- <th>Payment Type</th> --}}
                                            <th>@lang('common.action')</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($results) > 0)
                                            {!! $sl = null !!}
                                            @foreach ($results as $value)
                                                @if ($value->status != 0)
                                                    <tr>
                                                        <td style="width: 100px;">{!! ++$sl !!}</td>
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
                                                                <img style=" width: 70px; "
                                                                    src="{!! asset('uploads/employeePhoto/' . $value->employee->photo) !!}" alt="user-img"
                                                                    class="img-circle">
                                                            @else
                                                                <img style=" width: 70px; "
                                                                    src="{!! asset('admin_assets/img/default.png') !!}" alt="user-img"
                                                                    class="img-circle">
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if (isset($value->employee->first_name))
                                                                {!! $value->employee->first_name !!}
                                                                {{ $value->employee->last_name }}
                                                            @endif
                                                            @if (isset($value->employee->department->department_name))
                                                                <br>
                                                                <span class="text-muted">@lang('employee.department')
                                                                    :
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
                                                            <td>
                                                                <span
                                                                    class="label label-warning">@lang('salary_sheet.unpaid')</span>
                                                            </td>
                                                        @else
                                                            <td>
                                                                <span
                                                                    class="label label-success">@lang('salary_sheet.paid')</span>
                                                            </td>
                                                        @endif
                                                        {{-- <td class="text-left">
                                                            @if ($value->payment_method == 'RazorPay' || $value->payment_method == '')
                                                                <form action="{!! Url('generateSalarySheet/payment') !!}" method="POST">
                                                                    <script src="https://checkout.razorpay.com/v1/checkout.js" id="makeDigitalPayment" data-key="rzp_test_EzVAI0XNlc8bPq"
                                                                                                                                        data-amount="{!! $value->gross_salary * 100 !!}"
                                                                                                                                        data-buttontext="RazorPay"
                                                                                                                                        data-name="Monthly Salary"
                                                                                                                                        data-description="Total Amount"
                                                                                                                                        data-phone="{!! 8667224715 !!}"
                                                                                                                                        data-image="logo_url"
                                                                                                                                        data-prefill.name="@if (isset($value->employee->first_name)) {!! $value->employee->first_name !!} {{ $value->employee->last_name }} @endif"
                                                                                                                                        data-prefill.email="{!! $value->employee->first_name !!}@gmail.com"
                                                                                                                                        data-theme.color="#41b3f9"></script>
                                                                    <input type="hidden" name="_token"
                                                                        value="{!! csrf_token() !!}">
                                                                </form><br>
                                                            @elseif($condition)
                                                                <span
                                                                    class="label label-success">{!! $value->payment_method !!}</span>
                                                            @endif
                                                        </td> --}}
                                                        <td style="width: 100px">
                                                            @if ($value->status == 0)
                                                                <button class="btn btn-info waves-effect waves-light"
                                                                    data-salary_details_id="{!! $value->salary_details_id !!}"
                                                                    data-monthAndYearName="{!! $monthAndYearName !!}"
                                                                    data-basic_salary="{!! $value->basic_salary !!}"
                                                                    data-gross_salary="{!! $value->gross_salary !!}"
                                                                    data-total_allowance="{!! $value->total_allowance !!}"
                                                                    data-total_deduction="{!! $value->total_deduction !!}"
                                                                    data-employee_name="@if (isset($value->employee->first_name)) {!! $value->employee->first_name !!} {{ $value->employee->last_name }} @endif"
                                                                    data-toggle="modal"
                                                                    data-target="#responsive-modal"><span>@lang('salary_sheet.make_payment')</span>
                                                                </button>
                                                            @else
                                                            @endif
                                                            <a href="{{ url('downloadPayslip/generatePayslip', $value->salary_details_id) }}"
                                                                target="_blank"><button
                                                                    class="btn btn-success  waves-effect waves-light"><span>@lang('salary_sheet.generate_payslip')</span>
                                                                </button></a>
                                                        </td>

                                                    </tr>
                                                @endif
                                            @endforeach
                                            {{-- @else
                                            <tr>
                                                <td colspan="10">@lang('common.no_data_available') !</td>
                                            </tr> --}}
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
<script>
    $(function() {
        $('.data').on('click', '.pagination a', function(e) {
            getData($(this).attr('href').split('page=')[1]);
            e.preventDefault();
        });


        $(".monthField").change(function() {
            getData(1);
        });

    });

    function getData(page) {
        var monthField = $('.monthField').val();
        $.ajax({
            url: '?page=' + page + "&monthField=" + monthField,
            datatype: "html",
        }).done(function(data) {
            $('.data').html(data);
            $("html, body").animate({
                scrollTop: 0
            }, 100);
        }).fail(function() {
            alert('No response from server');
        });

    }
</script>
@endsection
