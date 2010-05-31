<?php
/**
 * Files controller
 */

class Files extends Controller {
	var $upload_path = '';

	function Files()
	{
		parent::Controller();
		$this->load->library('session');

		if( !is_array($this->session->userdata('doxentral_SSO')) ) {
			redirect('/auth/');
		}

		$this->load->library('form_validation', NULL, 'validation');
		$this->upload_path = $this->config->item('upload_path', 'settings');
	}

	function upload($type=NULL)
	{
		$doxentral_SSO = $this->session->userdata('doxentral_SSO');

		if( $type == 'quick' ):
			$this->load->view('quick_upload');
		else:
			$data['file_id'] = 0;

			$config['upload_path'] = $this->upload_path;
			//$config['allowed_types'] = 'gif|jpg|xls|pdf|doc|ppt|zip|rar|gz|tar|png|tiff|psd|txt|csv';
			$config['max_size']	= '100000000';
			$config['encrypt_name'] = TRUE;

			$this->load->library('upload', $config);

			if ( !$this->upload->do_upload('user_file') )
			{
				$error = array('error' => $this->upload->display_errors());
				$this->load->view('upload_status', $error);
			}
			else
			{
				$upload_data = $this->upload->data();

				//Set file permissions to 0666
				$f_full = $this->upload_path.$upload_data['file_name'];
				@chmod($f_full, 0777);

				/* Save the file details in DB */
				$this->load->model('FileModel');
				$insert_data = array();
				$insert_data['file_name'] =  $upload_data['orig_name'];
				$insert_data['file_on_disk'] =  $upload_data['file_name'];
				//$insert_data['file_title'] =  $this->input->post('file_title')=='' ? $upload_data['orig_name'] : $this->input->post('file_title');
				//$insert_data['file_desc'] =  $this->input->post('file_desc');
				$insert_data['file_status'] =  0;	//New
				$insert_data['file_created'] = date('Y-m-d H:i:s');
				$insert_data['file_owner'] = $doxentral_SSO['user_id'];

				$data['file_id'] = $this->FileModel->insert_file($insert_data);

				$data['upload_data'] = $upload_data;
				$this->load->view('upload_status', $data);
			}
		endif;
	}

	function update($_file_id)
	{
		$update_data = array();
		$update_data['file_title'] = $this->input->post('file_title');
		$update_data['file_desc'] = $this->input->post('file_desc');

		$this->load->model('FileModel');
		$this->FileModel->update_file($_file_id, $update_data);

		echo 'success';
	}

	function download($_file_id=NULL)
	{
		$_file_id = intval($_file_id);

		if( $_file_id<=0 ) :
			$this->output->set_status_header('404', 'Requested file not found.');
		else:
			$doxentral_SSO = $this->session->userdata('doxentral_SSO');

			$this->load->model('FileModel');
			$this->load->model('UserModel');

			$file_data = $this->FileModel->get_file($_file_id);

			if( count($file_data)>0 ):
				if( $file_data[0]['file_owner'] == $doxentral_SSO['user_id'] ) {
					//Current user is the owner
					$f_name = $file_data[0]['file_name'];
					$f_disk = $file_data[0]['file_on_disk'];
					$f_full = $this->upload_path.$f_disk;

					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename="'.$f_name.'"');
					header('Content-Length: ' . filesize($f_full));
				    ob_clean();
				    flush();
					readfile($f_full);
					exit;
				} else {
					//Check if part of ACL
					$file_acl = $this->FileModel->get_file_acl($_file_id, $doxentral_SSO['user_id']);

					if( count($file_acl)>0 ):
						//Current user is the owner
						$f_name = $file_data[0]['file_name'];
						$f_disk = $file_data[0]['file_on_disk'];
						$f_full = $this->upload_path.$f_disk;

						header('Content-Description: File Transfer');
						header('Content-Type: application/octet-stream');
						header('Content-Disposition: attachment; filename="'.$f_name.'"');
						header('Content-Length: ' . filesize($f_full));
					    ob_clean();
					    flush();
						readfile($f_full);
						exit;
					else:
						$this->output->set_status_header('401', 'You are not authorised.');
					endif;
				}
			else:
				$this->output->set_status_header('404', 'Requested file not found.');
			endif;
		endif;
	}

	function acl($_file_id=NULL,$_type=NULL,$_format=NULL)
	{
		$_file_id = intval($_file_id);

		$doxentral_SSO = $this->session->userdata('doxentral_SSO');
		$data['doxentral_SSO'] = $doxentral_SSO;
		$data['data_format'] = $_format;

		if( $_file_id > 0 ):
			if( $_type == 'view' ):
				//List out file ACL
				$data['file_data'] = array();
				$data['acl_data'] = array();
				$data['user_list'] = array();
				$data['error'] = '';

				$this->load->model('FileModel');
				$this->load->model('UserModel');

				$data['file_data'] = $this->FileModel->get_file($_file_id);

				if( $data['file_data'][0]['file_owner'] == $doxentral_SSO['user_id'] ) {
					$data['acl_data'] = $this->FileModel->get_acl($_file_id);
					$data['user_list'] = $this->UserModel->get_users(1);
				} else {
					$data['error'] = 'Only owner of the file can access this screen.';
				}

				$this->load->view('file_acl', $data);
			elseif( $_type == 'update' ):
				//Update file ACL
				$new_users = explode(",", $this->input->post('user_ids'));
				$this->load->model('FileModel');
				$this->load->model('UserModel');

				$file_data = $this->FileModel->get_file($_file_id);

				if( $file_data[0]['file_owner'] == $doxentral_SSO['user_id'] ) {
					$update_status = $this->FileModel->update_file_acl($_file_id, $doxentral_SSO['user_id'], $new_users);
					if( $update_status )
						echo 'File ACL Updated successfully!';
					else
						echo 'File ACL Update failed';
				} else {
					echo 'Only owner of the file can access this screen.';
				}
			endif;

		else:
			echo('Invalid file id.');
		endif;
	}

	function user_acl($_user_id=NULL,$_type=NULL,$_format=NULL)
	{
		$_user_id = intval($_user_id);

		$doxentral_SSO = $this->session->userdata('doxentral_SSO');
		$data['doxentral_SSO'] = $doxentral_SSO;
		$data['data_format'] = $_format;

		if( $_user_id > 0 ):
			if( $_type == 'view' ):
				//List out user ACL
				$data['user_id'] = $_user_id;
				$data['user_data'] = array();
				$data['file_list'] = array();
				$data['error'] = '';

				$this->load->model('FileModel');
				$this->load->model('UserModel');

				$data['user_data'] = $this->UserModel->get_user($_user_id);

				if( $doxentral_SSO['user_type'] == 1 ) {
					$data['file_list'] = $this->FileModel->get_assigned_files($_user_id);
				} else {
					$data['error'] = 'Only Administrator can access this screen.';
				}

				$this->load->view('user_acl', $data);
			elseif( $_type == 'update' ):
				//Update file ACL
				$new_files = explode(",", $this->input->post('file_ids'));
				$this->load->model('FileModel');
				$this->load->model('UserModel');

				if( $doxentral_SSO['user_type'] == 1 ) {
					$update_status = $this->UserModel->update_user_acl($_user_id, $new_files);
					if( $update_status )
						echo 'Updated successfully!';
					else
						echo 'Update failed';
				} else {
					echo 'Only Administrator can access this screen.';
				}
			endif;

		else:
			echo('Invalid User Id.');
		endif;
	}

	function delete($_file_id)
	{
		$data = array();
		$data['head'] = 0;
		$data['msg'] = 'File deletion failed!';
		$this->output->set_header('Content-type: text/x-json');

		$doxentral_SSO = $this->session->userdata('doxentral_SSO');

		$this->load->model('FileModel');
		$file_data = $this->FileModel->get_file($_file_id);

		//Verify Owner
		if( $file_data['file_owner'] == $doxentral_SSO['user_id'] || $doxentral_SSO['user_type'] == 1 ):
			//Delete ACL
			$this->FileModel->delete_file_acl($_file_id);

			//Delete Master Entry
			$this->FileModel->delete_file($_file_id);

			//Delete file on disk
			$f_full = $this->upload_path.$file_data['file_on_disk'];
			@unlink($f_full);

			$data['head'] = 1;
			$data['msg'] = 'File and its associations have been deleted.';
		else:
			$data['msg'] = 'File deletion failed! Only Owner of the file Or an Administrator can delete a file.';
		endif;//Verify Owner

		$this->load->view('file_delete_status', $data);
	}
}