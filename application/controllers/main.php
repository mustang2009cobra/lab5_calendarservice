<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

	/**
	 * Renders site homepage
	 */
	public function index()
	{
		$this->load->view('templates/header');
		$this->load->view('pages/home');
		$this->load->view('templates/header');
	}

	/**
	 * Renders login/register form
	 */
	public function login()
	{
		$this->load->view('templates/header');
		$this->load->view('pages/login');
		$this->load->view('templates/header');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */