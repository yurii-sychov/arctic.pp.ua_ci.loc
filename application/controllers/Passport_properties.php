<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Passport_properties extends CI_Controller
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
		$this->load->model('passport_property_model');
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

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		// if ($this->input->post('field') === 'value') {
		// 	$rules = 'required';
		// }

		// $this->form_validation->set_rules('value', '<strong>' . $this->input->post('field_title') . '</strong>', $rules);

		// if ($this->form_validation->run() == FALSE) {
		// 	$this->form_validation->set_error_delimiters('', '');
		// 	$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => validation_errors()], JSON_UNESCAPED_UNICODE));
		// 	return;
		// }

		$data = $this->set_data_update_field();

		if (!$data) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Дані для змін не встановлені!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$result = $this->passport_property_model->update_field($this->input->post('id', TRUE), $data);

		if ($result) {
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
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
}
