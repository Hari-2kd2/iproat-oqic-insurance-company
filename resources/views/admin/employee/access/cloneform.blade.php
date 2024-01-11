@php
use App\Model\AccessControl;
@endphp
@extends('admin.master')
@section('content')

@section('title')
    @lang('employee.copy_device')
@endsection



<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-4 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>@lang('dashboard.dashboard')</a></li>
                <li class=""><a href="{{ route('deviceConfigure.index') }}"></i>@lang('attendance.view_devices')</a></li>
                <li>@yield('title')</li>

            </ol>
        </div>
        <div class="col-lg-4 col-md-8 col-sm-8 col-xs-12">
            <a href="{{ route('deviceConfigure.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                    class="fa fa-list-ul" aria-hidden="true"></i> @lang('attendance.view_devices')</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        {{ Form::open([
                            'route' => 'access.clone',
                            'enctype' => 'multipart/form-data',
                            'class' => 'form-horizontal',
                        ]) }}

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-offset-2 col-md-8">
                                    <div class="status-msg">
                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible" role="alert">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-label="Close"><span aria-hidden="true">×</span></button>
                                            @foreach ($errors->all() as $error)
                                                <strong>{!! $error !!}</strong><br>
                                            @endforeach
                                        </div>
                                    @endif
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
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label col-md-2">@lang('employee.device_name') <span
                                                class="validateRq">*</span></label>
                                        <div class="col-md-10">
                                            {!! Form::text(
                                                'name',
                                                $device->name,
                                                $attributes = [
                                                    'class' => 'form-control required name',
                                                    'id' => 'name',
                                                    'readonly'=>true,
                                                    'placeholder' => __('Device Name'),
                                                ],
                                            ) !!}
                                            {!! Form::hidden(
                                                'device_id',
                                                $device->id,
                                                $attributes = [
                                                    'class' => 'form-control required name',
                                                    'id' => 'name',
                                                    'readonly'=>true,
                                                    'placeholder' => __('Device Name'),
                                                ],
                                            ) !!}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                          <label class="control-label col-md-2">@lang('employee.choosedevice') <span
                                                class="validateRq">*</span></label>
                                        <div class="col-md-10"> 
                                             <div class="col-md-2">
                                                <div class="checkbox checkbox-info"><input class="inputCheckbox" type="checkbox" id="selectall">
                                                    <label for="selectall" class="selectall_lbl">Select All</label>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12" style="border:1px solid lightgrey;min-height: 250px;">
                                            @foreach($map_device as $deviceinfo)
                                            @php
                                                $exist=0;
                                            @endphp
                                            @if($exist)
                                            <div class="col-md-3">
                                                <div class="checkbox checkbox-info"><input class="inputCheckbox" type="checkbox" id="inlineCheckbox{{$deviceinfo->id}}" checked="" name="devicelist[]" value="{{$deviceinfo->id}}">
                                                    <label for="inlineCheckbox{{$deviceinfo->id}}">{!! $deviceinfo->name.' ( '.$deviceinfo->ip.' )' !!}</label></div>
                                                
                                            </div>
                                            @else
                                            <div class="col-md-3">
                                                <div class="checkbox checkbox-info"><input class="inputCheckbox" type="checkbox" id="inlineCheckbox{{$deviceinfo->id}}" name="devicelist[]" value="{{$deviceinfo->id}}">
                                                    <label for="inlineCheckbox{{$deviceinfo->id}}">{!! $deviceinfo->name.' ( '.$deviceinfo->ip.' )' !!}</label></div>
                                                
                                            </div>
                                            @endif
                                             @endforeach
                                        </div>
                                    </div>
                                        
                                      
                                    </div>
                                </div>
                            </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-offset-4 col-md-8">
                                            @if (isset($editModeData))
                                                <button type="submit" class="btn btn-info btn_style"><i
                                                        class="fa fa-pencil"></i> @lang('common.update')</button>
                                            @else
                                                <button type="submit" class="btn btn-info btn_style saveclone"><i
                                                        class="fa fa-check"></i> @lang('common.save')</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page_scripts')
<script type="text/javascript">
        $('#selectall').click(function(){
          var checked = !$(this).data('checked');
          $('input:checkbox').prop('checked', checked);
          $('.selectall_lbl').html(checked ? 'Unselect All' : 'Select All' )
          $(this).data('checked', checked);
        });

        $(document).on('click', '.saveclone', function(e) {
            //$(this).attr('disabled',true);
            $('.status-msg').html('<div class="alert alert-success alert-dismissable" style="font-size: 23px;"> Your request is processing. Please wait & don\'t refresh or click back button....</strong></div>');
            setTimeout(function(){
                $('.status-msg').html('<div class="alert alert-danger alert-dismissable" style="font-size: 23px;">All device are offline. Restarting the device service. Please wait & don\'t refresh or click back button....</strong></div>');
            }, 10000);
        });
</script>
@endsection

