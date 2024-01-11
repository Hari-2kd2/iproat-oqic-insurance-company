@extends('admin.master')
@section('content')
@section('title')
    @lang('2fa.two_factor_authentication')
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
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i>Two-Factor Authentication</div>
                    {{-- <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i>@yield('title')</div> --}}
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
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                        aria-hidden="true">×</span></button>
                                @foreach ($errors->all() as $error)
                                    <strong>{!! $error !!}</strong><br>
                                @endforeach
                            </div>
                        @endif
                        <div class="col-md-offset-1">
                            @if (Auth::user()->google2fa_secret)
                                <div class="row">
                                    <div class="form-group">
                                        <label class="control-label col-md-2">@lang('2fa.password') <span
                                                class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            {!! Form::text(
                                                'password',
                                                old('password'),
                                                $attributes = [
                                                    'class' => 'form-control required password',
                                                    'id' => 'password',
                                                    'placeholder' => __('Password'),
                                                ],
                                            ) !!}
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="form-group">
                                        <label class="control-label col-md-2">@lang('2fa.password_confirmation') <span
                                                class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            {!! Form::text(
                                                'password_confirmation',
                                                old('password_confirmation'),
                                                $attributes = [
                                                    'class' => 'form-control required password_confirmation',
                                                    'id' => 'password_confirmation',
                                                    'placeholder' => __('Password confirmation'),
                                                ],
                                            ) !!}
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col-md-offset-3 col-md-8">
                                                    {{-- <a href="{{ url('2fa/disable') }}"><button
                                                            class="btn btn-warning">Disable
                                                            2FA</button> </a> --}}
                                                    <button class="btn btn-warning btnDisable2fa" type="submit">Disable
                                                        2FA</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- <a href="{{ url('2fa/disable') }}"><button class="btn btn-warning">Disable
                                                2FA</button> </a> --}}
                            @else
                                <a href="{{ url('2fa/enable') }}"><button class="btn btn-success">Enable
                                        2FA</button> </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('page_scripts')
    <script>
        $(document).ready(function() {

            $('.btnDisable2fa').click(function(e) {
                e.preventDefault();
                var password = $('.password').val();
                var password_confirmation = $('.password_confirmation').val();
                $.ajax({
                    type: "get",
                    url: "{{ route('2fa.disable') }}",
                    data: {
                        password: password,
                        password_confirmation: password_confirmation,
                    },
                    success: function(response) {
                        console.log(response);
                        if (response == 'error') {
                            alert(response);
                        }
                        if (response == 'validation_error') {
                            alert(response);
                        }
                    }
                });
            });

        });
    </script>
@endsection
