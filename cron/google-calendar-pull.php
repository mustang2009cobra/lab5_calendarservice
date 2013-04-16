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
$events = get_db_events($conn);
foreach($events as $event){
	if($event['notified'] == 1){
		continue;
	}

	$user = get_db_user($event['userId']);
	$calendar = get_db_user($event['calendarId']);

	var_dump($user);
	var_dump($calendar);
	die();
	if($event['allDayEvent'] == 0){ //If all-day event
		$startTimestamp = strtotime($event['start']); //Get timestamp for date
		if($startTimestamp > time()){
			if(($startTimestamp - time()) < 86400){ //Event is less than one day away
				echo "Notifying event\n";
				$summary = $event['summary'];
				echo "  Summary: $summary\n";

				//Update notified in DB
				set_db_event_notified($conn, $event);

				//Notify event
				make_curl_call($event[''], $type, $params);
			}
		}
	}
	else if($event['allDayEvent'] == 1){
		$startTimestamp = strtotime($event['start']);
		if($startTimestamp > time()){
			if(($startTimestamp - time()) < 300 ){
				echo "Notifying event\n";
				$summary = $event['summary'];
				echo "  Summary: $summary\n";
				//Notify event
			}
		}
	}
	else{
		echo "WARNING: Event is not designated as all-day or not";
	}
	
}



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

function get_db_events($conn){
	$eventsSql = "select * from events";
	$stmt = $conn->query($eventsSql);

	$events = array();
	while($row = $stmt->fetch()){
		$events[] = $row;
	}
	return $events;
}

function get_db_user($conn, $id){
	$usersSql = "select * from users where id = $id";
	$stmt = $conn->query($usersSql);

	$users = array();
	while($row = $stmt->fetch()){
		$users[] = $row;
	}
	return $users[0];
}

function get_db_calendar($conn, $id){
	$calendarsSql = "select * from calendars where id = $id";
	$stmt = $conn->query($calendarSql);

	$calendars = array();
	while($row = $stmt->fetch()){
		$calendars[] = $row;
	}
	return $calendars[0];
}

function set_db_event_notified($conn, $event){
	$conn->update('events', array('notified' => 1), array('id' => $event['id']));
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

			//Figure out whether event is all-day or not
			$allDayEvent = 0;
			$start = $event['start'];
			$end = $event['end'];
			if(!isset($event['start']['dateTime'])){
				$allDayEvent = 1;
				$start = $start['date'];
				$end = $end['date'];
			}
			else{
				$start = $start['dateTime'];
				$end = $end['dateTime'];
			}

			//Insert event into database
			$conn->insert('events', array(
				'userId' => $user['id'],
				'calendarId' => $calendar['id'],
				'googleId' => $event['id'],
				'summary' => $event['summary'],
				'htmlLink' => $event['htmlLink'],
				'created' => $event['created'],
				'updated' => $event['updated'],
				'start' => $start,
				'end' => $end,
				'allDayEvent' => $allDayEvent,
				'iCalUID' => $event['iCalUID'],
				'notified' => 0
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
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
	    $output = curl_exec($ch);
	    curl_close($ch);

	    $retData = json_decode($output, TRUE);
	    return $retData;
	}
	else if($type == "POST"){
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_POST, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        
        return $output;
	}
	else{
		throw new Exception("Method type not allowed");
	}
}


?>