<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Users_complete_renovation_object_Model extends CI_Model
{

	public function get_data()
	{
		$this->db->select('users_complete_renovation_objects.*, complete_renovation_objects.name as stantion');
		$this->db->from('users_complete_renovation_objects');
		$this->db->join('complete_renovation_objects', 'users_complete_renovation_objects.object_id = complete_renovation_objects.id');
		$query = $this->db->get();
		return $query->result();
	}
}
