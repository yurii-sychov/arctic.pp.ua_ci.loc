<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Ciphers_technic_Model extends CI_Model
{

	public function get_data($cipher_id, $price_year)
	{
		$this->db->select('technics.id as technic_id');
		$this->db->select('technics.name as technic');
		$this->db->select('technics.unit as unit');
		$this->db->select('ciphers_technics.quantity as quantity');
		$this->db->select('technics_prices.price as price');
		$this->db->select('ROUND((technics_prices.price * ciphers_technics.quantity), 2) as price_total');
		$this->db->where('technics.id = ciphers_technics.technic_id');
		$this->db->where('technics.id = technics_prices.technic_id');
		$this->db->where('ciphers_technics.cipher_id', $cipher_id);
		$this->db->where('technics_prices.price_year', $price_year);
		$this->db->from('ciphers_technics, technics, technics_prices');
		$this->db->order_by('technic', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function create($cipher_id, $technic_id)
	{
		$this->db->insert('ciphers_technics', ['cipher_id' => $cipher_id, 'technic_id' => $technic_id]);
	}

	public function delete($cipher_id, $technic_id)
	{
		$this->db->where('cipher_id', $cipher_id);
		$this->db->where('technic_id', $technic_id);
		$query = $this->db->delete('ciphers_technics');

		return $query;
	}

	public function change_quantity($field, $value, $cipher_id, $technic_id)
	{
		$this->db->set($field, $value === '' ? 0 : $value);
		// if ($this->session->user->group !== 'admin') {
		// 	$this->db->set('updated_by', $this->session->user->id);
		// 	$this->db->set('updated_at', date('Y-m-d H:i:s'));
		// }
		$this->db->where('cipher_id', $cipher_id);
		$this->db->where('technic_id', $technic_id);
		$query = $this->db->update('ciphers_technics');
		return $query;
	}
}
