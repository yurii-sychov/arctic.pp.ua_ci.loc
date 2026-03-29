<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Passports extends CI_Controller
{

	public $key = '81809f82e2074b59448635de2fcc121aaec62890';

	public function __construct()
	{
		parent::__construct();
		// $this->output->set_header('Access-Control-Allow-Origin: *');
		// $this->output->set_header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS, *");
		// $this->output->set_header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization, *");
		// $this->output->set_header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Authorization');
		// $this->output->set_content_type('application/json', 'utf-8');
		$this->load->model('api/passport_model');
	}

	public function index()
	{
		$apiKey = $this->input->get('key', true);
		$id   = $this->input->get('id', true);

		// Перевірка API ключа
		if ($apiKey !== $this->key) {
			return $this->json_response(
				['status' => 'ERROR', 'message' => 'Невірний Api Key'],
				403
			);
		}

		// Отримання даних
		$data = $this->passport_model->get_rows();

		if (!$data) {
			return $this->json_response(
				['status' => 'ERROR', 'message' => 'Дані не знайдено'],
				404
			);
		}

		return $this->json_response([
			'status' => 'SUCCESS',
			'data'  => $data,
			'message' => 'Дані знайдено'
		]);
	}

	public function view()
	{
		$apiKey = $this->input->get('key', true);
		$id   = $this->input->get('id', true);

		// Перевірка API ключа
		if ($apiKey !== $this->key) {
			return $this->json_response(
				['status' => 'ERROR', 'message' => 'Невірний Api Key'],
				403
			);
		}

		// Валідація ID
		$id = filter_var($id, FILTER_VALIDATE_INT);
		if (!$id) {
			return $this->json_response(
				['status' => 'ERROR', 'message' => 'Некоректний ID'],
				400
			);
		}

		// Отримання даних
		$data = $this->passport_model->get_row($id);

		if (!$data) {
			return $this->json_response(
				['status' => 'ERROR', 'message' => 'Дані не знайдено'],
				404
			);
		}

		return $this->json_response([
			'status' => 'SUCCESS',
			'data'  => $data,
			'message' => 'Дані знайдено'
		]);
	}

	/**
	 * Уніфікована відповідь API
	 */
	private function json_response(array $data, int $statusCode = 200)
	{
		return $this->output
			->set_status_header($statusCode)
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
	}

	public function index_olds()
	{
		$response = [
			'status' => 'SUCCESS',
			'data' => null,
			'message' => 'Дані знайдено'
		];

		$apiKey = $this->input->get('key', true);
		$id = $this->input->get('id', true);

		// Перевірка API ключа
		if ($apiKey !== $this->key) {
			$response['status'] = 'ERROR';
			$response['message'] = 'Невірний Api Key';

			return $this->output
				->set_status_header(403)
				->set_content_type('application/json', 'utf-8')
				->set_output(json_encode($response, JSON_UNESCAPED_UNICODE));
		}

		// Отримання даних
		if (!empty($id)) {
			$data = $this->passport_model->get_row((int)$id);

			if ($data) {
				$response['data'] = $data;
			} else {
				$response['status'] = 'ERROR';
				$response['message'] = 'Дані не знайдено';
			}
		} else {
			// Якщо потрібно — можна увімкнути отримання всіх записів
			// $response['data'] = $this->passport_model->get_rows();

			$response['message'] = 'Не передано ID';
		}

		return $this->output
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($response, JSON_UNESCAPED_UNICODE));
	}

	public function index_old()
	{
		$data = [];

		if ($this->input->get('key') !== $this->key) {
			$data['status'] = 'ERROR';
			$data['message'] = 'Не вірний Api Key!';

			$this->output->set_status_header(403);
			return $this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
		}

		if ($this->input->get('id')) {
			$data['data'] = $this->passport_model->get_row($this->input->get('id'));
		}
		// else {
		// 	$data['data'] = $this->passport_model->get_rows();
		// }

		$this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
	}

	public function search($text = NULL)
	{
		$data = [];

		if ($text) {
			$data['data'] = $this->passport_model->get_search(urldecode($text));
		} else {
			$data['data'] = $this->passport_model->get_rows();
		}

		$this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
	}
}
