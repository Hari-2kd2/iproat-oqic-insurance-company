new_admin.blade.php

@if ($ip_attendance_status == 1)
<!-- employe attendance  -->
@php
    $logged_user = employeeInfo();
@endphp
<div class="col-md-6">
    <div class="white-box">
        <h3 class="box-title">Hey {!! $logged_user[0]->user_name !!} please Check in/out your attendance</h3>
        <hr>
        <div class="noticeBord">
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
                    <strong>{{ session()->get('error') }}</strong>
                </div>
            @endif
            <form action="{{ route('ip.attendance') }}" method="POST">
                {{ csrf_field() }}
                <p>Your IP is {{ \Request::ip() }}</p>
                <input type="hidden" name="employee_id" value="{{ $logged_user[0]->user_name }}">

                <input type="hidden" name="ip_check_status" value="{{ $ip_check_status }}">
                <input type="hidden" name="finger_id" value="{{ $logged_user[0]->finger_id }}">
                @if ($count_user_login_today > 0)
                    <button class="btn btn-danger">
                        <i class="fa fa-clock-o"> </i>
                        Check Out
                    </button>
                @else
                    <button class="btn btn-primary">
                        <i class="fa fa-clock-o"> </i>
                        Check In
                    </button>
                @endif

            </form>
        </div>
    </div>
</div>

<!-- end attendance  -->
@endif