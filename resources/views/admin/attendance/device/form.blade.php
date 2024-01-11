@extends('admin.master')
@section('content')

@section('title')
    @if (isset($editModeData))
        @lang('attendance.edit_devices')
    @else
        @lang('attendance.add_devices')
    @endif
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
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
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
                        @if (isset($editModeData))
                            {{ Form::model($editModeData, [
                                'route' => ['deviceConfigure.update', $editModeData->id],
                                'method' => 'PUT',
                                'files' => 'true',
                                'class' => 'form-horizontal',
                            ]) }}
                        @else
                            {{ Form::open([
                                'route' => 'deviceConfigure.store',
                                'enctype' => 'multipart/form-data',
                                'class' => 'form-horizontal',
                            ]) }}
                        @endif

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-offset-2 col-md-6">
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
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('attendance.device_name') <span
                                                class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            {!! Form::text(
                                                'name',
                                                old('name'),
                                                $attributes = [
                                                    'class' => 'form-control required name',
                                                    'id' => 'name',
                                                    'placeholder' => __('Device Name'),
                                                ],
                                            ) !!}
                                        </div>
                                    </div>

                                    <!--  <div class="form-group">
                                            <label class="control-label col-md-4">@lang('attendance.device_model')
                                                <span class="validateRq">*</span></label>
                                            <div class="col-md-8">
                                                {!! Form::text(
                                                    'model',
                                                    old('model'),
                                                    $attributes = [
                                                        'class' => 'form-control required model',
                                                        'id' => 'model',
                                                        'placeholder' => __('Device Model'),
                                                    ],
                                                ) !!}
                                            </div>
                                        </div> -->
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('attendance.device_ip') <span
                                                class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            @if (isset($editModeData))
                                                {!! Form::text(
                                                    'ip',
                                                    old('ip'),
                                                    $attributes = [
                                                        'class' => 'form-control required ip',
                                                        'id' => 'ip',
                                                        'placeholder' => __('Device IP'),
                                                        'readonly'=>true,
                                                    ],
                                                ) !!}
                                            @else
                                             {!! Form::text(
                                                    'ip',
                                                    old('ip'),
                                                    $attributes = [
                                                        'class' => 'form-control required ip',
                                                        'id' => 'ip',
                                                        'placeholder' => __('Device IP'),
                                                    ],
                                                ) !!}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('attendance.device_protocol')
                                            <span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            {!! Form::text(
                                                'protocol',
                                                old('protocol') ? old('protocol') : 'ISAPI',
                                                $attributes = [
                                                    'class' => 'form-control required protocol',
                                                    'id' => 'protocol',
                                                    'placeholder' => __('Device Protocol'),
                                                ],
                                            ) !!}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('attendance.device_port') <span
                                                class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            {!! Form::number(
                                                'port',
                                                old('port'),
                                                $attributes = [
                                                    'class' => 'form-control required port',
                                                    'id' => 'port',
                                                    'placeholder' => __('Device Port'),
                                                ],
                                            ) !!}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('attendance.device_type') <span
                                                class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            {!! Form::select('type', [1 => 'In Device', 2 => 'Out Device'], old('type'), [
                                                'class' => 'form-control required',
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('attendance.device_username')
                                            <span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            {!! Form::text(
                                                'username',
                                                old('username'),
                                                $attributes = [
                                                    'class' => 'form-control required username',
                                                    'id' => 'username',
                                                    'placeholder' => __('Device Username'),
                                                ],
                                            ) !!}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('attendance.device_password')
                                            <span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            {!! Form::text(
                                                'password',
                                                old('password'),
                                                $attributes = [
                                                    'class' => 'form-control required password',
                                                    'id' => 'password',
                                                    'placeholder' => __('Device Password'),
                                                ],
                                            ) !!}
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
                                                <button type="submit" class="btn btn-info btn_style"><i
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
