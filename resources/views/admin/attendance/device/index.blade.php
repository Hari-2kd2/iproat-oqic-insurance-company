@extends('admin.master')
@section('content')
@section('title')
    @lang('attendance.devices_list')
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
            <a href="{{ route('deviceConfigure.create') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                    class="fa fa-plus-circle" aria-hidden="true"></i> @lang('attendance.add_devices')</a>

            <a href="{{ route('deviceConfigure.refresh') }}"
                class="btn btn-danger pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light refresh-device"> <i
                    class="fa fa-refresh" aria-hidden="true"></i> @lang('attendance.refresh_devices')</a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="text-left" style="font-size: 13px">
                            <div class="device-status">
                            @if ($message = Session::get('success'))
                                <div class="alert alert-success alert-block">
                                    <button type="button" class="close" data-dismiss="alert">x</button>
                                    <strong>{{ $message }}</strong>
                                </div>
                            @endif
                            @if ($message = Session::get('error'))
                                <div class="alert alert-danger alert-block">
                                    <button type="button" class="close" data-dismiss="alert">x</button>
                                    <strong>{{ $message }}</strong>
                                </div>
                            @endif
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered table-striped ">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>@lang('attendance.device_name')</th>
                                        <th>@lang('attendance.device_model')</th>
                                        <th>@lang('attendance.device_ip') & @lang('attendance.device_port')</th>
                                        <!-- <th>@lang('attendance.device_port')</th>
          <th>@lang('attendance.device_protocol')</th> -->
                                        <th>@lang('attendance.device_username')</th>
                                        <th>@lang('attendance.device_password')</th>
                                        <th>@lang('attendance.device_status')</th>
                                        <th>@lang('attendance.device_type')</th>
                                        {{-- <th>@lang('attendance.device_created_by')</th>
										<th>@lang('attendance.device_updated_by')</th> --}}
                                        <th style="text-align: center;">@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @foreach ($results as $value)
                                        {{-- @php
									dd($results);
									@endphp --}}
                                        <tr class="{!! $value->id !!}">
                                            <td style="width: 50px;">{!! ++$sl !!}</td>
                                            <td>{!! $value->name !!}</td>
                                            <td>{!! $value->model !!}</td>
                                            <td>{!! $value->ip . ':' . $value->port !!}</td>
                                            <!-- <td>{!! $value->port !!}</td>
          <td>{!! $value->protocol !!}</td> -->
                                            <td>{!! $value->username !!}</td>
                                            <td>{!! password(strlen($value->password)) !!}</td>
                                            @if ($value->device_status == 'online')
                                                <td class="text-left">
                                                    <span class="label label-success">Online</span>
                                                </td>
                                            @else
                                                <td class="text-left">
                                                    <span class="label label-warning">Offline</span>
                                                </td>
                                            @endif
                                            @if ($value->type == 1)
                                                <td class="text-left">
                                                    <span class="label label-success">IN</span>
                                                </td>
                                            @else
                                                <td class="text-left">
                                                    <span class="label label-warning">OUT</span>
                                                </td>
                                            @endif
                                            <!-- 	@if ($value->status == 1)
<td class="text-left">
              <span class="label label-success">Active</span>
              </td>
@else
<td class="text-left">
              <span class="label label-warning">In Active</span>
              </td>
@endif -->
                                            {{-- <td>{!! $value->created_by !!}</td>
											<td>{!! $value->updated_by !!}</td> --}}
                                            <td style="width: 160px;text-align: center;">
                                                <a href="{!! route('deviceConfigure.edit', $value->id) !!}"
                                                    class="btn btn-success btn-xs btnColor" title="Edit Device">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                </a>
                                                <a href="{!! route('deviceConfigure.delete', $value->id) !!}" data-token="{!! csrf_token() !!}"
                                                    data-id="{!! $value->id !!}"
                                                    class="delete btn btn-danger btn-xs deleteBtn btnColor"
                                                    title="Delete Device"><i class="fa fa-trash-o"
                                                        aria-hidden="true"></i></a>

                                                @if ($value->verification_status == 1)
                                                    <a href="{!! route('access.edit', $value->id) !!}"
                                                        class="btn btn-info btn-xs btnColor"
                                                        title="Allow access Employee">
                                                        <i class="glyphicon glyphicon-check" aria-hidden="true"></i>
                                                    </a>
                                                @endif
                                                @php
                                                    $access = \App\Model\AccessControl::where('device', $value->id)->first();
                                                @endphp

                                                @if (@$access)
                                                    <a href="{!! route('access.cloneform', $value->id) !!}"
                                                        class="btn btn-warning btn-xs btnColor"
                                                        title="Clone to Another Device">
                                                        <i class="glyphicon glyphicon-share" aria-hidden="true"></i>
                                                    </a>
                                                @endif

                                                <a href="{!! route('device.importemployee', ['device' => $value->id]) !!}"
                                                    class="btn btn-warning btn-xs btnColor"
                                                    title="Import Employee Details from Device">
                                                    <i class="glyphicon glyphicon-import" aria-hidden="true"></i>
                                                </a>
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
@section('page_scripts')
<script type="text/javascript">
    
    $(function() {  
        $(document).on('click', '.refresh-device', function(e) {
            $(this).attr('disabled',true);
            $('.device-status').html('<div class="alert alert-success alert-dismissable" style="font-size: 23px;"> Your request is processing. Please wait & don\'t refresh or click back button....</strong></div>');
            setTimeout(function(){
                $('.device-status').html('<div class="alert alert-danger alert-dismissable" style="font-size: 23px;">All device are offline. Restarting the device service. Please wait & don\'t refresh or click back button....</strong></div>');
            }, 10000);
        });
    });
</script>
@endsection
