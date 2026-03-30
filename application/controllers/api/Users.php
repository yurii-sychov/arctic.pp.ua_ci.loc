<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Users extends CI_Controller
{
	private $api_key;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('api/user_model');
		// Отримуємо ключ з config
		$this->api_key = $this->config->item('api_key');
	}
	public function index()
	{
		// Отримуємо заголовки
		$headers = $this->input->request_headers();

		// Перевірка API ключа
		if (!isset($headers['Api-Key']) || $headers['Api-Key'] !== $this->api_key) {
			return $this->output
				->set_status_header(401)
				->set_content_type('application/json')
				->set_output(json_encode([
					'status' => false,
					'message' => 'Unauthorized'
				]));
		}

		// Отримання користувачів
		$users = $this->user_model->get_rows();

		return $this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'status' => true,
				'data' => $users
			]));
	}

	public function user($id = NULL)
	{
		// Отримуємо заголовки
		$headers = $this->input->request_headers();

		// Перевірка API ключа
		if (!isset($headers['Api-Key']) || $headers['Api-Key'] !== $this->api_key) {
			return $this->output
				->set_status_header(401)
				->set_content_type('application/json')
				->set_output(json_encode([
					'status' => false,
					'message' => 'Unauthorized'
				]));
		}

		// Валідація ID
		$id = filter_var($id, FILTER_VALIDATE_INT);
		if (!$id) {
			return $this->output
				->set_status_header(401)
				->set_content_type('application/json')
				->set_output(json_encode([
					'status' => false,
					'message' => 'Некоректний ID'
				]));
		}

		// Отримання користувачів
		$user = $this->user_model->get_row($id);

		return $this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'status' => true,
				'data' => $user
			]));
	}

	private function json_response(array $data, int $statusCode = 200)
	{
		return $this->output
			->set_status_header($statusCode)
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
	}
}
