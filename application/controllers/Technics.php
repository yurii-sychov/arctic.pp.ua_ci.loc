<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Technics extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		if (!$this->session->user) {
			redirect('authentication/signin');
		}

		$this->load->model('technic_model');
	}

	public function index()
	{
		$data = [];
		$data['title'] = 'Матеріали';
		$data['content'] = 'technics/index';
		$data['page'] = 'technics/index';
		$data['page_js'] = 'technics';
		$data['datatables'] = FALSE;
		$data['title_heading'] = 'Матеріали';
		$data['title_heading_card'] = 'Матеріали';
		$data['technics'] = $this->technic_model->get_data();
		echo "<pre>";
		print_r($data['technics']);
		echo "</pre>";
		// $this->load->view('layout', $data);
	}

	public function get_technics_ajax()
	{
		$this->output->set_content_type('application/json');

		// if (!$this->input->is_ajax_request()) {
		// 	$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
		// 	return;
		// }

		$technics = $this->technic_model->get_data();

		if (!$technics) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Не вдалося отримати дані з реєстру!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'technics' => $technics], JSON_UNESCAPED_UNICODE));
	}
}
