<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Passport_Model extends CI_Model
{

	public function get_rows()
	{
		$this->db->select('passports.*, complete_renovation_objects.name as complete_renovation_object, specific_renovation_objects.name as 	specific_renovation_object, places.name as place');
		$this->db->from('passports, complete_renovation_objects, specific_renovation_objects, places');
		$this->db->where('passports.complete_renovation_object_id=complete_renovation_objects.id');
		$this->db->where('passports.specific_renovation_object_id=specific_renovation_objects.id');
		$this->db->where('passports.place_id=places.id');
		$this->db->order_by('passports.id', 'ASC');
		// $this->db->limit(10);
		$query = $this->db->get();
		return $query->result();
	}

	public function get_row($id)
	{
		$this->db->select('passports.*, complete_renovation_objects.name as complete_renovation_object, specific_renovation_objects.name as 	specific_renovation_object, places.name as place');
		$this->db->from('passports, complete_renovation_objects, specific_renovation_objects, places');
		$this->db->where('passports.complete_renovation_object_id=complete_renovation_objects.id');
		$this->db->where('passports.specific_renovation_object_id=specific_renovation_objects.id');
		$this->db->where('passports.id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_search($text)
	{
		$this->db->select('passports.*, complete_renovation_objects.name as complete_renovation_object, specific_renovation_objects.name as 	specific_renovation_object, places.name as place');
		$this->db->from('passports, complete_renovation_objects, specific_renovation_objects, places');
		$this->db->where('passports.complete_renovation_object_id=complete_renovation_objects.id');
		$this->db->where('passports.specific_renovation_object_id=specific_renovation_objects.id');
		$this->db->where('passports.place_id=places.id');
		$this->db->like('specific_renovation_objects.name', $text);
		$this->db->order_by('passports.id', 'ASC');
		// $this->db->limit(10);
		$query = $this->db->get();
		return $query->result();
	}
}
