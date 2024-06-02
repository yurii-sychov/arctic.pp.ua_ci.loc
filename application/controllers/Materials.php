<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Materials extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		if (!$this->session->user) {
			redirect('authentication/signin');
		}

		$this->load->model('material_model');
		$this->load->model('schedule_material_model');
	}

	public function index()
	{
		$data = [];
		$data['title'] = 'Матеріали';
		$data['content'] = 'materials/index';
		$data['page'] = 'materials/index';
		$data['page_js'] = 'materials';
		$data['datatables'] = FALSE;
		$data['title_heading'] = 'Матеріали';
		$data['title_heading_card'] = 'Матеріали';
		$data['materials'] = $this->material_model->get_data();

		$this->load->view('layout', $data);
	}

	public function get_materials_ajax()
	{
		$this->output->set_content_type('application/json');

		// if (!$this->input->is_ajax_request()) {
		// 	$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
		// 	return;
		// }

		$materials = $this->material_model->get_data();

		if (!$materials) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Не вдалося отримати дані з реєстру!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'materials' => $materials], JSON_UNESCAPED_UNICODE));
	}

	public function extra_materials()
	{
		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer') {
			show_404();
		}

		$data = [];
		$data['title'] = 'Матеріали';
		$data['content'] = 'materials/extra_materials_dt';
		$data['page'] = 'materials/extra_materials';
		$data['page_js'] = 'materials';
		$data['datatables'] = TRUE;
		$data['title_heading'] = 'Специфічна робота з матеріалами';
		$data['title_heading_card'] = 'Специфічна робота з матеріалами';
		$data['materials'] = $this->material_model->get_data();

		if ($this->input->get('material_id')) {
			$data['material_name'] = $this->material_model->get_row($this->input->get('material_id'));
			$data['objects'] = $this->schedule_material_model->get_materials_for_specific_add($this->input->get('material_id'));
		}

		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";

		$this->load->view('layout', $data);
	}

	public function add_extra_materials()
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

		$data_batch = [];
		for ($i = 0; $i < count($this->input->post('schedule_id')); $i++) {
			if ($this->input->post('quantity')[$i] > 0) {
				$data['schedule_id'] = $this->input->post('schedule_id')[$i];
				$data['material_id'] = $this->input->post('material_id')[$i];
				$data['quantity'] = $this->input->post('quantity')[$i];
				$data['year_service'] = (date('Y') + 1);
				$data['is_extra'] = 1;
				$data['created_by'] = $this->session->user->id;
				$data['updated_by'] = $this->session->user->id;
				$data['created_at'] = date('Y-m-d H:i:s');
				$data['updated_at'] = date('Y-m-d H:i:s');

				array_push($data_batch, $data);
			}
		}

		if ($this->schedule_material_model->insert_batch($data_batch)) {
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані записано', 'data' => $data], JSON_UNESCAPED_UNICODE));
			return;
		}
	}
}
