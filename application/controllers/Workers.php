<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Workers extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		if (!$this->session->user) {
			redirect('authentication/signin');
		}

		$this->load->model('worker_model');
	}

	public function index()
	{
		$data = [];
		$data['title'] = 'Матеріали';
		$data['content'] = 'workers/index';
		$data['page'] = 'workers/index';
		$data['page_js'] = 'workers';
		$data['datatables'] = FALSE;
		$data['title_heading'] = 'Матеріали';
		$data['title_heading_card'] = 'Матеріали';
		$data['workers'] = $this->worker_model->get_data();
		echo "<pre>";
		print_r($data['workers']);
		echo "</pre>";
		// $this->load->view('layout', $data);
	}

	public function get_workers_ajax()
	{
		$this->output->set_content_type('application/json');

		// if (!$this->input->is_ajax_request()) {
		// 	$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
		// 	return;
		// }

		$workers = $this->worker_model->get_data();

		if (!$workers) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Не вдалося отримати дані з реєстру!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'workers' => $workers], JSON_UNESCAPED_UNICODE));
	}
}
