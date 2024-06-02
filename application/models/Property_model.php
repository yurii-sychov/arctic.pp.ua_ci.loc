<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Property_Model extends CI_Model
{

	public function get_data_equipment($equipment_id)
	{
		$this->db->select('*');
		$this->db->where('equipment_id', $equipment_id);
		$this->db->order_by('sort', 'ASC');
		$this->db->from('properties');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_all_properties()
	{
		$this->db->select('properties.id, properties.equipment_id, properties.name, properties.sort');
		$this->db->from('properties');
		$query = $this->db->get();

		return $query->result();
	}

	public function get_data_for_equipment($equipment_id)
	{
		$this->db->select('properties.id, properties.equipment_id, properties.name, properties.sort');
		$this->db->where('properties.equipment_id', $equipment_id);
		$this->db->from('properties');
		$query = $this->db->get();

		return $query->result();
	}
}
