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
		$this->db->select('*');
		$this->db->from('subdivisions');
		$this->db->order_by('name', 'asc');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_data_for_user()
	{
		$this->db->select('subdivisions.*');
		$this->db->distinct();
		$this->db->from('subdivisions, complete_renovation_objects, users_complete_renovation_objects');
		$this->db->where('subdivisions.id = complete_renovation_objects.subdivision_id');
		$this->db->where('complete_renovation_objects.id = users_complete_renovation_objects.object_id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		$this->db->order_by('subdivisions.name', 'asc');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_row($id)
	{
		$this->db->select('*');
		$this->db->where('id', $id);
		$this->db->from('subdivisions');
		$query = $this->db->get();
		return $query->row();
	}

	public function get_data_srm()
	{
		$this->db->select('*');
		$this->db->from('subdivisions');
		$this->db->where_not_in('id', 1);
		$this->db->where_not_in('id', 2);
		$this->db->where_not_in('id', 23);
		$this->db->order_by('name', 'asc');
		$query = $this->db->get();
		return $query->result();
	}

	public function update_field($id, $data)
	{
		$this->db->where('id', $id);
		$query = $this->db->update('subdivisions', $data);
		return $query;
	}
}
