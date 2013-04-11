<?php

class Dashboard extends CI_Controller {
	
	/**
	 * Main entry page for user dashboard
	 */
	public function oauthreg(){
        $postData = $this->input->post(NULL, TRUE);
        $getData = $this->input->get(NULL, TRUE);

        var_dump($postData);
        var_dump($getData);
        die();
	}

}