<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Worker_Model extends CI_Model
{

	public function get_data()
	{
		$this->db->select('workers.*');
		$this->db->from('workers');
		$this->db->order_by('name', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_data_with_price($price_year)
	{
		$this->db->select('workers.*');
		$this->db->select('workers_prices.id as workers_prices_id');
		$this->db->select('workers_prices.price');
		$this->db->where('workers.id = workers_prices.worker_id');
		$this->db->where('price_year', $price_year);
		$this->db->from('workers, workers_prices');
		$this->db->order_by('name', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function insert($data)
	{
		$this->db->insert('schedules_workers', $data);
		return $this->db->insert_id();
	}
}
