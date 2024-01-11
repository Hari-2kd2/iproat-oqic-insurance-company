<?php

date_default_timezone_set("Asia/Kolkata");

$eventLog = $_REQUEST['event_log'];

$eventLog = json_decode($eventLog);



if (isset($eventLog->AccessControllerEvent->employeeNoString)) {

	$finger_print_id = $eventLog->AccessControllerEvent->employeeNoString;

	$device_name = $eventLog->AccessControllerEvent->deviceName;

	$date_time = $eventLog->dateTime;

	$ip_address = $eventLog->ipAddress;

	$mysqli = new mysqli("localhost", "root", "Admin@123", "db_bafna", "3306");

	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: " . $mysqli->connect_error;
		exit();
	}

	$datetime = DATE('Y-m-d H:i:s', strtotime($date_time));

	$device_date = DATE('Y-m-d', strtotime($datetime));

	$mysqli->query("INSERT INTO `ms_sql` (`primary_id`, `branch_id`, `local_primary_id`, `evtlguid`, `ID`, `type`, `datetime`, `status`, `employee`, `device`, `device_employee_id`, `sms_log`, `device_name`, `devuid`, `live_status`, `punching_time`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES(
					NULL,	NULL,	NULL,	NULL,	
					'" . $finger_print_id . "',
					NULL,	'" . $datetime . "',	0,
					NULL,	NULL,	NULL,	NULL,	NULl,	NULL,	NULL,	
					'" . date('Y-m-d H:i:s') . "',	NULL,	NULL,	'" . DATE('Y-m-d H:i:s') . "',	'" . DATE('Y-m-d H:i:s') . "')
		");


	//print_r($mysqli->error); exit;
	if ($mysqli->error) {
		$myfile = fopen("listen_error.txt", "a+") or die("Unable to open file!");
		$txt = print_r($mysqli->error, 1);
		fwrite($myfile, $txt);
		$txt = "DateTime Now. " . DATE('d-m-Y h:i:s A') . ".\n";
		fwrite($myfile, $txt);
		fclose($myfile);
	} else {
		$myfile = fopen("listen_success.txt", "a+") or die("Unable to open file!");
		$txt = print_r($employee, 1);
		fwrite($myfile, $txt);
		$txt = "DateTime Now. " . DATE('d-m-Y h:i:s A') . ".\n";
		fwrite($myfile, $txt);
		fclose($myfile);
	}

}

mysqli_close($mysqli);
