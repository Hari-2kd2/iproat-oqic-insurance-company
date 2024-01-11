@extends('admin.master')
@section('content')
@section('title')
    @lang('advancededuction.advancededuction_list')
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <a href="{{ route('advanceDeduction.create') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                    class="fa fa-plus-circle" aria-hidden="true"></i> @lang('advancededuction.add_advancededuction')</a>
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
                                <button type="button" class="close" data-dismiss="alert"
                                    aria-hidden="true">×</button>
                                <i
                                    class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert"
                                    aria-hidden="true">×</button>
                                <i
                                    class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>@lang('employee.name')</th>
                                        <th>@lang('advancededuction.advance_amount')</th>
                                        <th>@lang('advancededuction.date_of_advance_given')</th>
                                        <th>@lang('advancededuction.deduction_amouth_per_month')</th>
                                        <th>@lang('advancededuction.no_of_month_to_be_deducted')</th>
                                        <th>Month of Completion</th>
                                        <th>@lang('advancededuction.remaining_month')</th>
                                        <th>@lang('advancededuction.balance')</th>
                                        <th>@lang('common.status')</th>
                                        <th>@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @foreach ($results as $value)
                                        <tr class="{!! $value->advance_deduction_id !!} text-center">
                                            <td style="width: 100px;">{!! ++$sl !!}</td>
                                            <td class="text-left">{!! $value->first_name . ' ' . $value->last_name !!}</td>
                                            <td>{!! $value->advance_amount . ' Rs' !!}</td>
                                            <td>{!! dateConvertDBtoForm($value->date_of_advance_given) !!}</td>
                                            <td>{!! $value->deduction_amouth_per_month . ' Rs' !!}</td>
                                            <td>{!! $value->no_of_month_to_be_deducted !!}</td>
                                            @php
                                                $amount = $value->deduction_amouth_per_month;
                                                $date = $value->date_of_advance_given;
                                                $start_date = new DateTime($date);
                                                $total_period = $value->no_of_month_to_be_deducted;
                                                $end_period = \Carbon\Carbon::createFromFormat('Y-m-d', $date)->addMonth($total_period);
                                                // $start_date = new DateTime($date);
                                                // $end_date = new DateTime($end_period);
                                                $advanced_date = $start_date->format('d-m-Y');
                                                $current_date = \Carbon\Carbon::now()->submonth(1);
                                                $interval = $end_period->diffInMonths($current_date);
                                                $remaining_period = $interval;
                                                // $end_period = \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format('d-m-Y');
                                                // dd($end_date);
                                            @endphp
                                            <td>{!! $end_period->format('d-m-Y') !!}</td>
                                            <td>{!! $remaining_period !!}</td>
                                            <td>{!! $amount * $remaining_period !!}</td>
                                            {{-- <td>{!! $remaining_period <= 0 ? 0 : $remaining_period !!}</td>
                                            <td>{!! $remaining_period <= 0 ? 0 : $remaining_period * $value->deduction_amouth_per_month . ' Rs' !!}</td> --}}
                                            <td>
                                                @if ($value->status == 0)
                                                    <span class="label label-success">No Due</span>
                                                    </span>
                                                @elseif($value->status == 1)
                                                    <span class="label label-warning">Pending</span>
                                                @endif
                                            </td>

                                            <td style="width: 100px;">
                                                <a href="{!! route('advanceDeduction.edit', $value->advance_deduction_id) !!}"
                                                    class="btn btn-success btn-xs btnColor">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                </a>
                                                <a href="{!! route('advanceDeduction.delete', $value->advance_deduction_id) !!}" data-token="{!! csrf_token() !!}"
                                                    data-id="{!! $value->advance_deduction_id !!}"
                                                    class="delete btn btn-danger btn-xs deleteBtn btnColor"><i
                                                        class="fa fa-trash-o" aria-hidden="true"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        $('.data').on('click', '.pagination a', function(e) {
            getData($(this).attr('href').split('page=')[1]);
            e.preventDefault();
        });
        $(".employee_name").bind("keyup change", function(e) {
            getData(1);
        })
    });

    function getData(page) {
        var employee_name = $('.employee_name').val();

        $.ajax({
            url: '?page=' + page + "&employee_name=" + employee_name,
            datatype: "html",
        }).done(function(data) {
            $('.data').html(data);
        }).fail(function() {
            alert('No response from server');
        });
    }
</script>
@endsection
