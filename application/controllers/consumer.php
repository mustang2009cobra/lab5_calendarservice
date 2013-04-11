<?php

class Consumer extends CI_Controller {
	
	/**
	 * Main entry page for user dashboard
	 */
	public function oauthreg(){
        $getData = $this->input->get(NULL, TRUE);

        $code = $getData['code'];

        $requestPostData = array(
        	'code' => $code,
        	'client_id' => '128117557776.apps.googleusercontent.com',
        	'client_secret' => 'm4gHm2Sg-t62jg2wmZzN2za2',
        	'redirect_uri' => 'http://ec2-54-225-94-113.compute-1.amazonaws.com/lab5_calendarservice/index.php/consumer/oauthreg',
        	'grant_type' => 'authorization_code'
       	);

        //REQUEST ACCESS_TOKEN FROM FOURSQUARE
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://accounts.google.com/o/oauth2/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($requestPostData));
        $output = curl_exec($ch);
        curl_close($ch);

        var_dump($output);
        die();

        //$retData = json_decode($output);
        //$access_token = $retData->{"access_token"};
	}

}