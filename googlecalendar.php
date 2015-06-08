<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);

class GoogleCalendar 
{
	//Google cal uses RFC 3339
	//Get timezone form the calendar $calTimeZone = $results['timeZone']; 
	//Set timezone for $this->date_today->setTimezone(new DateTimeZone('America/New_York'));
	private $cal_id;
	private $devkey;
	private $query_url;
	private $date_today;	
	private $filestore;
	private $filename = "results.json";
	private $jsondata;
	
	private $base_url = "https://www.googleapis.com/calendar/v3/calendars/";
	private $query_settings = array(
		'singleEvents' => 'true', //Treat Recurring events as single events
		'orderBy' => 'startTime',
	 	'maxResults' => 3,
	 	'fields' => 'items(description,endTimeUnspecified,summary,start,start/timeZone,end),timeZone,updated'
	);

	public function __construct($calid, $devid)
	{ 
		require_once('jsonfilestore.php');
		$this->filestore = new JsonFileStore($this->filename);
       	$this->cal_id = $calid;
       	$this->devkey = $devid;
	   	//$this->date_today = date("c"); 
	   	$this->query_settings["timeMin"] = date("c"); //today's date (ISO 8601) 
	}

	public function fetchEventsJson() 
	{
		if ($this->filestore->doesExist()) {

			if ($this->filestore->isRecent()){
				//File recently updated
				//Use current file contents
		    	$this->jsondata = $this->filestore->read();
			} else {
		    	//No match
		    	//Fetch results using CURL with Google API 
		    	//Ref: https://developers.google.com/google-apps/calendar/v3/reference/events/list 
				$this->query_url = $this->buildUrl();
	   			$this->jsondata = $this->requestJsonData();
	   		}
		} else {
		    //Not a valid file - TODO handle 
		    return;
		}
		echo $this->jsondata;
		//return $this->jsondata;
	}

	private function buildUrl() 
	{
		$url = $this->base_url . $this->cal_id . "/events?";
		foreach ($this->query_settings as $key => $value) {
			$url .=  $key . "=" . $value . "&";
		}	
		return $url . "key=" . $this->devkey;
	}

	private function requestJsonData() 
	{
		$ch = curl_init();
		$timeout = 5;
		$foo = "https://www.googleapis.com/calendar/v3/calendars/ast496aed1q755k35v0dqieghk@group.calendar.google.com/events?singleEvents=true&orderBy=startTime&maxResults=3&fields=items(description,endTimeUnspecified,summary,start,start/timeZone,end),timeZone,updated&timeMin=2015-06-08T10:41:49-04:00&key=AIzaSyC6816UIAXI2r8IjMBkaeNeNoGIQF-cvBU";
		curl_setopt($ch, CURLOPT_URL, $this->query_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	
		if(! $data = curl_exec($ch)){ 
	        trigger_error(curl_error($ch)); 
	        //Handle if error thrown
	    }

		curl_close($ch);
		return $this->validateJson($data);
	}

	private function validateJson($data) 
	{
		if ($this->isValidJson($data)){
			$this->filestore->write($data);
			return $data; 
		} else {
			//not valid json - TODO Handle error/Do nothing
			//{"error": "Invalid JSON data"}
		}	
	}

	private function isValidJson($data) 
	{
		if (json_decode($data, true)){
			//valid json
			return true; 
		}
	}
}

?>