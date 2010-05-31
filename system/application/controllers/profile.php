<?php
/**
 * Profile controller
 */
class Profile extends Controller {

	function Profile()
	{
		parent::Controller();

		$this->load->library('session');

		if( !is_array($this->session->userdata('doxentral_SSO')) ) {
			redirect('/auth/');
		}
	}

	function index()
	{
		$doxentral_SSO = $this->session->userdata('doxentral_SSO');
		$_user_id = $doxentral_SSO['user_id'];

		$this->load->model('UserModel');
		$data['user_data'] = $this->UserModel->get_user($_user_id);

		$this->load->view('user_profile', $data);
	}

	function update($_user_id=NULL)
	{
		$doxentral_SSO = $this->session->userdata('doxentral_SSO');

		$_user_id = intval($_user_id);
		$_is_admin = 0;
		if( $_user_id<=0 ){
			$_user_id = intval($doxentral_SSO['user_id']);

			if( $_user_id<=0 )
				die('Invalid user session.');
		}elseif( $doxentral_SSO['user_type'] != 1 ){
			die('You are not authorized.');
		}else{
			$_is_admin = 1;
		}


		$this->load->library('form_validation', NULL, 'validation');

		$v_rules = array();
		$v_rules[] = array(
						'field' => 'user_password',
						'label' => 'Current Password',
						'rules' => 'trim|min_length[4]|xss_clean'
					);
		$v_rules[] = array(
						'field' => 'user_password_new',
						'label' => 'New Password',
						'rules' => 'trim|min_length[4]|xss_clean'
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

		if( $_is_admin == 1 ){
			$v_rules[] = array(
							'field' => 'user_type',
							'label' => 'User Type',
							'rules' => 'trim|required|numeric|xss_clean'
						);
		}

		$this->validation->set_rules($v_rules);

		$data['error'] = '';
		$data['update_status'] = '';
		if( $this->validation->run() == FALSE ) {
			//Validation failed
			$data['error'] = 'Update failed.';
		} else {
			$this->load->model('UserModel');
			$user_data = $this->UserModel->get_user($_user_id);

			if( count($user_data)>0 ):
				$update_data = array();
				$user_firstname = $this->input->post('user_firstname');
				$user_lastname = $this->input->post('user_lastname');
				$user_password = $this->input->post('user_password');
				$user_password_new = $this->input->post('user_password_new');
				$user_email = $this->input->post('user_email');
				if( $_is_admin == 1 ){
					$user_type = $this->input->post('user_type');
				}

				$pass_change = 0;
				//User wants a password change?
				if( $user_password_new != '' ):
					if( $user_password==$user_data['user_password'] ) {
						if( $user_password_new != $user_data['user_password'] ) {
							$pass_change = 1;
							$update_data['user_password'] = $user_password_new;
						}
					} else {
						$data['error'] = 'Incorrect Current Password.';
						$pass_change = -1; //Opted for password change but validation failed
					}
				endif; //Pass validation

				if( $pass_change >= 0 ):
					if( $user_firstname != $user_data['user_firstname'] )
						$update_data['user_firstname'] = $user_firstname;
					if( $user_lastname != $user_data['user_lastname'] )
						$update_data['user_lastname'] = $user_lastname;
					if( $user_email != $user_data['user_email'] )
						$update_data['user_email'] = $user_email;

					if( $_is_admin == 1 ){
						if( $user_type != $user_data['user_type'] )
							$update_data['user_type'] = $user_type;
					}

					if( count($update_data)>0 ) {
						$this->UserModel->update_user($_user_id, $update_data);

						$data['update_status'] = 'Changes have been saved successfully.';
					} else {
						$data['error'] = 'No changes to save!';
					}
				endif;
			else:
				$data['error'] = 'Error retreiving user profile.';
			endif;
		}

		$this->load->view('update_user_status', $data);
	}
}