<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class User_Model extends CI_Model
{
	public function get_rows()
	{
		return $this->db->get('users')->result();
	}

	public function get_row($id)
	{
		return $this->db->where('id', $id)->get('users')->result();
	}
}
