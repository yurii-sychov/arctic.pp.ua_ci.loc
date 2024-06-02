<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class User_Model extends CI_Model
{

	public function get_data()
	{
		$this->db->select('*');
		$this->db->from('users');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_user($id)
	{
		$this->db->select('*');
		$this->db->where('id', $id);
		$this->db->from('users');
		$query = $this->db->get();
		return $query->row();
	}

	public function update_data($data, $id)
	{
		$this->db->where('id', $id);
		$query = $this->db->update('users', $data);
		return $query;
	}
}
