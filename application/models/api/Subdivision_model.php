<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Subdivision_Model extends CI_Model
{

	public function get_data()
	{
		$this->db->select('id');
		$this->db->select('name as subdivision');
		$this->db->select('created_at');
		$this->db->select('updated_at');
		$this->db->select('(SELECT CONCAT(users.surname, " ", users.name) FROM users WHERE id = subdivisions.created_by) as created_by');
		$this->db->select('(SELECT CONCAT(users.surname, " ", users.name) FROM users WHERE id = subdivisions.updated_by) as updated_by');

		$this->db->from('subdivisions');
		$this->db->order_by('subdivisions.name', 'asc');
		$query = $this->db->get();
		return $query->result();
	}
}
