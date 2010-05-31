<?php

class FileModel extends Model {
	
	function FileModel() 
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

	function get_assigned_files($_user_id) {
		$this->db->select('a.file_id, a.acl_created');
		$this->db->select('u.user_name, u.user_firstname, u.user_lastname');
		$this->db->select('f.file_name, f.file_title, f.file_desc, f.file_owner');
		$this->db->from('file_acl a');
		$this->db->join('files f', 'f.file_id = a.file_id');
		$this->db->join('users u', 'u.user_id = f.file_owner');
		$this->db->where('a.user_id', $_user_id);
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
	
	function insert_file($_insert_data) {
		$this->db->insert('files', $_insert_data);
		return $this->db->insert_id();
	}

	function update_file($_file_id, $_update_data) {
		$this->db->where('file_id', $_file_id);
		$this->db->update('files', $_update_data);

		return true;
	}
	
	function get_file($_file_id)
	{
		$this->db->select('*');
		$this->db->from('files');
		$this->db->where('file_id', $_file_id);
		$rs_file = $this->db->get();

		$file_data = array();

		if( $rs_file->num_rows()>0 ) {
			$file_data = $rs_file->result_array();
		}

		$rs_file->free_result();

		return $file_data;
	}

	function get_acl($_file_id)
	{
		$this->db->select('*');
		$this->db->from('file_acl');
		$this->db->where('file_id', $_file_id);
		$rs_acl = $this->db->get();
		
		$file_acl = array();
		
		if( $rs_acl->num_rows()>0 ) {
			foreach( $rs_acl->result_array() as $row ) {
				$file_acl[] = $row;
			}
		}
		
		$rs_acl->free_result();
		
		return $file_acl;
	}

	function get_file_acl($_file_id, $_user_id)
	{
		$this->db->select('*');
		$this->db->where('file_id', $_file_id);
		$this->db->where('user_id', $_user_id);
		
		$rs_acl = $this->db->get('file_acl');
		
		$file_acl = array();

		if( $rs_acl->num_rows()>0 ) {
			foreach( $rs_acl->result_array() as $row ) {
				$file_acl[] = $row;
			}
		}
		
		$rs_acl->free_result();

		return $file_acl;
	}
	
	function update_file_acl($_file_id, $_user_id, $_new_users)
	{
		//Delete current file ACL
		$this->db->where('file_id', $_file_id);
		//$this->db->where('user_id', $_user_id);
		$this->db->delete('file_acl');

		//Insert new file ACL
		foreach($_new_users as $u) {
			if( $u>0 ):
				$insert_data = array(
							'file_id' => $_file_id,
							'user_id' => $u
						);

				$this->db->set($insert_data);
				$this->db->insert('file_acl');
			endif;
		}
		
		//Update file status
		$this->db->where('file_id', $_file_id);
		$this->db->update('files', array('file_status'=>'1'));	//Active

		return true;
	}
	
	function delete_file_acl($_file_id)
	{
		//Delete current file ACL
		$this->db->where('file_id', $_file_id);
		$this->db->delete('file_acl');
		
		return $this->db->affected_rows();
	}

	function delete_file($_file_id)
	{
		//Delete current file ACL
		$this->db->where('file_id', $_file_id);
		$this->db->delete('files');
		
		return $this->db->affected_rows();
	}
}