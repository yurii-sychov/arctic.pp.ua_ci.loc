<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Voltage_class_Model extends CI_Model
{

	public function get_data()
	{
		$this->db->select('*');
		$this->db->from('voltage_class');
		$query = $this->db->get();
		return $query->result();
	}
}
