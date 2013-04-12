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
	$calendars = make_curl_call("https://www.googleapis.com/calendar/v3/users/me/calendarList/", "GET", array('access_token' => $access_token));	
	var_dump($calendars);
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

	    $retData = json_decode($output);
	    return $retData;
	}
	else{
		throw new Exception("Method Type Not Allowed");
	}
}


?>