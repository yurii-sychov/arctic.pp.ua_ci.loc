<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Insulation_types extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		if (!$this->session->user) {
			redirect('authentication/signin');
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer' && $this->session->user->group !== 'master' && $this->session->user->group !== 'user' && $this->session->user->group !== 'head') {
			show_404();
		}

		$this->load->model('insulation_type_model');
	}

	public function get_data_ajax()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$results = $this->insulation_type_model->get_data();

		if ($results) {
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'OK!', 'data' => $results], JSON_UNESCAPED_UNICODE));
		}
	}
}
