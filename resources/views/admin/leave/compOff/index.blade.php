@extends('admin.master')
@section('content')
@section('title')
    @lang('comp_off.comp_off_list')
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
            <a href="{{ route('compOff.create') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                    class="fa fa-plus-circle" aria-hidden="true"></i> @lang('comp_off.add_comp_off')</a>
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


                        <div class="table-responsive">
                            <table id="myDataTable" class="table table-bordered">
                                <thead class="tr_header">
                                    <tr>
                                        <th>@lang('common.serial')</th>
                                        <th>@lang('comp_off.employee_id')</th>
                                        <th>@lang('comp_off.employee_name')</th>
                                        <th>@lang('comp_off.off_date')</th>
                                        <th>@lang('comp_off.working_date')</th>
                                        <th>@lang('comp_off.off_timing')</th>
                                        <th>@lang('comp_off.comment')</th>
                                        @if ((auth()->user() && auth()->user()->role_id == 1) || auth()->user()->role_id == 2)
                                            <th style="text-align: center;">@lang('common.action')</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @foreach ($results as $value)
                                        <tr
                                            class="{!! $value->comp_off_details_id !!} @if (date('Y-m-d') <= $value->off_date) {{ 'success' }} @endif
													">
                                            <td style="width: 50px;">{!! ++$sl !!}</td>
                                            <td>{!! $value->finger_print_id !!}</td>
                                            <td>
                                                @if (isset($value->employee->first_name))
                                                    {!! $value->employee->first_name !!}
                                                    {!! $value->employee->last_name !!}
                                                @endif
                                            </td>
                                            <td>{!! dateConvertDBtoForm($value->off_date) !!}</td>
                                            <td>{!! dateConvertDBtoForm($value->working_date) !!}</td>
                                            <td>{!! $value->off_timing == 0 ? 'Half Day' : 'Full Day' !!}</td>
                                            <td>{!! $value->comment !!}</td>
                                            @if ((auth()->user() && auth()->user()->role_id == 1) || auth()->user()->role_id == 2)
                                                <td style="width: 100px;">
                                                    {{-- <a href="{!! route('compOff.edit', $value->comp_off_details_id) !!}"
                                                    class="btn btn-success btn-xs btnColor">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                </a> --}}
                                                    <a href="{!! route('compOff.delete', $value->comp_off_details_id) !!}"
                                                        data-token="{!! csrf_token() !!}"
                                                        data-id="{!! $value->comp_off_details_id !!}"
                                                        class="delete btn btn-danger btn-xs deleteBtn btnColor"><i
                                                            class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                </td>
                                            @endif
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
@endsection
