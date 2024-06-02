<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Technic_Model extends CI_Model
{

	public function get_data()
	{
		$this->db->select('technics.*');
		$this->db->from('technics');
		$this->db->order_by('name', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_data_with_price($price_year)
	{
		$this->db->select('technics.*');
		$this->db->select('technics_prices.id as technics_prices_id');
		$this->db->select('technics_prices.price');
		$this->db->where('technics.id = technics_prices.technic_id');
		$this->db->where('price_year', $price_year);
		$this->db->from('technics, technics_prices');
		$this->db->order_by('name', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function insert($data)
	{
		$this->db->insert('schedules_technics', $data);
		return $this->db->insert_id();
	}
}
