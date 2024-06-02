<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Material_Model extends CI_Model
{

	public function get_data()
	{
		$this->db->select('materials.*');
		$this->db->from('materials');
		$this->db->order_by('name', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function insert($data)
	{
		$this->db->insert('materials', $data);
		$query = $this->db->insert_id();
		return $query;
	}

	public function get_data_with_price($price_year)
	{
		$this->db->select('materials.*');
		// $this->db->select('materials_prices.id as materials_prices_id');
		// $this->db->select('materials_prices.price');
		// $this->db->where('materials.id = materials_prices.material_id');
		// $this->db->where('price_year', $price_year);
		$this->db->from('materials');
		// $this->db->from('materials_prices');
		// $this->db->order_by('name', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function update_field($field, $value, $id)
	{
		$this->db->set($field, $value);
		$this->db->set('updated_by', $this->session->user->id);
		$this->db->set('updated_at', date('Y-m-d H:i:s'));
		$this->db->where('id', $id);
		$query = $this->db->update('materials');
		return $query;
	}

	public function delete($id)
	{
		$this->db->where('id', $id);
		$query = $this->db->delete('materials');
		return $query;
	}

	public function get_row($id)
	{
		$this->db->where('id', $id);
		$this->db->from('materials');
		$query = $this->db->get();
		return $query->row();
	}
}
