<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Subdivisions extends Ci_Controller
{
	public $key = '81809f82e2074b59448635de2fcc121aaec62890';

	function __construct()
	{
		parent::__construct();
		$this->output->set_header('Access-Control-Allow-Origin: *');
		$this->output->set_header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS, *");
		$this->output->set_header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization, *");
		// $this->output->set_header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Authorization');
		$this->output->set_content_type('application/json', 'utf-8');
		$this->load->model('api/subdivision_model');
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

		$result = $this->subdivision_model->get_data();
		$data['data'] = $result;
		$data['total'] = count($result);
		$data['message'] = 'Ok Read!';

		$this->output->set_status_header(200);
		return $this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
	}
}
