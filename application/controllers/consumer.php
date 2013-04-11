<?php

class Consumer extends CI_Controller {
	
	/**
	 * Main entry page for user dashboard
	 */
	public function oauthreg(){
        $getData = $this->input->get(NULL, TRUE);

        $code = $getData['code'];

        var_dump($code);
        die();
	}

}