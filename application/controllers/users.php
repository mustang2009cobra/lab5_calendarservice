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

	public function connect_to_google_calendar(){
		$calendarURL = "https://accounts.google.com/o/oauth2/auth";
		$calendarURL .= "?response_type=code";
		$calendarURL .= "&client_id=128117557776.apps.googleusercontent.com";
		$calendarURL .= "&redirect_uri=http://ec2-54-225-94-113.compute-1.amazonaws.com/lab5_calendarservice/index.php/consumer/oauthreg";
		$calendarURL .= "&scope=https://www.googleapis.com/auth/calendar";
		$calendarURL .= "&access_type=offline";
		redirect(urlencode($calendarURL), 'location');
	}
}

/* End of file uesrs.php */
/* Location: ./application/controllers/users.php */