<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Passport_photo_Model extends CI_Model
{
	public function add_data_batch($data)
	{
		$query = $this->db->insert_batch('passport_photos', $data);
		return $query;
	}

	public function get_data_is_main_photo($passport_id)
	{
		$this->db->select('passport_photos.*');
		$this->db->from('passport_photos');
		$this->db->where('passport_photos.passport_id', $passport_id);
		$this->db->where('passport_photos.is_main_photo', 1);
		$query = $this->db->get();

		return $query->row();
	}

	public function get_data_for_passport($passport_id)
	{
		$this->db->select('passport_photos.*');
		$this->db->from('passport_photos');
		$this->db->where('passport_photos.passport_id', $passport_id);
		$this->db->order_by('passport_photos.is_main_photo', 'DESC');
		$query = $this->db->get();
		return $query->result();
	}
}
