@extends('admin.master')
@section('content')
@section('title')
    @lang('holiday.weekly_holiday_list')
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-7 col-sm-7 col-md-7 col-xs-12">
            <a href="{{ route('weeklyHoliday.create') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                    class="fa fa-plus-circle" aria-hidden="true"></i> @lang('holiday.add_weekly_holiday')</a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-block alert-dismissable">
                                <ul>
                                    <button type="button" class="close" data-dismiss="alert">x</button>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if ($message = Session::get('success'))
                            <div class="alert alert-success alert-block alert-dismissable" style="margin-top: 12px;">
                                <button type="button" class="close" data-dismiss="alert">x</button>
                                <strong>{{ $message }}</strong>
                            </div>
                        @endif

                        @if ($message = Session::get('error'))
                            <div class="alert alert-danger alert-block alert-dismissable" style="margin-top: 12px;">
                                <button type="button" class="close" data-dismiss="alert">x</button>
                                <strong>{{ $message }}</strong>
                            </div>
                        @endif
                        <div class="border"
                            style="border: 1px solid #b9b8b5;border-radius:4px;margin:12px;padding:12px">
                            <a class="pull-right" href="{{ route('weeklyHoliday.weeklyHolidayTemplate') }}">
                                <div id="template1" class="btn btn-info btn-sm template1" value="Template"
                                    type="submit">
                                    <i class="fa fa-download" aria-hidden="true"></i><span>
                                        Template</span>
                                </div>
                            </a>
                            <div class="row hidden-xs hidden-sm">
                                <p class="border" style="margin-left:18px">
                                    <span><i class="fa fa-upload"></i></span>
                                    <span style="margin-left: 4px"> Import weekly holiday excel file.</span>
                                </p>
                                <form action="{{ route('weeklyHoliday.import') }}" method="post"
                                    enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div>
                                            <div class="col-md-4 text-right" style="margin-left:14px">
                                                <input type="file" name="select_file"
                                                    class="form-control custom-file-upload">
                                            </div>
                                            <div class="col-sm-1">
                                                <button class="btn btn-success btn-sm" type="submit"><span><i
                                                            class="fa fa-upload" aria-hidden="true"></i></span>
                                                    Upload</button>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>
                        <br>
                        <div class="table-responsive" style="font-size: 12px">
                            <table id="myDataTable" class="table table-bordered">
                                <thead class="tr_header">
                                    <tr>
                                        <th>@lang('common.serial')</th>
                                        <th>Employee ID</th>
                                        <th>@lang('holiday.weekly_holiday_name')</th>
                                        <th>@lang('common.month')</th>
                                        <th>@lang('common.date')</th>
                                        <th>Updated At</th>
                                        {{-- <th>@lang('common.status')</th> --}}
                                        <th style="text-align: center;">@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @foreach ($results as $value)
                                        <tr class="{!! $value->week_holiday_id !!}">
                                            <td style="width: 100px;">{!! ++$sl !!}</td>
                                            <td>{!! trim($value->employee->first_name . ' ' . $value->employee->last_name) .
                                                ' (' .
                                                $value->employee->finger_id .
                                                ')' !!}</td>
                                            <td>{!! $value->day_name !!}</td>
                                            @php
                                                $dates = str_replace('[', '', $value->weekoff_days);
                                                $dates = str_replace(']', '', $dates);
                                                $dates = str_replace('"', '', $dates);
                                            @endphp
                                            <td> @php echo "<p>".$value->month."</p>";  @endphp</td>
                                            <td> @php echo "<b>".$dates."</b>";  @endphp</td>
                                            <td>
                                                {{ date('Y-m-d H:i A', strtotime($value->updated_at)) }}
                                            </td>
                                            {{-- <td style="width: 100px;">
                                                <span
                                                    class="label label-{{ $value->status == 2 ? 'warning' : 'success' }}">{{ $value->status == 2 ? __('common.inactive') : __('common.active') }}</span>
                                            </td> --}}
                                            <td class="text-center" style="width: 100px;">
                                                <a href="{!! route('weeklyHoliday.edit', $value->week_holiday_id) !!}"
                                                    class="btn btn-success btn-xs btnColor">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                </a>
                                                <a href="{!! route('weeklyHoliday.delete', $value->week_holiday_id) !!}" data-token="{!! csrf_token() !!}"
                                                    data-id="{!! $value->week_holiday_id !!}"
                                                    class="btnColor delete btn btn-danger btn-xs deleteBtn"><i
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
@endsection
