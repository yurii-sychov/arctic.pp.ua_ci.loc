<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Complete_renovation_object_Model extends CI_Model
{

	public function get_data()
	{
		$this->db->select('*');
		$this->db->from('complete_renovation_objects');
		$this->db->order_by('name', 'asc');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_data_sp()
	{
		$this->db->select('*');
		$this->db->from('complete_renovation_objects');
		$this->db->where('subdivision_id', 1);
		$this->db->order_by('name', 'asc');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_data_for_subdivision($subdivision_id)
	{
		$this->db->select('*');
		$this->db->from('complete_renovation_objects');
		$this->db->where('subdivision_id', $subdivision_id);
		$this->db->order_by('name', 'asc');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_data_for_user()
	{
		$this->db->select('*');
		$this->db->from('complete_renovation_objects');
		$this->db->join('users_complete_renovation_objects', 'complete_renovation_objects.id = users_complete_renovation_objects.object_id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		$this->db->order_by('name', 'asc');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_data_with_subdivision_for_user($per_page, $offset)
	{
		$this->db->select('complete_renovation_objects.*, subdivisions.name as subdivision, (SELECT MAX(`created_at`) FROM operating_list_objects WHERE complete_renovation_object_id = complete_renovation_objects.id) as create_last_date, (SELECT count(*) FROM operating_list_objects WHERE complete_renovation_object_id = complete_renovation_objects.id ) as count_rows');
		$this->db->from('complete_renovation_objects, subdivisions');
		$this->db->join('users_complete_renovation_objects', 'complete_renovation_objects.id = users_complete_renovation_objects.object_id');
		$this->db->where('complete_renovation_objects.subdivision_id = subdivisions.id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		if ($this->input->get('subdivision_id')) {
			$this->db->where('complete_renovation_objects.subdivision_id', $this->input->get('subdivision_id'));
		}
		if ($this->input->get('stantion_id')) {
			$this->db->where('complete_renovation_objects.id', $this->input->get('stantion_id'));
		}
		if ($this->input->get('sort') && $this->input->get('order')) {
			$this->db->order_by($this->input->get('sort'), $this->input->get('order'));
		} else {
			$this->db->order_by('name', 'asc');
		}
		$this->db->limit($per_page, $offset);
		$query = $this->db->get();
		return $query->result();
	}

	public function get_total_complete_renovation_objects()
	{
		$this->db->select('complete_renovation_objects.*, subdivisions.name as subdivision, (SELECT MAX(`created_at`) FROM operating_list_objects WHERE complete_renovation_object_id = complete_renovation_objects.id) as create_last_date, (SELECT count(*) FROM operating_list_objects WHERE complete_renovation_object_id = complete_renovation_objects.id ) as count_rows');
		$this->db->from('complete_renovation_objects, subdivisions');
		$this->db->join('users_complete_renovation_objects', 'complete_renovation_objects.id = users_complete_renovation_objects.object_id');
		$this->db->where('complete_renovation_objects.subdivision_id = subdivisions.id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		if ($this->input->get('subdivision_id')) {
			$this->db->where('complete_renovation_objects.subdivision_id', $this->input->get('subdivision_id'));
		}
		if ($this->input->get('stantion_id')) {
			$this->db->where('complete_renovation_objects.id', $this->input->get('stantion_id'));
		}
		$this->db->get();

		return $this->db->affected_rows();
	}

	public function get_row($id)
	{
		$this->db->from('complete_renovation_objects');
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_subdivisions()
	{
		$this->db->select('subdivisions.id, subdivisions.name');
		$this->db->from('subdivisions');
		$this->db->order_by('name', 'ASC');
		$query = $this->db->get();

		return $query->result();
	}

	public function get_stantions()
	{
		$this->db->select('complete_renovation_objects.id, complete_renovation_objects.name');
		$this->db->from('complete_renovation_objects');
		$this->db->where('subdivision_id', $this->input->get('subdivision_id'));
		$this->db->order_by('name', 'ASC');
		$query = $this->db->get();

		return $query->result();
	}

	public function get_stantions_for_subdivision_and_user($subdivision_id)
	{
		$this->db->select('complete_renovation_objects.id, complete_renovation_objects.name');
		$this->db->where('subdivision_id', $subdivision_id);
		$this->db->where('complete_renovation_objects.id = users_complete_renovation_objects.object_id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);

		$this->db->from('complete_renovation_objects, users_complete_renovation_objects');
		$this->db->order_by('name', 'ASC');
		$query = $this->db->get();

		return $query->result();
	}

	public function get_stantions_for_subdivision_and_user_for_schedules($subdivision_id)
	{
		$this->db->select('complete_renovation_objects.id, complete_renovation_objects.name');
		$this->db->where('subdivision_id', $subdivision_id);
		$this->db->where('complete_renovation_objects.id = users_complete_renovation_objects.object_id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		$this->db->where('complete_renovation_objects.is_repair', 1);

		$this->db->from('complete_renovation_objects, users_complete_renovation_objects');
		$this->db->order_by('name', 'ASC');
		$query = $this->db->get();

		return $query->result();
	}

	public function change_value($field, $value, $id)
	{
		$this->db->set($field, $value === '' ? NULL : $value);
		$this->db->set('updated_by', $this->session->user->id);
		$this->db->set('updated_at', date('Y-m-d H:i:s'));
		$this->db->where('id', $id);
		$query = $this->db->update('complete_renovation_objects');
		return $query;
	}
}
