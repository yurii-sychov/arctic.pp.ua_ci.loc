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
		$this->output->set_header('Access-Control-Allow-Origin: *');
		$this->output->set_header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS, *");
		$this->output->set_header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization, *");
		// $this->output->set_header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Authorization');
		$this->output->set_content_type('application/json', 'utf-8');
		$this->load->model('api/passport_model');
	}

	public function index()
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
