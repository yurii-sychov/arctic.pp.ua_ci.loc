<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Users extends Api_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('api/user_model');
	}

	public function index()
	{
		if (!$this->authorize()) {
			// return $this->unauthorized();
		}

		$users = $this->user_model->get_rows();
		foreach ($users as $user) {
			unset($user->password_sha1);
			unset($user->password);
			unset($user->remember_token);
		}
		echo "<pre>";
		print_r($users);
		echo "</pre>";

		if (empty($users)) {
			return $this->json_response(false, 'No data found', [], 404);
		}

		return $this->json_response(true, 'Data found', $users);
	}

	public function view($id = null)
	{
		if (!$this->authorize()) {
			return $this->unauthorized();
		}

		$id = filter_var($id, FILTER_VALIDATE_INT);
		if (!$id) {
			return $this->json_response(false, 'Incorrect or missing ID', null, 400);
		}

		$user = $this->user_model->get_row($id);

		if (!$user) {
			return $this->json_response(false, 'No data found', null, 404);
		}

		return $this->json_response(true, 'Data found', $user);
	}
}
