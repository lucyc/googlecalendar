<?php
require_once('config.php');
require_once('googlecalendar.php');

// Set headers for CORS request
header('Access-Control-Allow-Origin: ' . $origin);
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json;charset=utf-8'); 

$calendar = new GoogleCalendar($credentials['calendarId'], $credentials['devKey']);		
$data = $calendar->fetchEventsJson();

if (is_null($data)) {
	//No data returned: TODO return error code or do nothing
} else {
	//Data Returned
	return $data; 
	echo $data;
}

?>

