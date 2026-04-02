<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Api_Controller extends CI_Controller
{

	protected string $api_key;

	public function __construct()
	{
		parent::__construct();
		$this->api_key = (string) $this->config->item('api_key');
	}

	// Перевірка API ключа
	protected function authorize(): bool
	{
		$headers = $this->input->request_headers();

		return isset($headers['Api-Key']) &&
			hash_equals($this->api_key, $headers['Api-Key']); // захист від timing attack
	}

	// Стандартна відповідь 401
	protected function unauthorized()
	{
		return $this->json_response(false, 'Unauthorized', null, 401);
	}

	// Уніфікована JSON відповідь
	protected function json_response(bool $status, string $message, $data = null, int $statusCode = 200)
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
