<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Logs extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		if (!$this->session->user) {
			redirect('authentication/signin');
		}

		$this->load->model('log_model');
	}

	public function index()
	{
		$data = [];
		$data['title'] = 'Логи';
		$data['content'] = 'logs/index';
		$data['page'] = 'logs/index';
		$data['page_js'] = 'logs';
		$data['datatables'] = FALSE;
		$data['title_heading'] = 'Логи';
		$data['title_heading_card'] = 'Логи';
		$logs = $this->log_model->get_data();
		foreach ($logs as $key => $value) {
			$value->data_before = json_decode($value->data_before);
			$value->data_after = json_decode($value->data_after);
			$value->created_at = date('d-m-Y H:i:s', strtotime($value->created_at));
		}
		$data['logs'] = $logs;
		// echo "<pre>";
		// print_r($data['logs']);
		// echo "</pre>";
		$this->load->view('layout', $data);
	}
}
