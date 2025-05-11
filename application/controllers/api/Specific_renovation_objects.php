<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Specific_renovation_objects extends CI_Controller
{

	protected $api_key;

	public $key = '81809f82e2074b59448635de2fcc121aaec62890';

	public function __construct()
	{
		parent::__construct();

		$this->output->set_header('Access-Control-Allow-Origin: *');
		$this->output->set_header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS, *");
		$this->output->set_header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization, *");
		// $this->output->set_header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Authorization');
		$this->output->set_content_type('application/json', 'utf-8');
		$this->load->model('api/specific_renovation_object_model');
	}

	public function index()
	{
		$data = [];

		if ($this->input->get('key') !== $this->key) {
			$data['status'] = 'ERROR';
			$data['message'] = 'Не вірний Api Key!';
			return $this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
		}

		if ($this->input->get('id')) {
			$result = $this->specific_renovation_object_model->get_row($this->input->get('id'));
			$data['data'] = $result;
		}
		// else {
		// 	$result = $this->specific_renovation_object_model->get_rows();
		// 	$data['data'] = $result;
		// 	$data['total'] = count($result);
		// }

		return $this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
	}

	public function search($key, $text = NULL)
	{
		$data = [];

		if ($key !== hash('ripemd160', 'react-native-repair')) {
			$data['status'] = 'ERROR';
			$data['message'] = 'Не вірний Api Key!';
			return $this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
		}

		if ($text) {
			$result = $this->specific_renovation_object_model->get_search(urldecode($text));
			$data['data'] = $result;
		} else {
			$result = $this->specific_renovation_object_model->get_rows();
			$data['data'] = $result;
		}

		$data['total'] = count($result);

		return $this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
	}
}
