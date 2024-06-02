<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Cipher_Model extends CI_Model
{

	public function get_data_for_select($type_service_id)
	{
		$this->db->select('ciphers.*');
		if ($type_service_id) {
			$this->db->where('type_service_id', $type_service_id);
		}
		$this->db->from('ciphers');
		$this->db->order_by('type_service_id', 'ASC');
		$this->db->order_by('cipher', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_data($type_service_id = NULL, $id = NULL)
	{
		$this->db->select('ciphers.*');
		if ($type_service_id) {
			$this->db->where('type_service_id', $type_service_id);
		}
		if ($id) {
			$this->db->where('id', $id);
		}
		$this->db->from('ciphers');
		$this->db->order_by('type_service_id', 'ASC');
		$this->db->order_by('cipher', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_row($id)
	{
		$this->db->select('ciphers.*');
		$this->db->where('id', $id);
		$this->db->from('ciphers');
		$query = $this->db->get();
		return $query->row();
	}
}
