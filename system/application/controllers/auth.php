<?php

class Auth extends Controller {

	function Auth()
	{
		parent::Controller();

		$this->load->library('form_validation', NULL, 'validation');
		$this->load->library('session');
	}

	function index()
	{
		$this->load->view('header');
		$this->load->view('login_form');
		$this->load->view('footer');
	}

	function login()
	{
		$data = array();
		$v_rules = array();
		$v_rules[] = array(
							'field' => 'user_name',
							'label' => 'Username',
							'rules' => 'trim|required|alpha_numeric|xss_clean'
						);
		$v_rules[] = array(
							'field' => 'user_password',
							'label' => 'Password',
							'rules' => 'trim|required|min_length[4]|xss_clean'
						);

		$this->validation->set_rules($v_rules);

		$this->validation->set_error_delimiters('<br /><span class="inlineErr">', '</span>');

		if( $this->validation->run() == FALSE ) {
			$this->load->view('header');
			$this->load->view('login_form', $data);
			$this->load->view('footer');
		} else {
			$user_name = $this->input->post('user_name');
			$user_password = $this->input->post('user_password');

			$this->db->select('user_id, user_name, user_firstname, user_lastname, user_type, user_status');
			$this->db->from('users');
			$this->db->where(array('user_name' => $user_name, 'user_password' => $user_password, 'user_status' => 1));
			$this->db->limit(1);
			$user_info = $this->db->get();

			if( $user_info->num_rows() > 0 ) {
				$user_data = $user_info->row_array();
				$this->session->set_userdata('doxentral_SSO', $user_data);

				redirect('home');
			} else {
				$data['err_msg'] = 'Login failed. Please check your Username and Password.';

				$this->load->view('header');
				$this->load->view('login_form', $data);
				$this->load->view('footer');
			}
		}
	}

	function logout() {
		$this->session->sess_destroy();
		redirect('home');
	}
}

/* End of file auth.php */