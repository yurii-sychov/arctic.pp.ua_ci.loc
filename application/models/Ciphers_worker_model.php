<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Ciphers_worker_Model extends CI_Model
{

	public function get_data($cipher_id, $price_year)
	{
		$this->db->select('workers.id as worker_id');
		$this->db->select('workers.name as worker');
		$this->db->select('workers.unit as unit');
		$this->db->select('ciphers_workers.quantity as quantity');
		$this->db->select('workers_prices.price as price');
		$this->db->select('ROUND((workers_prices.price * ciphers_workers.quantity), 2) as price_total');
		$this->db->where('workers.id = ciphers_workers.worker_id');
		$this->db->where('workers.id = workers_prices.worker_id');
		$this->db->where('ciphers_workers.cipher_id', $cipher_id);
		$this->db->where('workers_prices.price_year', $price_year);
		$this->db->from('ciphers_workers, workers, workers_prices');
		$this->db->order_by('worker', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_data_row($cipher_id, $worker_id)
	{
		$this->db->select('*');
		$this->db->where('cipher_id', $cipher_id);
		$this->db->where('worker_id', $worker_id);
		$this->db->from('ciphers_workers');
		$query = $this->db->get();
		return $query->row();
	}

	public function create($cipher_id, $worker_id)
	{
		$this->db->insert('ciphers_workers', ['cipher_id' => $cipher_id, 'worker_id' => $worker_id]);
	}

	public function delete($cipher_id, $worker_id)
	{
		$this->db->where('cipher_id', $cipher_id);
		$this->db->where('worker_id', $worker_id);
		$query = $this->db->delete('ciphers_workers');

		return $query;
	}

	public function change_quantity($field, $value, $cipher_id, $worker_id)
	{
		$this->db->set($field, $value === '' ? 0 : $value);
		// if ($this->session->user->group !== 'admin') {
		// 	$this->db->set('updated_by', $this->session->user->id);
		// 	$this->db->set('updated_at', date('Y-m-d H:i:s'));
		// }
		$this->db->where('cipher_id', $cipher_id);
		$this->db->where('worker_id', $worker_id);
		$query = $this->db->update('ciphers_workers');
		return $query;
	}
}
