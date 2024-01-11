@extends('admin.master')
@section('content')
@section('title')
    @lang('deduction.monthly_deduction')
@endsection
<style>
    .departmentName {
        position: relative;
    }

    #department_id-error {
        position: absolute;
        top: 66px;
        left: 0;
        width: 100%he;
        width: 100%;
        height: 100%;
    }
</style>
{{-- @php
if (isset($results)) {
    dd($result);
}
@endphp --}}
<script>
    jQuery(function() {
        $("#monthlyDeduction").validate();
    });
</script>
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
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        <div class="row">
                            <div id="searchBox">
                                {{ Form::open(['route' => 'monthlyDeduction.filter', 'id' => 'monthlyDeduction', 'method' => 'GET']) }}
                                <div class="col-md-2"></div>
                                <div class="col-md-3">
                                    <div class="form-group departmentName">
                                        <label class="control-label" for="email">@lang('employee.department')<span
                                                class="validateRq">*</span></label>
                                        <select class="form-control employee_id select2 required" required
                                            name="department_id">
                                            <option value="">---- @lang('common.please_select') ----</option>
                                            @foreach ($departmentList as $value)
                                                <option value="{{ $value->department_id }}"
                                                    @if (isset($_REQUEST['department_id'])) @if ($_REQUEST['department_id'] == $value->department_id) {{ 'selected' }} @endif
                                                    @endif>{{ $value->department_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInput">@lang('common.month')<span class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        {!! Form::text(
                                            'month',
                                            isset($month) ? $month : '',
                                            $attributes = ['class' => 'form-control required monthField', 'id' => 'month', 'placeholder' => __('common.month')],
                                        ) !!}
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="submit" id="filter" style="margin-top: 25px; width: 100px;"
                                            class="btn btn-info " value="@lang('common.filter')">
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                        <hr>
                        @if (isset($deductionData))
                            {{ Form::open(['route' => 'monthlyDeduction.store', 'id' => 'monthlyDeduction']) }}

                            <input type="hidden" name="department_id" value="{{ $_REQUEST['department_id'] }}">
                            <input type="hidden" name="month" value="{{ $_REQUEST['month'] }}">

                            <div class="table-responsive">
                                <table id="myTable" class="table table-bordered" style="margin-bottom: 47px">
                                    <thead class="tr_header">
                                        <tr>
                                            <th>@lang('common.serial')</th>
                                            {{-- <th>Employee Id</th> --}}
                                            <th>@lang('employee.finger_print_no')</th>
                                            <th>@lang('common.employee_name')</th>
                                            <th>Month of Deduction</th>
                                            <th>Total Call Limit</th>
                                            <th>Actual Call Count</th>
                                            <th>Breakfast Consumed</th>
                                            <th>Lunch Consumed</th>
                                            <th>Dinner Consumed</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $sl = 1;
                                            // dd($telephoneDeductionRule);
                                        @endphp
                                        @if (count($deductionData) > 0)
                                            @foreach ($deductionData as $key => $value)
                                                <tr>
                                                    {{-- <td>1</td> --}}
                                                    <td style="width: 6 0px">{{ $sl + $key }}</td>
                                                    {{-- <td style="width: 140px">{{ $value->employee_id }}</td> --}}
                                                    <td style="width: 140px">{{ $value->finger_id }}</td>
                                                    <td style="width: 240px">{{ $value->fullName }}</td>
                                                    <td style="width: 200px">
                                                        <div class="input-group">
                                                            <input type="hidden" name="employee_id[]"
                                                                value="{{ $value->employee_id }}">
                                                            <input type="hidden" name="finger_print_id[]"
                                                                value="{{ $value->finger_id }}">
                                                            <input class="form-control" type="text"
                                                                placeholder="Month" name="month_of_deduction[]"
                                                                value="{{ $_REQUEST['month'] }}" readonly>
                                                        </div>
                                                    </td>
                                                    <td style="width: 200px">
                                                        <div class="input-group">
                                                            <input class="form-control" type="number"
                                                                placeholder="Telephone Limit" name="limit_per_month"
                                                                value="{{ $telephoneDeductionRule->limit_per_month }}"
                                                                readonly>
                                                        </div>
                                                    </td>
                                                    <td style="width: 200px">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa fa-edit"></i>
                                                            </div>
                                                            @if ($value->pay_grade_id == 1)
                                                                <input class="form-control" type="number"
                                                                    placeholder="Call Count"
                                                                    name="call_consumed_per_month[]"
                                                                    value="{{ $value->call_consumed_per_month }}">
                                                            @else
                                                                <input class="form-control" type="number"
                                                                    placeholder="Call Count"
                                                                    name="call_consumed_per_month[]"
                                                                    value="{{ 0 }}" readonly>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td style="width: 200px">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa fa-edit"></i>
                                                            </div>
                                                            <input class="form-control" type="number"
                                                                placeholder="Breakfast Count" name="breakfast_count[]"
                                                                value="{{ $value->breakfast_count }}">
                                                        </div>
                                                    </td>
                                                    <td style="width: 200px">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa fa-edit"></i>
                                                            </div>
                                                            <input class="form-control" type="number"
                                                                placeholder="Lunch Count" name="lunch_count[]"
                                                                value="{{ $value->lunch_count }}">
                                                        </div>
                                                    </td>
                                                    <td style="width: 200px">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa fa-edit"></i>
                                                            </div>
                                                            <input class="form-control" type="number"
                                                                placeholder="Dinner Count" name="dinner_count[]"
                                                                value="{{ $value->dinner_count }}">
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5">@lang('common.no_data_available')</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            @if (count($deductionData) > 0)
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-12 ">
                                            <button type="submit" class="btn btn-info btn_style"><i
                                                    class="fa fa-check"></i> @lang('common.save')</button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            {{ Form::close() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
