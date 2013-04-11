<?php

class Consumer extends CI_Controller {
	
	/**
	 * Main entry page for user dashboard
	 */
	public function oauthreg(){
        $getData = $this->input->get(NULL, TRUE);

        $code = $getData['code'];

        //Get access token from Google API
        $requestPostData = array(
        	'code' => $code,
        	'client_id' => '128117557776.apps.googleusercontent.com',
        	'client_secret' => 'm4gHm2Sg-t62jg2wmZzN2za2',
        	'redirect_uri' => 'http://ec2-54-225-94-113.compute-1.amazonaws.com/lab5_calendarservice/index.php/consumer/oauthreg',
        	'grant_type' => 'authorization_code'
       	);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://accounts.google.com/o/oauth2/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($requestPostData));
        $output = curl_exec($ch);
        curl_close($ch);
        $retData = json_decode($output, TRUE);
        
        //Get relevant variables from Google API response
        $access_token = $retData['access_token'];
        $token_type = $retData['token_type'];
        $expires_in = $retData['expires_in'];
        $refresh_token = $retData['refresh_token'];

        var_dump($access_token);
        var_dump($token_type);
        var_dump($expires_in);
        var_dump($refresh_token);
        die();
	}

}