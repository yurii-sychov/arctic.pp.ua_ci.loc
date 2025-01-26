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

use function PHPSTORM_META\type;

class MYPDF extends TCPDF
{
	public function Header()
	{
		if ($this->page === 1) {
			$style = array(
				'border' => false,
				'padding' => 0,
				'fgcolor' => array(47, 79, 79),
				'bgcolor' => false
			);
			$image_file = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/logo.png';
			$this->Image($image_file, 16, 10, 20, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
			$this->write2DBarcode('Документ сгенеровано ' . date('d-m-Y року о H:i:s'), 'QRCODE,H', 375, 10, 29, 29, $style, 'N');
			// $this->write2DBarcode('Документ сгенеровано ' . date('d-m-Y року о H:i:s'), 'PDF417', 231, 10, 50, 50, $style, 'N');
			$this->SetFont('dejavusans', 'B', 20, '', true);
			// $this->Cell(0, 15, 'ПрАТ "Кіровоградобленерго"', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		}
	}
}

class Passports extends CI_Controller
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
		$this->load->model('subdivision_model');
		$this->load->model('complete_renovation_object_model');
		$this->load->model('specific_renovation_object_model');
		$this->load->model('passport_model');
		$this->load->model('operating_list_model');
		$this->load->model('schedule_model');
		$this->load->model('equipment_model');
		$this->load->model('voltage_class_model');
		$this->load->model('insulation_type_model');
		$this->load->model('place_model');
		$this->load->model('type_service_model');
		$this->load->model('property_model');
		$this->load->model('passport_property_model');
		$this->load->model('passport_photo_model');
		$this->load->model('log_model');
		$this->load->model('user_model');
	}

	public function index($subdivision_id = NULL, $complete_renovation_object_id = NULL, $equipment_id = NULL)
	{
		if ($subdivision_id && !is_numeric($subdivision_id)) {
			show_404();
		}

		if ($complete_renovation_object_id && !is_numeric($complete_renovation_object_id)) {
			show_404();
		}

		if ($equipment_id && !is_numeric($equipment_id)) {
			show_404();
		}

		$data = [];
		$data['title'] = 'Паспорти';
		$data['content'] = 'passports/index';
		$data['page'] = 'passports';
		$data['page_js'] = 'passports';
		$data['title_heading'] = 'Паспорти';
		$data['title_heading_card'] = 'Паспорти';
		$data['datatables'] = TRUE;
		$data['forms'] = TRUE;

		$subdivisions = $this->subdivision_model->get_data();
		$sort  = array_column($subdivisions, 'sort');
		array_multisort($sort, SORT_ASC, $subdivisions);

		$data['complete_renovation_objects'] = [];
		$data['equipments'] = [];
		$data['subdivisions'] = $subdivisions;

		if ($subdivision_id) {
			$complete_renovation_objects = $this->complete_renovation_object_model->get_data_for_subdivision($subdivision_id);
			$data['complete_renovation_objects'] = $complete_renovation_objects;
		}

		if ($subdivision_id && $complete_renovation_object_id) {
			$passports = $this->passport_model->get_data_for_specific_renovation_object($subdivision_id, $complete_renovation_object_id);
			$complete_renovation_object = $this->complete_renovation_object_model->get_row($complete_renovation_object_id);
			$data['title'] = 'Паспорти ' . str_replace("/", "_", $complete_renovation_object->name);

			// $specific_renovation_objects = $this->specific_renovation_object_model->get_all_for_complete_renovation_object($subdivision_id, $complete_renovation_object_id);
			$places = $this->place_model->get_data();
			$data['places'] = $places;

			$insulation_types = $this->insulation_type_model->get_data();
			$users = $this->user_model->get_data();
			foreach ($passports as $key => $passport) {
				// foreach ($specific_renovation_objects as $specific_renovation_object) {
				// 	if ($passport->specific_renovation_object_id == $specific_renovation_object->id) {
				// 		$passports[$key]->specific_renovation_object = $specific_renovation_object->name;
				// 	}
				// }
				$passports[$key]->voltage = ($passport->voltage / 1000);
				foreach ($places as $place) {
					if ($passport->place_id == $place->id) {
						$passports[$key]->place = $place->name;
					}
				}
				foreach ($insulation_types as $insulation_type) {
					if ($passport->insulation_type_id == $insulation_type->id) {
						$passports[$key]->insulation_type = $insulation_type->insulation_type;
					}
				}
				foreach ($users as $user) {
					if ($passport->created_by == $user->id) {
						$passports[$key]->created_by = $user->name . ' ' . mb_strtoupper($user->surname);
					}
					if ($passport->updated_by == $user->id) {
						$passports[$key]->updated_by = $user->name . ' ' . mb_strtoupper($user->surname);
					}
				}
				$passports_group[$passport->specific_renovation_object_id][] = $passport;
			}

			// if ($equipment_id) {
			// 	$specific_renovation_objects = array_filter($specific_renovation_objects, function ($row) {
			// 		return $row->equipment_id == $this->uri->segment(5);
			// 	});
			// }

			// echo "<pre>";
			// print_r(count($passports_group));
			// print_r($passports_group);
			// echo "</pre>";
			// exit;

			$data['results'] = $passports;
			// echo "<pre>";
			// print_r(count($passports_group));
			// print_r($passports[0]);
			// echo "</pre>";
		}

		$this->load->view('layout_lte', $data);
	}

	public function create()
	{
		$data = [];
		$data['title'] = 'Створення паспортів електрообладнання';
		$data['content'] = 'passports/form';
		$data['page'] = 'passports';
		$data['page_js'] = 'passports';
		$data['title_heading'] = 'Створення паспортів електрообладнання';
		$data['title_heading_card'] = 'Форма Створення паспортів електрообладнання';
		$data['datatables'] = FALSE;
		$data['forms'] = TRUE;

		$this->load->view('layout_lte', $data);
	}

	public function index_old()
	{
		$this->load->library('form_validation');

		$data = [];
		$data['export_to_excel'] = ($this->session->user->group == 'admin') ? TRUE : FALSE;

		$data['title'] = 'Паспорти обладнання';
		$data['content'] = 'passports/index_old';
		$data['page'] = 'passports';
		$data['page_js'] = 'passports_old';
		$data['datatables'] = TRUE;
		$data['title_heading'] = 'Паспорти обладнання';
		$data['title_heading_card'] = 'Паспорти обладнання';
		$data['subdivisions'] = $this->subdivision_model->get_data();
		$data['stantions'] = $this->complete_renovation_object_model->get_data_for_user();
		$data['equipments'] = $this->equipment_model->get_data();
		$data['voltage_class'] = $this->voltage_class_model->get_data();
		$data['insulation_type'] = $this->insulation_type_model->get_data();
		$data['places'] = $this->place_model->get_data();
		$data['type_services'] = $this->type_service_model->get_data();
		$this->load->view('layout', $data);
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

		if ($this->input->post('field') === 'place_id') {
			$rules = 'required';
		}
		if ($this->input->post('field') === 'type') {
			$rules = 'required';
		}
		if ($this->input->post('field') === 'short_type') {
			$rules = 'required';
		}
		if ($this->input->post('field') === 'number') {
			$rules = 'required';
		}
		if ($this->input->post('field') === 'is_block') {
			$rules = 'required';
		}
		if ($this->input->post('field') === 'commissioning_year') {
			$rules = 'required|numeric|min_length[4]|max_length[4]|integer';
		}
		if ($this->input->post('field') === 'insulation_type_id') {
			$rules = 'required';
		}
		if ($this->input->post('field') === 'sub_number_r3') {
			$rules = 'required|numeric|min_length[1]|max_length[2]|integer';
		}

		$this->form_validation->set_rules('value', '<strong>' . $this->input->post('field_title') . '</strong>', $rules);

		if ($this->form_validation->run() == FALSE) {
			$this->form_validation->set_error_delimiters('', '');
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => validation_errors()], JSON_UNESCAPED_UNICODE));
			return;
		}

		$data = $this->set_data_update_field();

		if (!$data) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Дані для змін не встановлені!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$result = $this->passport_model->update_field($this->input->post('id', TRUE), $data);

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

	public function get_data_server_side()
	{
		$this->output->set_content_type('application/json');
		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		foreach ($this->input->post('columns') as $key => $value) {
			$filter[$value['data']] = $value['search']['value'];
			if ($key == $this->input->post('order')[0]['column']) {
				$order_dir = $this->input->post('order')[0]['dir'];
				$order_field = $this->input->post('columns')[$key]['data'];
			}
		}

		$passports = $this->passport_model->get_data_datatables_server_side($this->input->post(), $filter, $order_dir, $order_field);

		// $passport_properties = $this->passport_property_model->get_all_passport_properties();

		// $properties = $this->property_model->get_all_properties();

		// $operating_list = $this->operating_list_model->get_all_operating_list();

		foreach ($passports as $passport) {
			$passport->passport_properties = [];
			// foreach ($passport_properties as $k => $v) {
			// 	if ($value->id === $v->passport_id) {
			// 		array_push($value->passport_properties, $v);
			// 	}
			// }
			$passport->passport_properties = $this->passport_property_model->get_data_for_passport($passport->id);

			$passport->properties = [];
			// foreach ($properties as $k => $v) {
			// 	if ($value->equipment_id === $v->equipment_id) {
			// 		array_push($value->properties, $v);
			// 	}
			// }
			$passport->propeties = $this->property_model->get_data_for_equipment($passport->equipment_id);

			$passport->operating_list = [];
			// foreach ($operating_list as $k => $v) {
			// 	if ($value->id === $v->passport_id) {
			// 		array_push($value->operating_list, $v);
			// 	}
			// }

			$passport->operating_list = $this->operating_list_model->get_data_for_passport($passport->id);

			$passport->passport_photos = $this->passport_photo_model->get_data_for_passport($passport->id);

			$passport->DT_RowId = $passport->id;

			$passport->DT_RowData['user_group'] = $this->session->user->group;
			$passport->DT_RowData['subdivision_id'] = $passport->subdivision_id;
			$passport->DT_RowData['complete_renovation_object_id'] = $passport->complete_renovation_object_id;
			$passport->DT_RowData['specific_renovation_object_id'] = $passport->specific_renovation_object_id;
			$passport->DT_RowData['place_id'] = $passport->place_id;
			$passport->DT_RowData['equipment_id'] = $passport->equipment_id;
			$passport->DT_RowData['id'] = $passport->id;

			$passport->DT_RowAttr['data-user_group'] = $this->session->user->group;
			$passport->DT_RowAttr['data-subdivision_id'] = $passport->subdivision_id;
			$passport->DT_RowAttr['data-complete_renovation_object_id'] = $passport->complete_renovation_object_id;
			$passport->DT_RowAttr['data-specific_renovation_object_id'] = $passport->specific_renovation_object_id;
			$passport->DT_RowAttr['data-place_id'] = $passport->place_id;
			$passport->DT_RowAttr['data-equipment_id'] = $passport->equipment_id;
			$passport->DT_RowAttr['data-id'] = $passport->id;

			// $passport->DT_RowClass = '';
		}

		$data['draw'] = $this->input->post('draw');
		$data['recordsTotal'] = $this->passport_model->get_count_all();
		$data['recordsFiltered'] = $this->passport_model->get_records_filtered($this->input->post(), $filter);
		$data['data'] = $passports;

		$data['user_group'] = $this->session->user->group;

		$data['post'] = $this->input->post();

		$this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
	}

	public function get_data()
	{
		// $this->output->set_content_type('application/json');
		// if (!$this->input->post()) {
		// 	$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
		// 	return;
		// }

		// $passports = $this->passport_model->get_data_datatables();

		// $passport_properties = $this->passport_property_model->get_all_passport_properties();

		// $properties = $this->property_model->get_all_properties();

		// $operating_list = $this->operating_list_model->get_all_operating_list();

		// foreach ($passports as $key => $value) {
		// 	$value->passport_properties = [];
		// 	foreach ($passport_properties as $k => $v) {
		// 		if ($value->id === $v->passport_id) {
		// 			array_push($value->passport_properties, $v);
		// 		}
		// 	}

		// 	$value->properties = [];
		// 	foreach ($properties as $k => $v) {
		// 		if ($value->equipment_id === $v->equipment_id) {
		// 			array_push($value->properties, $v);
		// 		}
		// 	}

		// 	$value->operating_list = [];
		// 	foreach ($operating_list as $k => $v) {
		// 		if ($value->id === $v->passport_id) {
		// 			array_push($value->operating_list, $v);
		// 		}
		// 	}
		// }

		// $data['data'] = $passports;
		// $data['user_group'] = $this->session->user->group;
		// $this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
	}

	public function copy_passport_properties($equipment_id, $specific_renovation_object_id, $passport_id = NULL)
	{
		if ($this->session->user->id != 41 && $this->session->user->id != 39 && $this->session->user->id != 7 && $this->session->user->id != 10 && $this->session->user->id != 1) {
			redirect('/passports/index_old');
		}

		$data = [];
		$data['title'] = 'Копіювання характеристик обладнання';
		$data['content'] = 'passports/copy_passport_properties';
		$data['page'] = 'passports/copy_passport_properties';
		$data['page_js'] = 'passports';
		$data['datatables'] = TRUE;
		$data['title_heading'] = 'Копіювання характеристик обладнання';
		$data['title_heading_card'] = 'Форма для копіювання характеристик обладнання';
		$donor = $this->passport_model->get_passport_for_donor($passport_id);

		if (isset($donor)) {
			$data['donor'] = $donor;
			$data['patients'] = $this->specific_renovation_object_model->get_specific_renovation_object_for_copy($donor);
			$data['passport_properties'] = $this->passport_property_model->get_data_for_passport($donor->id);
		}

		$this->load->view('layout', $data);
	}

	public function copy_passport_properties_insert()
	{
		if ($this->session->user->id != 41 && $this->session->user->id != 39 && $this->session->user->id != 7 && $this->session->user->id != 10 && $this->session->user->id != 1) {
			redirect('/passports/index_old');
		}

		if (!$this->input->post('passport_id')) {
			$this->session->set_flashdata('message', 'Потрібно вибрати обладнання.');
			redirect('/passports/copy_passport_properties/' . $this->input->post('donor_equipment_id') . '/' . $this->input->post('donor_specific_renovation_object_id'));
		}

		$passport_properties = $this->passport_property_model->get_data_for_passport($this->input->post('donor_passport_id'));

		foreach ($this->input->post('passport_id') as $key => $value) {
			$data = $this->set_properties_copy_data($passport_properties, $value);

			$is_delete = $this->passport_property_model->delete_row($value);

			foreach ($data as $key => $value) {

				$result = $this->passport_property_model->add_data_row($value);
				if ($result) {
					$data_after = $value;
					$log_data = $this->set_log_data("Копіювання паспортних даних.", 'create', json_encode(NULL, JSON_UNESCAPED_UNICODE), json_encode($data_after, JSON_UNESCAPED_UNICODE), 2);
					$this->log_model->insert_data($log_data);
				}
			}
		}
		redirect('/passports/index_old');
	}

	public function add_passport()
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

		if ($this->session->user->id != 1 && $this->session->user->group !== 'engineer') {
			show_404();
		}

		$this->load->library('form_validation');

		$this->form_validation->set_rules('complete_renovation_object_id', 'Підстанція', 'required');
		$this->form_validation->set_rules('equipment_id', 'Вид обладнання', 'required');
		$this->form_validation->set_rules('insulation_type_id', 'Вид ізоляції', 'required');
		$this->form_validation->set_rules('place_id', 'Місце встановлення', 'required');
		$this->form_validation->set_rules('voltage_class_id', 'Клас напруги', 'required');
		$this->form_validation->set_rules('specific_renovation_object', 'Диспечерське найменування', 'required|trim');
		$this->form_validation->set_rules('type', 'Тип обладнання', 'required|trim');
		$this->form_validation->set_rules('production_date', 'Дата виготовлення', 'required|trim');
		$this->form_validation->set_rules('number', 'Номер', 'required|trim');

		if ($this->form_validation->run() == FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Щось пішло не так!', 'errors' => $this->form_validation->error_array()], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			// print_r($this->input->post());
			// Проверяем есть ли такая запись в таблице specific_renovation_objects
			$disp = $this->specific_renovation_object_model->is_specific_renovation_object($this->input->post('complete_renovation_object_id'), $this->input->post('specific_renovation_object'), $this->input->post('equipment_id'));
			// print_r($disp);
			// exit;
			if (!$disp) {
				$specific_renovation_object_data = $this->set_specific_renovation_object_add_data($this->input->post());
				$specific_renovation_object_id = $this->specific_renovation_object_model->add_data($specific_renovation_object_data);
				for ($i = 1; $i <= 3; $i++) {
					$schedule_data = $this->set_schedule_add_data($this->input->post(), $specific_renovation_object_id, $i);
					$this->schedule_model->add_data($schedule_data);
				}
				$passport_data = $this->set_passport_add_data($this->input->post(), $specific_renovation_object_id);
				$passport_id = $this->passport_model->add_data($passport_data);
			} else {
				$specific_renovation_object_id = $disp->id;
				$passport_data = $this->set_passport_add_data($this->input->post(), $specific_renovation_object_id);
				$passport_id = $this->passport_model->add_data($passport_data);
			}

			if ($passport_id > 0) {
				$log_data = $this->set_log_data("Створення паспортних даних.", 'create', json_encode(NULL, JSON_UNESCAPED_UNICODE), json_encode($passport_data, JSON_UNESCAPED_UNICODE), 2);
				$this->log_model->insert_data($log_data);

				$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані додано!', 'specific_renovation_object_id' => $specific_renovation_object_id, 'passport_id' => $passport_id], JSON_UNESCAPED_UNICODE));
				return;
			}
		}
	}

	public function edit_passport()
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

		$this->load->library('form_validation');

		// $this->form_validation->set_rules('complete_renovation_object_id', 'Підстанція', 'required');
		// $this->form_validation->set_rules('equipment_id', 'Вид обладнання', 'required');
		// $this->form_validation->set_rules('insulation_type_id', 'Вид ізоляції', 'required');
		// $this->form_validation->set_rules('place_id', 'Місце встановлення', 'required');
		// $this->form_validation->set_rules('voltage_class_id', 'Клас напруги', 'required');
		// $this->form_validation->set_rules('disp', 'Диспечерське найменування', 'required');
		$this->form_validation->set_rules('type', 'Тип обладнання', 'required|trim');
		// $this->form_validation->set_rules('production_date', 'Дата виготовлення', 'required');
		$this->form_validation->set_rules('commissioning_year', 'Рік вводу в експлуатацію', 'min_length[4]|max_length[4]|integer|callback_more_then_year_now');
		$this->form_validation->set_rules('number', 'Номер', 'trim');

		if ($this->form_validation->run() == FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Щось пішло не так!', 'errors' => $this->form_validation->error_array()], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$passport_data_before = $this->passport_model->get_passport($this->input->post('id'));
			$passport_data_after = $this->set_passport_edit_data($this->input->post());
			$result = $this->passport_model->edit_data($passport_data_after, $this->input->post('id'));

			if ($result) {
				$log_data = $this->set_log_data("Зміна паспортних даних.", 'update', json_encode($passport_data_before, JSON_UNESCAPED_UNICODE), json_encode($passport_data_after, JSON_UNESCAPED_UNICODE), 3);
				$this->log_model->insert_data($log_data);

				$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
				return;
			} else {
				$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Помилка на сервері!'], JSON_UNESCAPED_UNICODE));
				return;
			}
		}
	}

	public function move_passport()
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

		$this->load->library('form_validation');

		$this->form_validation->set_rules('subdivision_id', 'Підрозділ', 'required');
		$this->form_validation->set_rules('complete_renovation_object_id', 'Підстанція', 'required');
		$this->form_validation->set_rules('specific_renovation_object_id', 'Диспечерське найменування', 'required');
		$this->form_validation->set_rules('place_id', 'Місце встановлення', 'required');

		if ($this->form_validation->run() == FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Щось пішло не так!', 'errors' => $this->form_validation->error_array()], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$passport_data_before = $this->passport_model->get_passport($this->input->post('id'));
			$passport_data_after = $this->set_passport_move_data($this->input->post());
			$result = $this->passport_model->edit_data($passport_data_after, $this->input->post('id'));

			if ($result) {
				$log_data = $this->set_log_data("Перенесення паспортних даних.", 'update', json_encode($passport_data_before, JSON_UNESCAPED_UNICODE), json_encode($passport_data_after, JSON_UNESCAPED_UNICODE), 3);
				$this->log_model->insert_data($log_data);

				$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
				return;
			} else {
				$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Помилка на сервері!'], JSON_UNESCAPED_UNICODE));
				return;
			}
		}
	}

	public function get_data_passport()
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

		if ($this->session->user->group === 'user' || $this->session->user->group === 'head') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		$passport = $this->passport_model->get_passport($this->input->post('id'));

		if (!$passport) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Не вдалося отримати дані з реєстру!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$disp = $this->specific_renovation_object_model->get_specific_renovation_object($passport->specific_renovation_object_id);

		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані для зміни отримано', 'passport' => $passport, 'disp' => $disp], JSON_UNESCAPED_UNICODE));
	}

	public function get_data_passport_ajax($id = NULL)
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!$id) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'В запросі немає ідентифікатора!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!is_numeric($id)) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Ідентифікатор повиненн бути цілим числом!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if ($this->session->user->group === 'user' || $this->session->user->group === 'head') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		// $this->load->library('form_validation');

		$passport = $this->passport_model->get_passport($id);

		if (!isset($passport)) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Не вдалося отримати дані з реєстру!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$disp = $this->specific_renovation_object_model->get_specific_renovation_object($passport->specific_renovation_object_id);

		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані для зміни отримано', 'passport' => $passport, 'disp' => $disp], JSON_UNESCAPED_UNICODE));
	}

	public function get_data_passport_for_move()
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

		if ($this->session->user->group === 'user' || $this->session->user->group === 'head' || $this->session->user->group === 'master') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		$passport = $this->passport_model->get_passport($this->input->post('id'));

		if (!$passport) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Не вдалося отримати дані з реєстру!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$disp = $this->specific_renovation_object_model->get_specific_renovation_object($passport->specific_renovation_object_id);

		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'passport' => $passport, 'disp' => $disp], JSON_UNESCAPED_UNICODE));
	}

	public function delete_passport($passport_id = NULL)
	{
		if ($this->session->user->group !== 'admin') {
			show_404();
		}

		if (!is_numeric($passport_id) || !isset($passport_id)) {
			show_404();
		}

		// $this->passport_model->delete_passport_full($passport_id);
		redirect('/passports/index_old');
	}

	public function get_properties()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if ($this->session->user->group === 'user' || $this->session->user->group === 'head') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$is_passport_properties = $this->passport_property_model->get_data_for_passport($this->input->post('passport_id'));

		if ($is_passport_properties) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вже є дані для цього паспорту. Можливо Ви хочете змінити дані.'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$is_properties = $this->property_model->get_data_equipment($this->input->post('equipment_id'));

		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Характеристики завантажено!', 'passport_properties' => $is_passport_properties, 'properties' => $is_properties], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function add_properties()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Відсутні дані POST!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$data = $this->set_properties_add_data($this->input->post());

		$result = $this->passport_property_model->add_data_batch($data);

		if ($result) {
			$log_data = $this->set_log_data("Створення технічних характеристик.", 'create', json_encode(NULL, JSON_UNESCAPED_UNICODE), json_encode($data, JSON_UNESCAPED_UNICODE), 2);
			$this->log_model->insert_data($log_data);
		}

		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані додано!', 'result' => $result], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function edit_property()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Відсутні дані POST!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if ($this->session->user->group === 'user' || $this->session->user->group === 'head') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		$this->form_validation->set_rules('value', 'Значення', 'required|trim');

		if ($this->form_validation->run() == FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Щось пішло не так!', 'errors' => $this->form_validation->error_array()], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$property_data_defore = $this->passport_property_model->get_data_row($this->input->post('id'));
			$property_data_after = $this->set_properties_edit_data($this->input->post());

			$result = $this->passport_property_model->edit_data_row($property_data_after, $this->input->post('id'));

			if ($result) {
				$log_data = $this->set_log_data("Зміна технічних характеристик.", 'update', json_encode($property_data_defore, JSON_UNESCAPED_UNICODE), json_encode($property_data_after, JSON_UNESCAPED_UNICODE), 3);
				$this->log_model->insert_data($log_data);

				$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!', 'result' => $result], JSON_UNESCAPED_UNICODE));
				return;
			}
		}
	}

	public function add_operating_list()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Відсутні дані POST!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if ($this->session->user->group === 'user' || $this->session->user->group === 'head') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		$this->form_validation->set_rules('service_date', 'Дата обслуговування обладнання', 'required|trim');
		$this->form_validation->set_rules('type_service_id', 'Тип обслуговування', 'required');
		$this->form_validation->set_rules('service_data', 'Дані з експлуатації обладнання', 'required|trim');
		$this->form_validation->set_rules('executor', 'Виконавець робіт', 'required|trim');

		if ($this->form_validation->run() == FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Щось пішло не так!', 'errors' => $this->form_validation->error_array()], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$operating_list_data = $this->set_oparating_list_add_data($this->input->post());

			$result = $this->operating_list_model->add_data($operating_list_data);

			if ($result) {
				$log_data = $this->set_log_data("Створення експлуатаційних даних.", 'create', json_encode(NULL, JSON_UNESCAPED_UNICODE), json_encode($operating_list_data, JSON_UNESCAPED_UNICODE), 2);
				$this->log_model->insert_data($log_data);
			}

			if ($this->input->post('places')) {
				foreach ($this->input->post('places') as $key => $place) {
					$operating_list_copy_data = $this->set_oparating_list_copy_data($this->input->post(), $place, $this->input->post('passports')[$place]);

					$result = $this->operating_list_model->add_data($operating_list_copy_data);

					if ($result) {
						$log_data = $this->set_log_data("Створення експлуатаційних даних.", 'create', json_encode(NULL, JSON_UNESCAPED_UNICODE), json_encode($operating_list_copy_data, JSON_UNESCAPED_UNICODE), 2);
						$this->log_model->insert_data($log_data);
					}
				}
			}

			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані додано!', 'result' => $result], JSON_UNESCAPED_UNICODE));
			return;
		}
	}

	public function edit_operating_list()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Відсутні дані POST!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if ($this->session->user->group === 'user' || $this->session->user->group === 'head') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		$this->form_validation->set_rules('service_date', 'Дата обслуговування', 'required|trim');
		$this->form_validation->set_rules('service_data', 'Дані з експлуатації', 'required|trim');
		$this->form_validation->set_rules('executor', 'Виконавець', 'required|trim');

		if ($this->form_validation->run() == FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Щось пішло не так!', 'errors' => $this->form_validation->error_array()], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$operating_list_data_before = $this->operating_list_model->get_data_row($this->input->post('id'));
			$operating_list_data_after = $this->set_oparating_list_edit_data($this->input->post());

			$result = $this->operating_list_model->edit_data_row($operating_list_data_after, $this->input->post('id'));

			if ($result) {
				$log_data = $this->set_log_data("Зміна експлуатаційних даних.", 'update', json_encode($operating_list_data_before, JSON_UNESCAPED_UNICODE), json_encode($operating_list_data_after, JSON_UNESCAPED_UNICODE), 3);
				$this->log_model->insert_data($log_data);

				$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!', 'result' => $result], JSON_UNESCAPED_UNICODE));
				return;
			}
		}
	}

	public function delete_operating_list()
	{
		$this->output->set_content_type('application/json');

		// Якщо це не Ajax-запрос
		if ($this->input->is_ajax_request() === FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax-запрос!']));
			return;
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'master') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Ви не меєте прав видаляти ці дані!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!$this->input->post('id') || $this->input->post('id') === 'undefined') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Відсутні дані POST!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$operating_list_data_before = $this->operating_list_model->get_data_row($this->input->post('id'));

		$result = $this->operating_list_model->delete_data_row($this->input->post('id'));

		if ($result) {
			$log_data = $this->set_log_data("Видалення експлуатаційних даних.", 'delete', json_encode($operating_list_data_before, JSON_UNESCAPED_UNICODE), json_encode(NULL, JSON_UNESCAPED_UNICODE), 4);
			$this->log_model->insert_data($log_data);

			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані видалено!', 'result' => $result], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Щось пішло не так!'], JSON_UNESCAPED_UNICODE));
			return;
		}
	}

	public function gen_passport_pdf($id, $is_hide = NULL)
	{
		$data = [];
		$passport = $this->passport_model->get_row($id);
		$properties = $this->passport_property_model->get_data_for_passport($id);
		$passport->properties = $properties;
		$data['passport'] = $passport;
		$data['operating_list'] = $this->operating_list_model->get_data_for_passport($id);
		$data['is_hide'] = $is_hide;

		// create new PDF document
		$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A3', true, 'UTF-8', false);

		// set document information
		$pdf->SetAuthor('Yurii Sychov');
		$pdf->SetTitle('Passport');

		// set default header data
		// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
		// $pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

		// set header and footer fonts
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set default font subsetting mode
		$pdf->setFontSubsetting(true);

		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->SetFont('dejavusans', '', 11, '', true);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();

		// set text shadow effect
		$pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

		// Set some content to print
		$html = $this->load->view('passports/passport_pdf', $data, TRUE);

		// Print text using writeHTMLCell()
		$pdf->writeHTMLCell(0, 0, '', 20, $html, 0, 1, 0, true, '', true);

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.

		$pdf->Output("Passport.pdf", 'I');
	}

	public function gen_passport_object_pdf($id = NULL, $equipment_id = NULL)
	{
		if (!is_numeric($id)) {
			show_404();
		}

		$data = [];

		$complete_renovation_object = $this->complete_renovation_object_model->get_row($id);

		$passports = $this->passport_model->get_rows($id, $equipment_id);

		$data['equipment_id'] = $equipment_id;

		$equipments_group = [];
		foreach ($passports as $row) {
			$row->color = '';
			// switch ($row->insulation_type_id) {
			// 	case 4:
			// 		$row->color = 'red';
			// 		break;
			// case 2:
			// 	$row->color = 'green';
			// 	break;
			// case 6:
			// 	$row->color = 'green';
			// 	break;
			// }

			$equipments_group[$row->equipment . ' ' . $row->equipment_voltage][] = [
				'disp' => $row->disp,
				'place' => $row->place,
				'equipment_id' => $row->equipment_id,
				'equipment' => $row->equipment . ' ' . $row->voltage / 1000 . ' кВ',
				'type' => $row->type,
				'number' => $row->number,
				'year' => date('Y', strtotime($row->production_date)),
				'c_year' => $row->commissioning_year,
				'properties' => $this->passport_property_model->get_data_for_complete_renovation_object($row->id),
				'insulation_type' => mb_strtolower($row->insulation_type),
				'id_w' => '5%',
				'disp_w' => '12%',
				'type_w' => '20%',
				'number_w' => '8%',
				'year_w' => '13%',
				'properties_w' => '42%',

				'color' => $row->color,

				'disp_bc' => $row->disp === '' ? '#00ff00' : '#ffffff',
				'type_bc' => $row->type === '' ? '#00ff00' : '#ffffff',
				'number_bc' => $row->number === '' ? '#00ff00' : '#ffffff',
				'year_bc' => ($row->production_date === '' || $row->production_date === '0000-00-00') ? '#00ff00' : '#ffffff',
			];
		}

		$data['complete_renovation_object'] = $complete_renovation_object;
		$data['results'] = $equipments_group;

		// $data['title'] = 'Паспорти обладнання';
		// $data['content'] = 'passports/passport_object_pdf';
		// $data['page'] = 'passports/passport_object_pdf';
		// $data['page_js'] = 'passports';
		// $data['datatables'] = TRUE;
		// $data['title_heading'] = 'Паспорти обладнання';
		// $data['title_heading_card'] = 'Паспорти обладнання';
		// $this->load->view('layout', $data);
		// exit;
		// echo "<pre>";
		// print_r($passports);
		// print_r($equipments_group);
		// echo "</pre>";
		// exit;
		// $properties = $this->passport_property_model->get_properties($id);
		// $passports->properties = $properties;
		// $data['passport'] = $passports;
		// $data['operating_list'] = $this->operating_list_model->get_data_for_passport($id);

		// echo "<pre>";
		// print_r($passport);
		// echo "</pre>";

		// create new PDF document
		$pdf = new MYPDF('L', PDF_UNIT, 'A3', true, 'UTF-8', false);

		// set document information
		$pdf->SetAuthor('Yurii Sychov');
		$pdf->SetTitle('Passport Object');

		// set default header data
		// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
		// $pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

		// set header and footer fonts
		// $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		// $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set default font subsetting mode
		$pdf->setFontSubsetting(true);

		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->SetFont('dejavusans', '', 12, '', true);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();

		// set text shadow effect
		$pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

		// Set some content to print
		$html = $this->load->view('passports/passport_object_pdf', $data, TRUE);

		// Print text using writeHTMLCell()
		$pdf->writeHTMLCell(0, 0, '', 10, $html, 0, 1, 0, true, '', true);

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$pdf->Output('passport_object.pdf', 'I');
	}

	public function gen_operating_list_object_pdf($id = NULL, $year = NULL)
	{
		if (!is_numeric($id)) {
			show_404();
		}

		$data = [];
		$complete_renovation_object = $this->complete_renovation_object_model->get_row($id);
		if (!$complete_renovation_object) {
			show_404();
		}
		$operating_list = $this->operating_list_model->get_data_for_object($id, $year);
		$data['complete_renovation_object'] = $complete_renovation_object;
		$data['results'] = $operating_list;
		$data['year'] = $year;

		// echo "<pre>";
		// print_r($data);
		// print_r($operating_list);
		// print_r($equipments_group);
		// echo "</pre>";
		// exit;

		// create new PDF document
		$pdf = new MYPDF('L', PDF_UNIT, 'A3', true, 'UTF-8', false);

		// set document information
		$pdf->SetAuthor('Yurii Sychov');
		$pdf->SetTitle('Operating list Object');

		// set default header data
		// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
		// $pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

		// set header and footer fonts
		// $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		// $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set default font subsetting mode
		$pdf->setFontSubsetting(true);

		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->SetFont('dejavusans', '', 12, '', true);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();

		// set text shadow effect
		$pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

		// Set some content to print
		$html = $this->load->view('passports/operating_list_object_pdf', $data, TRUE);

		// Print text using writeHTMLCell()
		$pdf->writeHTMLCell(0, 0, '', 15, $html, 0, 1, 0, true, '', true);

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$pdf->Output('operating_list_object.pdf', 'I');
	}

	public function get_places()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Відсутні дані POST!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if ($this->session->user->group === 'user' || $this->session->user->group === 'head') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$specific_renovation_object_id = $this->input->post('specific_renovation_object_id');
		$place_id = $this->input->post('place_id');

		$places = $this->passport_model->get_places_for_specific_renovation_object($specific_renovation_object_id, $place_id);

		if ($places) {
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Є ще місця встановлення обладнання для цього диспетчерського найменування.', 'places' => $places], JSON_UNESCAPED_UNICODE));
			return;
		}
	}

	public function get_value($value = NULL)
	{
		if (!$value) {
			show_404();
		}
		$data['values'] = $this->operating_list_model->get_value($value);
		$data['field'] = $value;
		$this->load->view('value', $data);
	}

	public function get_specific_renovation_objects()
	{
		// $this->output->set_content_type('application/json');
		// if ($this->input->post('disp')) {
		// 	$results = $this->specific_renovation_object_model->get_search($this->input->post('disp'), $this->input->post('equipment'));
		// }
		// if (isset($results)) {
		// 	$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані отримано!', 'results' => $results], JSON_UNESCAPED_UNICODE));
		// 	return;
		// } else {
		// 	$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Сталася помилка!'], JSON_UNESCAPED_UNICODE));
		// 	return;
		// }
	}

	public function change_is_block_ajax()
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

		if ($this->session->user->group !== 'admin') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->passport_model->change_value('is_block', $this->input->post('value'), $this->input->post('id'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function change_is_block_property_ajax()
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

		if ($this->session->user->group !== 'admin') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->passport_property_model->change_value('is_block', $this->input->post('value'), $this->input->post('id'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function get_data_excel($subdivision_id = NULL, $complete_renovation_object_id = NULL)
	{
		if ((!$subdivision_id || !is_numeric($subdivision_id))) {
			show_404();
		}

		$passports = $this->passport_model->get_data_for_excel($subdivision_id, $complete_renovation_object_id);

		foreach ($passports as $k => $row) {
			$subdivision = $row->subdivision;
			$complete_renovation_object = $row->complete_renovation_object;
			$group = 'Підрозділ (' . $row->subdivision . ')_Об\'єкт (' . $row->complete_renovation_object . ')_ДНО (' . $row->specific_renovation_object . ')';
			$group_passports[$group]['num'] = null;
			$group_passports[$group]['subdivision'] = $row->subdivision;
			$group_passports[$group]['complete_renovation_object'] = $row->complete_renovation_object;
			$group_passports[$group]['specific_renovation_object'] = $row->specific_renovation_object;
			$group_passports[$group]['full_class_voltage_cro'] = $row->full_class_voltage_cro;
			$group_passports[$group]['short_class_voltage_cro'] = $row->short_class_voltage_cro . ' кВ';
			$group_passports[$group]['equipment'] = $row->equipment;
			$group_passports[$group]['equipment_voltage'] = ($row->equipment_voltage < 1000) ? number_format($row->equipment_voltage / 1000, 2, ',', ' ') : number_format($row->equipment_voltage / 1000, 0, ',', ' ');
			$group_passports[$group]['place_id']['place_' . $row->place_id] = $row->place_id;
			$group_passports[$group]['place']['place_' . $row->place_id] = $row->place;
			$group_passports[$group]['type']['place_' . $row->place_id] = $row->type;
			$group_passports[$group]['short_type']['place_' . $row->place_id] = $row->short_type;
			$group_passports[$group]['number']['place_' . $row->place_id] = $row->number;
			$group_passports[$group]['insulation_type']['place_' . $row->place_id] = $row->insulation_type;
			$group_passports[$group]['production_date']['place_' . $row->place_id] = date('Y', strtotime($row->production_date));
			$group_passports[$group]['commissioning_year']['place_' . $row->place_id] = $row->commissioning_year;
			$group_passports[$group]['properties'][] = implode("\n", explode("|", $row->properties ?? ""));
		}

		// echo "<pre>";
		// print_r($passports[0]);
		// print_r($group_passports);
		// echo "</pre>";
		// exit;

		$spreadsheet = new Spreadsheet();
		$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$spreadsheet->getActiveSheet()->getPageSetup()->setFitToPage(false)->setScale(40);
		$spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.392);
		$spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.392);
		$spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.784);
		$spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.392);

		$spreadsheet->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
		$spreadsheet->getActiveSheet()->getSheetView()->setZoomScale(52);
		$spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
		$spreadsheet->getDefaultStyle()->getFont()->setSize(12);

		$array_width = [
			'A' => 8,
			'B' => 30,
			'C' => 40,
			'D' => 20,
			'E' => 35,
			'F' => 16,
			'G' => 20,
			'H' => 20,
			'I' => 35,
			'J' => 35,
			'K' => 23,
			'L' => 20,
			'M' => 20,
			'N' => 135,
		];
		foreach ($array_width as $column => $width) {
			$spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth($width);
		}

		$sheet = $spreadsheet->getActiveSheet()->setTitle('Обладнання');
		$sheet->getTabColor()->setRGB('FF0000');

		$sheet->mergeCells('A1:N1')->setCellValue('A1', 'Обладнання ' . $complete_renovation_object)->getStyle('A1:N1')->getFont()->setSize(18);
		$sheet->getStyle('A1:N1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setWrapText(true);
		$sheet->getStyle('A1:N1')->getFont()->setBold(true);
		$sheet->getRowDimension('1')->setRowHeight(40);

		$array_data = [
			'num' => '№ п/п',
			'subdivision' => 'Підрозділ',
			'complete_renovation_object' => 'Об\'єкт',
			'specific_renovation_object' => 'ДНО',
			'equipment' => 'Вид обладнання',
			'equipment_voltage' => 'Напруга, кВ',
			'place' => 'Місце встановлення',
			'insulation_type' => 'Вид ізоляції',
			'type' => 'Тип обладнання (повний)',
			'short_type' => 'Тип обладнання (краткий)',
			'number' => 'Заводський номер',
			'production_date' => 'Рік виготовлення',
			'commissioning_year' => 'Рік вводу',
			'properties' => 'Технічні характеристики'
		];

		$col = 'A';
		foreach ($array_data as $column) {

			$sheet->setCellValue($col . '2', $column);
			$sheet->getStyle($col . '2')->getFont()->setSize(14);
			$sheet->getStyle($col . '2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setWrapText(true);
			$sheet->getStyle($col . '2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('C0C0C0');
			$sheet->getStyle($col . '2')->getFont()->setBold(true);
			$sheet->getStyle($col . '2')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
			$sheet->getRowDimension('2')->setRowHeight(40);
			$col++;
		}

		$r = 3;
		foreach ($group_passports as $row) {
			$col = 'A';
			foreach ($array_data as $key => $column) {
				if (is_array($row[$key])) {
					$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
					$richText->createText('');

					// foreach ($row[$key] as $k => $item) {
					// 	if ($k > 'place_3' && count($row[$key]) == 1) {
					// 		$color = $richText->createTextRun($item);
					// 		$color->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKBLUE));
					// 	}
					// 	if ($k > 'place_3' && count($row[$key]) == 2) {
					// 		$color = $richText->createTextRun($item . "\n");
					// 		$color->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKBLUE));
					// 	}


					// 	if ($k == 'place_1' && count($row[$key]) == 1) {
					// 		$color = $richText->createTextRun($item);
					// 		$color->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKYELLOW));
					// 	}
					// 	if ($k == 'place_2' && count($row[$key]) == 1) {
					// 		$color = $richText->createTextRun($item);
					// 		$color->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKGREEN));
					// 	}
					// 	if ($k == 'place_3' && count($row[$key]) == 1) {
					// 		$color = $richText->createTextRun($item);
					// 		$color->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKRED));
					// 	}
					// 	if ($k == 'place_1' && count($row[$key]) > 1) {
					// 		$color = $richText->createTextRun($item . "\n");
					// 		$color->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKYELLOW));
					// 	}
					// 	if ($k == 'place_2' && count($row[$key]) > 1) {
					// 		$color = $richText->createTextRun($item . "\n");
					// 		$color->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKGREEN));
					// 	}
					// 	if ($k == 'place_3' && count($row[$key]) > 1) {
					// 		$color = $richText->createTextRun($item);
					// 		$color->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKRED));
					// 	}
					// 	$sheet->getCell($col . $r)->setValue($richText);
					// }
					$sheet->setCellValue($col . $r, implode("\n", $row[$key]));
					$sheet->getStyle($col . $r)->getAlignment()->setWrapText(true);
				} else {
					$sheet->getCell($col . $r)->setValue('$row[$key]');
					$sheet->setCellValue($col . $r, $row[$key]);
				}
				$col++;
			}

			$sheet->setCellValue('A' . $r, $r - 2);

			$sheet->getStyle('A' . $r . ':C' . $r)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle('D' . $r . ':E' . $r)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
			$sheet->getStyle('F' . $r)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle('G' . $r . ':K' . $r)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
			$sheet->getStyle('L' . $r . ':M' . $r)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle('N' . $r)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

			$sheet->getStyle('A' . $r . ':N' . $r)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
			if (count($row['place']) == 2) {
				$sheet->getRowDimension($r)->setRowHeight(40);
			} else if (count($row['place']) == 3) {
				$sheet->getRowDimension($r)->setRowHeight(60);
			} else if (count($row['place']) == 4) {
				$sheet->getRowDimension($r)->setRowHeight(80);
			} else {
				$sheet->getRowDimension($r)->setRowHeight(20);
			}
			$sheet->getRowDimension($r)->setRowHeight(
				13.5 * (substr_count($sheet->getCell('N' . $r)->getValue(), "\n") + 1)
			);
			$r = $r + 1;
		}

		$sheet->getStyle('A1');

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		if ($complete_renovation_object_id) {
			header('Content-Disposition: attachment;filename="Паспортні дані по ' . $complete_renovation_object . '.xlsx"');
		} else {
			header('Content-Disposition: attachment;filename="Паспортні дані по ' . $subdivision . '.xlsx"');
		}

		header('Cache-Control: max-age=0');

		$writer = new Xlsx($spreadsheet);

		$writer->save('php://output');
	}

	private function set_properties_add_data($post)
	{
		$data = [];

		foreach ($post as $key => $subarr) {
			if (is_array($subarr) || is_object($subarr)) {
				foreach ($subarr as $subkey => $subvalue) {
					$data[$subkey][$key] = $subvalue;
					$data[$subkey]['created_by'] = $this->session->user->id;
					$data[$subkey]['updated_by'] = $this->session->user->id;
					$data[$subkey]['created_at'] = date('Y-m-d H:i:s');
					$data[$subkey]['updated_at'] = date('Y-m-d H:i:s');
				}
			}
		}
		return $data;
	}

	private function set_properties_copy_data($donor, $passport_id)
	{
		$array_new = [];

		foreach ($donor as $k => $v) {
			$data['passport_id'] = $passport_id;
			$data['property_id'] = $v->property_id;
			$data['value'] = $v->value;
			$data['is_copy'] = 1;
			$data['is_block'] = $v->is_block;
			$data['created_by'] = $this->session->user->id;
			$data['updated_by'] = $this->session->user->id;
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = date('Y-m-d H:i:s');
			array_push($array_new, $data);
		}

		// foreach ($array_new as $key => $subarr) {
		// 	foreach ($subarr as $subkey => $subvalue) {
		// 		$data[$subkey][$key] = $subvalue;
		// 		// $data[$subkey]['created_by'] = $this->session->user->id;
		// 		// $data[$subkey]['updated_by'] = $this->session->user->id;
		// 		// $data[$subkey]['created_at'] = date('Y-m-d H:i:s');
		// 		// $data[$subkey]['updated_at'] = date('Y-m-d H:i:s');
		// 	}
		// }

		return $array_new;
	}

	private function set_properties_edit_data($post)
	{
		$data = [];

		$data['value'] = $post['value'];
		$data['updated_by'] = $this->session->user->id;
		$data['updated_at'] = date('Y-m-d H:i:s');

		return $data;
	}

	private function set_specific_renovation_object_add_data($post)
	{
		$data = [];
		$data['subdivision_id'] = $this->complete_renovation_object_model->get_row($post['complete_renovation_object_id'])->subdivision_id;
		$data['complete_renovation_object_id'] = $post['complete_renovation_object_id'];
		$data['name'] = $post['specific_renovation_object'];
		$data['year_commissioning'] = NULL;
		$data['equipment_id'] = $post['equipment_id'];
		$data['voltage_class_id'] = $post['voltage_class_id'];
		$data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		return $data;
	}

	private function set_passport_add_data($post, $specific_renovation_object_id)
	{
		$data = [];
		$data['subdivision_id'] = $this->complete_renovation_object_model->get_row($post['complete_renovation_object_id'])->subdivision_id;
		$data['complete_renovation_object_id'] = $post['complete_renovation_object_id'];
		$data['specific_renovation_object_id'] = $specific_renovation_object_id;
		$data['place_id'] = $post['place_id'];
		$data['insulation_type_id'] = $post['insulation_type_id'];
		$data['type'] = $post['type'];
		$data['production_date'] = date('Y-m-d', strtotime($post['production_date']));
		$data['number'] = $post['number'];
		$data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		return $data;
	}

	private function set_schedule_add_data($post, $specific_renovation_object_id, $i)
	{
		$data = [];
		$data['specific_renovation_object_id'] = $specific_renovation_object_id;
		$data['type_service_id'] = $i;
		$data['is_repair'] = 1;
		$data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		return $data;
	}

	private function set_passport_edit_data($post)
	{
		$data = [];
		// $data['subdivision_id'] = 1;
		// $data['complete_renovation_object_id'] = $post['complete_renovation_object_id'];
		// $data['specific_renovation_object_id'] = $specific_renovation_object_id;
		// $data['place_id'] = $post['place_id'];
		$data['insulation_type_id'] = $post['insulation_type_id'];
		$data['type'] = $post['type'];
		if ($this->session->user->group === 'admin') {
			$data['short_type'] = $post['short_type'];
		}
		$data['production_date'] = $post['production_date'] ? date('Y-m-d', strtotime($post['production_date'])) : NULL;
		$data['commissioning_year'] = $post['commissioning_year'] ? $post['commissioning_year'] : NULL;
		$data['number'] = $post['number'];
		$data['refinement_method'] = $post['refinement_method'];
		// $data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		// $data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		return $data;
	}

	private function set_passport_move_data($post)
	{
		$data = [];
		$data['subdivision_id'] = $post['subdivision_id'];
		$data['complete_renovation_object_id'] = $post['complete_renovation_object_id'];
		$data['specific_renovation_object_id'] = $post['specific_renovation_object_id'];
		$data['place_id'] = $post['place_id'];
		// $data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		// $data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		return $data;
	}

	private function set_oparating_list_add_data($post)
	{

		$data = [];

		$data['subdivision_id'] = $post['subdivision_id'];
		$data['complete_renovation_object_id'] = $post['complete_renovation_object_id'];
		$data['specific_renovation_object_id'] = $post['specific_renovation_object_id'];
		$data['place_id'] = $post['place_id'];
		$data['type_service_id'] = $post['type_service_id'];
		$data['passport_id'] = $post['passport_id'];
		$data['service_date'] = date('Y-m-d', strtotime($post['service_date']));
		$data['service_data'] = $post['service_data'];
		$data['executor'] = $post['executor'];
		$data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');

		return $data;
	}

	private function set_oparating_list_copy_data($post, $place_id, $passport_id)
	{

		$data = [];

		$data['subdivision_id'] = $post['subdivision_id'];
		$data['complete_renovation_object_id'] = $post['complete_renovation_object_id'];
		$data['specific_renovation_object_id'] = $post['specific_renovation_object_id'];
		$data['place_id'] = $place_id;
		$data['type_service_id'] = $post['type_service_id'];
		$data['passport_id'] = $passport_id;
		$data['service_date'] = date('Y-m-d', strtotime($post['service_date']));
		$data['service_data'] = $post['service_data'];
		$data['executor'] = $post['executor'];
		$data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');

		return $data;
	}

	private function set_oparating_list_edit_data($post)
	{
		$data = [];

		$data['service_date'] = date('Y-m-d', strtotime($post['service_date']));
		$data['service_data'] = $post['service_data'];
		$data['executor'] = $post['executor'];
		$data['updated_by'] = $this->session->user->id;
		$data['updated_at'] = date('Y-m-d H:i:s');

		return $data;
	}

	private function set_log_data($action, $short_action, $data_before, $data_after, $importance)
	{
		$this->load->library('user_agent');
		$data = [];

		$data['user_id'] = $this->session->user->id;
		$data['link'] = uri_string();
		$data['action'] = $action;
		$data['short_action'] = $short_action;
		$data['data_before'] = $data_before;
		$data['data_after'] = $data_after;
		$data['browser'] = $this->agent->browser();
		$data['ip'] = $this->input->ip_address();
		$data['platform'] = $this->agent->platform();
		$data['importance'] = $importance;
		$data['created_at'] = date('Y-m-d H:i:s');

		return $data;
	}

	// Універсальна фукція отримання даних
	public function action_ajax()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		switch ($this->input->post('name_private_method_controller')) {
			case 'get_subdivisions':
				$results = $this->get_subdivisions();
				break;
			case 'complete_renovation_objects':
				$results = $this->complete_renovation_objects();
				break;
			default:
				return $this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Відсутній приватний метод!'], JSON_UNESCAPED_UNICODE));
				break;
		}

		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Запрос виконано успішно!', 'response' => $results], JSON_UNESCAPED_UNICODE));
	}

	private function get_subdivisions()
	{
		return 'private function get_subdivisions()';
	}

	private function complete_renovation_objects()
	{
		return 'private function get_complete_renovation_objects()';
	}

	// Custom validation functions
	public function more_then_year_now($value)
	{
		if ($value > date('Y')) {
			$this->form_validation->set_message('more_then_year_now', 'Поле {field} не може бути більшим ніж ' . date('Y'));
			return FALSE;
		} else {
			return TRUE;
		}
	}

	// Trash ----------------------------------------------------------------------------------------------------
	public function gen_passport_object($id)
	{
		$data = [];

		$complete_renovation_object = $this->complete_renovation_object_model->get_row($id);

		$data['title'] = 'Паспорт ' . $complete_renovation_object->name;
		$data['content'] = 'passports/passport_object';
		$data['page'] = 'passports/passport_object';

		$passports = $this->passport_model->get_rows($id);

		$equipments_group = [];
		foreach ($passports as $row) {
			$row->color = '';
			switch ($row->insulation_type_id) {
				case 4:
					$row->color = 'red';
					break;
					// case 2:
					// 	$row->color = 'green';
					// 	break;
					// case 6:
					// 	$row->color = 'green';
					// 	break;
			}

			$equipments_group[$row->equipment_voltage][] = [
				'disp' => $row->disp,
				'place' => $row->place,
				'equipment' => $row->equipment . ' ' . $row->voltage / 1000 . ' кВ',
				'type' => $row->type,
				'number' => $row->number,
				'year' => date('Y', strtotime($row->production_date)),
				'insulation_type' => mb_strtolower($row->insulation_type),

				'id_w' => '5%',
				'disp_w' => '35%',
				'type_w' => '30%',
				'number_w' => '20%',
				'year_w' => '10%',

				'color' => $row->color,
			];
		}

		$data['complete_renovation_object'] = $complete_renovation_object;
		$data['results'] = $equipments_group;

		// echo "<pre>";
		// print_r($passports);
		// print_r($equipments_group);
		// echo "</pre>";
		// exit;

		$this->load->view('layout', $data);
	}

	// Trash ----------------------------------------------------------------------------------------------------
	public function gen_operating_list_object($id)
	{
		$data = [];

		$complete_renovation_object = $this->complete_renovation_object_model->get_row($id);

		$data['title'] = 'Експлуатаційна відомість ' . $complete_renovation_object->name;
		$data['content'] = 'passports/operating_list_object';
		$data['page'] = 'passports/operating_list_object';

		$operating_list = $this->operating_list_model->get_data_for_object($id);

		$data['complete_renovation_object'] = $complete_renovation_object;
		$data['results'] = $operating_list;

		// echo "<pre>";
		// print_r($operating_list);
		// print_r($equipments_group);
		// echo "</pre>";
		// exit;

		$this->load->view('layout', $data);
	}
}
