<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Ciphers_material_Model extends CI_Model
{

	public function get_data($cipher_id, $price_year)
	{
		$this->db->select('materials.id as material_id');
		$this->db->select('materials.name as material');
		$this->db->select('materials.unit as unit');
		$this->db->select('ciphers_materials.quantity as quantity');
		$this->db->select('materials.r3_id as number_r3');
		$this->db->select('materials_prices.price as price');
		$this->db->select('ROUND((materials_prices.price * ciphers_materials.quantity), 2) as price_total');
		$this->db->where('materials.id = ciphers_materials.material_id');
		$this->db->where('materials.id = materials_prices.material_id');
		$this->db->where('ciphers_materials.cipher_id', $cipher_id);
		$this->db->where('materials_prices.price_year', $price_year);
		$this->db->from('ciphers_materials, materials, materials_prices');
		$this->db->order_by('material', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function create($cipher_id, $material_id)
	{
		$this->db->insert('ciphers_materials', ['cipher_id' => $cipher_id, 'material_id' => $material_id]);
	}

	public function delete($cipher_id, $material_id)
	{
		$this->db->where('cipher_id', $cipher_id);
		$this->db->where('material_id', $material_id);
		$query = $this->db->delete('ciphers_materials');

		return $query;
	}

	public function change_quantity($field, $value, $cipher_id, $material_id)
	{
		$this->db->set($field, $value === '' ? 0 : $value);
		// if ($this->session->user->group !== 'admin') {
		// 	$this->db->set('updated_by', $this->session->user->id);
		// 	$this->db->set('updated_at', date('Y-m-d H:i:s'));
		// }
		$this->db->where('cipher_id', $cipher_id);
		$this->db->where('material_id', $material_id);
		$query = $this->db->update('ciphers_materials');
		return $query;
	}

	public function get_material_ids()
	{
		$this->db->distinct();
		$this->db->select('ciphers_materials.material_id as material_id');
		$this->db->from('ciphers_materials');
		$query = $this->db->get();
		return $query->result();
	}
}
