@extends('admin.master')
@section('content')
@section('title')
    @lang('shift_change.my_application_list')
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="#"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <a href="{{ route('applyForShiftChange.create') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                    class="fa fa-plus-circle" aria-hidden="true"></i> @lang('shift_change.apply_for_shift_change')</a>
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
                        <div class="">
                            <table class="table table-hover manage-u-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('common.employee_name')</th>
                                        <th>@lang('shift_change.regular_shift')</th>
                                        <th>@lang('shift_change.work_shift')</th>
                                        <th>@lang('shift_change.request_duration')</th>
                                        <th>@lang('common.approve_status')</th>
                                        <th>@lang('common.reject_status')</th>
                                        <th>@lang('common.status')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @foreach ($results as $value)
                                        <tr>
                                            <td style="width: 100px;">{!! ++$sl !!}</td>
                                            <td>
                                                @if (isset($value->employee->first_name))
                                                    {!! $value->employee->first_name !!}
                                                @endif
                                                @if (isset($value->employee->last_name))
                                                    {!! $value->employee->last_name !!}
                                                @endif
                                            </td>
                                            <td>
                                                {!! shiftList($value->regular_shift) !!}
                                            </td>
                                            <td>
                                                {!! shiftList($value->work_shift_id) !!}
                                            </td>
                                            <td>
                                                {!! dateConvertDBtoForm($value->application_from_date) !!} <b>to</b> {!! dateConvertDBtoForm($value->application_to_date) !!}
                                                <br /><span class="text-muted">Application Date :
                                                    {!! dateConvertDBtoForm($value->application_date) !!}</span>
                                            </td>
                                            <td>
                                                @if (isset($value->approveBy->first_name))
                                                    {!! $value->approveBy->first_name !!} {!! $value->approveBy->last_name !!}
                                                    <br /><span class="text-muted">@lang('leave.approve_date') :
                                                        {!! dateConvertDBtoForm($value->approve_date) !!}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($value->rejectBy->first_name))
                                                    {!! $value->rejectBy->first_name !!} {!! $value->rejectBy->last_name !!}
                                                    <br /><span class="text-muted">@lang('common.reject_date') :
                                                        {!! dateConvertDBtoForm($value->reject_date) !!}</span>
                                                @endif
                                            </td>
                                            @if ($value->status == 1)
                                                <td style="width: 100px;">
                                                    <span class="label label-warning">@lang('common.pending')</span>
                                                </td>
                                            @elseif($value->status == 2)
                                                <td style="width: 100px;">
                                                    <span class="label label-success">@lang('common.approved')</span>
                                                </td>
                                            @else
                                                <td style="width: 100px;">
                                                    <span class="label label-danger">@lang('common.rejected')</span>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="text-center">
                                {{ $results->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection