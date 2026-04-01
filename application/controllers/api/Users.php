<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Users extends CI_Controller
{
	private string $api_key;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('api/user_model');
		$this->api_key = (string) $this->config->item('api_key');
	}

	public function index()
	{
		if (!$this->authorize()) {
			return $this->unauthorized();
		}

		$users = $this->user_model->get_rows();

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

	/**
	 * Перевірка API ключа
	 */
	private function authorize(): bool
	{
		$headers = $this->input->request_headers();

		return isset($headers['Api-Key']) &&
			hash_equals($this->api_key, $headers['Api-Key']); // захист від timing attack
	}

	/**
	 * Стандартна відповідь 401
	 */
	private function unauthorized()
	{
		return $this->json_response(false, 'Unauthorized', null, 401);
	}

	/**
	 * Уніфікована JSON відповідь
	 */
	private function json_response(bool $status, string $message, $data = null, int $statusCode = 200)
	{
		return $this->output
			->set_status_header($statusCode)
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode([
				'status'  => $status,
				'message' => $message,
				'data'    => $data
			], JSON_UNESCAPED_UNICODE));
	}
}
