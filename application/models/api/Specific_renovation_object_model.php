<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Specific_renovation_object_Model extends CI_Model
{

	public function get_rows()
	{
		$this->db->select('specific_renovation_objects.*, complete_renovation_objects.name as complete_renovation_object, equipments.name as equipment, voltage_class.voltage');
		$this->db->from('specific_renovation_objects, complete_renovation_objects, equipments, voltage_class');
		$this->db->where('specific_renovation_objects.complete_renovation_object_id=complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id=equipments.id');
		$this->db->where('specific_renovation_objects.voltage_class_id=voltage_class.id');
		$this->db->order_by('specific_renovation_objects.id', 'ASC');
		// $this->db->limit(10);
		$query = $this->db->get();
		return $query->result();
	}

	public function get_row($id)
	{
		$this->db->select('specific_renovation_objects.*, complete_renovation_objects.name as complete_renovation_object, equipments.name as equipment, voltage_class.voltage');
		$this->db->from('specific_renovation_objects, complete_renovation_objects, equipments, voltage_class');
		$this->db->where('specific_renovation_objects.complete_renovation_object_id=complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id=equipments.id');
		$this->db->where('specific_renovation_objects.voltage_class_id=voltage_class.id');
		$this->db->order_by('specific_renovation_objects.id', 'ASC');
		$this->db->where('specific_renovation_objects.id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_search($text)
	{
		$this->db->select('specific_renovation_objects.*, complete_renovation_objects.name as complete_renovation_object, equipments.name as equipment, voltage_class.voltage');
		$this->db->from('specific_renovation_objects, complete_renovation_objects, equipments, voltage_class');
		$this->db->where('specific_renovation_objects.complete_renovation_object_id=complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id=equipments.id');
		$this->db->where('specific_renovation_objects.voltage_class_id=voltage_class.id');
		$this->db->like('specific_renovation_objects.name', $text);
		$this->db->order_by('specific_renovation_objects.id', 'ASC');
		// $this->db->limit(10);
		$query = $this->db->get();
		return $query->result();
	}
}
