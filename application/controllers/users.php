<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('users_model');
	}

	/**
	 * Logs the user in and redirects them to the dashboard
	 */
	public function login()
	{
		$success = $this->users_model->authenticate_user();

        if($success){
            redirect(site_url('dashboard/main'), 'location');
        }
        else{
            redirect(site_url('main/login?error=baduser'), 'location');
        }
	}

	public function logout()
	{
		$this->session->unset_userdata('user');
        redirect(site_url('main/index'), 'location');
	}

	/**
	 * Registers a new user and redirects them to the dashboard
	 */
	public function register()
	{
		$success = $this->users_model->create_user();
		
		if($success){
			redirect(site_url('dashboard/main'), 'location');
		}
		else{
			throw new Exception("Could not create user");
		}
	}
}

/* End of file uesrs.php */
/* Location: ./application/controllers/users.php */