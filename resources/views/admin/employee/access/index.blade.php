@extends('admin.master')
@section('content')
@section('title')
@lang('employee.attendancelog_tit')
@endsection
<div class="container-fluid">
	<div class="row bg-title">
		<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
			<ol class="breadcrumb">
				<li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
						@lang('dashboard.dashboard')</a></li>
				<li>@yield('title')</li>
			</ol>
		</div>
			<div class="col-lg-6 col-sm-8 col-md-8 col-xs-12">
			<a href="{{ route('access.log',['redirect'=>1]) }}"
				class="btn btn-success pull-right hidden-xs hidden-sm waves-effect waves-light sync-attendance"> <i
					class="fa fa-refresh" aria-hidden="true"></i> @lang('attendance.sync_attendance')</a>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="panel panel-info">
				<div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title') <small>( @lang('attendance.last_sync') : {{$lastsync}} )</small></div>
				<div class="panel-wrapper collapse in" aria-expanded="true">
					<div class="panel-body">
						<div class="sync-status">
						@if(session()->has('success'))
						<div class="alert alert-success alert-dismissable">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<small>{{ session()->get('success')
								}}</small>
						</div>
						@endif
						@if(session()->has('error'))
						<div class="alert alert-danger alert-dismissable">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<i class="glyphicon glyphicon-remove"></i>&nbsp;<small>{{ session()->get('error')
								}}</small>
						</div>
						@endif
						</div>

						<div class="data" style="margin: 8px;padding:8px">
                            @include('admin.employee.access.pagination')
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
		$(document).on('click', '.sync-attendance', function(e) {
	    	$(this).attr('disabled',true);
	    	$('.sync-status').html('<div class="alert alert-success alert-dismissable" style="font-size: 14px;"> Your request is processing. Please wait & don\'t refresh or click back button....</strong></div>');
	    	
	    });

	    $('.data').on('click', '.pagination a', function(e) {
	        getData($(this).attr('href').split('page=')[1]);
	        e.preventDefault();
	    });

	});



    function getData(page) {
		/* var employee_name = $('.employee_name').val();
        var department_id = $('.department_id').val();
        var designation_id = $('.designation_id').val();
        var role_id = $('.role_id').val();*/

		$.ajax({
            url: '?page=' + page, // + "&employee_name=" + employee_name + "&department_id=" + department_id +"&designation_id=" + designation_id + "&role_id=" + role_id
            datatype: "html",
        }).done(function(data){
            $('.data').html(data);
            $("html, body").animate({
                scrollTop: 0
            }, 800);
        }).fail(function(){
            alert('No response from server');
        });
    }
/*
setInterval(function () {
	 getData(1)
}, 10000);*/

</script>
@endsection

<!-- setTimeout(function(){
                $('.sync-status').html('<div class="alert alert-danger alert-dismissable" style="font-size: 23px;">All device are offline. Restarting the device service. Please wait & don\'t refresh or click back button....</strong></div>');
            }, 10000); -->