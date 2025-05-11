<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\JsonFormatter;

class Subdivisions extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		if (!$this->session->user) {
			redirect('authentication/signin');
		}

		if (!$this->session->user->group) {
			show_404();
		}

		$this->load->model('subdivision_model');
		$this->load->model('user_model');

		$this->monolog();
	}

	public function index()
	{
		$data = [];
		$data['title'] = 'Підрозділи';
		$data['content'] = 'subdivisions/index';
		$data['page'] = 'subdivisions';
		$data['page_js'] = 'subdivisions';
		$data['title_heading'] = 'Підрозділи';
		$data['title_heading_card'] = 'Підрозділи';
		$data['datatables'] = TRUE;
		$data['forms'] = FALSE;

		$subdivisions = $this->subdivision_model->get_data();
		$users = $this->user_model->get_data();

		foreach ($subdivisions as $key => $subdivision) {
			foreach ($users as $user) {
				if ($subdivision->created_by == $user->id) {
					$subdivisions[$key]->created_by = $user->name . ' ' . $user->surname;
				}
				if ($subdivision->updated_by == $user->id) {
					$subdivisions[$key]->updated_by = $user->name . ' ' . $user->surname;
				}
			}
		}

		$data['results'] = $subdivisions;
		$this->load->view('layout_lte', $data);
	}

	public function create()
	{
		$data = [];
		$data['title'] = 'Створення підрозділу';
		$data['content'] = 'subdivisions/form';
		$data['page'] = 'subdivisions';
		$data['page_js'] = 'subdivisions';
		$data['title_heading'] = 'Створення підрозділу';
		$data['title_heading_card'] = 'Форма створення підрозділу';
		$data['datatables'] = FALSE;
		$data['forms'] = TRUE;

		$this->load->view('layout_lte', $data);
	}

	public function update_field_ajax()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if ($this->session->user->group !== 'admin') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		if ($this->input->post('field') === 'name') {
			$rules = 'required';
		}
		if ($this->input->post('field') === 'founding_year') {
			$rules = 'numeric|exact_length[4]|greater_than_equal_to[1901]|less_than_equal_to[2155]';
		}
		if ($this->input->post('field') === 'sort') {
			$rules = 'required|numeric';
		}

		$this->form_validation->set_rules('value', '<strong>' . $this->input->post('field_title') . '</strong>', $rules);

		if ($this->form_validation->run() == FALSE) {
			$this->form_validation->set_error_delimiters('', '');
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => validation_errors()], JSON_UNESCAPED_UNICODE));
			return;
		}

		$data = $this->set_data_update_field();

		if (!$data) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Дані для змін не встановлені!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$result = $this->subdivision_model->update_field($this->input->post('id', TRUE), $data);

		if ($result) {
			$data['id'] = $this->input->post('id', TRUE);
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!', 'data' => $data], JSON_UNESCAPED_UNICODE));
			return;
		}
	}

	private function set_data_update_field()
	{
		$set_data[$this->input->post('field', TRUE)] = $this->input->post('value', TRUE);
		$set_data['updated_by'] = $this->session->user->id;
		$set_data['updated_at'] = date('Y-m-d H:i:s');

		return $set_data;
	}

	public function get_subdivisions_ajax()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_status_header(400);
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}


		if (!$this->input->get()) {
			$this->output->set_status_header(400);
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не GET запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$data = $this->subdivision_model->get_data();

		if (!$data) {
			$this->output->set_status_header(400);
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Не вдалося отримати дані з реєстру!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'subdivisions' => $data], JSON_UNESCAPED_UNICODE));
	}

	private function monolog()
	{
		if (phpversion() > 8) {
			$log = new Logger('log');
			$log->pushHandler(new StreamHandler('uploads/logs/logs.log', Level::Debug));
			$log->info('Перегляд сторінки: ' . current_url() . ' - Користувач: ' . $this->session->user->login);
		}
	}
}
