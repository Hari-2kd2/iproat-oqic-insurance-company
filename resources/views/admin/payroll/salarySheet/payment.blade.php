@extends('admin.master')
@section('content')
@section('title')
    @lang('salary_sheet.generate_salary_sheet')
@endsection
<style>
    .table>tbody>tr>td {
        padding: 5px 7px;
    }

    .address {
        margin-top: 22px;
    }

    .employeeName {
        position: relative;
    }

    #employee_id-error {
        position: absolute;
        top: 66px;
        left: 0;
        width: 100%he;
        width: 100%;
        height: 100%;
    }

    .icon-question {
        color: #7460ee;
        font-size: 16px;
        vertical-align: text-bottom;
    }

</style>
@section('content')

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

        <div class="container">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    @if ($message = Session::get('error'))
                        <div class="alert alert-danger alert-dismissible fade in" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            <strong>Error!</strong> {{ $message }}
                        </div>
                    @endif
                    {!! Session::forget('error') !!}
                    @if ($message = Session::get('success'))
                        <div class="alert alert-info alert-dismissible fade in" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            <strong>Success!</strong> {{ $message }}
                        </div>
                    @endif
                    {!! Session::forget('success') !!}
                    <div class="panel panel-default">
                        <div class="panel-heading">Pay With Razorpay</div>
                        <div class="panel-body text-center">
                            <form action="{!! Url('generateSalarySheet/payment') !!}" method="POST">
                                <!-- Note that the amount is in paise = 50 INR -->
                                <!--amount need to be in paisa-->
                                <script src="https://checkout.razorpay.com/v1/checkout.js" data-key="rzp_test_EzVAI0XNlc8bPq"
                                                                data-amount="1000" data-buttontext="Pay 10 INR" data-name="Monthly Salary"
                                                                data-description="Order Value" data-image="yout_logo_url" data-prefill.name="name"
                                                                data-prefill.email="email" data-theme.color="#ff7529">
                                </script>
                                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
