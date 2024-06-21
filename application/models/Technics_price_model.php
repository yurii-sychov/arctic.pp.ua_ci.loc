<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Technics_price_Model extends CI_Model
{
	public function update_field($field, $value, $id)
	{
		$this->db->set($field, $value);
		$this->db->set('updated_by', $this->session->user->id);
		$this->db->set('updated_at', date('Y-m-d H:i:s'));
		$this->db->where('id', $id);
		$query = $this->db->update('technics_prices');
		return $query;
	}

	public function insert($data)
	{
		$this->db->insert('technics_prices', $data);
		$query = $this->db->insert_id();
		return $query;
	}
}
