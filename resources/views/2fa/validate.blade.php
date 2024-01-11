@include('admin.layout.css')
@include('admin.layout.custom')
@include('admin.layout.javascript')
<div class="container-fluid">
    <div class="spark-screen">
        <div class="row" style="margin: 10% auto">
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
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

            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default" style="background: #fcfcfc;">
                    <div class="panel-heading" style="color:#fff">2FA</div>
                    <div class="panel-body" style="border:1px solid #9e9e9e;border-top:0">
                        <form class="form-horizontal" role="form" method="POST" action="/2fa/validate">
                            {!! csrf_field() !!}

                            <div class="form-group{{ $errors->has('totp') ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label">One-Time
                                    Password</label>

                                <div class="col-md-6">
                                    <input type="number" class="form-control" name="totp">

                                    @if ($errors->has('totp'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('totp') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-info">
                                        <i class="fa fa-btn"></i>Validate
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
