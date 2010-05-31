<?php
/**
 * Users controller
 */
class Users extends Controller {
	var $upload_path = '';

	function Users()
	{
		parent::Controller();

		$this->load->library('session');

		if( !is_array($this->session->userdata('doxentral_SSO')) ) {
			redirect('/auth/');
		}

		$doxentral_SSO = $this->session->userdata('doxentral_SSO');
		if( $doxentral_SSO['user_type'] != 1 ) {
			redirect('/home');
		}

		$this->upload_path = $this->config->item('upload_path', 'settings');

		$this->load->library('email');
		$this->load->library('parser');
		$this->load->library('form_validation', NULL, 'validation');
	}

	function index()
	{
		if( is_array($this->session->userdata('doxentral_SSO')) ) {
			$del_status = $this->session->flashdata('del_status');
			$data['del_status'] = $del_status;
			$data['user_data'] = $this->session->userdata('doxentral_SSO');
			$this->load->view('header');
			$this->load->view('navbar', $data);

			$this->load->model('UserModel');
			$data['users'] = $this->UserModel->get_users();
			$this->load->view('manage_users', $data);

			$this->load->view('footer');
		} else {
			redirect('/auth/');
		}
	}

	/**
	 * Create New User
	 */
	function create()
	{
		$data = array();
		$v_rules[] = array();

		$v_rules[] = array(
							'field' => 'user_name',
							'label' => 'Username',
							'rules' => 'trim|required|min_length[4]|alpha_dash|xss_clean'
						);
		$v_rules[] = array(
							'field' => 'user_password',
							'label' => 'Password',
							'rules' => 'trim|required|min_length[4]|xss_clean'
						);
		$v_rules[] = array(
							'field' => 'user_firstname',
							'label' => 'Firstname',
							'rules' => 'trim|required|xss_clean'
						);
		$v_rules[] = array(
							'field' => 'user_lastname',
							'label' => 'Lastname',
							'rules' => 'trim|required|xss_clean'
						);
		$v_rules[] = array(
							'field' => 'user_email',
							'label' => 'E-Mail Address',
							'rules' => 'trim|required|email|xss_clean'
						);
		$this->validation->set_rules($v_rules);
		$this->validation->set_error_delimiters('<span class="inlineErr">', '</span><br />');

		if( $this->validation->run() != FALSE ) {
			$this->load->model('UserModel');

			$insert_data = array();
			$insert_data['user_name'] =  $this->input->post('user_name');
			$insert_data['user_password'] =  $this->input->post('user_password');
			$insert_data['user_firstname'] =  $this->input->post('user_firstname');
			$insert_data['user_lastname'] =  $this->input->post('user_lastname');
			$insert_data['user_status'] =  1;	//New
			$insert_data['user_created'] = date('Y-m-d H:i:s');
			$insert_data['user_email'] =  $this->input->post('user_email');

			$data['user_id'] = $this->UserModel->create_user($insert_data);

			//Notify user
			if( $this->input->post('notify_user')=='1' && $data['user_id']>0 ) {
				$this->email->from('admin@localhost', 'doxentral');
				$this->email->to($this->input->post('user_email'));

				$this->email->subject('Your doxentral Account Info');
				$tpl_data = array(
					            'user_firstname' => ucwords($this->input->post('user_firstname')),
					            'user_name' => $this->input->post('user_name'),
								'user_password' => $this->input->post('user_password'),
								'base_url' => base_url().'home/'
				            );

				$email_msg = $this->parser->parse('tpl_newuser_notice', $tpl_data, TRUE);
				$this->email->message($email_msg);

				$this->email->send();
			}
		}

		$this->load->view('user_create_status', $data);
	}

	function delete($_user_id)
	{
		$del_status = array();
		$this->load->model('UserModel');
		$this->load->model('FileModel');

		$user_data = $this->UserModel->get_user($_user_id);

		if( $user_data['user_type'] != 1 ):
			//Get owned files and delete them on disk
			$owned_files = array();
			$owned_files = $this->UserModel->get_owned_files($_user_id, $user_data['user_type']);

			$del_status['file_count'] = count($owned_files);

			foreach( $owned_files as $f ):
				$f_full = $this->upload_path.$f->file_on_disk;
				@unlink($f_full);

				//Delete file entries from acl
				$this->FileModel->delete_file_acl($f->file_id);

				//Delete master file entry
				$this->FileModel->delete_file_acl($f->file_id);
			endforeach;

			//Delete file associations
			$del_status['acl_count'] = $this->UserModel->delete_user_acl($_user_id);

			//Delete master entry
			$del_status['user_count'] = $this->UserModel->delete_user($_user_id);

			$this->session->set_flashdata('del_status', $del_status);
		else:
			$this->session->set_flashdata('del_status', 'User is an Admin!');
		endif;

		redirect('users');
	}

	function edit($_user_id=NULL) {

		$_user_id = intval($_user_id);

		$doxentral_SSO = $this->session->userdata('doxentral_SSO');
		$data['doxentral_SSO'] = $doxentral_SSO;

		$this->load->model('UserModel');
		$data['user_data'] = $this->UserModel->get_user($_user_id);

		$this->load->view('user_edit', $data);
	}
}