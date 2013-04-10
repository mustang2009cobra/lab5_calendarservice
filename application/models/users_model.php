<?php
class Users_model extends CI_Model {

	public function __construct(){
		$this->load->database();
	}
	
	public function create_user(){
		$formData = $this->input->post(NULL, TRUE);
		
		$data = array(
			'email' => $formData['createUserEmail'],
			'name' => $formData['createUserName'],
			'password' => $formData['createUserPassword']
		);
		
		$result = $this->db->insert('users', $data);

        $query = $this->db->get_where('users', $data)->result();
        $user = $query[0];
        $this->set_user_session($user);

        return $result;
	}

	public function authenticate_user() {
		$formData = $this->input->post(NULL, TRUE);

        $query = $this->db->get_where('users', array('email' => $formData['loginUserEmail'],
                                                     'password' => $formData['loginUserPassword']));

        if($query->num_rows != 0){
            $result = $query->result();
            $user = $result[0];
            $this->set_user_session($user);
            return true;
        }
        else{
            return false;
        }
	}

    private function set_user_session($user){
        $this->session->set_userdata('user', $user);
    }
}