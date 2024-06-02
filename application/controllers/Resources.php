<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Resources extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		if (!$this->session->user) {
			redirect('authentication/signin');
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer') {
			show_404();
		}
		// $this->load->model('cipher_model');
		// $this->load->model('ciphers_material_model');
		// $this->load->model('type_service_model');
		$this->load->model('material_model');
		$this->load->model('worker_model');
		$this->load->model('technic_model');
		$this->load->model('materials_price_model');
	}

	public function index()
	{
		show_404();
		$data = [];
		$data['export_to_pdf'] = TRUE;
		$data['export_to_word'] = TRUE;
		$data['export_to_excel'] = TRUE;
		$data['title'] = 'Перелік ресурсів';
		$data['content'] = 'resources/index';
		$data['page'] = 'resources';
		$data['page_js'] = 'resources';
		$data['datatables'] = TRUE;
		$data['title_heading'] = 'Перелік ресурсів для виконання РП';
		$data['title_heading_card'] = $this->uri->segment(3) ? 'Перелік ресурсів для виконання ремонтної програми на ' . $this->uri->segment(3) . ' рік' : 'Перелік ресурсів для виконання ремонтної програми на ' . (date('Y') + 1) . ' рік';
		$price_year = $this->uri->segment(3) ? $this->uri->segment(3) : (date('Y') + 1);
		$data['materials'] = $this->material_model->get_data_with_price($price_year);
		$data['workers'] = $this->worker_model->get_data_with_price($price_year);
		$data['technics'] = $this->technic_model->get_data_with_price($price_year);
		// echo "<pre>";
		// print_r($data['materials']);
		// echo "</pre>";
		$this->load->view('layout', $data);
	}

	public function create()
	{
		redirect('/resources');
		$this->load->library('user_agent');
		$this->load->library('form_validation');

		$data = [];
		$data['title'] = 'Додавання ресурсів';
		$data['content'] = 'resources/form';
		$data['page'] = 'resources';
		$data['page_js'] = 'resources';
		$data['datatables'] = TRUE;
		$data['title_heading'] = 'Форма для додавання ресурсів';
		$data['title_heading_card'] = 'Форма для додавання ресурсів';

		$this->form_validation->set_error_delimiters('<div>', '</div>');
		$this->form_validation->set_rules('type_resource', '<strong>Тип ресурсу</strong>', 'required');
		$this->form_validation->set_rules('name', '<strong>Назва ресурсу</strong>', 'required|trim');
		$this->form_validation->set_rules('unit', '<strong>Одиниця виміру</strong>', 'required');
		$this->form_validation->set_rules('r3_id', '<strong>Номер R3</strong>', 'numeric|max_length[8]|trim');
		if ($this->form_validation->run() == FALSE) {
			$this->load->view('layout', $data);
		} else {
			if ($this->input->post('type_resource') == 1) {
				$material_id = $this->material_model->insert($this->set_data_material($this->input->post()));
				$this->materials_price_model->insert($this->set_data_material_price($material_id));
			}
			redirect('/resources');
		}
	}

	private function set_data_material($post)
	{
		$data['name'] = $post['name'];
		$data['unit'] = $post['unit'];
		$data['r3_id'] = $post['r3_id'];
		$data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');

		if ($post['type_resource'] != 1) {
			unset($data['r3_id']);
		}

		return $data;
	}

	private function set_data_material_price($material_id)
	{
		$data['price_year'] = (date('Y') + 1);
		$data['material_id'] = $material_id;
		$data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');

		return $data;
	}

	public function delete_material($id)
	{
		$this->load->library('user_agent');
		if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') {
			$this->material_model->delete($id);
			redirect($this->agent->referrer());
		} else {
			redirect($this->agent->referrer());
		}
	}

	public function edit_resource_ajax()
	{
		$this->output->set_content_type('application/json');

		$this->load->library('form_validation');

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

		$rules = ($this->input->post('field') === 'r3_id') ? 'required|numeric' : 'required';
		$this->form_validation->set_rules('value', '<strong>' . $this->input->post('field_name') . '</strong>', $rules);

		if ($this->form_validation->run() == FALSE) {
			$this->form_validation->set_error_delimiters('', '');
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => validation_errors()], JSON_UNESCAPED_UNICODE));
			return;
		}

		if ($this->input->post('table') === 'materials') {
			$this->material_model->update_field($this->input->post('field'), $this->input->post('value'), $this->input->post('id'));
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
			return;
		} else if ($this->input->post('table') === 'worker') {
			$this->worker_model->update_field($this->input->post('field'), $this->input->post('value'), $this->input->post('id'));
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
			return;
		} else if ($this->input->post('table') === 'technic') {
			$this->technic_model->update_field($this->input->post('field'), $this->input->post('value'), $this->input->post('id'));
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Fucking chert!'], JSON_UNESCAPED_UNICODE));
			return;
		}
	}

	public function edit_price_ajax()
	{
		$this->output->set_content_type('application/json');

		$this->load->library('form_validation');

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

		$this->form_validation->set_rules('value', '<strong>' . $this->input->post('field_name') . '</strong>', 'required|numeric');

		if ($this->form_validation->run() == FALSE) {
			$this->form_validation->set_error_delimiters('', '');
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => validation_errors()], JSON_UNESCAPED_UNICODE));
			return;
		}

		if ($this->input->post('table') === 'materials_prices') {
			$this->materials_price_model->update_field($this->input->post('field'), $this->input->post('value'), $this->input->post('id'));
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
			return;
		} else if ($this->input->post('table') === 'worker_prices') {
			$this->workers_price_model->update_field($this->input->post('field'), $this->input->post('value'), $this->input->post('id'));
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
			return;
		} else if ($this->input->post('table') === 'technic_prices') {
			$this->techics_price_model->update_field($this->input->post('field'), $this->input->post('value'), $this->input->post('id'));
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Fucking chert!'], JSON_UNESCAPED_UNICODE));
			return;
		}
	}
}
