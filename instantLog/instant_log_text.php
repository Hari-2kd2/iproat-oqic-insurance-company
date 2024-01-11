<?php

$eventLog = $_REQUEST['event_log'];

$eventLog = json_decode($eventLog);

if (isset($eventLog->AccessControllerEvent->employeeNoString)) {

	$myfile = fopen("instant_log.txt", "a+") or die("Unable to open file!");

	$txt = print_r($eventLog, 1);

	fwrite($myfile, $txt);

	$txt = "DateTime Now. " . DATE('d-m-Y h:i:s A') . ".\n";

	fwrite($myfile, $txt);

	fclose($myfile);
}
