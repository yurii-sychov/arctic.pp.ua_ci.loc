<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Equipment_Model extends CI_Model
{

	public function get_data()
	{
		$this->db->select('*');
		$this->db->from('equipments');
		$this->db->order_by('name', 'asc');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_data_is_menu_show()
	{
		$this->db->select('*');
		$this->db->from('equipments');
		$this->db->order_by('name', 'asc');
		$this->db->where('is_menu_show', 1);
		$query = $this->db->get();
		return $query->result();
	}

	public function get_data_row($id)
	{
		$this->db->select('*');
		$this->db->from('equipments');
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_data_for_equipment_id($equipment_id)
	{
		$this->db->select('complete_renovation_objects.name as stantion, specific_renovation_objects.name as disp, passports.id as passport_id, passports.type');
		$this->db->select('(SELECT photo from passport_photos WHERE passport_id = passports.id AND is_main_photo = 1) as photo');
		$this->db->from('specific_renovation_objects, complete_renovation_objects, passports');
		$this->db->where('specific_renovation_objects.id = passports.specific_renovation_object_id');
		$this->db->where('complete_renovation_objects.id = passports.complete_renovation_object_id');
		$this->db->where('equipment_id', $equipment_id);
		$this->db->order_by('stantion');
		$query = $this->db->get();
		return $query->result();
	}
}
