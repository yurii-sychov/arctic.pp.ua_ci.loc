<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Log_Model extends CI_Model
{
	public function insert_data($data)
	{
		$this->db->insert('logs', $data);
	}

	public function get_data()
	{
		$this->db->select('logs.*, users.surname, users.name, users.patronymic');
		$this->db->from('logs');
		$this->db->join('users', 'users.id = logs.user_id');
		$this->db->order_by('created_at', 'DESC');
		$this->db->limit(200);
		$query = $this->db->get();

		return $query->result();
	}
}
