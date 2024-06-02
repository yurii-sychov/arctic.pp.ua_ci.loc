<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Avr_price_Model extends CI_Model
{
	public function insert($data)
	{
		$this->db->insert('avr_price', $data);
	}

	public function delete_for_schedule_avr_price($year_service)
	{
		$this->db->where('year_service', $year_service);
		$query = $this->db->delete('avr_price');
		return $query;
	}

	public function get_avr_price_for_year($year_service)
	{
		$this->db->select('avr_price.*');
		$this->db->where('avr_price.year_service', $year_service);
		$this->db->from('avr_price');
		$query = $this->db->get();
		return $query->row();
	}
}
