<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Font, Border, Alignment};

class Realization extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		if (!$this->session->user) {
			redirect('authentication/signin');
		}

		$this->load->model('complete_renovation_object_model');
		// $this->load->model('specific_renovation_object_model');
		$this->load->model('subdivision_model');
		// $this->load->model('schedule_model');
		// $this->load->model('schedule_note_model');
		$this->load->model('schedule_year_model');
		// $this->load->model('schedule_material_model');
		// $this->load->model('schedule_worker_model');
		// $this->load->model('schedule_technic_model');
		// $this->load->model('ciphers_material_model');
		// $this->load->model('ciphers_worker_model');
		// $this->load->model('ciphers_technic_model');
		// $this->load->model('passport_model');
		// $this->load->model('worker_model');
		// $this->load->model('technic_model');
		// $this->load->model('avr_price_model');
	}

	public function index()
	{
		$data = [];
		if ($this->input->get('subdivision_id') && $this->input->get('stantion_id')) {
			$data['custom_button'][] = ['action' => 'generateScheduleExcel(event)', 'title' => 'Згенерувати графік', 'class' => 'btn-success', 'icon' => 'bi bi-file-earmark-excel'];
		}
		$data['title'] = 'Виконання плану ремонтів поточного року';
		$data['content'] = 'realization/index_dt';
		$data['page'] = 'realization';
		$data['page_js'] = 'realization';
		$data['datatables'] = TRUE;
		$data['pagination'] = FALSE;
		$data['title_heading'] = 'Виконання плану ремонтів поточного року';
		$data['title_heading_card'] = 'Річний план-графік на поточний рік';

		$subdivisions = $this->subdivision_model->get_data_for_user();
		$data['subdivisions'] = $subdivisions;
		$data['complete_renovation_objects'] = [];
		$data['equipments'] = [];

		if ($this->input->get('subdivision_id')) {
			$data['complete_renovation_objects'] = $this->complete_renovation_object_model->get_stantions_for_subdivision_and_user_for_schedules($this->input->get('subdivision_id'));
		}

		if ($this->input->get('subdivision_id') && $this->input->get('stantion_id')) {
			$data['complete_renovation_object'] = $this->complete_renovation_object_model->get_row($this->input->get('stantion_id'));
			$data['equipments'] = $this->schedule_year_model->get_data($this->input->get('stantion_id'));
		}

		$this->load->view('layout', $data);
	}

	public function edit_date_service_actual_ajax()
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

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer' && $this->session->user->group !== 'master') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$value = !$this->input->post('value') ? '0000-00-00' :  date('Y-m-d', strtotime($this->input->post('value')));

		$this->schedule_year_model->change_date_service_actual('date_service_actual', $value, $this->input->post('schedule_id'), $this->input->post('year_service'), $this->input->post('is_contract_method'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}
}
