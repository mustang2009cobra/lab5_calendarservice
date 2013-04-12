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
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/calendar/v3/users/me/calendarList/?access_token=$access_token");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    $output = curl_exec($ch);
    curl_close($ch);

    $retData = json_decode($output);
    var_dump($retData);
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


?>