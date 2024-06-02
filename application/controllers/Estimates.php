<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

use phpDocumentor\Reflection\PseudoTypes\Numeric_;

defined('BASEPATH') or exit('No direct script access allowed');

class Estimates extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		if (!$this->session->user) {
			redirect('authentication/signin');
		}
		$this->load->model('cipher_model');
		$this->load->model('ciphers_material_model');
		$this->load->model('ciphers_worker_model');
		$this->load->model('ciphers_technic_model');
		$this->load->model('type_service_model');
		$this->load->model('material_model');
		$this->load->model('worker_model');
		$this->load->model('technic_model');
	}

	public function index($type_service_id = NULL, $cipher_id = NULL)
	{
		$this->session->unset_userdata('estimates_referrer');
		$data = [];
		$data['export_to_pdf'] = TRUE;
		$data['export_to_word'] = TRUE;
		$data['export_to_excel'] = TRUE;
		$data['title'] = $this->uri->segment(5) ? 'Кошториси на ' . $this->uri->segment(5) . ' рік' : 'Кошториси на ' . (date('Y') + 1) . ' рік';
		$data['content'] = 'estimates/index';
		$data['page'] = 'estimates';
		$data['page_js'] = 'estimates';
		$data['datatables'] = FALSE;
		$data['title_heading'] = 'Кошториси для виконання ремонтної програми';
		$data['title_heading_card'] = $this->uri->segment(5) ? 'Стандартні кошториси для виконання ремонтної програми на ' . $this->uri->segment(5) . ' рік' : 'Стандартні кошториси для виконання ремонтної програми на ' . (date('Y') + 1) . ' рік';

		$type_services = $this->type_service_model->get_data();
		$data['type_services'] = $type_services;

		if ($type_service_id) {
			$estimates = $this->cipher_model->get_data_for_select($type_service_id);
			$data['estimates'] = $estimates;
		}

		if ($type_service_id && $cipher_id) {
			$ciphers = $this->cipher_model->get_data($type_service_id, $cipher_id);

			$price_year = $this->uri->segment(5) ? $this->uri->segment(5) : (date('Y') + 1);

			foreach ($ciphers as $key => $row) {
				$row->materials = $this->ciphers_material_model->get_data($row->id, $price_year);

				$row->workers = $this->ciphers_worker_model->get_data($row->id, $price_year);

				$row->technics = $this->ciphers_technic_model->get_data($row->id, $price_year);
			}

			$data['ciphers'] = $ciphers;
			$this->session->set_userdata('estimates_referrer', '/estimates/index/' . $type_service_id . '/' . $cipher_id);
		}

		$this->load->view('layout', $data);
	}

	public function add_materials($id = NULL)
	{
		if (!is_numeric($id)) {
			show_404();
		}

		$cipher = $this->cipher_model->get_row($id);

		if (!$cipher) {
			show_404();
		}

		$this->load->library('user_agent');
		$this->load->library('form_validation');

		$data = [];
		$data['title'] = 'Кошториси (додавання матеріалів)';
		$data['content'] = 'estimates/create_materials';
		$data['page'] = 'estimates';
		$data['page_js'] = 'estimates';
		$data['datatables'] = FALSE;
		$data['title_heading'] = 'Кошториси (додавання матеріалів до кошторису)';
		$data['title_heading_card'] = 'Форма для додавання матеріалів до кошторису';

		$data['cipher'] = $cipher;
		$materials = $this->material_model->get_data();

		$price_year = $this->uri->segment(5) ? $this->uri->segment(5) : (date('Y') + 1);
		$ciphers_materials = $this->ciphers_material_model->get_data($cipher->id, $price_year);
		foreach ($materials as $material) {
			$material->checked = 0;
			foreach ($ciphers_materials as $ciphers_material) {
				if ($material->id == $ciphers_material->material_id) {
					$material->checked = 1;
				}
			}
		}
		$data['materials'] = $materials;

		$this->form_validation->set_rules('material_id[]', 'Матеріали', 'required');
		if ($this->form_validation->run() == FALSE) {
			$this->load->view('layout', $data);
		} else {
			foreach ($this->input->post('material_id') as $material_id) {
				$this->ciphers_material_model->create($this->input->post('cipher_id'), $material_id);
			}
			redirect($this->session->estimates_referrer);
		}
	}

	public function add_workers($id = NULL)
	{
		if (!is_numeric($id)) {
			show_404();
		}

		$cipher = $this->cipher_model->get_row($id);

		if (!$cipher) {
			show_404();
		}

		$this->load->library('user_agent');
		$this->load->library('form_validation');

		$data = [];
		$data['title'] = 'Кошториси (додавання працівників)';
		$data['content'] = 'estimates/create_workers';
		$data['page'] = 'estimates';
		$data['page_js'] = 'estimates';
		$data['datatables'] = FALSE;
		$data['title_heading'] = 'Кошториси (додавання працівників до кошторису)';
		$data['title_heading_card'] = 'Форма для додавання працівників до кошторису';

		$data['cipher'] = $cipher;
		$workers = $this->worker_model->get_data();

		$price_year = $this->uri->segment(5) ? $this->uri->segment(5) : (date('Y') + 1);
		$ciphers_workers = $this->ciphers_worker_model->get_data($cipher->id, $price_year);
		foreach ($workers as $worker) {
			$worker->checked = 0;
			foreach ($ciphers_workers as $ciphers_worker) {
				if ($worker->id == $ciphers_worker->worker_id) {
					$worker->checked = 1;
				}
			}
		}
		$data['workers'] = $workers;

		$this->form_validation->set_rules('worker_id[]', 'Працівники', 'required');
		if ($this->form_validation->run() == FALSE) {
			$this->load->view('layout', $data);
		} else {
			foreach ($this->input->post('worker_id') as $worker_id) {
				$this->ciphers_worker_model->create($this->input->post('cipher_id'), $worker_id);
			}
			redirect($this->session->estimates_referrer);
		}
	}

	public function add_technics($id = NULL)
	{
		if (!is_numeric($id)) {
			show_404();
		}

		$cipher = $this->cipher_model->get_row($id);

		if (!$cipher) {
			show_404();
		}

		$this->load->library('user_agent');
		$this->load->library('form_validation');

		$data = [];
		$data['title'] = 'Кошториси (додавання працівників)';
		$data['content'] = 'estimates/create_technics';
		$data['page'] = 'estimates';
		$data['page_js'] = 'estimates';
		$data['datatables'] = FALSE;
		$data['title_heading'] = 'Кошториси (додавання працівників до кошторису)';
		$data['title_heading_card'] = 'Форма для додавання працівників до кошторису';

		$data['cipher'] = $cipher;
		$technics = $this->technic_model->get_data();

		$price_year = $this->uri->segment(5) ? $this->uri->segment(5) : (date('Y') + 1);
		$ciphers_technics = $this->ciphers_technic_model->get_data($cipher->id, $price_year);
		foreach ($technics as $technic) {
			$technic->checked = 0;
			foreach ($ciphers_technics as $ciphers_technic) {
				if ($technic->id == $ciphers_technic->technic_id) {
					$technic->checked = 1;
				}
			}
		}
		$data['technics'] = $technics;

		$this->form_validation->set_rules('technic_id[]', 'Працівники', 'required');
		if ($this->form_validation->run() == FALSE) {
			$this->load->view('layout', $data);
		} else {
			foreach ($this->input->post('technic_id') as $technic_id) {
				$this->ciphers_technic_model->create($this->input->post('cipher_id'), $technic_id);
			}
			redirect($this->session->estimates_referrer);
		}
	}

	public function delete_material($cipher_id, $material_id)
	{
		$this->load->library('user_agent');

		if (!$cipher_id || !$material_id || !is_numeric($cipher_id) || !is_numeric($material_id)) {
			show_404();
		}

		if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') {
			$this->ciphers_material_model->delete($cipher_id, $material_id);
			redirect($this->agent->referrer());
		} else {
			redirect($this->agent->referrer());
		}
	}

	public function copy_worker($cipher_id, $worker_id)
	{
		$this->load->library('user_agent');

		if (!$cipher_id || !$worker_id || !is_numeric($cipher_id) || !is_numeric($worker_id)) {
			show_404();
		}

		if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') {
			$data_copy = $this->ciphers_worker_model->get_data_row($cipher_id, $worker_id);
			$this->ciphers_worker_model->create($data_copy->cipher_id, $data_copy->worker_id);
			redirect($this->agent->referrer());
		} else {
			redirect($this->agent->referrer());
		}
	}

	public function delete_worker($cipher_id, $worker_id)
	{
		$this->load->library('user_agent');

		if (!$cipher_id || !$worker_id || !is_numeric($cipher_id) || !is_numeric($worker_id)) {
			show_404();
		}

		if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') {
			$this->ciphers_worker_model->delete($cipher_id, $worker_id);
			redirect($this->agent->referrer());
		} else {
			redirect($this->agent->referrer());
		}
	}

	public function delete_technic($cipher_id, $technic_id)
	{
		$this->load->library('user_agent');

		if (!$cipher_id || !$technic_id || !is_numeric($cipher_id) || !is_numeric($technic_id)) {
			show_404();
		}

		if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') {
			$this->ciphers_technic_model->delete($cipher_id, $technic_id);
			redirect($this->agent->referrer());
		} else {
			redirect($this->agent->referrer());
		}
	}

	public function edit_quantity_material_ajax()
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

		$this->form_validation->set_rules('value', 'Кількість', 'required|numeric');
		$this->form_validation->set_rules('cipher_id', 'Шифр ID', 'required');
		$this->form_validation->set_rules('material_id', 'Матеріал ID', 'required');

		if ($this->form_validation->run() == FALSE) {
			$this->form_validation->set_error_delimiters('', '');
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => validation_errors()], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->ciphers_material_model->change_quantity('quantity', $this->input->post('value'), $this->input->post('cipher_id'), $this->input->post('material_id'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function edit_quantity_worker_ajax()
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

		$this->form_validation->set_rules('value', 'Кількість', 'required|numeric');
		$this->form_validation->set_rules('cipher_id', 'Шифр ID', 'required');
		$this->form_validation->set_rules('worker_id', 'Працівник ID', 'required');

		if ($this->form_validation->run() == FALSE) {
			$this->form_validation->set_error_delimiters('', '');
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => validation_errors()], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->ciphers_worker_model->change_quantity('quantity', $this->input->post('value'), $this->input->post('cipher_id'), $this->input->post('worker_id'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function edit_quantity_technic_ajax()
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

		$this->form_validation->set_rules('value', 'Кількість', 'required|numeric');
		$this->form_validation->set_rules('cipher_id', 'Шифр ID', 'required');
		$this->form_validation->set_rules('technic_id', 'Техніка ID', 'required');

		if ($this->form_validation->run() == FALSE) {
			$this->form_validation->set_error_delimiters('', '');
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => validation_errors()], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->ciphers_technic_model->change_quantity('quantity', $this->input->post('value'), $this->input->post('cipher_id'), $this->input->post('technic_id'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function generate_excel()
	{
		for ($col = 'A'; $col !== "AA"; $col++) {
			echo $col . ' ';
		}
	}
}
