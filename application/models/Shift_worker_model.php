<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Shift_worker_Model extends CI_Model
{
	public function get_data()
	{
		$this->db->select('shift_workers.*');
		$this->db->where('shift_workers.id = shift_workers_users.shift_worker_id');
		$this->db->where('shift_workers_users.user_id', $this->session->user->id);
		$this->db->from('shift_workers, shift_workers_users');
		$query = $this->db->get();
		return $query->result();
	}
}
