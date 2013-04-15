<?php

use Doctrine\Common\ClassLoader;

require './Doctrine/Doctrine/Common/ClassLoader.php';

$classLoader = new ClassLoader('Doctrine', './Doctrine');
$classLoader->register();


/**
 * Setup Initial Database Connection
 */ 
$config = new \Doctrine\DBAL\Configuration();
$connectionParams = array(
	'dbname' => 'calendar_service',
	'user' => 'root',
	'password' => '',
	'host' => 'localhost',
	'driver' => 'pdo_mysql'
);
$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);


/**
 * Query Google Calendar API for each user
 */
$users = get_db_users($conn);
foreach($users as $user){
	//Make call to Google API
	$access_token = $user['googleAccessToken'];
	$result = make_curl_call("https://www.googleapis.com/calendar/v3/users/me/calendarList/", "GET", array('access_token' => $access_token));	
	$calendars = $result['items'];

	//Store calendars in MySQL DB
	update_db_calendars($conn, $calendars, $user);

	foreach($calendars as $calendar){
		$calendarId = $calendar['id'];
		$result = make_curl_call("https://www.googleapis.com/calendar/v3/calendars/$calendarId/events/", "GET", array('access_token' => $access_token));
		$events = $result['items'];

		update_db_events($conn, $events, $calendar, $user);	
	}
}

/**
 * Go through events in DB and notify users of upcoming
 */




////////////////////////////////////
//SCRIPT FUNCTIONS
////////////////////////////////////
function get_db_users($conn){
	$usersSql = "select * from users";
	$stmt = $conn->query($usersSql);

	$users = array();
	while($row = $stmt->fetch()){
		$users[] = $row;
	}
	return $users;
}

function update_db_calendars($conn, $calendars, $user){
	$calendarsSql = "select * from calendars";
	$stmt = $conn->query($calendarsSql);

	//Get the existing calendars from the DB and stick them in a hash
	$existingCalendars = array();
	while($row = $stmt->fetch()){
		$calGoogleId = $row['googleId'];
		$existingCalendars[$calGoogleId] = $row;
	}

	//Add each new calendar to the DB if it's not already there
	foreach($calendars as $calendar){
		$newCalGoogleId = $calendar['id'];
		if(!isset($existingCalendars[$newCalGoogleId])){
			//Add new calendar to DB
			$conn->insert('calendars', array(
				'userId' => $user['id'],
				'googleId' => $calendar['id'],
				'summary' => $calendar['summary']
			));
		}
	}
}

function update_db_events($conn, $events, $calendar, $user){
	$eventsSql = "select * from events";
	$stmt = $conn->query($eventsSql);

	$existingEvents = array();
	while($row = $stmt->fetch()){
		$eventGoogleId = $row['googleId'];
		$existingEvents[$eventGoogleId] = $row;
	}

	foreach($events as $event){
		$newEventGoogleId = $event['id'];
		if(!isset($existingEvents[$newEventGoogleId])){
			$conn->insert('events', array(
				'userId' => $user['id'],
				'calendarId' => $calendar['id'],
				'googleId' => $event['id'],
				'summary' => $event['summary'],
				'htmlLink' => $event['htmlLink'],
				'created' => $event['created'],
				'updated' => $event['updated'],
				'start' => $event['start']['dateTime'],
				'end' => $event['end']['dateTime'],
				'iCalUID' => $event['iCalUID']
			));
		}
	}
}

function make_curl_call($url, $type, $params){
	if($type == "GET"){
		//Set Params on URL
		if(count($params) > 0){
			$url .= "?";
			$url .= http_build_query($params);
		}

		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
	    $output = curl_exec($ch);
	    curl_close($ch);

	    $retData = json_decode($output, TRUE);
	    return $retData;
	}
	else{
		throw new Exception("Method Type Not Allowed");
	}
}


?>