<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Authentication_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	public function get_user($login, $password)
	{
		$this->db->where('login', $login);
		$this->db->where('password_sha1', sha1($password));
		$this->db->where('is_active', 1);
		$query = $this->db->get('users');
		return $query->row();
	}

	public function get_user_id($id)
	{
		$this->db->where('id', $id);
		$query = $this->db->get('users');
		return $query->row();
	}

	public function create_user($data)
	{
		$query = $this->db->insert('users', $data);
		return $query;
	}

	public function is_email($email)
	{
		$this->db->where('email', $email);
		$query = $this->db->get('users');
		return $query->row('email');
	}

	public function is_login($login)
	{
		$this->db->where('login', $login);
		$query = $this->db->get('users');
		return $query->row('login');
	}
}
