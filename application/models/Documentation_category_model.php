<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Documentation_category_Model extends CI_Model
{
	public function get_data()
	{
		$this->db->select('*');
		$this->db->from('documentations_categories');
		$this->db->order_by('parent_id DESC');
		$query = $this->db->get();

		return $query->result();
	}
}
