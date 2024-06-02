<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Passports extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->output->set_header('Access-Control-Allow-Origin: *');
		$this->output->set_header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Authorization');
		$this->output->set_content_type('application/json', 'utf-8');
		$this->load->model('api/passport_model');
	}

	public function index($id = NULL)
	{
		$data = [];

		if ($id) {
			$data['data'] = $this->passport_model->get_row($id);
		} else {
			$data['data'] = $this->passport_model->get_rows();
		}

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
