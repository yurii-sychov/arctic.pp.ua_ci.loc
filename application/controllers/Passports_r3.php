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

class Passports_r3 extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		if (!$this->session->user) {
			redirect('authentication/signin');
		}

		$this->load->model('complete_renovation_object_model');
		$this->load->model('specific_renovation_object_model');
		$this->load->model('subdivision_model');
		$this->load->model('schedule_model');
		$this->load->model('schedule_note_model');
		$this->load->model('schedule_year_model');
		$this->load->model('schedule_material_model');
		$this->load->model('schedule_worker_model');
		$this->load->model('schedule_technic_model');
		$this->load->model('ciphers_material_model');
		$this->load->model('ciphers_worker_model');
		$this->load->model('ciphers_technic_model');
		$this->load->model('passport_model');
		$this->load->model('worker_model');
		$this->load->model('technic_model');
		$this->load->model('avr_price_model');
	}

	public function index()
	{
		$this->load->library('pagination');
		$this->load->library('user_agent');

		$data = [];
		// $data['export_to_pdf'] = ($this->input->get('subdivision_id') && $this->input->get('stantion_id')) ? TRUE : FALSE;
		// $data['export_to_word'] = ($this->input->get('subdivision_id') && $this->input->get('stantion_id')) ? TRUE : FALSE;
		// $data['export_to_excel'] = ($this->input->get('subdivision_id') && $this->input->get('stantion_id')) ? TRUE : FALSE;
		// $data['custom_button'][] = ['action' => 'addSpecificRepair(event)', 'title' => 'Додати специфічний ремонт', 'class' => 'btn-info'];
		// $data['custom_button'][] = ['action' => 'genarate_program_excel(event)', 'title' => 'Програма ремонту на ' . (date('Y') + 1) . ' рік', 'class' => 'btn-outline-dark'];
		$data['title'] = 'Зв\'язування даних з R3';
		$data['content'] = 'passports_r3/index_dt';
		$data['page'] = 'passports_r3';
		$data['page_js'] = 'passports_r3';
		$data['datatables'] = TRUE;
		$data['pagination'] = FALSE;
		$data['title_heading'] = 'Зв\'язування даних з R3';
		$data['title_heading_card'] = 'Зв\'язування даних з R3';

		$subdivisions = $this->subdivision_model->get_data_for_user();
		$data['subdivisions'] = $subdivisions;
		$data['complete_renovation_objects'] = [];

		if ($this->input->get('subdivision_id')) {
			$data['complete_renovation_objects'] = $this->complete_renovation_object_model->get_stantions_for_subdivision_and_user_for_schedules($this->input->get('subdivision_id'));
		}

		if ($this->input->get('subdivision_id') && $this->input->get('stantion_id')) {
			$data['complete_renovation_object'] = $this->complete_renovation_object_model->get_row($this->input->get('stantion_id'));
		}

		if (isset($data['complete_renovation_object'])) {

			$specific_renovation_objects = $this->specific_renovation_object_model->get_data_for_complete_renovation_object($data['complete_renovation_object']->id);

			foreach ($specific_renovation_objects as $key => $value) {
				$passports = $this->passport_model->get_passports($value->id);
				$places = [];

				$value->passports = $passports;

				foreach ($passports as $k => $v) {
					if ($value->id == $v->specific_renovation_object_id) {
						if ($v->place_id == 1) {
							$place_color = 'warning';
						} elseif ($v->place_id == 2) {
							$place_color = 'success';
						} elseif ($v->place_id == 3) {
							$place_color = 'danger';
						} else {
							$place_color = 'primary';
						}
						array_push($places, [
							'passport_id' => $v->id,
							'place_name' => $v->name,
							'place_color' => $place_color,
							'type' => $v->type,
							'number' => $v->number,
							'sub_number_r3' => $v->sub_number_r3,
							'production_date' => date('Y', strtotime($v->production_date)),
						]);
						$value->places = $places;
					}
				}
			}
			$data['specific_renovation_objects'] = $specific_renovation_objects;

			// echo "<pre>";
			// print_r($specific_renovation_objects);
			// echo "</pre>";
		}

		$this->load->view('layout', $data);
	}

	public function edit_sub_number_r3_ajax()
	{
		if (!$this->session->user) {
			redirect('authentication/signin');
		}

		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$data = json_decode($this->input->raw_input_stream, TRUE);

		if (!$data) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Дані не надійшли з сервера!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$id = $data['passport_id'];
		unset($data['passport_id']);
		$data['sub_number_r3'] = (int) $data['sub_number_r3'];

		$data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');

		$this->passport_model->edit_value($data, $id);
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}
}
