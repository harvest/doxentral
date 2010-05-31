<?php

class UserModel extends Model {

	function UserModel() 
	{
		parent::Model();
	}

	function get_owned_files($_user_id, $_user_type=1) {
		$this->db->select('*');
		$this->db->from('files');
		if( $_user_type != 1 )
			$this->db->where('file_owner', $_user_id);
		$rs_files = $this->db->get();

		$own_files = array();

		if( $rs_files->num_rows()>0 ) {
			foreach( $rs_files->result() as $row ) {
				$own_files[] = $row;
			}
		}

		$rs_files->free_result();

		return $own_files;
	}

	function get_user($_user_id)
	{
		$this->db->select('*');
		$this->db->where('user_id', $_user_id);
		$this->db->from('users');

		$rs_users = $this->db->get();
		
		$user_data = array();
		
		if( $rs_users->num_rows()>0 ) {
			foreach( $rs_users->result_array() as $row ) {
				$user_data = $row;
			}
		}

		$rs_users->free_result();

		return $user_data;
	}
	
	function get_users($_status=NULL)
	{
		$this->db->select('*');
		$this->db->from('users');
		if( $_status != NULL )
			$this->db->where('user_status', $_status);
		$rs_users = $this->db->get();

		$users = array();

		if( $rs_users->num_rows()>0 ) {
			foreach( $rs_users->result_array() as $row ) {
				$users[] = $row;
			}
		}

		$rs_users->free_result();

		return $users;
	}
	
	function create_user($_insert_data) {
		$this->db->insert('users', $_insert_data);

		return $this->db->insert_id();
	}

	function update_user($_user_id, $_update_data) {
		$this->db->where('user_id', $_user_id);
		$this->db->update('users', $_update_data);

		return $this->db->affected_rows();
	}
	
	function delete_user($_user_id)
	{
		//Delete current file ACL
		$this->db->where('user_id', $_user_id);
		$this->db->where('user_type !=', 1);	//Not an admin
		$this->db->delete('users');

		return $this->db->affected_rows();
	}

	function update_user_acl($_user_id, $_new_files)
	{
		//Delete current User ACL
		$this->db->where('user_id', $_user_id);
		$this->db->delete('file_acl');

		//Insert new User ACL
		foreach($_new_files as $f) {
			if( $f>0 ):
				$insert_data = array(
							'file_id' => $f,
							'user_id' => $_user_id
						);

				$this->db->set($insert_data);
				$this->db->insert('file_acl');
			endif;
		}

		return true;
	}
	
	function delete_user_acl($_user_id)
	{
		//Delete current file ACL
		$this->db->where('user_id', $_user_id);
		$this->db->delete('file_acl');

		return $this->db->affected_rows();
	}
}