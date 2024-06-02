<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Passport_property_Model extends CI_Model
{
	public function add_data_batch($data)
	{
		$query = $this->db->insert_batch('passport_properties', $data);
		return $query;
	}

	public function add_data_row($data)
	{
		$query = $this->db->insert('passport_properties', $data);
		return $query;
	}

	public function get_all_passport_properties()
	{
		$this->db->select('passport_properties.*, properties.name as property, properties.equipment_id');
		$this->db->from('passport_properties');
		$this->db->join('properties', 'properties.id = passport_properties.property_id');
		$query = $this->db->get();

		return $query->result();
	}

	public function get_data_for_passport($passport_id)
	{
		$this->db->select('passport_properties.*, properties.name as property');
		$this->db->from('passport_properties');
		$this->db->join('properties', 'properties.id = passport_properties.property_id');
		$this->db->where('passport_properties.passport_id', $passport_id);
		$this->db->order_by('properties.sort', 'ASC');
		$query = $this->db->get();

		return $query->result();
	}

	public function get_data_for_complete_renovation_object($passport_id)
	{
		$this->db->select('passport_properties.value, properties.name as property');
		$this->db->from('passport_properties');
		$this->db->join('properties', 'properties.id = passport_properties.property_id');
		$this->db->where('passport_properties.passport_id', $passport_id);
		$this->db->where('main', 1);
		$this->db->order_by('properties.sort', 'ASC');
		$query = $this->db->get();

		return $query->result();
	}

	public function edit_data_row($data, $id)
	{
		$this->db->where('id', $id);
		$query = $this->db->update('passport_properties', $data);
		return $query;
	}

	public function get_data_row($id)
	{
		$this->db->select('*');
		$this->db->where('id', $id);
		$this->db->from('passport_properties');
		$query = $this->db->get();

		return $query->row();
	}

	public function delete_row($passport_id)
	{
		$this->db->where('passport_id', $passport_id);
		$query = $this->db->delete('passport_properties');

		return $query;
	}

	public function change_value($field, $value, $id)
	{
		$this->db->set($field, $value === '' ? NULL : $value);
		if ($this->session->user->group !== 'admin') {
			$this->db->set('updated_by', $this->session->user->id);
			$this->db->set('updated_at', date('Y-m-d H:i:s'));
		}
		$this->db->where('id', $id);
		$query = $this->db->update('passport_properties');
		return $query;
	}
}
