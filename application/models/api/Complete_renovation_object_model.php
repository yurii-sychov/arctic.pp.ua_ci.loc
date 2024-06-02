<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Complete_renovation_object_Model extends CI_Model
{

	public function get_data($offset = NULL, $limit = NULL)
	{
		$this->db->select('id');
		$this->db->select('(SELECT subdivisions.name FROM subdivisions WHERE id = complete_renovation_objects.subdivision_id) as subdivision');
		$this->db->select('name');
		$this->db->select('year_commissioning');
		$this->db->select('r3_id');
		$this->db->select('created_at');
		$this->db->select('updated_at');
		$this->db->select('(SELECT CONCAT(users.surname, " ", users.name) FROM users WHERE id = complete_renovation_objects.created_by) as created_by');
		$this->db->select('(SELECT CONCAT(users.surname, " ", users.name) FROM users WHERE id = complete_renovation_objects.updated_by) as updated_by');

		$this->db->from('complete_renovation_objects');
		$this->db->order_by('complete_renovation_objects.name', 'asc');
		if ($limit > 0) {
			$this->db->limit($limit, $offset);
		}
		$query = $this->db->get();
		return $query->result();
	}

	public function insert($data)
	{
		$this->db->insert('complete_renovation_objects', $data);
		$query = $this->db->insert_id();
		return $query;
	}

	public function update_field($field, $value, $id)
	{
		$this->db->set($field, $value);
		$this->db->where('id', $id);
		$query = $this->db->update('complete_renovation_objects');
		return $query;
	}

	public function delete($id)
	{
		$this->db->where('id', $id);
		$query = $this->db->delete('complete_renovation_objects');
		return $query;
	}
}
