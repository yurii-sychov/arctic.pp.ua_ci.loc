<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class News_Model extends CI_Model
{

	public function get_data()
	{
		$this->db->select('news.*');
		$this->db->select('DATE_FORMAT(news.created_at, "%d-%m-%Y %H-%i-%s") as date_created');
		$this->db->from('news');
		$this->db->order_by('created_at', 'DESC');
		$query = $this->db->get();
		return $query->result();
	}
}
