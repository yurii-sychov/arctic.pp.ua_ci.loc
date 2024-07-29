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

class Schedules extends CI_Controller
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
		$data['custom_button'][] = ['action' => 'addSpecificRepair(event)', 'title' => 'Додати специфічний ремонт', 'class' => 'btn-info'];
		if ($this->session->user->id == 1 || $this->session->user->id == 42) {
			$data['custom_button'][] = ['action' => 'genarate_multi_year_schedule_kr_excel(event)', 'title' => 'Багаторічний графік КР', 'class' => 'btn-danger'];
			$data['custom_button'][] = ['action' => 'genarate_multi_year_schedule_pr_excel(event)', 'title' => 'Багаторічний графік ПР', 'class' => 'btn-warning'];
			$data['custom_button'][] = ['action' => 'genarate_multi_year_schedule_to_excel(event)', 'title' => 'Багаторічний графік ТО', 'class' => 'btn-success'];
		}
		$data['custom_button'][] = ['action' => 'genarate_program_excel(event)', 'title' => 'Програма ремонту на ' . (date('Y') + 1) . ' рік', 'class' => 'btn-outline-dark'];
		$data['title'] = 'Планування ремонтної програми на ' . (date('Y') + 1) . ' рік';
		$data['content'] = 'schedules/index_dt';
		$data['page'] = 'schedules';
		$data['page_js'] = 'schedules';
		$data['datatables'] = TRUE;
		$data['pagination'] = FALSE;
		$data['title_heading'] = 'Планування ремонтної програми на ' . (date('Y') + 1) . ' рік';
		$data['title_heading_card'] = 'Річний план-графік на ' . (date('Y') + 1) . ' рік';

		$subdivisions = $this->subdivision_model->get_data_for_user();
		$data['subdivisions'] = $subdivisions;
		$data['complete_renovation_objects'] = [];

		if ($this->input->get('subdivision_id')) {
			$data['complete_renovation_objects'] = $this->complete_renovation_object_model->get_stantions_for_subdivision_and_user_for_schedules($this->input->get('subdivision_id'));
		}

		if ($this->input->get('subdivision_id') && $this->input->get('stantion_id')) {
			$data['complete_renovation_object'] = $this->complete_renovation_object_model->get_row($this->input->get('stantion_id'));
		}

		// Start Pagination
		if ($this->input->get('rows')) {
			$this->session->set_userdata('rows', $this->input->get('rows'));
		}
		$link = '/schedules/index/';
		$total_rows = $this->schedule_model->get_total_schedules_for_complete_renovation_object($this->input->get('subdivision_id'), $this->input->get('stantion_id'));

		$this->load->helper('config_pagination');
		$config = get_config_pagination($link, $total_rows);
		$this->pagination->initialize($config);

		$per_page = $this->session->userdata('rows') ? $this->session->userdata('rows') : $config['per_page'];
		$offset = $this->input->get('page') ? ($this->input->get('page') - 1) * $per_page : 0;
		$total_rows = $config['total_rows'];

		$data['per_page'] = !$this->input->get('page') ? 1 : (($this->input->get('page') - 1) * $per_page + 1);
		$data['offset'] = $this->input->get('page') ? $offset : $per_page;
		$data['total_rows'] = $total_rows;
		// End Pagination

		$data['workers'] = $this->worker_model->get_data();
		$data['technics'] = $this->technic_model->get_data();

		$equipments = $this->schedule_model->get_schedules_for_complete_renovation_object($this->input->get('subdivision_id'), $this->input->get('stantion_id'), $per_page, $offset, $data['pagination']);

		foreach ($equipments as $row) {
			$row->passports = $this->passport_model->get_passports($row->specific_renovation_object_id);
			foreach ($row->passports as $passport) {
				if ($passport->place_id == 1) {
					$passport->color = 'bg-warning text-dark';
				} else if ($passport->place_id == 2) {
					$passport->color = 'bg-success';
				} else if ($passport->place_id == 3) {
					$passport->color = 'bg-danger';
				} else {
					$passport->color = 'bg-primary';
				}

				if ($row->equipment_id == 3 and $passport->insulation_type_id == 2) {
					$row->equipment = 'Елегазовий вимикач';
				}
				if ($row->equipment_id == 3 and $passport->insulation_type_id == 3) {
					$row->equipment = 'Вакуумний вимикач';
				}
				if ($row->equipment_id == 3 and $passport->insulation_type_id == 4) {
					$row->equipment = 'Масляний вимикач';
				}
			}
			if (count($row->passports) > 1) {
				$row->equipment = $row->equipment_plural_name;
			}
			$row->materials = $this->schedule_material_model->get_materials_for_schedule_id($row->id);
			$row->materials_is_extra = array_filter($row->materials, function ($v, $k) {
				return $v->is_extra == 1;
			}, ARRAY_FILTER_USE_BOTH);
			$row->workers = $this->schedule_worker_model->get_workers_for_schedule_id($row->id);
			$row->workers_is_extra = array_filter($row->workers, function ($v, $k) {
				return $v->is_extra == 1;
			}, ARRAY_FILTER_USE_BOTH);
			$row->technics = $this->schedule_technic_model->get_technics_for_schedule_id($row->id);
			$row->technics_is_extra = array_filter($row->technics, function ($v, $k) {
				return $v->is_extra == 1;
			}, ARRAY_FILTER_USE_BOTH);
		}

		// foreach ($equipments as $key => $row) {
		// 	$equipment[$key]  = $row->equipment;
		// 	$type_service_id[$key]  = $row->type_service_id;
		// }
		// if (isset($equipment)) {
		// 	array_multisort($type_service_id, SORT_ASC, $equipment, SORT_ASC, $equipments);
		// }

		// $sort  = array_column($equipments, $this->input->get('field'));

		// if (count($sort) > 0 && $this->input->get('sort') === 'asc') {
		// 	array_multisort($sort, SORT_ASC, $equipments);
		// }
		// if (count($sort) > 0 && $this->input->get('sort') === 'desc') {
		// 	array_multisort($sort, SORT_DESC, $equipments);
		// }



		$data['equipments'] = $equipments;

		$data['avr_price'] = $this->avr_price_model->get_avr_price_for_year((date('Y') + 1));

		// echo "<pre>";
		// print_r($data['workers']);
		// print_r(uri_string());
		// echo "<br>";
		// print_r($_SERVER);
		// print_r($equipments);
		// print_r(count($sort));
		// echo "</pre>";

		$this->load->view('layout', $data);
	}

	public function genarate_schedule($subdivision_id = NULL, $complete_renovation_object_id = NULL)
	{
		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer') {
			show_404();
		}

		if (!is_numeric($subdivision_id) || !$subdivision_id || !is_numeric($complete_renovation_object_id) || !$complete_renovation_object_id) {
			show_404();
		}

		// $count_rows_next_year = $this->schedule_material_model->get_count_rows_next_year((date('Y') + 1));

		// if ($count_rows_next_year > 0) {
		// 	show_404();
		// }

		if ($subdivision_id == 1) {
			$data = $this->schedule_model->get_schedules_for_complete_renovation_object_sp($subdivision_id, $complete_renovation_object_id);
		} else {
			$data = $this->schedule_model->get_schedules_for_complete_renovation_object_srm($subdivision_id, $complete_renovation_object_id);
		}

		// Відфільтровуємо по полю is_repair = 1
		// $data = array_filter($data, function ($v) {
		// 	return $v->is_repair == 1;
		// });

		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// exit;
		// $data = array_filter($data, function ($v) {
		// 	return $v->year_repair <= (date('Y') + 1) || $v->will_add == 1;
		// });

		// Скидаємо таблицю schedules по полю is_repair та will_delete в нуль
		$id_specific_renovation_objects = $this->specific_renovation_object_model->get_id_for_complete_renovation_object($subdivision_id, $complete_renovation_object_id);

		foreach ($id_specific_renovation_objects as $row) {
			$id_schedules = $this->schedule_model->get_id_for_specific_renovation_object($row->id);

			// Видаляємо дані з таблиць ресурсів
			foreach ($id_schedules as $schedule_id) {
				// Видаляємо з таблиці materials_for_schedule записи по полю is_extra, що дорівнюює нулю
				$this->schedule_material_model->delete_materials_for_schedule($schedule_id->id, (date('Y') + 1));
				// Видаляємо з таблиці workers_for_schedule записи по полю is_extra_worker, що дорівнюює нулю
				$this->schedule_worker_model->delete_workers_for_schedule($schedule_id->id, (date('Y') + 1));
				// Видаляємо з таблиці technics_for_schedule записи по полю is_extra_technic, що дорівнюює нулю
				$this->schedule_technic_model->delete_technics_for_schedule($schedule_id->id, (date('Y') + 1));

				// $this->schedule_model->update_for_complete_renovation_object(['is_repair' => 0, 'will_delete' => 0], $schedule_id->id);
				$this->schedule_model->update_for_complete_renovation_object(['will_delete' => 0], $schedule_id->id);
				$this->schedule_year_model->delete_for_schedule_id_and_year($schedule_id->id, (date('Y') + 1));
				// $this->schedule_year_model->delete(['schedule_id' => $schedule_id->id, 'year_service' => (date('Y') + 1)]);
			}
		}

		foreach ($data as $row) {
			if ($row->year_repair == (date('Y') + 1) or $row->year_repair < (date('Y') + 1) or $row->will_add == 1) {
				// $this->schedule_model->update(['is_repair' => 1], $row->id);

				$data_year = [
					'schedule_id' => $row->id,
					'month_service' => $row->month,
					'year_service' => (date('Y') + 1),
					'is_contract_method' => $row->is_contract_method,
					'created_by' => $this->session->user->id,
					'updated_by' => $this->session->user->id,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				];
				$this->schedule_year_model->insert($data_year);

				$materials = $this->ciphers_material_model->get_data($row->cipher_id, (date('Y') + 1));
				foreach ($materials as $material) {
					$data_material = [
						'schedule_id' => $row->id,
						'material_id' => $material->material_id,
						'quantity' => $material->quantity * $row->amount,
						'year_service' => (date('Y') + 1),
						'is_extra' => 0,
						'created_by' => $this->session->user->id,
						'updated_by' => $this->session->user->id,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s')
					];
					if (!$this->schedule_material_model->get_is_material($row->id, $material->material_id, (date('Y') + 1))) {
						$this->schedule_material_model->insert($data_material);
					}
				}

				$workers = $this->ciphers_worker_model->get_data($row->cipher_id, (date('Y') + 1));

				// echo "<pre>";
				// print_r($workers);
				// echo "</pre>";
				// exit;

				$i = 1;
				foreach ($workers as $worker) {
					$data_worker = [
						'schedule_id' => $row->id,
						'worker_id' => $worker->worker_id,
						'quantity' => $worker->quantity * $row->amount,
						'year_service' => (date('Y') + 1),
						'is_extra' => 0,
						'count' => $i,
						'created_by' => $this->session->user->id,
						'updated_by' => $this->session->user->id,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s')
					];
					if (!$this->schedule_worker_model->get_is_worker($row->id, $worker->worker_id, (date('Y') + 1), $i)) {
						$this->schedule_worker_model->insert($data_worker);
					}
					$i++;
				}

				$technics = $this->ciphers_technic_model->get_data($row->cipher_id, (date('Y') + 1));
				foreach ($technics as $technic) {
					$data_technic = [
						'schedule_id' => $row->id,
						'technic_id' => $technic->technic_id,
						'quantity' => $technic->quantity * $row->amount,
						'year_service' => (date('Y') + 1),
						'is_extra' => 0,
						'created_by' => $this->session->user->id,
						'updated_by' => $this->session->user->id,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s')
					];
					if (!$this->schedule_technic_model->get_is_technic($row->id, $technic->technic_id, (date('Y') + 1))) {
						$this->schedule_technic_model->insert($data_technic);
					}
				}
			}

			if ($row->year_repair == date('Y')) {
				$this->schedule_model->update(['will_delete' => 1], $row->id);
			}

			// -----------------------------------------------------------------------------------------------------------------
			// if ($row->type_service_id == 2 && (((date('Y') + 1) - $row->year_repair) < 3 && $row->year_repair != (date('Y') + 1))) {
			// 	$data_kr = $this->schedule_model->get_row_kr($row->specific_renovation_object_id);

			// 	if (isset($data_kr) && ($data_kr->periodicity + $data_kr->year_last_service) < (date('Y') + 1)) {
			// 		$kr_id = $data_kr->id;
			// 		$this->schedule_material_model->delete_for_schedule_id_and_year($kr_id, (date('Y') + 1));
			// 		$this->schedule_worker_model->delete_for_schedule_id_and_year($kr_id, (date('Y') + 1));
			// 		$this->schedule_technic_model->delete_for_schedule_id_and_year($kr_id, (date('Y') + 1));
			// 		$this->schedule_year_model->delete_for_schedule_id_and_year($kr_id, (date('Y') + 1));
			// 	}
			// }
			// -----------------------------------------------------------------------------------------------------------------

			if ($row->type_service_id != 1 && $this->schedule_model->get_row_kr($row->specific_renovation_object_id) && $row->will_add == 0) {
				// $this->schedule_model->update(['is_repair' => 0], $row->id);
				$this->schedule_material_model->delete_for_schedule_id_and_year($row->id, (date('Y') + 1));
				$this->schedule_worker_model->delete_for_schedule_id_and_year($row->id, (date('Y') + 1));
				$this->schedule_technic_model->delete_for_schedule_id_and_year($row->id, (date('Y') + 1));
				$this->schedule_year_model->delete_for_schedule_id_and_year($row->id, (date('Y') + 1));
				// $this->schedule_year_model->delete(['schedule_id' => $row->id, 'repair_year' => (date('Y') + 1)]);
			}
		}

		redirect('/schedules/index/?subdivision_id=' . $this->uri->segment(3) . '&stantion_id=' . $this->uri->segment(4));
	}

	public function delete($id)
	{
		if (!is_numeric($id)) {
			show_404();
		}

		$this->load->library('user_agent');

		$this->schedule_material_model->delete_for_schedule_id_and_year($id, (date('Y') + 1));
		$this->schedule_worker_model->delete_for_schedule_id_and_year($id, (date('Y') + 1));
		$this->schedule_technic_model->delete_for_schedule_id_and_year($id, (date('Y') + 1));
		$this->schedule_year_model->delete_for_schedule_id_and_year($id, (date('Y') + 1));

		// $this->schedule_model->update(['is_repair' => 0, 'will_delete' => 0], $id);
		$this->schedule_model->update(['will_delete' => 0], $id);

		// $this->schedule_year_model->delete(['schedule_id' => $id, 'repair_year' => (date('Y') + 1)]);

		redirect($this->agent->referrer());
	}

	public function add_material_ajax()
	{
		$this->output->set_content_type('application/json');

		$this->load->library('form_validation');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		// print_r($this->input->post());

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->form_validation->set_rules('material_id[]', 'Матеріал', 'required');

		if ($this->form_validation->run() == FALSE) {
			$this->form_validation->set_error_delimiters('', '');
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => form_error('material_id[]')], JSON_UNESCAPED_UNICODE));
			return;
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$data = $this->set_schedule_materials_add_data($this->input->post());

		$result = $this->schedule_material_model->add_data_batch($data);


		// if ($result) {
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані додано!', 'result' => $result], JSON_UNESCAPED_UNICODE));
		return;
		// }

		// $this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Щось пішло не так!'], JSON_UNESCAPED_UNICODE));
		// return;
	}

	public function edit_material_quantity_ajax()
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

		$this->schedule_material_model->change_quantity('quantity', $this->input->post('value'), $this->input->post('schedule_id'), $this->input->post('material_id'), $this->input->post('year_service'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function edit_worker_quantity_ajax()
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

		$this->schedule_worker_model->change_quantity('quantity', $this->input->post('value'), $this->input->post('schedule_id'), $this->input->post('worker_id'), $this->input->post('year_service'), $this->input->post('count'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function edit_technic_quantity_ajax()
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

		$this->schedule_technic_model->change_quantity('quantity', $this->input->post('value'), $this->input->post('schedule_id'), $this->input->post('technic_id'), $this->input->post('year_service'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function edit_month_ajax()
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

		$this->schedule_model->change_value('month', $this->input->post('value'), $this->input->post('id'));
		$this->schedule_year_model->change_value('month_service', $this->input->post('value'), $this->input->post('id'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function delete_material_ajax()
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

		$this->schedule_material_model->delete_material($this->input->post('schedule_id'), $this->input->post('material_id'), $this->input->post('year_service'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані видалено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function delete_worker_ajax()
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

		$this->schedule_worker_model->delete_worker($this->input->post('schedule_id'), $this->input->post('worker_id'), $this->input->post('year_service'), $this->input->post('count'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані видалено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function delete_technic_ajax()
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

		$this->schedule_technic_model->delete_technic($this->input->post('schedule_id'), $this->input->post('technic_id'), $this->input->post('year_service'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані видалено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function genarate_defect_list_excel($stantion_id)
	{
		$stantion = $this->complete_renovation_object_model->get_row($stantion_id);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Відомість дефектів та витрат на ' . (date('Y') + 1) . ' для ' . $stantion->name . '.xlsx"');
		header('Cache-Control: max-age=0');

		$spreadsheet = new Spreadsheet();

		$worksheet_all = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Всі види ремонтів');
		$spreadsheet->addSheet($worksheet_all, 0);
		$worksheet_kr = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Капітальний ремонт');
		$spreadsheet->addSheet($worksheet_kr, 1);
		$worksheet_pr = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Поточний ремонт');
		$spreadsheet->addSheet($worksheet_pr, 2);
		$worksheet_to = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Технічне обслуговування');
		$spreadsheet->addSheet($worksheet_to, 3);

		$sheetIndex = $spreadsheet->getIndex($spreadsheet->getSheetByName('Worksheet'));
		$spreadsheet->removeSheetByIndex($sheetIndex);

		$spreadsheet->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
		$spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
		$spreadsheet->getDefaultStyle()->getFont()->setSize(10);

		$type_services_name = '';
		for ($sheet = 0; $sheet <= 3; $sheet++) {
			if ($sheet == 0) {
				$type_service_id = NULL;
				$type_services_name = 'ремонтів';
				$workers = $this->get_workers($stantion_id, NULL);
				$materials = $this->get_materials($stantion_id, NULL);
			}
			if ($sheet == 1) {
				$type_service_id = 1;
				$type_services_name = 'капітального ремонту';
				$workers = $this->get_workers($stantion_id, 1);
				$materials = $this->get_materials($stantion_id, 1);
			}
			if ($sheet == 2) {
				$type_service_id = 2;
				$type_services_name = 'поточного ремонту';
				$workers = $this->get_workers($stantion_id, 2);
				$materials = $this->get_materials($stantion_id, 2);
			}
			if ($sheet == 3) {
				$type_service_id = 3;
				$type_services_name = 'технічного обслуговування';
				$workers = $this->get_workers($stantion_id, 3);
				$materials = $this->get_materials($stantion_id, 3);
			}

			$active_sheet = $spreadsheet->setActiveSheetIndex($sheet);

			$active_sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
			$active_sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
			$active_sheet->getPageSetup()->setFitToPage(false)->setScale(40);

			$active_sheet->getPageMargins()->setTop(0.392);
			$active_sheet->getPageMargins()->setRight(0.392);
			$active_sheet->getPageMargins()->setLeft(0.784);
			$active_sheet->getPageMargins()->setBottom(0.392);

			$active_sheet->getSheetView()->setZoomScale(75);

			$active_sheet->getColumnDimension('A')->setWidth(10);
			$active_sheet->getColumnDimension('B')->setWidth(50);
			$active_sheet->getColumnDimension('C')->setWidth(16);
			$active_sheet->getColumnDimension('D')->setWidth(80);
			$active_sheet->getColumnDimension('E')->setWidth(15);
			$active_sheet->getColumnDimension('F')->setWidth(10);
			$active_sheet->getColumnDimension('G')->setWidth(10);
			$active_sheet->getColumnDimension('H')->setWidth(10);
			$active_sheet->getColumnDimension('I')->setWidth(10);
			$active_sheet->getColumnDimension('J')->setWidth(10);
			$active_sheet->getColumnDimension('K')->setWidth(10);
			$active_sheet->getColumnDimension('L')->setWidth(10);
			$active_sheet->getColumnDimension('M')->setWidth(10);
			$active_sheet->getColumnDimension('N')->setWidth(10);
			$active_sheet->getColumnDimension('O')->setWidth(10);
			$active_sheet->getColumnDimension('P')->setWidth(10);
			$active_sheet->getColumnDimension('Q')->setWidth(10);
			$active_sheet->getColumnDimension('R')->setWidth(10);
			$active_sheet->getColumnDimension('S')->setWidth(10);
			$active_sheet->getColumnDimension('T')->setWidth(10);
			$active_sheet->getColumnDimension('U')->setWidth(10);
			$active_sheet->getColumnDimension('V')->setWidth(10);
			$active_sheet->getColumnDimension('W')->setWidth(10);
			$active_sheet->getColumnDimension('X')->setWidth(10);
			$active_sheet->getColumnDimension('Y')->setWidth(10);

			$active_sheet->getRowDimension('1')->setRowHeight(20);
			$active_sheet->getRowDimension('2')->setRowHeight(20);
			$active_sheet->getRowDimension('3')->setRowHeight(20);
			$active_sheet->getRowDimension('4')->setRowHeight(40);
			$active_sheet->getRowDimension('5')->setRowHeight(20);
			$active_sheet->getRowDimension('6')->setRowHeight(20);

			$active_sheet->mergeCells('A1:Y1')->setCellValue('A1', 'ВІДОМІСТЬ')->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$active_sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
			$active_sheet->getStyle('A2')->getFont()->setSize(14);
			$active_sheet->getStyle('A3')->getFont()->setSize(14);

			$active_sheet->mergeCells('A2:Y2')->setCellValue('A2', 'дефектів та витрат на виконання ' . $type_services_name . ' по СП')->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$active_sheet->mergeCells('A3:Y3')->setCellValue('A3', $stantion->name)->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

			$active_sheet->getStyle('A4:Y8')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('26EDE0');
			$active_sheet->getStyle('A4:Y8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setWrapText(true);
			$active_sheet->getStyle('A4:Y8')->getFont()->setBold(true);
			$active_sheet->getStyle('A4:Y8')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);

			$active_sheet->mergeCells('A4:A5')->setCellValue('A4', '№ п/п')->setCellValue('A6', '1');
			$active_sheet->mergeCells('B4:B5')->setCellValue('B4', 'Найменування обладнання')->setCellValue('B6', '2');
			$active_sheet->mergeCells('C4:C5')->setCellValue('C4', 'Дисп. назва')->setCellValue('C6', '3');
			$active_sheet->mergeCells('D4:D5')->setCellValue('D4', 'Перелік робіт')->setCellValue('D6', '4');
			$active_sheet->mergeCells('E4:E5')->setCellValue('E4', 'Шифр роботи')->setCellValue('E6', '5');
			$active_sheet->mergeCells('F4:F5')->setCellValue('F4', 'Од. вим.')->setCellValue('F6', '6');
			$active_sheet->mergeCells('G4:G5')->setCellValue('G4', 'План на рік')->setCellValue('G6', '7');
			$active_sheet->mergeCells('H4:I4')->setCellValue('H4', 'Норма часу, л/год.');
			$active_sheet->setCellValue('H5', 'од.')->setCellValue('H6', '8');
			$active_sheet->setCellValue('I5', 'всього')->setCellValue('I6', '9');
			$active_sheet->mergeCells('J4:J5')->setCellValue('J4', 'План на' . "\n" . 'І кв.')->setCellValue('J6', '10');
			$active_sheet->mergeCells('K4:M4')->setCellValue('K4', 'в тому числі по місяцям');
			$active_sheet->setCellValue('K5', 'січ.')->setCellValue('K6', '11');
			$active_sheet->setCellValue('L5', 'лют.')->setCellValue('L6', '12');
			$active_sheet->setCellValue('M5', 'бер.')->setCellValue('M6', '13');
			$active_sheet->mergeCells('N4:N5')->setCellValue('N4', 'План на' . "\n" . 'ІІ кв.')->setCellValue('N6', '14');
			$active_sheet->mergeCells('O4:Q4')->setCellValue('O4', 'в тому числі по місяцям');
			$active_sheet->setCellValue('O5', 'квіт.')->setCellValue('O6', '15');
			$active_sheet->setCellValue('P5', 'трав.')->setCellValue('P6', '16');
			$active_sheet->setCellValue('Q5', 'черв.')->setCellValue('Q6', '17');
			$active_sheet->mergeCells('R4:R5')->setCellValue('R4', 'План на' . "\n" . 'ІІІ кв.')->setCellValue('R6', '18');
			$active_sheet->mergeCells('S4:U4')->setCellValue('S4', 'в тому числі по місяцям');
			$active_sheet->setCellValue('S5', 'лип.')->setCellValue('S6', '19');
			$active_sheet->setCellValue('T5', 'серп.')->setCellValue('T6', '20');
			$active_sheet->setCellValue('U5', 'вер.')->setCellValue('U6', '21');
			$active_sheet->mergeCells('V4:V5')->setCellValue('V4', 'План на' . "\n" . 'ІV кв.')->setCellValue('V6', '22');
			$active_sheet->mergeCells('W4:Y4')->setCellValue('W4', 'в тому числі по місяцям');
			$active_sheet->setCellValue('W5', 'жовт.')->setCellValue('W6', '23');
			$active_sheet->setCellValue('X5', 'лист.')->setCellValue('X6', '24');
			$active_sheet->setCellValue('Y5', 'груд.')->setCellValue('Y6', '25');

			$active_sheet->mergeCells('A7:A8')->setCellValue('A7', '№ п/п');
			$active_sheet->mergeCells('B7:B8')->setCellValue('B7', 'Найменування обладнання');
			$active_sheet->mergeCells('C7:C8')->setCellValue('C7', 'Кіл-ть');
			$active_sheet->mergeCells('D7:D8')->setCellValue('D7', 'Перелік матеріалів та обладнання');
			$active_sheet->mergeCells('E7:E8')->setCellValue('E7', 'Номер R3');
			$active_sheet->mergeCells('F7:F8')->setCellValue('F7', 'Од. вим.');
			$active_sheet->mergeCells('G7:G8')->setCellValue('G7', 'План на рік');
			$active_sheet->mergeCells('H7:I7')->setCellValue('H7', 'Вартість, тис. грн.');
			$active_sheet->setCellValue('H8', 'од.');
			$active_sheet->setCellValue('I8', 'всього');
			$active_sheet->mergeCells('J7:J8')->setCellValue('J7', 'План на' . "\n" . 'І кв.,' . "\n" . 'фіз. од');
			$active_sheet->mergeCells('K7:M7')->setCellValue('K7', 'в тому числі по місяцям');
			$active_sheet->setCellValue('K8', 'січ.');
			$active_sheet->setCellValue('L8', 'лют.');
			$active_sheet->setCellValue('M8', 'бер.');
			$active_sheet->mergeCells('N7:N8')->setCellValue('N7', 'План на' . "\n" . 'ІІ кв.,' . "\n" . 'фіз. од');
			$active_sheet->mergeCells('O7:Q7')->setCellValue('O7', 'в тому числі по місяцям');
			$active_sheet->setCellValue('O8', 'квіт.');
			$active_sheet->setCellValue('P8', 'трав.');
			$active_sheet->setCellValue('Q8', 'черв.');
			$active_sheet->mergeCells('R7:R8')->setCellValue('R7', 'План на' . "\n" . 'ІІІ кв.,' . "\n" . 'фіз. од');
			$active_sheet->mergeCells('S7:U7')->setCellValue('S7', 'в тому числі по місяцям');
			$active_sheet->setCellValue('S8', 'лип.');
			$active_sheet->setCellValue('T8', 'серп.');
			$active_sheet->setCellValue('U8', 'вер.');
			$active_sheet->mergeCells('V7:V8')->setCellValue('V7', 'План на' . "\n" . 'ІV кв.,' . "\n" . 'фіз. од');
			$active_sheet->mergeCells('W7:Y7')->setCellValue('W7', 'в тому числі по місяцям');
			$active_sheet->setCellValue('W8', 'жовт.');
			$active_sheet->setCellValue('X8', 'лист.');
			$active_sheet->setCellValue('Y8', 'груд.');

			if (count($workers) > 0) {
				$active_sheet->insertNewRowBefore(7, count($workers));
			}

			$i = 1;
			foreach ($workers as $row) {
				$active_sheet->getStyle('A' . (6 + $i) . ':Y' . (6 + $i))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');
				$active_sheet->getStyle('A' . (6 + $i) . ':Y' . (6 + $i))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setWrapText(false);
				$active_sheet->getStyle('B' . (6 + $i))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)->setWrapText(false);
				$active_sheet->getStyle('D' . (6 + $i))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)->setWrapText(false);
				$active_sheet->getStyle('A' . (6 + $i) . ':Y' . (6 + $i))->getFont()->setBold(false);
				$active_sheet->getStyle('A' . (6 + $i) . ':Y' . (6 + $i))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

				$active_sheet->getRowDimension(6 + $i)->setRowHeight(12.75);

				$active_sheet->setCellValue('A' . (6 + $i), $row['count']);
				$active_sheet->setCellValue('B' . (6 + $i), $row['oborud_with_voltage']);
				$active_sheet->setCellValue('C' . (6 + $i), $row['disp']);
				$active_sheet->setCellValue('D' . (6 + $i), $row['list_works']);
				$active_sheet->setCellValue('E' . (6 + $i), $row['cipher']);
				$active_sheet->setCellValue('F' . (6 + $i), $row['unit']);
				$active_sheet->setCellValue('G' . (6 + $i), $row['quantity_equipment']);
				$active_sheet->setCellValue('H' . (6 + $i), array_sum($row['norma']));
				$active_sheet->setCellValue('I' . (6 + $i), $row['norma_total']);
				$active_sheet->setCellValue('J' . (6 + $i), $row['quantity_quarter_1']);
				$active_sheet->setCellValue('K' . (6 + $i), $row['quantity_month_1']);
				$active_sheet->setCellValue('L' . (6 + $i), $row['quantity_month_2']);
				$active_sheet->setCellValue('M' . (6 + $i), $row['quantity_month_3']);
				$active_sheet->setCellValue('N' . (6 + $i), $row['quantity_quarter_2']);
				$active_sheet->setCellValue('O' . (6 + $i), $row['quantity_month_4']);
				$active_sheet->setCellValue('P' . (6 + $i), $row['quantity_month_5']);
				$active_sheet->setCellValue('Q' . (6 + $i), $row['quantity_month_6']);
				$active_sheet->setCellValue('R' . (6 + $i), $row['quantity_quarter_3']);
				$active_sheet->setCellValue('S' . (6 + $i), $row['quantity_month_7']);
				$active_sheet->setCellValue('T' . (6 + $i), $row['quantity_month_8']);
				$active_sheet->setCellValue('U' . (6 + $i), $row['quantity_month_9']);
				$active_sheet->setCellValue('V' . (6 + $i), $row['quantity_quarter_4']);
				$active_sheet->setCellValue('W' . (6 + $i), $row['quantity_month_10']);
				$active_sheet->setCellValue('X' . (6 + $i), $row['quantity_month_11']);
				$active_sheet->setCellValue('Y' . (6 + $i), $row['quantity_month_12']);

				$active_sheet->getStyle('H' . (6 + $i))->getNumberFormat()->setFormatCode('0.00');
				$active_sheet->getStyle('I' . (6 + $i))->getNumberFormat()->setFormatCode('0.00');
				$i++;
			}

			$active_sheet->insertNewRowBefore((6 + $i), 1);
			$active_sheet->setCellValue('B' . (6 + $i), 'Всього по роботам');
			$active_sheet->setCellValue('F' . (6 + $i), 'шт');
			//************************************************************************************************* 2024-04-16 Зміна 5 на 6 в формулі СУММ */
			$active_sheet->setCellValue('G' . (6 + $i), '=SUM(G7:G' . (6 + $i - 1) . ')');
			for ($column = 'I'; $column < 'Z'; $column++) {
				$active_sheet->setCellValue($column . (6 + $i), '=SUM(' . $column . '7:' . $column . (6 + $i - 1) . ')');
			}

			$active_sheet->getStyle('A' . (6 + $i) . ':Y' . (6 + $i))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');
			$active_sheet->getStyle('A' . (6 + $i) . ':Y' . (6 + $i))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setWrapText(false);
			$active_sheet->getStyle('B' . (6 + $i))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)->setWrapText(false);
			$active_sheet->getStyle('A' . (6 + $i) . ':Y' . (6 + $i))->getFont()->setBold(true);
			$active_sheet->getStyle('A' . (6 + $i) . ':Y' . (6 + $i))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);

			$active_sheet->getRowDimension($i + 7)->setRowHeight(40);
			$active_sheet->getRowDimension($i + 8)->setRowHeight(20);

			foreach ($materials as $row) {
				$active_sheet->setCellValue('A' . (9 + $i), $row['number']);
				$active_sheet->setCellValue('B' . (9 + $i), $row['equipment']);
				$active_sheet->setCellValue('C' . (9 + $i), $row['equipment_quantity']);

				foreach ($row['material'] as $k => $item) {
					$active_sheet->getStyle('A' . (9 + $i) . ':Y' . (9 + $i))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');
					$active_sheet->getStyle('A' . (9 + $i) . ':Y' . (9 + $i))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setWrapText(false);
					$active_sheet->getStyle('B' . (9 + $i))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)->setWrapText(false);
					$active_sheet->getStyle('D' . (9 + $i))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)->setWrapText(false);
					$active_sheet->getStyle('A' . (9 + $i) . ':Y' . (9 + $i))->getFont()->setBold(false);
					$active_sheet->getStyle('A' . (9 + $i) . ':Y' . (9 + $i))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

					if ($row['is_extra'][$k]) {
						// 		$active_sheet->getStyle('D' . (9 + $i))->getFont()->setBold(true);
						// 		$active_sheet->getStyle('D' . (9 + $i))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('C0C0C0');
					}

					$active_sheet->getRowDimension(9 + $i)->setRowHeight(12.75);

					$active_sheet->setCellValue('D' . (9 + $i), $row['material'][$k]);
					$active_sheet->setCellValue('E' . (9 + $i), $row['number_r3'][$k]);
					$active_sheet->setCellValue('F' . (9 + $i), $row['unit'][$k]);
					$active_sheet->setCellValue('G' . (9 + $i), $row['quantity'][$k]);
					$active_sheet->setCellValue('H' . (9 + $i), $row['price_no_vat'][$k]);
					$active_sheet->setCellValue('I' . (9 + $i), $row['price_total_no_vat'][$k]);
					$active_sheet->setCellValue('J' . (9 + $i), isset($row['quantity_quarter_1'][$k]) ? $row['quantity_quarter_1'][$k] : '');
					$active_sheet->setCellValue('K' . (9 + $i), isset($row['quantity_month_1'][$k]) ? $row['quantity_month_1'][$k] : '');
					$active_sheet->setCellValue('L' . (9 + $i), isset($row['quantity_month_2'][$k]) ? $row['quantity_month_2'][$k] : '');
					$active_sheet->setCellValue('M' . (9 + $i), isset($row['quantity_month_3'][$k]) ? $row['quantity_month_3'][$k] : '');
					$active_sheet->setCellValue('N' . (9 + $i), isset($row['quantity_quarter_2'][$k]) ? $row['quantity_quarter_2'][$k] : '');
					$active_sheet->setCellValue('O' . (9 + $i), isset($row['quantity_month_4'][$k]) ? $row['quantity_month_4'][$k] : '');
					$active_sheet->setCellValue('P' . (9 + $i), isset($row['quantity_month_5'][$k]) ? $row['quantity_month_5'][$k] : '');
					$active_sheet->setCellValue('Q' . (9 + $i), isset($row['quantity_month_6'][$k]) ? $row['quantity_month_6'][$k] : '');
					$active_sheet->setCellValue('R' . (9 + $i), isset($row['quantity_quarter_3'][$k]) ? $row['quantity_quarter_3'][$k] : '');
					$active_sheet->setCellValue('S' . (9 + $i), isset($row['quantity_month_7'][$k]) ? $row['quantity_month_7'][$k] : '');
					$active_sheet->setCellValue('T' . (9 + $i), isset($row['quantity_month_8'][$k]) ? $row['quantity_month_8'][$k] : '');
					$active_sheet->setCellValue('U' . (9 + $i), isset($row['quantity_month_9'][$k]) ? $row['quantity_month_9'][$k] : '');
					$active_sheet->setCellValue('V' . (9 + $i), isset($row['quantity_quarter_4'][$k]) ? $row['quantity_quarter_4'][$k] : '');
					$active_sheet->setCellValue('W' . (9 + $i), isset($row['quantity_month_10'][$k]) ? $row['quantity_month_10'][$k] : '');
					$active_sheet->setCellValue('X' . (9 + $i), isset($row['quantity_month_11'][$k]) ? $row['quantity_month_11'][$k] : '');
					$active_sheet->setCellValue('Y' . (9 + $i), isset($row['quantity_month_12'][$k]) ? $row['quantity_month_12'][$k] : '');

					$active_sheet->getStyle('G' . (9 + $i))->getNumberFormat()->setFormatCode('0.00');
					$active_sheet->getStyle('H' . (9 + $i) . ':I' . (9 + $i))->getNumberFormat()->setFormatCode('0.0000');
					$active_sheet->getStyle('J' . (9 + $i) . ':Y' . (9 + $i))->getNumberFormat()->setFormatCode('0.00');
					$i++;
				}
			}

			$active_sheet->getStyle('A' . (9 + $i) . ':Y' . (12 + $i))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');
			$active_sheet->getStyle('A' . (9 + $i) . ':Y' . (12 + $i))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setWrapText(false);
			$active_sheet->getStyle('B' . (9 + $i) . ':B' . (12 + $i))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)->setWrapText(false);
			$active_sheet->getStyle('A' . (9 + $i) . ':Y' . (12 + $i))->getFont()->setBold(true);
			$active_sheet->getStyle('A' . (9 + $i) . ':Y' . (12 + $i))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);

			$active_sheet->setCellValue('B' . (9 + $i), 'Всього матеріали та обладнання');
			$active_sheet->setCellValue('B' . (10 + $i), 'Всього витрати на заробітну плату');
			$active_sheet->setCellValue('B' . (11 + $i), 'Всього витрати на машини та спец.механізми');
			$active_sheet->setCellValue('B' . (12 + $i), 'Всього по об`єкту');

			$active_sheet->setCellValue('C' . (9 + $i), 'тис.грн');
			$active_sheet->setCellValue('C' . (10 + $i), 'тис.грн');
			$active_sheet->setCellValue('C' . (11 + $i), 'тис.грн');
			$active_sheet->setCellValue('C' . (12 + $i), 'тис.грн');


			$materials_summa = $this->schedule_material_model->get_summa($stantion_id, $type_service_id) / 1000;
			$workers_summa = $this->schedule_worker_model->get_summa($stantion_id, $type_service_id) / 1000;
			$technics_summa = $this->schedule_technic_model->get_summa($stantion_id, $type_service_id) / 1000;
			$active_sheet->setCellValue('I' . (9 + $i), $materials_summa);
			$active_sheet->setCellValue('I' . (10 + $i), $workers_summa);
			$active_sheet->setCellValue('I' . (11 + $i), $technics_summa);
			$active_sheet->setCellValue('I' . (12 + $i), $materials_summa + $workers_summa + $technics_summa);

			$materials_summa_quarter_1 = $this->schedule_material_model->get_summa($stantion_id, $type_service_id, NULL, 1) / 1000;
			$materials_summa_quarter_2 = $this->schedule_material_model->get_summa($stantion_id, $type_service_id, NULL, 2) / 1000;
			$materials_summa_quarter_3 = $this->schedule_material_model->get_summa($stantion_id, $type_service_id, NULL, 3) / 1000;
			$materials_summa_quarter_4 = $this->schedule_material_model->get_summa($stantion_id, $type_service_id, NULL, 4) / 1000;
			$active_sheet->setCellValue('J' . (9 + $i), $materials_summa_quarter_1);
			$active_sheet->setCellValue('N' . (9 + $i), $materials_summa_quarter_2);
			$active_sheet->setCellValue('R' . (9 + $i), $materials_summa_quarter_3);
			$active_sheet->setCellValue('V' . (9 + $i), $materials_summa_quarter_4);

			$workers_summa_quarter_1 = $this->schedule_worker_model->get_summa($stantion_id, $type_service_id, NULL, 1) / 1000;
			$workers_summa_quarter_2 = $this->schedule_worker_model->get_summa($stantion_id, $type_service_id, NULL, 2) / 1000;
			$workers_summa_quarter_3 = $this->schedule_worker_model->get_summa($stantion_id, $type_service_id, NULL, 3) / 1000;
			$workers_summa_quarter_4 = $this->schedule_worker_model->get_summa($stantion_id, $type_service_id, NULL, 4) / 1000;
			$active_sheet->setCellValue('J' . (10 + $i), $workers_summa_quarter_1);
			$active_sheet->setCellValue('N' . (10 + $i), $workers_summa_quarter_2);
			$active_sheet->setCellValue('R' . (10 + $i), $workers_summa_quarter_3);
			$active_sheet->setCellValue('V' . (10 + $i), $workers_summa_quarter_4);

			$technics_summa_quarter_1 = $this->schedule_technic_model->get_summa($stantion_id, $type_service_id, NULL, 1) / 1000;
			$technics_summa_quarter_2 = $this->schedule_technic_model->get_summa($stantion_id, $type_service_id, NULL, 2) / 1000;
			$technics_summa_quarter_3 = $this->schedule_technic_model->get_summa($stantion_id, $type_service_id, NULL, 3) / 1000;
			$technics_summa_quarter_4 = $this->schedule_technic_model->get_summa($stantion_id, $type_service_id, NULL, 4) / 1000;
			$active_sheet->setCellValue('J' . (11 + $i), $technics_summa_quarter_1);
			$active_sheet->setCellValue('N' . (11 + $i), $technics_summa_quarter_2);
			$active_sheet->setCellValue('R' . (11 + $i), $technics_summa_quarter_3);
			$active_sheet->setCellValue('V' . (11 + $i), $technics_summa_quarter_4);

			$materials_summa_month_1 = $this->schedule_material_model->get_summa($stantion_id, $type_service_id, 1) / 1000;
			$materials_summa_month_2 = $this->schedule_material_model->get_summa($stantion_id, $type_service_id, 2) / 1000;
			$materials_summa_month_3 = $this->schedule_material_model->get_summa($stantion_id, $type_service_id, 3) / 1000;
			$materials_summa_month_4 = $this->schedule_material_model->get_summa($stantion_id, $type_service_id, 4) / 1000;
			$materials_summa_month_5 = $this->schedule_material_model->get_summa($stantion_id, $type_service_id, 5) / 1000;
			$materials_summa_month_6 = $this->schedule_material_model->get_summa($stantion_id, $type_service_id, 6) / 1000;
			$materials_summa_month_7 = $this->schedule_material_model->get_summa($stantion_id, $type_service_id, 7) / 1000;
			$materials_summa_month_8 = $this->schedule_material_model->get_summa($stantion_id, $type_service_id, 8) / 1000;
			$materials_summa_month_9 = $this->schedule_material_model->get_summa($stantion_id, $type_service_id, 9) / 1000;
			$materials_summa_month_10 = $this->schedule_material_model->get_summa($stantion_id, $type_service_id, 10) / 1000;
			$materials_summa_month_11 = $this->schedule_material_model->get_summa($stantion_id, $type_service_id, 11) / 1000;
			$materials_summa_month_12 = $this->schedule_material_model->get_summa($stantion_id, $type_service_id, 12) / 1000;
			$active_sheet->setCellValue('K' . (9 + $i), $materials_summa_month_1);
			$active_sheet->setCellValue('L' . (9 + $i), $materials_summa_month_2);
			$active_sheet->setCellValue('M' . (9 + $i), $materials_summa_month_3);
			$active_sheet->setCellValue('O' . (9 + $i), $materials_summa_month_4);
			$active_sheet->setCellValue('P' . (9 + $i), $materials_summa_month_5);
			$active_sheet->setCellValue('Q' . (9 + $i), $materials_summa_month_6);
			$active_sheet->setCellValue('S' . (9 + $i), $materials_summa_month_7);
			$active_sheet->setCellValue('T' . (9 + $i), $materials_summa_month_8);
			$active_sheet->setCellValue('U' . (9 + $i), $materials_summa_month_9);
			$active_sheet->setCellValue('W' . (9 + $i), $materials_summa_month_10);
			$active_sheet->setCellValue('X' . (9 + $i), $materials_summa_month_11);
			$active_sheet->setCellValue('Y' . (9 + $i), $materials_summa_month_12);

			$workers_summa_month_1 = $this->schedule_worker_model->get_summa($stantion_id, $type_service_id, 1) / 1000;
			$workers_summa_month_2 = $this->schedule_worker_model->get_summa($stantion_id, $type_service_id, 2) / 1000;
			$workers_summa_month_3 = $this->schedule_worker_model->get_summa($stantion_id, $type_service_id, 3) / 1000;
			$workers_summa_month_4 = $this->schedule_worker_model->get_summa($stantion_id, $type_service_id, 4) / 1000;
			$workers_summa_month_5 = $this->schedule_worker_model->get_summa($stantion_id, $type_service_id, 5) / 1000;
			$workers_summa_month_6 = $this->schedule_worker_model->get_summa($stantion_id, $type_service_id, 6) / 1000;
			$workers_summa_month_7 = $this->schedule_worker_model->get_summa($stantion_id, $type_service_id, 7) / 1000;
			$workers_summa_month_8 = $this->schedule_worker_model->get_summa($stantion_id, $type_service_id, 8) / 1000;
			$workers_summa_month_9 = $this->schedule_worker_model->get_summa($stantion_id, $type_service_id, 9) / 1000;
			$workers_summa_month_10 = $this->schedule_worker_model->get_summa($stantion_id, $type_service_id, 10) / 1000;
			$workers_summa_month_11 = $this->schedule_worker_model->get_summa($stantion_id, $type_service_id, 11) / 1000;
			$workers_summa_month_12 = $this->schedule_worker_model->get_summa($stantion_id, $type_service_id, 12) / 1000;
			$active_sheet->setCellValue('K' . (10 + $i), $workers_summa_month_1);
			$active_sheet->setCellValue('L' . (10 + $i), $workers_summa_month_2);
			$active_sheet->setCellValue('M' . (10 + $i), $workers_summa_month_3);
			$active_sheet->setCellValue('O' . (10 + $i), $workers_summa_month_4);
			$active_sheet->setCellValue('P' . (10 + $i), $workers_summa_month_5);
			$active_sheet->setCellValue('Q' . (10 + $i), $workers_summa_month_6);
			$active_sheet->setCellValue('S' . (10 + $i), $workers_summa_month_7);
			$active_sheet->setCellValue('T' . (10 + $i), $workers_summa_month_8);
			$active_sheet->setCellValue('U' . (10 + $i), $workers_summa_month_10);
			$active_sheet->setCellValue('W' . (10 + $i), $workers_summa_month_10);
			$active_sheet->setCellValue('X' . (10 + $i), $workers_summa_month_11);
			$active_sheet->setCellValue('Y' . (10 + $i), $workers_summa_month_12);

			$technics_summa_month_1 = $this->schedule_technic_model->get_summa($stantion_id, $type_service_id, 1) / 1000;
			$technics_summa_month_2 = $this->schedule_technic_model->get_summa($stantion_id, $type_service_id, 2) / 1000;
			$technics_summa_month_3 = $this->schedule_technic_model->get_summa($stantion_id, $type_service_id, 3) / 1000;
			$technics_summa_month_4 = $this->schedule_technic_model->get_summa($stantion_id, $type_service_id, 4) / 1000;
			$technics_summa_month_5 = $this->schedule_technic_model->get_summa($stantion_id, $type_service_id, 5) / 1000;
			$technics_summa_month_6 = $this->schedule_technic_model->get_summa($stantion_id, $type_service_id, 6) / 1000;
			$technics_summa_month_7 = $this->schedule_technic_model->get_summa($stantion_id, $type_service_id, 7) / 1000;
			$technics_summa_month_8 = $this->schedule_technic_model->get_summa($stantion_id, $type_service_id, 8) / 1000;
			$technics_summa_month_9 = $this->schedule_technic_model->get_summa($stantion_id, $type_service_id, 9) / 1000;
			$technics_summa_month_10 = $this->schedule_technic_model->get_summa($stantion_id, $type_service_id, 10) / 1000;
			$technics_summa_month_11 = $this->schedule_technic_model->get_summa($stantion_id, $type_service_id, 11) / 1000;
			$technics_summa_month_12 = $this->schedule_technic_model->get_summa($stantion_id, $type_service_id, 12) / 1000;
			$active_sheet->setCellValue('K' . (11 + $i), $technics_summa_month_1);
			$active_sheet->setCellValue('L' . (11 + $i), $technics_summa_month_2);
			$active_sheet->setCellValue('M' . (11 + $i), $technics_summa_month_3);
			$active_sheet->setCellValue('O' . (11 + $i), $technics_summa_month_4);
			$active_sheet->setCellValue('P' . (11 + $i), $technics_summa_month_5);
			$active_sheet->setCellValue('Q' . (11 + $i), $technics_summa_month_6);
			$active_sheet->setCellValue('S' . (11 + $i), $technics_summa_month_7);
			$active_sheet->setCellValue('T' . (11 + $i), $technics_summa_month_8);
			$active_sheet->setCellValue('U' . (11 + $i), $technics_summa_month_11);
			$active_sheet->setCellValue('W' . (11 + $i), $technics_summa_month_10);
			$active_sheet->setCellValue('X' . (11 + $i), $technics_summa_month_11);
			$active_sheet->setCellValue('Y' . (11 + $i), $technics_summa_month_12);

			$active_sheet->setCellValue('J' . (12 + $i), $materials_summa_quarter_1 + $workers_summa_quarter_1 + $technics_summa_quarter_1);
			$active_sheet->setCellValue('N' . (12 + $i), $materials_summa_quarter_2 + $workers_summa_quarter_2 + $technics_summa_quarter_2);
			$active_sheet->setCellValue('R' . (12 + $i), $materials_summa_quarter_3 + $workers_summa_quarter_3 + $technics_summa_quarter_3);
			$active_sheet->setCellValue('V' . (12 + $i), $materials_summa_quarter_4 + $workers_summa_quarter_4 + $technics_summa_quarter_4);
			$active_sheet->setCellValue('K' . (12 + $i), $materials_summa_month_1 + $workers_summa_month_1 + $technics_summa_month_1);
			$active_sheet->setCellValue('L' . (12 + $i), $materials_summa_month_2 + $workers_summa_month_1 + $technics_summa_month_2);
			$active_sheet->setCellValue('M' . (12 + $i), $materials_summa_month_3 + $workers_summa_month_3 + $technics_summa_month_3);
			$active_sheet->setCellValue('O' . (12 + $i), $materials_summa_month_4 + $workers_summa_month_4 + $technics_summa_month_4);
			$active_sheet->setCellValue('P' . (12 + $i), $materials_summa_month_5 + $workers_summa_month_5 + $technics_summa_month_5);
			$active_sheet->setCellValue('Q' . (12 + $i), $materials_summa_month_6 + $workers_summa_month_6 + $technics_summa_month_6);
			$active_sheet->setCellValue('S' . (12 + $i), $materials_summa_month_7 + $workers_summa_month_7 + $technics_summa_month_7);
			$active_sheet->setCellValue('T' . (12 + $i), $materials_summa_month_8 + $workers_summa_month_8 + $technics_summa_month_8);
			$active_sheet->setCellValue('U' . (12 + $i), $materials_summa_month_9 + $workers_summa_month_9 + $technics_summa_month_9);
			$active_sheet->setCellValue('W' . (12 + $i), $materials_summa_month_10 + $workers_summa_month_10 + $technics_summa_month_10);
			$active_sheet->setCellValue('X' . (12 + $i), $materials_summa_month_11 + $workers_summa_month_11 + $technics_summa_month_11);
			$active_sheet->setCellValue('Y' . (12 + $i), $materials_summa_month_12 + $workers_summa_month_12 + $technics_summa_month_12);

			$active_sheet->getStyle('I' . (9 + $i) . ':Y' . (12 + $i))->getNumberFormat()->setFormatCode('0.000');

			$active_sheet->getStyle('A1');
		}

		$spreadsheet->setActiveSheetIndex(0);

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	public function genarate_year_schedule_complex_excel($stantion_id)
	{
		$stantion = $this->complete_renovation_object_model->get_row($stantion_id);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Річний план-графік на ' . (date('Y') + 1) . ' для ' . $stantion->name . '.xlsx"');
		header('Cache-Control: max-age=0');

		$spreadsheet = new Spreadsheet();

		$worksheet_all = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Всі види ремонтів');
		$spreadsheet->addSheet($worksheet_all, 0);
		$worksheet_kr = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Капітальний ремонт');
		$spreadsheet->addSheet($worksheet_kr, 1);
		$worksheet_pr = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Поточний ремонт');
		$spreadsheet->addSheet($worksheet_pr, 2);
		$worksheet_to = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Технічне обслуговування');
		$spreadsheet->addSheet($worksheet_to, 3);

		$sheetIndex = $spreadsheet->getIndex(
			$spreadsheet->getSheetByName('Worksheet')
		);
		$spreadsheet->removeSheetByIndex($sheetIndex);

		$spreadsheet->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
		$spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
		$spreadsheet->getDefaultStyle()->getFont()->setSize(10);

		$type_services_name = '';
		for ($sheet = 0; $sheet <= 3; $sheet++) {
			if ($sheet == 0) {
				$type_services_name = 'ремонтів';
				$data_schedile = $this->get_data_for_schedule_year($stantion_id, NULL);
			}
			if ($sheet == 1) {
				$type_services_name = 'капітального ремонту';
				$data_schedile = $this->get_data_for_schedule_year($stantion_id, 1);
			}
			if ($sheet == 2) {
				$type_services_name = 'поточного ремонту';
				$data_schedile = $this->get_data_for_schedule_year($stantion_id, 2);
			}
			if ($sheet == 3) {
				$type_services_name = 'технічного обслуговування';
				$data_schedile = $this->get_data_for_schedule_year($stantion_id, 3);
			}

			$active_sheet = $spreadsheet->setActiveSheetIndex($sheet);

			$active_sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
			$active_sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
			$active_sheet->getPageSetup()->setFitToPage(false)->setScale(53);

			$active_sheet->getPageMargins()->setTop(0.392);
			$active_sheet->getPageMargins()->setRight(0.392);
			$active_sheet->getPageMargins()->setLeft(0.784);
			$active_sheet->getPageMargins()->setBottom(0.392);

			$active_sheet->getSheetView()->setZoomScale(100);

			$active_sheet->getColumnDimension('A')->setWidth(10);
			$active_sheet->getColumnDimension('B')->setWidth(45);
			$active_sheet->getColumnDimension('C')->setWidth(17);
			$active_sheet->getColumnDimension('D')->setWidth(17);
			$active_sheet->getColumnDimension('E')->setWidth(17);
			$active_sheet->getColumnDimension('F')->setWidth(10);
			$active_sheet->getColumnDimension('G')->setWidth(10);
			$active_sheet->getColumnDimension('H')->setWidth(17);
			$active_sheet->getColumnDimension('I')->setWidth(10);
			$active_sheet->getColumnDimension('J')->setWidth(15);
			$active_sheet->getColumnDimension('K')->setWidth(15);
			$active_sheet->getColumnDimension('L')->setWidth(7);
			$active_sheet->getColumnDimension('M')->setWidth(7);
			$active_sheet->getColumnDimension('N')->setWidth(7);
			$active_sheet->getColumnDimension('O')->setWidth(7);
			$active_sheet->getColumnDimension('P')->setWidth(7);
			$active_sheet->getColumnDimension('Q')->setWidth(7);
			$active_sheet->getColumnDimension('R')->setWidth(7);
			$active_sheet->getColumnDimension('S')->setWidth(7);
			$active_sheet->getColumnDimension('T')->setWidth(7);
			$active_sheet->getColumnDimension('U')->setWidth(7);
			$active_sheet->getColumnDimension('V')->setWidth(7);
			$active_sheet->getColumnDimension('W')->setWidth(7);
			$active_sheet->getColumnDimension('X')->setWidth(15);

			$active_sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
			$active_sheet->getStyle('A2')->getFont()->setSize(14);
			$active_sheet->getStyle('A3')->getFont()->setSize(14);

			$spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
			$spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
			$spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
			$spreadsheet->getActiveSheet()->getRowDimension('6')->setRowHeight(40);

			$active_sheet->mergeCells('A1:X1')->setCellValue('A1', 'РІЧНИЙ ПЛАН-ГРАФІК')->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$active_sheet->mergeCells('A2:X2')->setCellValue('A2', $type_services_name . ' ' . $stantion->name)->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$active_sheet->mergeCells('A3:X3')->setCellValue('A3', 'на ' . (date('Y') + 1) . ' рік')->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

			$active_sheet->getStyle('A4:X7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setWrapText(true);
			$active_sheet->getStyle('A4:X7')->getFont()->setBold(true);
			$active_sheet->getStyle('A4:X7')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);

			$active_sheet->mergeCells('A4:A6')->setCellValue('A4', '№ п/п')->setCellValue('A7', '1');
			$active_sheet->mergeCells('B4:B6')->setCellValue('B4', 'Найменування обладнання')->setCellValue('B7', '2');
			$active_sheet->mergeCells('C4:C6')->setCellValue('C4', 'Дисп. назва')->setCellValue('C7', '3');
			$active_sheet->mergeCells('D4:D6')->setCellValue('D4', 'Рік введення в експлуатацію')->setCellValue('D7', '4');
			$active_sheet->mergeCells('E4:E6')->setCellValue('E4', 'Вид обслуговування')->setCellValue('E7', '5');
			$active_sheet->mergeCells('F4:G4')->setCellValue('F4', 'Спосіб виконання');
			$active_sheet->mergeCells('F5:F6')->setCellValue('F5', 'Госп.')->setCellValue('F7', '6');
			$active_sheet->mergeCells('G5:G6')->setCellValue('G5', 'Підр.')->setCellValue('G7', '7');
			$active_sheet->mergeCells('H4:H6')->setCellValue('H4', 'Трудовитрати, люд/год (госп. спосіб)')->setCellValue('H7', '8');
			$active_sheet->mergeCells('I4:K4')->setCellValue('I4', 'Вартість, тис. грн.');
			$active_sheet->mergeCells('I5:I6')->setCellValue('I5', 'Всього')->setCellValue('I7', '9');
			$active_sheet->mergeCells('J5:K5')->setCellValue('J5', 'Підлягає капіталізації');
			$active_sheet->setCellValue('J6', 'Витрати на матеріали та обладнання')->setCellValue('J7', '10');
			$active_sheet->setCellValue('K6', 'Всього')->setCellValue('K7', '11');
			$active_sheet->mergeCells('L4:W4')->setCellValue('L4', 'План на ' . (date('Y') + 1) . ' рік');
			$active_sheet->mergeCells('L5:L6')->setCellValue('L5', 'січ.')->setCellValue('L7', '12');
			$active_sheet->mergeCells('M5:M6')->setCellValue('M5', 'лют.')->setCellValue('M7', '13');
			$active_sheet->mergeCells('N5:N6')->setCellValue('N5', 'бер.')->setCellValue('N7', '14');
			$active_sheet->mergeCells('O5:O6')->setCellValue('O5', 'квіт.')->setCellValue('O7', '15');
			$active_sheet->mergeCells('P5:P6')->setCellValue('P5', 'трав')->setCellValue('P7', '16');
			$active_sheet->mergeCells('Q5:Q6')->setCellValue('Q5', 'черв')->setCellValue('Q7', '17');
			$active_sheet->mergeCells('R5:R6')->setCellValue('R5', 'лип.')->setCellValue('R7', '18');
			$active_sheet->mergeCells('S5:S6')->setCellValue('S5', 'серп.')->setCellValue('S7', '19');
			$active_sheet->mergeCells('T5:T6')->setCellValue('T5', 'вер.')->setCellValue('T7', '20');
			$active_sheet->mergeCells('U5:U6')->setCellValue('U5', 'жовт.')->setCellValue('U7', '21');
			$active_sheet->mergeCells('V5:V6')->setCellValue('V5', 'лист.')->setCellValue('V7', '22');
			$active_sheet->mergeCells('W5:W6')->setCellValue('W5', 'груд.')->setCellValue('W7', '23');
			$active_sheet->mergeCells('X4:X6')->setCellValue('X4', 'Місяць фактичного виконання запланованих робіт')->setCellValue('X7', '24');

			$i = 0;
			$r = 8;
			foreach ($data_schedile as $row) {

				$active_sheet->getStyle('A' . ($r + $i) . ':X' . ($r + $i))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');
				$active_sheet->getStyle('A' . ($r + $i) . ':X' . ($r + $i))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setWrapText(false);
				$active_sheet->getStyle('B' . ($r + $i))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)->setWrapText(false);
				$active_sheet->getStyle('A' . ($r + $i) . ':X' . ($r + $i))->getFont()->setBold(false);
				$active_sheet->getStyle('A' . ($r + $i) . ':X' . ($r + $i))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

				$active_sheet->getRowDimension($r + $i)->setRowHeight(12.75);

				$active_sheet->setCellValue('A' . ($r + $i), $row['count']);
				$active_sheet->setCellValue('B' . ($r + $i), $row['oborud_with_voltage'])->getStyle('A1');
				$active_sheet->setCellValue('C' . ($r + $i), $row['disp']);
				$active_sheet->setCellValue('D' . ($r + $i), $row['year_commissioning']);
				$active_sheet->setCellValue('E' . ($r + $i), $row['repair_type']);
				$active_sheet->setCellValue('F' . ($r + $i), $row['repair_method_1']);
				$active_sheet->setCellValue('G' . ($r + $i), $row['repair_method_2']);
				$active_sheet->setCellValue('H' . ($r + $i), $row['workers']);
				$active_sheet->getStyle('H' . ($r + $i))->getNumberFormat()->setFormatCode('0.00');
				$active_sheet->setCellValue('I' . ($r + $i), $row['materials']);
				$active_sheet->getStyle('I' . ($r + $i))->getNumberFormat()->setFormatCode('0.0000');

				$month = 1;
				for ($column = 'L'; $column < 'X'; $column++) {
					$active_sheet->setCellValue($column . ($r + $i), $row['month_service_' . $month]);
					$month++;
				}
				$i++;
			}

			$active_sheet->getStyle('A' . ($r + $i) . ':X' . ($r + $i))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');
			$active_sheet->getStyle('A' . ($r + $i) . ':X' . ($r + $i))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setWrapText(false);
			$active_sheet->getStyle('B' . ($r + $i))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setWrapText(false);
			$active_sheet->getStyle('A' . ($r + $i) . ':X' . ($r + $i))->getFont()->setBold(true);
			$active_sheet->getStyle('A' . ($r + $i) . ':X' . ($r + $i))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);


			$active_sheet->getStyle('H' . ($r + $i))->getNumberFormat()->setFormatCode('0.00');
			$active_sheet->getStyle('I' . ($r + $i))->getNumberFormat()->setFormatCode('0.000');

			$active_sheet->setCellValue('A' . ($r + $i), 'Всього:');
			$active_sheet->setCellValue('B' . ($r + $i), $type_services_name);
			$active_sheet->setCellValue('C' . ($r + $i), $i);

			for ($column = 'H'; $column < 'X'; $column++) {
				$active_sheet->setCellValue($column . ($r + $i), '=SUM(' . $column . '8:' . $column . ($r + $i - 1) . ')');
			}

			$active_sheet->getStyle('A1');
		}

		$spreadsheet->setActiveSheetIndex(0);

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	public function genarate_year_schedule_simple_excel($stantion_id, $current_year = NULL)
	{
		$stantion = $this->complete_renovation_object_model->get_row($stantion_id);

		$data = $this->schedule_year_model->get_data_for_simple_year($stantion_id, $current_year);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Річний план-графік для майстрів на ' . (date('Y') + 1) . ' для ' . $stantion->name . '.xlsx"');
		header('Cache-Control: max-age=0');

		$spreadsheet = new Spreadsheet();

		$worksheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Річний план графік');
		$spreadsheet->addSheet($worksheet, 0);

		$sheetIndex = $spreadsheet->getIndex(
			$spreadsheet->getSheetByName('Worksheet')
		);
		$spreadsheet->removeSheetByIndex($sheetIndex);

		$spreadsheet->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
		$spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
		$spreadsheet->getDefaultStyle()->getFont()->setSize(10);

		$active_sheet = $spreadsheet->getActiveSheet();

		$active_sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$active_sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
		$active_sheet->getPageSetup()->setFitToPage(false)->setScale(75);

		$active_sheet->getPageMargins()->setTop(0.392);
		$active_sheet->getPageMargins()->setRight(0.392);
		$active_sheet->getPageMargins()->setLeft(0.784);
		$active_sheet->getPageMargins()->setBottom(0.392);

		$active_sheet->getSheetView()->setZoomScale(100);

		$active_sheet->getColumnDimension('A')->setWidth(10);
		$active_sheet->getColumnDimension('B')->setWidth(35);
		$active_sheet->getColumnDimension('C')->setWidth(15);
		$active_sheet->getColumnDimension('D')->setWidth(30);
		$active_sheet->getColumnDimension('E')->setWidth(15);
		$active_sheet->getColumnDimension('F')->setWidth(10);
		$active_sheet->getColumnDimension('G')->setWidth(20);

		$active_sheet->getStyle('A1:G6')->getFont()->setSize(12)->setBold(true);
		$active_sheet->getStyle('A1:G6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
		$active_sheet->mergeCells('A1:G1')->setCellValue('A1', '"Затверджую"');
		$active_sheet->mergeCells('A2:G2')->setCellValue('A2', 'Начальник СП');
		$active_sheet->mergeCells('A3:G3')->setCellValue('A3', '_________________ Юрій СИЧОВ');
		$active_sheet->mergeCells('A4:G4')->setCellValue('A4', '" ____ " ___________ 20 ____ року');

		$active_sheet->getRowDimension('6')->setRowHeight(20);
		$active_sheet->mergeCells('A6:G6')->setCellValue('A6', 'РІЧНИЙ ПЛАН-ГРАФІК')->getStyle('A6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$active_sheet->getStyle('A6:G6')->getFont()->setSize(16)->setBold(true);

		$active_sheet->mergeCells('A7:G7')->setCellValue('A7', $stantion->name)->getStyle('A7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$active_sheet->getStyle('A7:G7')->getFont()->setSize(14)->setBold(false);

		$year = $current_year ? date('Y') : (date('Y') + 1);
		$active_sheet->mergeCells('A8:G8')->setCellValue('A8', 'на ' . $year . ' рік')->getStyle('A8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$active_sheet->getStyle('A8:G8')->getFont()->setSize(14)->setBold(false);


		$active_sheet->getStyle('A9:G10')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$active_sheet->getStyle('A9:G10')->getFont()->setSize(12)->setBold(true);
		$active_sheet->getStyle('A9:G9')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
		$active_sheet->mergeCells('A9')->setCellValue('A9', '№ п/п');
		$active_sheet->mergeCells('B9')->setCellValue('B9', 'Найменування обладнання');
		$active_sheet->mergeCells('C9')->setCellValue('C9', 'Дисп. назва');
		$active_sheet->mergeCells('D9')->setCellValue('D9', 'Тип');
		$active_sheet->mergeCells('E9')->setCellValue('E9', 'Вид ремонту');
		$active_sheet->mergeCells('F9')->setCellValue('F9', 'План, м.');
		$active_sheet->mergeCells('G9')->setCellValue('G9', 'Факт, ч. м. р.');

		$i = 1;
		for ($col = 'A'; $col <= 'G'; $col++) {
			$active_sheet->getStyle('A10:G10')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
			$active_sheet->mergeCells($col . '10')->setCellValue($col . '10', $i);
			$i++;
		}

		$i = 1;
		$row = 11;
		foreach ($data as $item) {
			$active_sheet->getStyle('A' . $row)->getFont()->setSize(12)->setBold(false);
			$active_sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$active_sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$active_sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$active_sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$active_sheet->getStyle('A' . $row . ':G' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

			$active_sheet->mergeCells('A' . $row)->setCellValue('A' . $row, $i);
			$active_sheet->mergeCells('B' . $row)->setCellValue('B' . $row, $item->oborud . ' ' . round($item->voltage, 0) . ' кВ');
			$active_sheet->mergeCells('C' . $row)->setCellValue('C' . $row, $item->disp);
			$active_sheet->mergeCells('D' . $row)->setCellValue('D' . $row, $item->type);
			$active_sheet->mergeCells('E' . $row)->setCellValue('E' . $row, $item->type_service);
			$active_sheet->mergeCells('F' . $row)->setCellValue('F' . $row, $item->month);
			$active_sheet->mergeCells('G' . $row)->setCellValue('G' . $row, $item->date_service_actual == '0000-00-00' ? '' : date('d.m.Y', strtotime($item->date_service_actual)));

			$i++;
			$row++;
		}

		$active_sheet->getStyle('A6');

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	public function genarate_multi_year_schedule_excel($type_service = NULL)
	{
		if (!is_numeric($type_service) || !$type_service) {
			show_404();
		}
		$stations = $this->complete_renovation_object_model->get_stantions_for_subdivision_and_user(1);

		$year_head = (date('Y') + 1);
		$cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q'];

		$spreadsheet = new Spreadsheet();

		$sheetIndex = $spreadsheet->getIndex($spreadsheet->getSheetByName('Worksheet'));
		$spreadsheet->removeSheetByIndex($sheetIndex);

		foreach ($stations as $index => $station) {
			$worksheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, mb_substr(str_replace('/', '_', $station->name), 0, 30));
			$spreadsheet->addSheet($worksheet, $index);
		}

		$spreadsheet->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
		$spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
		$spreadsheet->getDefaultStyle()->getFont()->setSize(10);

		foreach ($stations as $index => $station) {
			$data = $this->get_data_for_multi_year_schedule($station->id, $type_service);

			$active_sheet = $spreadsheet->setActiveSheetIndex($index);

			$active_sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
			$active_sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
			$active_sheet->getPageSetup()->setFitToPage(false)->setScale(40);
			$active_sheet->getPageMargins()->setTop(0.392);
			$active_sheet->getPageMargins()->setRight(0.392);
			$active_sheet->getPageMargins()->setLeft(0.784);
			$active_sheet->getPageMargins()->setBottom(0.392);

			$active_sheet->getSheetView()->setZoomScale(80);

			$active_sheet->getStyle('A4:Q6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('DCDCDC');

			$active_sheet->getColumnDimension('A')->setWidth(10);
			$active_sheet->getColumnDimension('B')->setWidth(50);
			$active_sheet->getColumnDimension('C')->setWidth(20);
			$active_sheet->getColumnDimension('D')->setWidth(20);
			$active_sheet->getColumnDimension('E')->setWidth(20);
			$active_sheet->getColumnDimension('F')->setWidth(15);
			$active_sheet->getColumnDimension('G')->setWidth(15);
			$active_sheet->getColumnDimension('H')->setWidth(15);
			$active_sheet->getColumnDimension('I')->setWidth(15);
			$active_sheet->getColumnDimension('J')->setWidth(15);
			$active_sheet->getColumnDimension('K')->setWidth(15);
			$active_sheet->getColumnDimension('L')->setWidth(15);
			$active_sheet->getColumnDimension('M')->setWidth(15);
			$active_sheet->getColumnDimension('N')->setWidth(15);
			$active_sheet->getColumnDimension('O')->setWidth(15);
			$active_sheet->getColumnDimension('P')->setWidth(15);
			$active_sheet->getColumnDimension('Q')->setWidth(15);

			$active_sheet->getRowDimension('1')->setRowHeight(20);
			$active_sheet->getRowDimension('2')->setRowHeight(20);
			$active_sheet->getRowDimension('3')->setRowHeight(20);
			$active_sheet->getRowDimension('4')->setRowHeight(40);
			$active_sheet->getRowDimension('5')->setRowHeight(20);
			$active_sheet->getRowDimension('6')->setRowHeight(20);

			$active_sheet->mergeCells('A1:Q1')->setCellValue('A1', 'БАГАТОРІЧНИЙ ПЛАН-ГРАФІК')->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$active_sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
			$active_sheet->getStyle('A2')->getFont()->setSize(14);
			$active_sheet->getStyle('A3')->getFont()->setSize(14);

			$name = '';
			if ($type_service == 1) {
				$name = 'капітального ремонту';
			}
			if ($type_service == 2) {
				$name = 'поточного ремонту';
			}
			if ($type_service == 3) {
				$name = 'технічного обслуговування';
			}
			$active_sheet->mergeCells('A2:Q2')->setCellValue('A2', $name . ' по СП')->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$active_sheet->mergeCells('A3:Q3')->setCellValue('A3', $station->name)->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

			$active_sheet->getStyle('A4:Q6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setWrapText(true);
			$active_sheet->getStyle('A4:Q6')->getFont()->setBold(true);
			$active_sheet->getStyle('A4:Q6')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);

			$active_sheet->mergeCells('A4:A5')->setCellValue('A4', '№ п/п');
			$active_sheet->mergeCells('B4:B5')->setCellValue('B4', 'Найменування обладнання');
			$active_sheet->mergeCells('C4:C5')->setCellValue('C4', 'Дисп. назва');
			$active_sheet->mergeCells('D4:D5')->setCellValue('D4', 'Рік вводу');
			$active_sheet->mergeCells('E4:E5')->setCellValue('E4', 'Періодичність згідно РД');
			$active_sheet->mergeCells('F4:H4')->setCellValue('F4', 'Рік останнього КР');
			$active_sheet->mergeCells('I4:K4')->setCellValue('I4', 'Рік заходу в ІП фактично');
			$active_sheet->mergeCells('L4:L5')->setCellValue('L4', ($year_head));
			$active_sheet->mergeCells('M4:M5')->setCellValue('M4', ($year_head + 1));
			$active_sheet->mergeCells('N4:N5')->setCellValue('N4', ($year_head + 2));
			$active_sheet->mergeCells('O4:O5')->setCellValue('O4', ($year_head + 3));
			$active_sheet->mergeCells('P4:P5')->setCellValue('P4', ($year_head + 4));
			$active_sheet->mergeCells('Q4:Q5')->setCellValue('Q4', 'Плановий рік включення в ІП');

			$active_sheet->setCellValue('F5', '150 (110)');
			$active_sheet->setCellValue('G5', '35');
			$active_sheet->setCellValue('H5', '10 (6)');
			$active_sheet->setCellValue('I5', '150 (110)');
			$active_sheet->setCellValue('J5', '35');
			$active_sheet->setCellValue('K5', '10 (6)');

			$i = 1;
			foreach ($cols as $cell) {
				$active_sheet->setCellValue($cell . '6', $i);
				$i++;
			}

			$row = 7;
			$q = 1;
			foreach ($data as $item) {
				$active_sheet->setCellValue('A' . $row, $q);
				$active_sheet->setCellValue('B' . $row, $item['oborud'] . ' ' . $item['class_voltage'] . ' кВ');
				$active_sheet->setCellValue('C' . $row, $item['disp']);
				$active_sheet->setCellValue('D' . $row, $item['year_start']);
				$active_sheet->setCellValue('E' . $row, $item['period']);
				$active_sheet->setCellValue('F' . $row, $item['repair_year_last_voltage_vn']);
				$active_sheet->setCellValue('G' . $row, $item['repair_year_last_voltage_sn']);
				$active_sheet->setCellValue('H' . $row, $item['repair_year_last_voltage_nn']);
				$active_sheet->setCellValue('I' . $row, $item['invest_year_last_voltage_vn']);
				$active_sheet->setCellValue('J' . $row, $item['invest_year_last_voltage_sn']);
				$active_sheet->setCellValue('K' . $row, $item['invest_year_last_voltage_nn']);
				$active_sheet->setCellValue('L' . $row, $item['is_repair_1']);
				$active_sheet->setCellValue('M' . $row, $item['is_repair_2']);
				$active_sheet->setCellValue('N' . $row, $item['is_repair_3']);
				$active_sheet->setCellValue('O' . $row, $item['is_repair_4']);
				$active_sheet->setCellValue('P' . $row, $item['is_repair_5']);
				$active_sheet->setCellValue('Q' . $row, $item['invest_year_plan']);

				$active_sheet->getStyle('A' . $row . ':Q' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
				$active_sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
				$active_sheet->getStyle('A' . $row . ':Q' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

				$row++;
				$q++;
			}

			$active_sheet->mergeCells('A' . ($row + 3) . ':Q' . ($row + 3))->setCellValue('A' . ($row + 3), 'Начальник СП ________________ Юрій СИЧОВ');
			$active_sheet->mergeCells('A' . ($row + 3 + 3) . ':Q' . ($row + 3 + 3))->setCellValue('A' . ($row + 3 + 3), 'Графік склав ______________');
			$active_sheet->mergeCells('A' . ($row + 3 + 3 + 3) . ':Q' . ($row + 3 + 3 + 3))->setCellValue('A' . ($row + 3 + 3 + 3), 'Файл створено за допомогою АПроСТОР v1.0');

			$active_sheet->getStyle('A1');
		}

		$spreadsheet->removeSheetByIndex(0);

		if ($data) {
			$spreadsheet->setActiveSheetIndex(0);
		}

		$name = '';
		if ($type_service == 1) {
			$name = 'капітальних ремонтів';
		}
		if ($type_service == 2) {
			$name = 'поточних ремонтів';
		}
		if ($type_service == 3) {
			$name = 'технічного обслуговування';
		}

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Багаторічний план графік ' . $name . ' по СП.xlsx"');
		header('Cache-Control: max-age=0');

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');

		// $writer->save('output_data/Багаторічний план графік по SP.xlsx');
	}

	private function get_data_for_multi_year_schedule($complete_renovation_object_id, $type_service_id)
	{
		$new_data_full = $this->specific_renovation_object_model->get_data_for_multi_year_schedule($complete_renovation_object_id, $type_service_id);

		$i = 1;
		$data = [];
		foreach ($new_data_full as $row) {
			$new_data['number'] = $i;
			$new_data['station'] = $row['station'];
			$new_data['oborud'] = $row['oborud'];
			$new_data['disp'] = $row['disp'];
			$new_data['year_start'] = $row['year_start'];
			$new_data['period'] = $row['period'];
			$new_data['class_voltage'] = ($row['class_voltage'] < 1) ? number_format($row['class_voltage'], 2, ',', ' ') : number_format($row['class_voltage'], 0, ',', ' ');

			$new_data['repair_year_last_voltage_vn'] = '';
			$new_data['invest_year_last_voltage_vn'] = '';
			if ($row['class_voltage'] == 110 || $row['class_voltage'] == 150) {
				$new_data['repair_year_last_voltage_vn'] = $row['repair_year_last'] ? $row['repair_year_last'] : NULL;
				$new_data['invest_year_last_voltage_vn'] = $row['invest_year_last'] ? $row['invest_year_last'] : NULL;
			}

			$new_data['repair_year_last_voltage_sn'] = '';
			$new_data['invest_year_last_voltage_sn'] = '';
			if ($row['class_voltage'] == 35) {
				$new_data['repair_year_last_voltage_sn'] = $row['repair_year_last'] ? $row['repair_year_last'] : NULL;
				$new_data['invest_year_last_voltage_sn'] = $row['invest_year_last'] ? $row['invest_year_last'] : NULL;
			}

			$new_data['repair_year_last_voltage_nn'] = '';
			$new_data['invest_year_last_voltage_nn'] = '';
			if ($row['class_voltage'] == 6 || $row['class_voltage'] == 10) {
				$new_data['repair_year_last_voltage_nn'] = $row['repair_year_last'] ? $row['repair_year_last'] : NULL;
				$new_data['invest_year_last_voltage_nn'] = $row['invest_year_last'] ? $row['invest_year_last'] : NULL;
			}

			$year = (date('Y') + 1);
			// $year = (date('Y') + 1);

			// $new_data['year_2'] = '';
			// $new_data['year_3'] = '';
			// $new_data['year_4'] = '';
			// $new_data['year_5'] = '';

			$new_data['is_repair_1'] = '';
			$new_data['is_repair_2'] = '';
			$new_data['is_repair_3'] = '';
			$new_data['is_repair_4'] = '';
			$new_data['is_repair_5'] = '';

			for ($y = 1; $y < 6; $y++) {
				if ($y == 1) {
					if (($row['period'] + $row['repair_year_last'] == $year) || ($row['period'] + $row['repair_year_last'] < $year && $row['period'] + $row['repair_year_last'] > 0)) {
						$new_data['is_repair_' . $y] = 1;
						$year_pred_1 = $year;
					} else {
						$new_data['is_repair_' . $y] = '';
						$year_pred_1 = 0;
					}
				}

				if ($y == 2) {
					if (($row['period'] + $row['repair_year_last'] == $year) || ($row['period'] + $year_pred_1 == $year)) {
						$new_data['is_repair_' . $y] = 1;
						$year_pred_2 = $year;
					} else {
						$new_data['is_repair_' . $y] = '';
						$year_pred_2 = 0;
					}
				}

				if ($y == 3) {
					if (($row['period'] + $row['repair_year_last']) == $year || ($row['period'] + $year_pred_1 == $year) || ($row['period'] + $year_pred_2 == $year)) {
						$new_data['is_repair_' . $y] = 1;
						$year_pred_3 = $year;
					} else {
						$new_data['is_repair_' . $y] = '';
						$year_pred_3 = 0;
					}
				}

				if ($y == 4) {
					if (($row['period'] + $row['repair_year_last']) == $year || ($row['period'] + $year_pred_1 == $year) || ($row['period'] + $year_pred_2 == $year) || ($row['period'] + $year_pred_3 == $year)) {
						$new_data['is_repair_' . $y] = 1;
						$year_pred_4 = $year;
					} else {
						$new_data['is_repair_' . $y] = '';
						$year_pred_4 = 0;
					}
				}

				if ($y == 5) {
					if (($row['period'] + $row['repair_year_last']) == $year || ($row['period'] + $year_pred_1 == $year) || ($row['period'] + $year_pred_2 == $year) || ($row['period'] + $year_pred_3 == $year) || ($row['period'] + $year_pred_4 == $year)) {
						$new_data['is_repair_' . $y] = 1;
						$year_pred_5 = $year;
					} else {
						$new_data['is_repair_' . $y] = '';
						$year_pred_5 = 0;
					}
				}

				$year++;
			}

			$new_data['invest_year_plan'] = $row['invest_year_plan'];

			$i++;
			array_push($data, $new_data);
		}

		return $data;
	}

	public function genarate_program_excel()
	{
		$this->load->library('user_agent');

		$new_array = $this->get_data_for_program();

		if (!$new_array) {
			$this->session->set_flashdata('message', 'На жаль відсутні дані в таблиці з матеріалами для виконання графіку!');
			redirect($this->agent->referrer());
		}

		$letters = ['F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY'];

		// $letters_2 = ['H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY'];

		$letters_2 = ['H', 'I', 'L', 'M', 'P', 'Q', 'T', 'U', 'X', 'Y', 'AB', 'AC', 'AF', 'AG', 'AJ', 'AK', 'AN', 'AO', 'AR', 'AS', 'AV', 'AW', 'AZ', 'BA', 'BD', 'BE', 'BH', 'BI', 'BL', 'BM', 'BP', 'BQ', 'BT', 'BU', 'BX', 'BY'];

		$spreadsheet = new Spreadsheet();
		$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$spreadsheet->getActiveSheet()->getPageSetup()->setFitToPage(false)->setScale(38);
		$spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.392);
		$spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.392);
		$spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.784);
		$spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.392);

		$spreadsheet->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
		$spreadsheet->getActiveSheet()->getSheetView()->setZoomScale(76);
		$spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
		$spreadsheet->getDefaultStyle()->getFont()->setSize(10);
		$spreadsheet->getActiveSheet()->getStyle('A4:BY7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('DCDCDC');
		$spreadsheet->getActiveSheet()->getStyle('BZ4:CH7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F5A9F2');

		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(15);

		for ($i = 0; $i < 71; $i++) {
			$spreadsheet->getActiveSheet()->getColumnDimensionByColumn($i + 9)->setWidth(12);
		}

		for ($i = 68; $i < 77; $i++) {
			$spreadsheet->getActiveSheet()->getColumnDimensionByColumn($i + 9)->setWidth(15);
		}

		$spreadsheet->getActiveSheet()->getColumnDimension('CH')->setWidth(40);

		$sheet = $spreadsheet->getActiveSheet()->setTitle('Капітальний та поточний ремонти');

		$sheet->mergeCells('A1:CH1')->setCellValue('A1', 'ПЛАН РЕМОНТНОЇ ПРОГРАМИ СП на ' . (date('Y') + 1) . ' рік')->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

		$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
		$sheet->getStyle('A2')->getFont()->setSize(14);
		$sheet->getStyle('A3')->getFont()->setSize(14);

		$spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
		$spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
		$spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
		$spreadsheet->getActiveSheet()->getRowDimension('6')->setRowHeight(60);

		$sheet->getStyle('A4:CH7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setWrapText(true);
		$sheet->getStyle('A4:CH7')->getFont()->setBold(true);
		$sheet->getStyle('A4:CH7')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);

		$sheet->mergeCells('A4:A6')->setCellValue('A4', '№ п/п');
		$sheet->mergeCells('B4:B6')->setCellValue('B4', 'Найменування обладнання');
		$sheet->mergeCells('C4:C6')->setCellValue('C4', 'Інвентарний номер');
		$sheet->mergeCells('D4:D6')->setCellValue('D4', 'Cпосіб виконання робіт');
		$sheet->mergeCells('E4:E6')->setCellValue('E4', 'Од. виміру');

		$sheet->mergeCells('F4:H4')->setCellValue('F4', 'Усього заплановано на рік');
		$sheet->mergeCells('F5:F6')->setCellValue('F5', 'к-ть');
		$sheet->mergeCells('G5:G6')->setCellValue('G5', 'Загальна вартість, тис.грн без ПДВ');
		$sheet->mergeCells('H5:H6')->setCellValue('H5', 'Вартість підрядних  робіт, тис.грн без ПДВ');
		$sheet->mergeCells('I5:I6')->setCellValue('I5', 'Вартість матеріалів та сировини, тис.грн без ПДВ');

		$sheet->mergeCells('J4:M4')->setCellValue('J4', 'Січень');
		$sheet->mergeCells('J5:K5')->setCellValue('J5', 'Кількість');
		$sheet->mergeCells('L5:M5')->setCellValue('L5', 'Вартість');
		$sheet->setCellValue('J6', 'План');
		$sheet->setCellValue('K6', 'Факт');
		$sheet->setCellValue('L6', 'План');
		$sheet->setCellValue('M6', 'Факт');

		$sheet->mergeCells('N4:Q4')->setCellValue('N4', 'Лютий');
		$sheet->mergeCells('N5:O5')->setCellValue('N5', 'Кількість');
		$sheet->mergeCells('P5:Q5')->setCellValue('P5', 'Вартість');
		$sheet->setCellValue('N6', 'План');
		$sheet->setCellValue('O6', 'Факт');
		$sheet->setCellValue('P6', 'План');
		$sheet->setCellValue('Q6', 'Факт');

		$sheet->mergeCells('R4:U4')->setCellValue('R4', 'Березень');
		$sheet->mergeCells('R5:S5')->setCellValue('R5', 'Кількість');
		$sheet->mergeCells('T5:U5')->setCellValue('T5', 'Вартість');
		$sheet->setCellValue('R6', 'План');
		$sheet->setCellValue('S6', 'Факт');
		$sheet->setCellValue('T6', 'План');
		$sheet->setCellValue('U6', 'Факт');

		$sheet->mergeCells('V4:Y4')->setCellValue('V4', 'І квартал');
		$sheet->mergeCells('V5:W5')->setCellValue('V5', 'Кількість');
		$sheet->mergeCells('X5:Y5')->setCellValue('X5', 'Вартість');
		$sheet->setCellValue('V6', 'План');
		$sheet->setCellValue('W6', 'Факт');
		$sheet->setCellValue('X6', 'План');
		$sheet->setCellValue('Y6', 'Факт');

		$sheet->mergeCells('Z4:AC4')->setCellValue('Z4', 'Квітень');
		$sheet->mergeCells('Z5:AA5')->setCellValue('Z5', 'Кількість');
		$sheet->mergeCells('AB5:AC5')->setCellValue('AA5', 'Вартість');
		$sheet->setCellValue('Z6', 'План');
		$sheet->setCellValue('AA6', 'Факт');
		$sheet->setCellValue('AB6', 'План');
		$sheet->setCellValue('AC6', 'Факт');

		$sheet->mergeCells('AD4:AG4')->setCellValue('AD4', 'Травень');
		$sheet->mergeCells('AD5:AE5')->setCellValue('AD5', 'Кількість');
		$sheet->mergeCells('AF5:AG5')->setCellValue('AF5', 'Вартість');
		$sheet->setCellValue('AD6', 'План');
		$sheet->setCellValue('AE6', 'Факт');
		$sheet->setCellValue('AF6', 'План');
		$sheet->setCellValue('AG6', 'Факт');

		$sheet->mergeCells('AH4:AK4')->setCellValue('AH4', 'Червень');
		$sheet->mergeCells('AH5:AI5')->setCellValue('AH5', 'Кількість');
		$sheet->mergeCells('AJ5:AK5')->setCellValue('AJ5', 'Вартість');
		$sheet->setCellValue('AH6', 'План');
		$sheet->setCellValue('AI6', 'Факт');
		$sheet->setCellValue('AJ6', 'План');
		$sheet->setCellValue('AK6', 'Факт');

		$sheet->mergeCells('AL4:AO4')->setCellValue('AL4', 'ІІ квартал');
		$sheet->mergeCells('AL5:AM5')->setCellValue('AL5', 'Кількість');
		$sheet->mergeCells('AN5:AO5')->setCellValue('AN5', 'Вартість');
		$sheet->setCellValue('AL6', 'План');
		$sheet->setCellValue('AM6', 'Факт');
		$sheet->setCellValue('AN6', 'План');
		$sheet->setCellValue('AO6', 'Факт');

		$sheet->mergeCells('AP4:AS4')->setCellValue('AP4', 'Липень');
		$sheet->mergeCells('AP5:AQ5')->setCellValue('AP5', 'Кількість');
		$sheet->mergeCells('AR5:AS5')->setCellValue('AR5', 'Вартість');
		$sheet->setCellValue('AP6', 'План');
		$sheet->setCellValue('AQ6', 'Факт');
		$sheet->setCellValue('AR6', 'План');
		$sheet->setCellValue('AS6', 'Факт');

		$sheet->mergeCells('AT4:AW4')->setCellValue('AT4', 'Серпень');
		$sheet->mergeCells('AT5:AU5')->setCellValue('AT5', 'Кількість');
		$sheet->mergeCells('AV5:AW5')->setCellValue('AV5', 'Вартість');
		$sheet->setCellValue('AT6', 'План');
		$sheet->setCellValue('AU6', 'Факт');
		$sheet->setCellValue('AV6', 'План');
		$sheet->setCellValue('AW6', 'Факт');

		$sheet->mergeCells('AX4:BA4')->setCellValue('AX4', 'Вересень');
		$sheet->mergeCells('AX5:AY5')->setCellValue('AX5', 'Кількість');
		$sheet->mergeCells('AZ5:BA5')->setCellValue('AZ5', 'Вартість');
		$sheet->setCellValue('AX6', 'План');
		$sheet->setCellValue('AY6', 'Факт');
		$sheet->setCellValue('AZ6', 'План');
		$sheet->setCellValue('BA6', 'Факт');

		$sheet->mergeCells('BB4:BE4')->setCellValue('BB4', 'ІІІ квартал');
		$sheet->mergeCells('BB5:BC5')->setCellValue('BB5', 'Кількість');
		$sheet->mergeCells('BD5:BE5')->setCellValue('BD5', 'Вартість');
		$sheet->setCellValue('BB6', 'План');
		$sheet->setCellValue('BC6', 'Факт');
		$sheet->setCellValue('BD6', 'План');
		$sheet->setCellValue('BE6', 'Факт');

		$sheet->mergeCells('BF4:BI4')->setCellValue('BF4', 'Жовтень');
		$sheet->mergeCells('BF5:BG5')->setCellValue('BF5', 'Кількість');
		$sheet->mergeCells('BH5:BI5')->setCellValue('BH5', 'Вартість');
		$sheet->setCellValue('BF6', 'План');
		$sheet->setCellValue('BG6', 'Факт');
		$sheet->setCellValue('BH6', 'План');
		$sheet->setCellValue('BI6', 'Факт');

		$sheet->mergeCells('BJ4:BM4')->setCellValue('BJ4', 'Листопад');
		$sheet->mergeCells('BJ5:BK5')->setCellValue('BJ5', 'Кількість');
		$sheet->mergeCells('BL5:BM5')->setCellValue('BL5', 'Вартість');
		$sheet->setCellValue('BJ6', 'План');
		$sheet->setCellValue('BK6', 'Факт');
		$sheet->setCellValue('BL6', 'План');
		$sheet->setCellValue('BM6', 'Факт');

		$sheet->mergeCells('BN4:BQ4')->setCellValue('BN4', 'Грудень');
		$sheet->mergeCells('BN5:BO5')->setCellValue('BN5', 'Кількість');
		$sheet->mergeCells('BP5:BQ5')->setCellValue('BP5', 'Вартість');
		$sheet->setCellValue('BN6', 'План');
		$sheet->setCellValue('BO6', 'Факт');
		$sheet->setCellValue('BP6', 'План');
		$sheet->setCellValue('BQ6', 'Факт');

		$sheet->mergeCells('BR4:BU4')->setCellValue('BR4', 'ІV квартал');
		$sheet->mergeCells('BR5:BS5')->setCellValue('BR5', 'Кількість');
		$sheet->mergeCells('BT5:BU5')->setCellValue('BT5', 'Вартість');
		$sheet->setCellValue('BR6', 'План');
		$sheet->setCellValue('BS6', 'Факт');
		$sheet->setCellValue('BT6', 'План');
		$sheet->setCellValue('BU6', 'Факт');

		$sheet->mergeCells('BV4:BY4')->setCellValue('BV4', 'Всього по програмі');
		$sheet->mergeCells('BV5:BW5')->setCellValue('BV5', 'Кількість');
		$sheet->mergeCells('BX5:BY5')->setCellValue('BX5', 'Вартість');
		$sheet->setCellValue('BV6', 'План');
		$sheet->setCellValue('BW6', 'Факт');
		$sheet->setCellValue('BX6', 'План');
		$sheet->setCellValue('BY6', 'Факт');

		$sheet->mergeCells('BZ4:CA4')->setCellValue('BZ4', 'І квартал');
		$sheet->mergeCells('CB4:CC4')->setCellValue('CB4', 'ІІ квартал');
		$sheet->mergeCells('CD4:CE4')->setCellValue('CD4', 'ІІІ квартал');
		$sheet->mergeCells('CF4:CG4')->setCellValue('CF4', 'ІV квартал');

		$sheet->mergeCells('BZ5:CA5')->setCellValue('BZ5', 'Дата виконання');
		$sheet->mergeCells('CB5:CC5')->setCellValue('CB5', 'Дата виконання');
		$sheet->mergeCells('CD5:CE5')->setCellValue('CD5', 'Дата виконання');
		$sheet->mergeCells('CF5:CG5')->setCellValue('CF5', 'Дата виконання');

		$sheet->setCellValue('BZ6', 'початок');
		$sheet->setCellValue('CA6', 'кінець');
		$sheet->setCellValue('CB6', 'початок');
		$sheet->setCellValue('CC6', 'кінець');
		$sheet->setCellValue('CD6', 'початок');
		$sheet->setCellValue('CE6', 'кінець');
		$sheet->setCellValue('CF6', 'початок');
		$sheet->setCellValue('CG6', 'кінець');

		$sheet->mergeCells('CH4:CH6')->setCellValue('CH4', 'Стислий опис робіт (план)');

		for ($i = 1; $i <= 86; $i++) {
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($i, 7, $i);
		}

		// Групируємо колонки
		for ($i = 10; $i <= 21; $i++) {
			$spreadsheet->getActiveSheet()->getColumnDimensionByColumn($i)->setOutlineLevel(1);
			$spreadsheet->getActiveSheet()->getColumnDimensionByColumn(10)->setCollapsed(true);
			$spreadsheet->getActiveSheet()->getColumnDimensionByColumn($i)->setVisible(false);
			// $spreadsheet->getActiveSheet()->setShowSummaryBelow(true);
		}
		for ($i = 26; $i <= 37; $i++) {
			$spreadsheet->getActiveSheet()->getColumnDimensionByColumn($i)->setOutlineLevel(1);
			$spreadsheet->getActiveSheet()->getColumnDimensionByColumn($i)->setVisible(false);
		}

		for ($i = 42; $i <= 53; $i++) {
			$spreadsheet->getActiveSheet()->getColumnDimensionByColumn($i)->setOutlineLevel(1);
			$spreadsheet->getActiveSheet()->getColumnDimensionByColumn($i)->setVisible(false);
		}

		for ($i = 58; $i <= 69; $i++) {
			$spreadsheet->getActiveSheet()->getColumnDimensionByColumn($i)->setOutlineLevel(1);
			$spreadsheet->getActiveSheet()->getColumnDimensionByColumn($i)->setVisible(false);
		}

		$spreadsheet->getActiveSheet()->insertNewRowBefore(8, count($new_array));

		$r = 8;
		$i = 1;
		$start = '1.4';
		$sum_group_array = [];
		foreach ($new_array as $k_group => $v_group) {
			// print_r($v_group);
			$spreadsheet->getActiveSheet()->insertNewRowBefore($r, count($v_group));
			$sheet->setCellValue('A' . $r, $start . '.' . $i);
			// $sheet->setCellValue('B' . $r, $v_group['group']);

			$q = 1;
			$spreadsheet->getActiveSheet()->setShowSummaryBelow(false);

			foreach ($v_group as $row) {
				$spreadsheet->getActiveSheet()->getRowDimension(($q + $r))->setOutlineLevel(1);
				// $spreadsheet->getActiveSheet()->getRowDimension(($q + $r))->setVisible(false);
				$spreadsheet->getActiveSheet()->getRowDimension(($r))->setOutlineLevel(0);
				// $spreadsheet->getActiveSheet()->getRowDimension(($r))->setVisible(true);
				//

				$r_title = ($v_group[$row['stantion']]['class_voltage'] == 150) ? ' ПС-35-' . $v_group[$row['stantion']]['class_voltage'] . ' кВ (СП) ' : ' ПС-' . $v_group[$row['stantion']]['class_voltage'] . ' (РЕМ) ';
				if ($row['class_voltage'] != '') {
					$group = $v_group[$row['stantion']]['repair_type'] . $r_title . $v_group[$row['stantion']]['repair_method'];
				} else {
					$group = $v_group[$row['stantion']]['repair_type'] . ' ' . $v_group[$row['stantion']]['repair_method'];
				}

				$sheet->setCellValue('B' . $r, $group);
				$sheet->setCellValue('A' . ($q + $r), $start . '.' . $i . '.' . $q);
				$note = ($row['note_for_contract'] !== '') ? "\n(" . $row['note_for_contract'] . ")" : "";
				$sheet->setCellValue('B' . ($q + $r), $row['stantion'] . $note);
				$sheet->setCellValue('C' . ($q + $r), $row['inventar_number']);
				$sheet->setCellValue('D' . ($q + $r), $row['repair_method']);
				$sheet->setCellValue('E' . ($q + $r), $row['unit']);
				$sheet->setCellValue('F' . ($q + $r), $row['quantity']);
				$sheet->setCellValue('G' . ($q + $r), ($row['price_total'] != 0 && $row['repair_method'] === 'госп.') ? $row['price_total'] : NULL);
				$sheet->setCellValue('H' . ($q + $r), $row['price_contract'] != 0 ? $row['price_contract'] : NULL);
				$sheet->setCellValue('I' . ($q + $r), $row['price_materials'] != 0 ? $row['price_materials'] : NULL);

				// Полный 1 квартал
				$sheet->setCellValue('J' . ($q + $r), $row['quantity_materials_month_plan_01'] != 0 ? $row['quantity_materials_month_plan_01'] : NULL);
				$sheet->setCellValue('L' . ($q + $r), $row['price_materials_month_plan_01'] != 0 ? $row['price_materials_month_plan_01'] : NULL);

				$sheet->setCellValue('N' . ($q + $r), $row['quantity_materials_month_plan_02'] != 0 ? $row['quantity_materials_month_plan_02'] : NULL);
				$sheet->setCellValue('P' . ($q + $r), $row['price_materials_month_plan_02'] != 0 ? $row['price_materials_month_plan_02'] : NULL);

				$sheet->setCellValue('R' . ($q + $r), $row['quantity_materials_month_plan_03'] != 0 ? $row['quantity_materials_month_plan_03'] : NULL);
				$sheet->setCellValue('T' . ($q + $r), $row['price_materials_month_plan_03'] != 0 ? $row['price_materials_month_plan_03'] : NULL);

				$sheet->setCellValue('V' . ($q + $r), $row['quantity_materials_quarter_plan_01'] != 0 ? $row['quantity_materials_quarter_plan_01'] : NULL);
				$sheet->setCellValue('X' . ($q + $r), $row['price_materials_quarter_plan_01'] != 0 ? $row['price_materials_quarter_plan_01'] : NULL);

				// Полный 2 квартал
				$sheet->setCellValue('Z' . ($q + $r), $row['quantity_materials_month_plan_04'] != 0 ? $row['quantity_materials_month_plan_04'] : NULL);
				$sheet->setCellValue('AB' . ($q + $r), $row['price_materials_month_plan_04'] != 0 ? $row['price_materials_month_plan_04'] : NULL);

				$sheet->setCellValue('AD' . ($q + $r), $row['quantity_materials_month_plan_05'] != 0 ? $row['quantity_materials_month_plan_05'] : NULL);
				$sheet->setCellValue('AF' . ($q + $r), $row['price_materials_month_plan_05'] != 0 ? $row['price_materials_month_plan_05'] : NULL);

				$sheet->setCellValue('AH' . ($q + $r), $row['quantity_materials_month_plan_06'] != 0 ? $row['quantity_materials_month_plan_06'] : NULL);
				// $sheet->setCellValue('AH' . ($q + $r), '=AJ' . ($q + $r) . '/ G' . ($q + $r));
				$sheet->setCellValue('AJ' . ($q + $r), $row['price_materials_month_plan_06'] != 0 ? $row['price_materials_month_plan_06'] : NULL);

				$sheet->setCellValue('AL' . ($q + $r), $row['quantity_materials_quarter_plan_02'] != 0 ? $row['quantity_materials_quarter_plan_02'] : NULL);
				$sheet->setCellValue('AN' . ($q + $r), $row['price_materials_quarter_plan_02'] != 0 ? $row['price_materials_quarter_plan_02'] : NULL);

				// Полный 3 квартал
				$sheet->setCellValue('AP' . ($q + $r), $row['quantity_materials_month_plan_07'] != 0 ? $row['quantity_materials_month_plan_07'] : NULL);
				$sheet->setCellValue('AR' . ($q + $r), $row['price_materials_month_plan_07'] != 0 ? $row['price_materials_month_plan_07'] : NULL);

				$sheet->setCellValue('AT' . ($q + $r), $row['quantity_materials_month_plan_08'] != 0 ? $row['quantity_materials_month_plan_08'] : NULL);
				$sheet->setCellValue('AV' . ($q + $r), $row['price_materials_month_plan_08'] != 0 ? $row['price_materials_month_plan_08'] : NULL);

				$sheet->setCellValue('AX' . ($q + $r), $row['quantity_materials_month_plan_09'] != 0 ? $row['quantity_materials_month_plan_09'] : NULL);
				$sheet->setCellValue('AZ' . ($q + $r), $row['price_materials_month_plan_09'] != 0 ? $row['price_materials_month_plan_09'] : NULL);

				$sheet->setCellValue('BB' . ($q + $r), $row['quantity_materials_quarter_plan_03'] != 0 ? $row['quantity_materials_quarter_plan_03'] : NULL);
				$sheet->setCellValue('BD' . ($q + $r), $row['price_materials_quarter_plan_03'] != 0 ? $row['price_materials_quarter_plan_03'] : NULL);

				// Полный 4 квартал
				$sheet->setCellValue('BF' . ($q + $r), $row['quantity_materials_month_plan_10'] != 0 ? $row['quantity_materials_month_plan_10'] : NULL);
				$sheet->setCellValue('BH' . ($q + $r), $row['price_materials_month_plan_10'] != 0 ? $row['price_materials_month_plan_10'] : NULL);

				$sheet->setCellValue('BJ' . ($q + $r), $row['quantity_materials_month_plan_11'] != 0 ? $row['quantity_materials_month_plan_11'] : NULL);
				$sheet->setCellValue('BL' . ($q + $r), $row['price_materials_month_plan_11'] != 0 ? $row['price_materials_month_plan_11'] : NULL);

				$sheet->setCellValue('BN' . ($q + $r), $row['quantity_materials_month_plan_12'] != 0 ? $row['quantity_materials_month_plan_12'] : NULL);
				$sheet->setCellValue('BP' . ($q + $r), $row['price_materials_month_plan_12'] != 0 ? $row['price_materials_month_plan_12'] : NULL);

				$sheet->setCellValue('BR' . ($q + $r), $row['quantity_materials_quarter_plan_04'] != 0 ? $row['quantity_materials_quarter_plan_04'] : NULL);
				$sheet->setCellValue('BT' . ($q + $r), $row['price_materials_quarter_plan_04'] != 0 ? $row['price_materials_quarter_plan_04'] : NULL);

				// Весь рік
				// $sheet->setCellValue('BV' . ($q + $r), 1);
				// $sheet->setCellValue('BX' . ($q + $r), $row['price_total_program'] != 0 ? $row['price_total_program'] : NULL);
				// $spreadsheet->getActiveSheet()->setCellValue('BU' .($q+$r), 1);
				// $spreadsheet->getActiveSheet()->setCellValue('BW' .($q+$r), $row['price_materials'] != 0 ? $row['price_materials'] : NULL);
				$spreadsheet->getActiveSheet()->setCellValue('BV' . ($q + $r), '=J' . ($q + $r) . '+N' . ($q + $r) . '+R' . ($q + $r) . '+Z' . ($q + $r) . '+AD' . ($q + $r) . '+AH' . ($q + $r) . '+AP' . ($q + $r) . '+AT' . ($q + $r) . '+AX' . ($q + $r) . '+BF' . ($q + $r) . '+BJ' . ($q + $r) . '+BN' . ($q + $r));
				$spreadsheet->getActiveSheet()->setCellValue('BX' . ($q + $r), '=L' . ($q + $r) . '+P' . ($q + $r) . '+T' . ($q + $r) . '+AB' . ($q + $r) . '+AF' . ($q + $r) . '+AJ' . ($q + $r) . '+AR' . ($q + $r) . '+AV' . ($q + $r) . '+AZ' . ($q + $r) . '+BH' . ($q + $r) . '+BL' . ($q + $r) . '+BP' . ($q + $r));

				// $spreadsheet->getActiveSheet()->setCellValue('F' .$r,'=SUM(F'.($r+1).':F' .($q+$r).')');
				// $spreadsheet->getActiveSheet()->setCellValue('G' .$r,'=SUM(G'.($r+1).':G' .($q+$r).')');
				// $spreadsheet->getActiveSheet()->setCellValue('H' .$r,'=SUM(H'.($r+1).':H' .($q+$r).')');

				$sheet->setCellValue('BZ' . ($q + $r), $row['start_work_quarter_1']);
				$sheet->setCellValue('CA' . ($q + $r), $row['end_work_quarter_1']);
				$sheet->setCellValue('CB' . ($q + $r), $row['start_work_quarter_2']);
				$sheet->setCellValue('CC' . ($q + $r), $row['end_work_quarter_2']);
				$sheet->setCellValue('CD' . ($q + $r), $row['start_work_quarter_3']);
				$sheet->setCellValue('CE' . ($q + $r), $row['end_work_quarter_3']);
				$sheet->setCellValue('CF' . ($q + $r), $row['start_work_quarter_4']);
				$sheet->setCellValue('CG' . ($q + $r), $row['end_work_quarter_4']);
				$sheet->setCellValue('CH' . ($q + $r), $row['short_description'])
					->getStyle('CH' . ($q + $r))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

				foreach ($letters as $letter) {
					$spreadsheet->getActiveSheet()->setCellValue($letter . $r, '=SUM(' . $letter . ($r + 1) . ':' . $letter . ($q + $r) . ')');
				}

				$spreadsheet->getActiveSheet()->getStyle('G' . ($r) . ':BY' . ($q + $r))->getNumberFormat()->setFormatCode('0.000');
				$sheet->getStyle('B' . ($q + $r))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
				$sheet->getStyle('A' . ($q + $r) . ':CH' . ($q + $r))->getFont()->setBold(false);
				$sheet->getStyle('A' . ($q + $r) . ':CH' . ($q + $r))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
				$spreadsheet->getActiveSheet()->getStyle('A' . ($q + $r) . ':CH' . ($q + $r))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');
				// $spreadsheet->getActiveSheet()->getStyle('C' . ($q + $r))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('0000FF');

				$q++;
			}

			array_push($sum_group_array, $r);

			$sheet->getStyle('B' . ($r))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
			$sheet->getStyle('A' . ($r) . ':CH' . ($r))->getFont()->setBold(true);
			$sheet->getStyle('A' . ($r) . ':CH' . ($r))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
			$bg_color = ($row['repair_method'] == 'підр.') ? 'F0E68C' : '05BD4C';
			$spreadsheet->getActiveSheet()->getStyle('A' . $r . ':CH' . $r)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB($bg_color);

			$r += count($v_group) + 1;
			$i++;
		}

		// ######################################################################################################################
		// print_r($sum_group_array);
		// exit;

		$sheet->setCellValue('B' . ($r), 'Всього:');
		$sheet->setCellValue('G' . ($r), '=H' . ($r) . '+I' . ($r));
		// $sheet->setCellValue('H' . ($r), $sum_contract);
		// $sheet->setCellValue('I' . ($r), $sum_economic);

		foreach ($letters_2 as $letter) {
			$sum = $letter . implode("+" . $letter, $sum_group_array);
			foreach ($sum_group_array as $cell) {
				$spreadsheet->getActiveSheet()->setCellValue($letter . $r, '=' . $sum);
			}
		}

		$sheet->setCellValue('B' . ($r + 1), 'Аварійно-відновлювальні роботи');
		// $sheet->setCellValue('G' . ($r + 1), trim($sum_avr));
		// $sheet->setCellValue('I' . ($r + 1), trim($sum_avr));

		$sheet->setCellValue('B' . ($r + 2), 'Всього по службі підстанцій:');
		$spreadsheet->getActiveSheet()->setCellValue('G' . ($r + 2), '=G' . ($r) . '+G' . ($r + 1))
			->getStyle('G' . ($r + 2))->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
		$spreadsheet->getActiveSheet()->setCellValue('H' . ($r + 2), '=H' . ($r));
		$spreadsheet->getActiveSheet()->setCellValue('I' . ($r + 2), '=I' . ($r));

		$spreadsheet->getActiveSheet()->getStyle('A' . ($r) . ':CH' . ($r))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('6495ED');
		$spreadsheet->getActiveSheet()->getStyle('A' . ($r + 2) . ':CH' . ($r + 2))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('6495ED');

		for ($i = 0; $i < 3; $i++) {
			$sheet->getStyle('A' . ($r + $i))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle('B' . ($r + $i))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
			$sheet->getStyle('C' . ($r + $i) . ':CH' . ($r + $i))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle('A' . ($r + $i) . ':CH' . ($r + $i))->getFont()->setBold(true)->setSize(12);
			$sheet->getStyle('A' . ($r + $i) . ':CH' . ($r + $i))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
			$spreadsheet->getActiveSheet()->getStyle('G' . ($r + $i) . ':BY' . ($r + $i))->getNumberFormat()->setFormatCode('0.000');
		}

		$sheet->mergeCells('A' . ($r + 6) . ':Y' . ($r + 6))->setCellValue('A' . ($r + 6), 'Начальник СП ________________ Юрій СИЧОВ');
		$sheet->mergeCells('A' . ($r + 6 + 3) . ':Y' . ($r + 6 + 3))->setCellValue('A' . ($r + 6 + 3), 'План склав ______________');
		$sheet->mergeCells('A' . ($r + 6 + 3 + 3) . ':Y' . ($r + 6 + 3 + 3))->setCellValue('A' . ($r + 6 + 3 + 3), 'Файл створено за допомогою АПроСТОР v1.0');

		// Додаємо нові зміни в шаблон
		// $spreadsheet->getActiveSheet()->insertNewColumnBefore('G', 1);
		// $sheet->mergeCells('G5:G6')->setCellValue('G5', 'Загальна вартість, тис.грн без ПДВ');
		// $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);

		// $r = 8;
		// $i = 1;
		// $start = '1.4';
		// foreach ($new_array as $k_group => $v_group) {
		// }
		// Кінець змін

		$sheet->getStyle('A1');

		$writer = new Xlsx($spreadsheet);

		$filename = 'План ремонтної програми на ' . (date('Y') + 1) . ' рік.xlsx';
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');

		// $writer->save('output_data/' . htmlspecialchars($title) . '.xlsx');
	}

	public function delete_schedule($subdividion_id = NULL, $complete_renovation_object_id = NULL)
	{
		if ($this->session->user->group !== 'admin') {
			redirect($this->agent->referrer());
		}

		if (($subdividion_id && !is_numeric($subdividion_id)) || ($complete_renovation_object_id && !is_numeric($complete_renovation_object_id))) {
			show_404();
		}
		$this->load->library('user_agent');

		$complete_renovation_objects_id = $this->schedule_model->get_schedules_id_for_complete_renovation_object($subdividion_id, $complete_renovation_object_id);

		foreach ($complete_renovation_objects_id as $row) {
			$this->schedule_material_model->delete_for_schedule_id_and_year($row->id, (date('Y') + 1));
			$this->schedule_worker_model->delete_for_schedule_id_and_year($row->id, (date('Y') + 1));
			$this->schedule_technic_model->delete_for_schedule_id_and_year($row->id, (date('Y') + 1));
			$this->schedule_year_model->delete_for_schedule_id_and_year($row->id, (date('Y') + 1));
			// $this->schedule_note_model->delete_for_schedule_id_and_year($row->id, (date('Y') + 1));
		}

		redirect($this->agent->referrer());
	}

	public function materials()
	{
		$data = [];
		$data['title'] = 'Необхідність в матеріаліалах на ' . (date('Y') + 1) . ' рік';
		$data['content'] = 'schedules/materials';
		$data['page'] = 'schedules/materials';
		$data['page_js'] = 'schedules';
		$data['datatables'] = TRUE;
		$data['datatables_button'] = TRUE;
		$data['title_heading'] = 'Перелік матеріалів на наступний рік';
		$data['title_heading_card'] = 'Перелік матеріалів на наступний рік';
		$data['materials'] = $this->schedule_material_model->get_materials_for_next_year_group();

		// echo "<pre>";
		// print_r($data['materials']);
		// echo "</pre>";

		$this->load->view('layout', $data);
	}

	public function get_materials_for_schedule($schedule_id = NULL)
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!$schedule_id) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'В запросі немає ідентифікатора!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!is_numeric($schedule_id)) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Ідентифікатор повиненн бути цілим числом!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$data = $this->schedule_material_model->get_materials_for_schedule_id($schedule_id);

		if ($data) {
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані отримано', 'data' => $data], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані отримано', 'data' => []], JSON_UNESCAPED_UNICODE));
		}
	}

	public function get_workers_for_schedule($schedule_id = NULL)
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!$schedule_id) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'В запросі немає ідентифікатора!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!is_numeric($schedule_id)) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Ідентифікатор повиненн бути цілим числом!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$data = $this->schedule_worker_model->get_workers_for_schedule_id($schedule_id);

		if ($data) {
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані отримано', 'data' => $data], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані отримано', 'data' => []], JSON_UNESCAPED_UNICODE));
		}
	}

	public function get_technics_for_schedule($schedule_id = NULL)
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!$schedule_id) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'В запросі немає ідентифікатора!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!is_numeric($schedule_id)) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Ідентифікатор повиненн бути цілим числом!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$data = $this->schedule_technic_model->get_technics_for_schedule_id($schedule_id);

		if ($data) {
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані отримано', 'data' => $data], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані отримано', 'data' => []], JSON_UNESCAPED_UNICODE));
		}
	}

	public function get_prices_materials_for_schedule($schedule_id = NULL)
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!$schedule_id) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'В запросі немає ідентифікатора!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!is_numeric($schedule_id)) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Ідентифікатор повиненн бути цілим числом!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$data = $this->schedule_material_model->get_prices_materials_for_schedule_id($schedule_id);

		if ($data) {
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані отримано', 'data' => $data], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані отримано', 'data' => 0], JSON_UNESCAPED_UNICODE));
		}
	}

	public function get_note_for_schedule($schedule_id = NULL)
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!$schedule_id) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'В запросі немає ідентифікатора!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!is_numeric($schedule_id)) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Ідентифікатор повиненн бути цілим числом!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$data = $this->schedule_note_model->get_note_for_schedule_id($schedule_id);

		if ($data) {
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані отримано', 'data' => $data], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані отримано', 'data' => 0], JSON_UNESCAPED_UNICODE));
		}
	}

	public function add_note_for_schedule()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$data = json_decode(file_get_contents("php://input"), true);
		$data['year_service'] = (date('Y') + 1);
		$data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');

		$this->schedule_note_model->delete_for_schedule_id_and_year($data['schedule_id'], (date('Y') + 1));
		$this->schedule_note_model->insert($data);

		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані записано', 'data' => $data], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function add_worker_for_schedule()
	{
		$this->output->set_content_type('application/json');

		$this->load->library('form_validation');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$data['schedule_id'] = $this->input->post('schedule_id');
		$data['worker_id'] = $this->input->post('worker_id');
		$data['quantity'] = $this->input->post('quantity');
		$data['year_service'] = (date('Y') + 1);
		$data['is_extra'] = 1;
		$data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');

		$this->form_validation->set_rules('worker_id', 'Працівник', 'required');
		$this->form_validation->set_rules('quantity', 'Кількість, люд.год', 'required|numeric');

		if ($this->form_validation->run() == FALSE) {
			$this->form_validation->set_error_delimiters('', '');
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => $this->form_validation->error_array()], JSON_UNESCAPED_UNICODE));
			return;
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->schedule_worker_model->insert($data);

		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані записано', 'data' => $data], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function add_workers_for_schedule()
	{
		$this->output->set_content_type('application/json');

		$this->load->library('form_validation');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->form_validation->set_rules('quantity[]', 'Кількість, люд.год', 'numeric');

		if ($this->form_validation->run() == FALSE) {
			$this->form_validation->set_error_delimiters('', '');
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => $this->form_validation->error_array()], JSON_UNESCAPED_UNICODE));
			return;
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$data = $this->set_schedule_workers_add_data($this->input->post());

		if (!$data) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Введіть дані!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$result = $this->schedule_worker_model->add_data_batch($data);

		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані записано', 'data' => $data], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function add_technic_for_schedule()
	{
		$this->output->set_content_type('application/json');

		$this->load->library('form_validation');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$data['schedule_id'] = $this->input->post('schedule_id');
		$data['technic_id'] = $this->input->post('technic_id');
		$data['quantity'] = $this->input->post('quantity');
		$data['year_service'] = (date('Y') + 1);
		$data['is_extra'] = 1;
		$data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');

		$this->form_validation->set_rules('technic_id', 'Техніка', 'required');
		$this->form_validation->set_rules('quantity', 'Кількість, маш.год', 'required|numeric');

		if ($this->form_validation->run() == FALSE) {
			$this->form_validation->set_error_delimiters('', '');
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => $this->form_validation->error_array()], JSON_UNESCAPED_UNICODE));
			return;
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->schedule_technic_model->insert($data);

		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані записано', 'data' => $data], JSON_UNESCAPED_UNICODE));
		return;
	}

	private function get_workers($complete_renovation_object_id = NULL, $type_service = NULL)
	{
		$results = $this->schedule_worker_model->get_workers_for_defect_list($complete_renovation_object_id, $type_service);
		$workers = [];

		// if (!$type_service) {
		// 	$equipment  = array_column($results, 'equipment');
		// 	$type_service  = array_column($results, 'type_service');

		// 	array_multisort($equipment, SORT_ASC, $type_service, SORT_ASC, $results);
		// echo "<pre>";
		// print_r($results);
		// echo "</pre>";
		// }
		// exit;

		foreach ($results as $row) {
			$group_array[$row->disp . '_' . $row->type_service_id][] = $row;
		}

		if (isset($group_array)) {
			$i = 1;
			foreach ($group_array as $data) {
				$new_array['count'] = $i++;
				$new_array['oborud_with_voltage'] = '';
				$new_array['disp'] = '';
				$new_array['list_works'] = '';
				$new_array['cipher'] = '';
				$new_array['unit'] = '';
				$new_array['quantity_equipment'] = '';
				$new_array['norma'] = [];
				$new_array['norma_total'] = '';
				$new_array['quantity_quarter_1'] = NULL;
				$new_array['quantity_quarter_2'] = NULL;
				$new_array['quantity_quarter_3'] = NULL;
				$new_array['quantity_quarter_4'] = NULL;
				$new_array['quantity_month_1'] = NULL;
				$new_array['quantity_month_2'] = NULL;
				$new_array['quantity_month_3'] = NULL;
				$new_array['quantity_month_4'] = NULL;
				$new_array['quantity_month_5'] = NULL;
				$new_array['quantity_month_6'] = NULL;
				$new_array['quantity_month_7'] = NULL;
				$new_array['quantity_month_8'] = NULL;
				$new_array['quantity_month_9'] = NULL;
				$new_array['quantity_month_10'] = NULL;
				$new_array['quantity_month_11'] = NULL;
				$new_array['quantity_month_12'] = NULL;

				foreach ($data as $key => $row) {
					if ($row->type_service_id == 1) {
						$row->type_service = 'Капітальний ремонт';
					} elseif ($row->type_service_id == 2) {
						$row->type_service = 'Поточний ремонт';
					} else {
						$row->type_service = 'Технічне обслуговування';
					}

					$new_array['oborud_with_voltage'] = $row->equipment . ' ' . round($row->voltage, 0) . ' кВ';
					$new_array['disp'] = $row->disp;
					$new_array['list_works'] = $row->type ? $row->type_service . ' ' . $row->type : $row->type_service . ' ' . $row->short_type;
					$new_array['cipher'] = $row->cipher;
					$new_array['unit'] = 'шт';
					$new_array['quantity_equipment'] = $row->quantity_equipment;
					$new_array['norma'][] = $row->quantity / $row->quantity_equipment;
					$new_array['norma_total'] = array_sum($new_array['norma']) * $row->quantity_equipment;
					if ($row->month > 0 && $row->month <= 3) {
						$new_array['quantity_quarter_1'] = 1;
					}
					if ($row->month > 3 && $row->month <= 6) {
						$new_array['quantity_quarter_2'] = 1;
					}
					if ($row->month > 6 && $row->month <= 9) {
						$new_array['quantity_quarter_3'] = 1;
					}
					if ($row->month > 9 && $row->month <= 12) {
						$new_array['quantity_quarter_4'] = 1;
					}
					if ($row->month == 1) {
						$new_array['quantity_month_1'] = 1;
					}
					if ($row->month == 2) {
						$new_array['quantity_month_2'] = 1;
					}
					if ($row->month == 3) {
						$new_array['quantity_month_3'] = 1;
					}
					if ($row->month == 4) {
						$new_array['quantity_month_4'] = 1;
					}
					if ($row->month == 5) {
						$new_array['quantity_month_5'] = 1;
					}
					if ($row->month == 6) {
						$new_array['quantity_month_6'] = 1;
					}
					if ($row->month == 7) {
						$new_array['quantity_month_7'] = 1;
					}
					if ($row->month == 8) {
						$new_array['quantity_month_8'] = 1;
					}
					if ($row->month == 9) {
						$new_array['quantity_month_9'] = 1;
					}
					if ($row->month == 10) {
						$new_array['quantity_month_10'] = 1;
					}
					if ($row->month == 11) {
						$new_array['quantity_month_11'] = 1;
					}
					if ($row->month == 12) {
						$new_array['quantity_month_12'] = 1;
					}
				}
				array_push($workers, $new_array);
			}
		}

		// echo "<pre>";
		// print_r($results);
		// print_r($group_array['ВД-1_1']);
		// echo count($group_array);
		// print_r($workers);
		// echo "</pre>";
		// exit;
		return $workers;
	}

	private function get_materials($complete_renovation_object_id = NULL, $type_service = NULL)
	{
		$results = $this->schedule_material_model->get_materials_for_defect_list($complete_renovation_object_id, $type_service);
		$materials = [];

		foreach ($results as $row) {
			// $group_array[$row->equipment . '_' . $row->voltage . '_' . $row->name][] = $row;
			$group_array[$row->equipment . '_' . $row->voltage][] = $row;
		}

		if (isset($group_array)) {
			$i = 1;
			foreach ($group_array as $data) {
				$new_array['number'] = '';
				$new_array['equipment'] = '';
				$new_array['equipment_quantity_temp'] = [];
				$new_array['equipment_quantity'] = '';
				$new_array['material'] = [];
				$new_array['is_extra'] = [];
				$new_array['number_r3'] = [];
				$new_array['unit'] = [];
				$new_array['quantity_temp'] = [];
				$new_array['quantity'] = [];
				$new_array['price_no_vat'] = [];
				$new_array['price_total_no_vat'] = [];
				$new_array['quantity_quarter_1'] = [];
				$new_array['quantity_quarter_2'] = [];
				$new_array['quantity_quarter_3'] = [];
				$new_array['quantity_quarter_4'] = [];
				$new_array['quantity_month_1'] = [];
				$new_array['quantity_month_2'] = [];
				$new_array['quantity_month_3'] = [];
				$new_array['quantity_month_4'] = [];
				$new_array['quantity_month_5'] = [];
				$new_array['quantity_month_6'] = [];
				$new_array['quantity_month_7'] = [];
				$new_array['quantity_month_8'] = [];
				$new_array['quantity_month_9'] = [];
				$new_array['quantity_month_10'] = [];
				$new_array['quantity_month_11'] = [];
				$new_array['quantity_month_12'] = [];
				foreach ($data as $row) {
					$new_array['number'] = $i;
					$new_array['equipment'] = $row->equipment . ' ' . round($row->voltage, 0) . ' кВ';
					$new_array['equipment_quantity_temp'][$row->schedule_id] = $row->equipment;
					$new_array['equipment_quantity'] = count($new_array['equipment_quantity_temp']) * $row->amount . ' шт';
					$new_array['material'][$row->name] = $row->name;
					$new_array['is_extra'][$row->name] = $row->is_extra;
					$new_array['number_r3'][$row->name] = $row->r3;
					$new_array['unit'][$row->name] = $row->unit;
					$new_array['quantity_temp'][$row->name][] = $row->quantity;
					$new_array['quantity'][$row->name] = array_sum($new_array['quantity_temp'][$row->name]);
					$new_array['price_no_vat'][$row->name] = $row->price / 1000;
					$new_array['price_total_no_vat'][$row->name] = ($row->price * array_sum($new_array['quantity_temp'][$row->name]) / 1000);
					if ($row->month > 0 && $row->month <= 3) {
						if (!isset($new_array['quantity_quarter_1'][$row->name])) {
							$new_array['quantity_quarter_1'][$row->name] = $row->quantity;
						} else {
							$new_array['quantity_quarter_1'][$row->name] += $row->quantity;
						}
					}
					if ($row->month > 3 && $row->month <= 6) {
						if (!isset($new_array['quantity_quarter_2'][$row->name])) {
							$new_array['quantity_quarter_2'][$row->name] = $row->quantity;
						} else {
							$new_array['quantity_quarter_2'][$row->name] += $row->quantity;
						}
					}
					if ($row->month > 6 && $row->month <= 9) {
						if (!isset($new_array['quantity_quarter_3'][$row->name])) {
							$new_array['quantity_quarter_3'][$row->name] = $row->quantity;
						} else {
							$new_array['quantity_quarter_3'][$row->name] += $row->quantity;
						}
					}
					if ($row->month > 9 && $row->month <= 12) {
						if (!isset($new_array['quantity_quarter_4'][$row->name])) {
							$new_array['quantity_quarter_4'][$row->name] = $row->quantity;
						} else {
							$new_array['quantity_quarter_4'][$row->name] += $row->quantity;
						}
					}
					if ($row->month == 1) {
						if (!isset($new_array['quantity_month_1'][$row->name])) {
							$new_array['quantity_month_1'][$row->name] = $row->quantity;
						} else {
							$new_array['quantity_month_1'][$row->name] += $row->quantity;
						}
					}
					if ($row->month == 2) {
						if (!isset($new_array['quantity_month_2'][$row->name])) {
							$new_array['quantity_month_2'][$row->name] = $row->quantity;
						} else {
							$new_array['quantity_month_2'][$row->name] += $row->quantity;
						}
					}
					if ($row->month == 3) {
						if (!isset($new_array['quantity_month_3'][$row->name])) {
							$new_array['quantity_month_3'][$row->name] = $row->quantity;
						} else {
							$new_array['quantity_month_3'][$row->name] += $row->quantity;
						}
					}
					if ($row->month == 4) {
						if (!isset($new_array['quantity_month_4'][$row->name])) {
							$new_array['quantity_month_4'][$row->name] = $row->quantity;
						} else {
							$new_array['quantity_month_4'][$row->name] += $row->quantity;
						}
					}
					if ($row->month == 5) {
						if (!isset($new_array['quantity_month_5'][$row->name])) {
							$new_array['quantity_month_5'][$row->name] = $row->quantity;
						} else {
							$new_array['quantity_month_5'][$row->name] += $row->quantity;
						}
					}
					if ($row->month == 6) {
						if (!isset($new_array['quantity_month_6'][$row->name])) {
							$new_array['quantity_month_6'][$row->name] = $row->quantity;
						} else {
							$new_array['quantity_month_6'][$row->name] += $row->quantity;
						}
					}
					if ($row->month == 7) {
						if (!isset($new_array['quantity_month_7'][$row->name])) {
							$new_array['quantity_month_7'][$row->name] = $row->quantity;
						} else {
							$new_array['quantity_month_7'][$row->name] += $row->quantity;
						}
					}
					if ($row->month == 8) {
						if (!isset($new_array['quantity_month_8'][$row->name])) {
							$new_array['quantity_month_8'][$row->name] = $row->quantity;
						} else {
							$new_array['quantity_month_8'][$row->name] += $row->quantity;
						}
					}
					if ($row->month == 9) {
						if (!isset($new_array['quantity_month_9'][$row->name])) {
							$new_array['quantity_month_9'][$row->name] = $row->quantity;
						} else {
							$new_array['quantity_month_9'][$row->name] += $row->quantity;
						}
					}
					if ($row->month == 10) {
						if (!isset($new_array['quantity_month_10'][$row->name])) {
							$new_array['quantity_month_10'][$row->name] = $row->quantity;
						} else {
							$new_array['quantity_month_10'][$row->name] += $row->quantity;
						}
					}
					if ($row->month == 11) {
						if (!isset($new_array['quantity_month_11'][$row->name])) {
							$new_array['quantity_month_11'][$row->name] = $row->quantity;
						} else {
							$new_array['quantity_month_11'][$row->name] += $row->quantity;
						}
					}
					if ($row->month == 12) {
						if (!isset($new_array['quantity_month_12'][$row->name])) {
							$new_array['quantity_month_12'][$row->name] = $row->quantity;
						} else {
							$new_array['quantity_month_12'][$row->name] += $row->quantity;
						}
					}
				}
				array_push($materials, $new_array);
				$i++;
			}
		}

		$equipment  = array_column($materials, 'equipment');
		array_multisort($equipment, SORT_ASC, $materials);

		// echo "<pre>";
		// print_r($results);
		// print_r($group_array);
		// echo count($group_array);
		// print_r($materials);
		// echo "</pre>";
		// exit;
		return $materials;
	}

	private function get_data_for_schedule_year($complete_renovation_object_id = NULL, $type_service = NULL)
	{
		$results = $this->schedule_year_model->get_data_for_schedule_year($complete_renovation_object_id, $type_service);

		$i = 1;
		$new_array = [];
		foreach ($results as $k => $row) {
			$new_array[$row->disp . ' ' . $row->type_service]['count'] = $i;
			$new_array[$row->disp . ' ' . $row->type_service]['oborud_with_voltage'] = $row->equipment . ' ' . round($row->voltage, 0) . ' кВ';
			$new_array[$row->disp . ' ' . $row->type_service]['disp'] = $row->disp;
			$new_array[$row->disp . ' ' . $row->type_service]['year_commissioning'] = $row->year_commissioning;
			$new_array[$row->disp . ' ' . $row->type_service]['repair_type'] = $row->type_service;
			$new_array[$row->disp . ' ' . $row->type_service]['repair_method_1'] = $row->is_contract_method ? '' : 'госп.';
			$new_array[$row->disp . ' ' . $row->type_service]['repair_method_2'] = $row->is_contract_method ? 'підр.' : '';
			$new_array[$row->disp . ' ' . $row->type_service]['workers'] = $row->workers;
			$new_array[$row->disp . ' ' . $row->type_service]['materials'] = $row->materials;
			$new_array[$row->disp . ' ' . $row->type_service]['month_service_1'] = NULL;
			$new_array[$row->disp . ' ' . $row->type_service]['month_service_2'] = NULL;
			$new_array[$row->disp . ' ' . $row->type_service]['month_service_3'] = NULL;
			$new_array[$row->disp . ' ' . $row->type_service]['month_service_4'] = NULL;
			$new_array[$row->disp . ' ' . $row->type_service]['month_service_5'] = NULL;
			$new_array[$row->disp . ' ' . $row->type_service]['month_service_6'] = NULL;
			$new_array[$row->disp . ' ' . $row->type_service]['month_service_7'] = NULL;
			$new_array[$row->disp . ' ' . $row->type_service]['month_service_8'] = NULL;
			$new_array[$row->disp . ' ' . $row->type_service]['month_service_9'] = NULL;
			$new_array[$row->disp . ' ' . $row->type_service]['month_service_10'] = NULL;
			$new_array[$row->disp . ' ' . $row->type_service]['month_service_11'] = NULL;
			$new_array[$row->disp . ' ' . $row->type_service]['month_service_12'] = NULL;
			$new_array[$row->disp . ' ' . $row->type_service]['total_oborud_month_9'] = NULL;

			// if ($row['resource_type'] == 'Матеріал') {
			// 	$total[$row['stantion']]['total_materials'][] = $row['price_total_no_vat'] / 1000;
			// 	$new_array[$row->disp . ' ' . $row->type_service]['price_total_no_vat'][] = $row['price_total_no_vat'] / 1000;
			// }
			if ($row->month_service == 1) {
				$new_array[$row->disp . ' ' . $row->type_service]['month_service_1'] = 1;
			}
			if ($row->month_service == 2) {
				$new_array[$row->disp . ' ' . $row->type_service]['month_service_2'] = 1;
			}
			if ($row->month_service == 3) {
				$new_array[$row->disp . ' ' . $row->type_service]['month_service_3'] = 1;
			}
			if ($row->month_service == 4) {
				$new_array[$row->disp . ' ' . $row->type_service]['month_service_4'] = 1;
			}
			if ($row->month_service == 5) {
				$new_array[$row->disp . ' ' . $row->type_service]['month_service_5'] = 1;
			}
			if ($row->month_service == 6) {
				$new_array[$row->disp . ' ' . $row->type_service]['month_service_6'] = 1;
			}
			if ($row->month_service == 7) {
				$new_array[$row->disp . ' ' . $row->type_service]['month_service_7'] = 1;
			}
			if ($row->month_service == 8) {
				$new_array[$row->disp . ' ' . $row->type_service]['month_service_8'] = 1;
			}
			if ($row->month_service == 9) {
				$new_array[$row->disp . ' ' . $row->type_service]['month_service_9'] = 1;
			}
			if ($row->month_service == 10) {
				$new_array[$row->disp . ' ' . $row->type_service]['month_service_10'] = 1;
			}
			if ($row->month_service == 11) {
				$new_array[$row->disp . ' ' . $row->type_service]['month_service_11'] = 1;
			}
			if ($row->month_service == 12) {
				$new_array[$row->disp . ' ' . $row->type_service]['month_service_12'] = 1;
			}

			$new_array[$row->disp . ' ' . $row->type_service]['class_voltage_full'] = $row->voltage;

			// $new_array[$row->disp . ' ' . $row->type_service]['note'] = $row['note'];
			$i++;
		}


		// echo "<pre>";
		// print_r($results);
		// print_r($new_array);
		// echo "</pre>";
		// exit;

		return $new_array;
	}

	private function get_data_for_program()
	{
		$data = $this->schedule_year_model->get_prices_materials_for_next_year();

		if (!$data) {
			return;
		}


		$start_month_to_array = [
			1 => (date('Y') + 1) . '-01-01',
			2 => (date('Y') + 1) . '-02-01',
			3 => (date('Y') + 1) . '-03-01',
			4 => (date('Y') + 1) . '-04-01',
			5 => (date('Y') + 1) . '-05-01',
			6 => (date('Y') + 1) . '-06-01',
			7 => (date('Y') + 1) . '-07-01',
			8 => (date('Y') + 1) . '-08-01',
			9 => (date('Y') + 1) . '-09-01',
			10 => (date('Y') + 1) . '-10-01',
			11 => (date('Y') + 1) . '-11-01',
			12 => (date('Y') + 1) . '-12-01',
		];

		$end_month_to_array = [
			1 => (date('Y') + 1) . '-01-31',
			2 => (date('Y') + 1) . '-02-28',
			3 => (date('Y') + 1) . '-03-31',
			4 => (date('Y') + 1) . '-04-30',
			5 => (date('Y') + 1) . '-05-31',
			6 => (date('Y') + 1) . '-06-30',
			7 => (date('Y') + 1) . '-07-31',
			8 => (date('Y') + 1) . '-08-31',
			9 => (date('Y') + 1) . '-09-30',
			10 => (date('Y') + 1) . '-10-31',
			11 => (date('Y') + 1) . '-11-30',
			12 => (date('Y') + 1) . '-12-31',
		];

		foreach ($data as $k => $row) {

			if ($row['repair_type'] == 'КР') {
				$row['repair_type'] = 'Капітальний ремонт';
			}
			if ($row['repair_type'] == 'ПР') {
				$row['repair_type'] = 'Поточний ремонт';
			}

			if ($row['repair_method'] === 'ПС') {
				$group = $row['repair_type'] . '_' . $row['repair_method'] . '_' . ' ПС-' . $row['class_voltage'] . ' кВ ' . $row['disp'];
			} else {
				$group = $row['repair_type'] . '_' . $row['repair_method'] . '_' . ' ПС-' . $row['class_voltage'] . ' кВ';
			}

			$new_array[$group][$row['stantion']]['repair_type'] = $row['repair_type'];
			$new_array[$group][$row['stantion']]['class_voltage'] = $row['class_voltage'];

			// Column B
			$new_array[$group][$row['stantion']]['stantion'] = $row['stantion'];
			$new_array[$group][$row['stantion']]['note_for_contract'] = $row['repair_method'] === 'ПС' ? $row['note_for_contract'] : '';

			// Column C
			$new_array[$group][$row['stantion']]['inventar_number'] = $row['inventar_number'];

			// Column D
			$new_array[$group][$row['stantion']]['repair_method'] = $row['repair_method'] === 'ГС' ? 'госп.' : 'підр.';

			// Column E
			$new_array[$group][$row['stantion']]['unit'] = 'шт.';

			// Column F
			$new_array[$group][$row['stantion']]['quantity'] = 1;

			// Column G
			if (!isset($new_array[$group][$row['stantion']]['price_total'])) {
				$new_array[$group][$row['stantion']]['price_total'] = 0;
			}
			$new_array[$group][$row['stantion']]['price_total'] += $row['price_total_no_vat'] / 1000;

			// Column H
			if (!isset($new_array[$group][$row['stantion']]['price_contract'])) {
				$new_array[$group][$row['stantion']]['price_contract'] = 0;
			}
			$new_array[$group][$row['stantion']]['price_contract'] += ($row['repair_method'] === 'ПС') ? $row['price_total_no_vat'] / 1000 : 0;

			// Column I
			if (!isset($new_array[$group][$row['stantion']]['price_materials'])) {
				$new_array[$group][$row['stantion']]['price_materials'] = 0;
			}
			$new_array[$group][$row['stantion']]['price_materials'] += ($row['repair_method'] === 'ГС') ? $row['price_total_no_vat'] / 1000 : 0;

			// Column L
			if (!isset($new_array[$group][$row['stantion']]['price_materials_month_plan_01'])) {
				$new_array[$group][$row['stantion']]['price_materials_month_plan_01'] = 0;
			}
			$new_array[$group][$row['stantion']]['price_materials_month_plan_01'] += ($row['month'] == 1) ? $row['price_total_no_vat'] / 1000 : 0;

			// Column J
			if (!isset($new_array[$group][$row['stantion']]['quantity_materials_month_plan_01'])) {
				$new_array[$group][$row['stantion']]['quantity_materials_month_plan_01'] = 0;
			}
			$new_array[$group][$row['stantion']]['quantity_materials_month_plan_01'] = $new_array[$group][$row['stantion']]['price_materials_month_plan_01'] / $new_array[$group][$row['stantion']]['price_total'];

			// Column P
			if (!isset($new_array[$group][$row['stantion']]['price_materials_month_plan_02'])) {
				$new_array[$group][$row['stantion']]['price_materials_month_plan_02'] = 0;
			}
			$new_array[$group][$row['stantion']]['price_materials_month_plan_02'] += ($row['month'] == 2) ? $row['price_total_no_vat'] / 1000 : 0;

			// Column N
			if (!isset($new_array[$group][$row['stantion']]['quantity_materials_month_plan_02'])) {
				$new_array[$group][$row['stantion']]['quantity_materials_month_plan_02'] = 0;
			}
			$new_array[$group][$row['stantion']]['quantity_materials_month_plan_02'] = $new_array[$group][$row['stantion']]['price_materials_month_plan_02'] / $new_array[$group][$row['stantion']]['price_total'];

			// Column T
			if (!isset($new_array[$group][$row['stantion']]['price_materials_month_plan_03'])) {
				$new_array[$group][$row['stantion']]['price_materials_month_plan_03'] = 0;
			}
			$new_array[$group][$row['stantion']]['price_materials_month_plan_03'] += ($row['month'] == 3) ? $row['price_total_no_vat'] / 1000 : 0;

			// Column R
			if (!isset($new_array[$group][$row['stantion']]['quantity_materials_month_plan_03'])) {
				$new_array[$group][$row['stantion']]['quantity_materials_month_plan_03'] = 0;
			}
			$new_array[$group][$row['stantion']]['quantity_materials_month_plan_03'] = $new_array[$group][$row['stantion']]['price_materials_month_plan_03'] / $new_array[$group][$row['stantion']]['price_total'];

			// Column X
			if (!isset($new_array[$group][$row['stantion']]['price_materials_quarter_plan_01'])) {
				$new_array[$group][$row['stantion']]['price_materials_quarter_plan_01'] = 0;
			}
			$new_array[$group][$row['stantion']]['price_materials_quarter_plan_01'] = $new_array[$group][$row['stantion']]['price_materials_month_plan_01'] + $new_array[$group][$row['stantion']]['price_materials_month_plan_02'] + $new_array[$group][$row['stantion']]['price_materials_month_plan_03'];

			// Column V
			if (!isset($new_array[$group][$row['stantion']]['quantity_materials_quarter_plan_01'])) {
				$new_array[$group][$row['stantion']]['quantity_materials_quarter_plan_01'] = 0;
			}
			$new_array[$group][$row['stantion']]['quantity_materials_quarter_plan_01'] = $new_array[$group][$row['stantion']]['price_materials_quarter_plan_01'] / $new_array[$group][$row['stantion']]['price_total'];

			// Column AB
			if (!isset($new_array[$group][$row['stantion']]['price_materials_month_plan_04'])) {
				$new_array[$group][$row['stantion']]['price_materials_month_plan_04'] = 0;
			}
			$new_array[$group][$row['stantion']]['price_materials_month_plan_04'] += ($row['month'] == 4) ? $row['price_total_no_vat'] / 1000 : 0;

			// Column Z
			if (!isset($new_array[$group][$row['stantion']]['quantity_materials_month_plan_04'])) {
				$new_array[$group][$row['stantion']]['quantity_materials_month_plan_04'] = 0;
			}
			$new_array[$group][$row['stantion']]['quantity_materials_month_plan_04'] = $new_array[$group][$row['stantion']]['price_materials_month_plan_04'] / $new_array[$group][$row['stantion']]['price_total'];

			// Column AF
			if (!isset($new_array[$group][$row['stantion']]['price_materials_month_plan_05'])) {
				$new_array[$group][$row['stantion']]['price_materials_month_plan_05'] = 0;
			}
			$new_array[$group][$row['stantion']]['price_materials_month_plan_05'] += ($row['month'] == 5) ? $row['price_total_no_vat'] / 1000 : 0;

			// Column AD
			if (!isset($new_array[$group][$row['stantion']]['quantity_materials_month_plan_05'])) {
				$new_array[$group][$row['stantion']]['quantity_materials_month_plan_05'] = 0;
			}
			$new_array[$group][$row['stantion']]['quantity_materials_month_plan_05'] = $new_array[$group][$row['stantion']]['price_materials_month_plan_05'] / $new_array[$group][$row['stantion']]['price_total'];

			// Column AJ
			if (!isset($new_array[$group][$row['stantion']]['price_materials_month_plan_06'])) {
				$new_array[$group][$row['stantion']]['price_materials_month_plan_06'] = 0;
			}
			$new_array[$group][$row['stantion']]['price_materials_month_plan_06'] += ($row['month'] == 6) ? $row['price_total_no_vat'] / 1000 : 0;

			// Column AH
			if (!isset($new_array[$group][$row['stantion']]['quantity_materials_month_plan_06'])) {
				$new_array[$group][$row['stantion']]['quantity_materials_month_plan_06'] = 0;
			}
			$new_array[$group][$row['stantion']]['quantity_materials_month_plan_06'] = $new_array[$group][$row['stantion']]['price_materials_month_plan_06'] / $new_array[$group][$row['stantion']]['price_total'];

			// Column AN
			if (!isset($new_array[$group][$row['stantion']]['price_materials_quarter_plan_02'])) {
				$new_array[$group][$row['stantion']]['price_materials_quarter_plan_02'] = 0;
			}
			$new_array[$group][$row['stantion']]['price_materials_quarter_plan_02'] = $new_array[$group][$row['stantion']]['price_materials_month_plan_04'] + $new_array[$group][$row['stantion']]['price_materials_month_plan_05'] + $new_array[$group][$row['stantion']]['price_materials_month_plan_06'];

			// Column AL
			if (!isset($new_array[$group][$row['stantion']]['quantity_materials_quarter_plan_02'])) {
				$new_array[$group][$row['stantion']]['quantity_materials_quarter_plan_02'] = 0;
			}
			$new_array[$group][$row['stantion']]['quantity_materials_quarter_plan_02'] = $new_array[$group][$row['stantion']]['price_materials_quarter_plan_02'] / $new_array[$group][$row['stantion']]['price_total'];

			// Column AR
			if (!isset($new_array[$group][$row['stantion']]['price_materials_month_plan_07'])) {
				$new_array[$group][$row['stantion']]['price_materials_month_plan_07'] = 0;
			}
			$new_array[$group][$row['stantion']]['price_materials_month_plan_07'] += ($row['month'] == 7) ? $row['price_total_no_vat'] / 1000 : 0;

			// Column AP
			if (!isset($new_array[$group][$row['stantion']]['quantity_materials_month_plan_07'])) {
				$new_array[$group][$row['stantion']]['quantity_materials_month_plan_07'] = 0;
			}
			$new_array[$group][$row['stantion']]['quantity_materials_month_plan_07'] = $new_array[$group][$row['stantion']]['price_materials_month_plan_07'] / $new_array[$group][$row['stantion']]['price_total'];

			// Column AV
			if (!isset($new_array[$group][$row['stantion']]['price_materials_month_plan_08'])) {
				$new_array[$group][$row['stantion']]['price_materials_month_plan_08'] = 0;
			}
			$new_array[$group][$row['stantion']]['price_materials_month_plan_08'] += ($row['month'] == 8) ? $row['price_total_no_vat'] / 1000 : 0;

			// Column AT
			if (!isset($new_array[$group][$row['stantion']]['quantity_materials_month_plan_08'])) {
				$new_array[$group][$row['stantion']]['quantity_materials_month_plan_08'] = 0;
			}
			$new_array[$group][$row['stantion']]['quantity_materials_month_plan_08'] = $new_array[$group][$row['stantion']]['price_materials_month_plan_08'] / $new_array[$group][$row['stantion']]['price_total'];

			// Column AZ
			if (!isset($new_array[$group][$row['stantion']]['price_materials_month_plan_09'])) {
				$new_array[$group][$row['stantion']]['price_materials_month_plan_09'] = 0;
			}
			$new_array[$group][$row['stantion']]['price_materials_month_plan_09'] += ($row['month'] == 9) ? $row['price_total_no_vat'] / 1000 : 0;

			// Column AX
			if (!isset($new_array[$group][$row['stantion']]['quantity_materials_month_plan_09'])) {
				$new_array[$group][$row['stantion']]['quantity_materials_month_plan_09'] = 0;
			}
			$new_array[$group][$row['stantion']]['quantity_materials_month_plan_09'] = $new_array[$group][$row['stantion']]['price_materials_month_plan_09'] / $new_array[$group][$row['stantion']]['price_total'];

			// Column BD
			if (!isset($new_array[$group][$row['stantion']]['price_materials_quarter_plan_03'])) {
				$new_array[$group][$row['stantion']]['price_materials_quarter_plan_03'] = 0;
			}
			$new_array[$group][$row['stantion']]['price_materials_quarter_plan_03'] = $new_array[$group][$row['stantion']]['price_materials_month_plan_07'] + $new_array[$group][$row['stantion']]['price_materials_month_plan_08'] + $new_array[$group][$row['stantion']]['price_materials_month_plan_09'];

			// Column BB
			if (!isset($new_array[$group][$row['stantion']]['quantity_materials_quarter_plan_03'])) {
				$new_array[$group][$row['stantion']]['quantity_materials_quarter_plan_03'] = 0;
			}
			$new_array[$group][$row['stantion']]['quantity_materials_quarter_plan_03'] = $new_array[$group][$row['stantion']]['price_materials_quarter_plan_03'] / $new_array[$group][$row['stantion']]['price_total'];

			// Column BH
			if (!isset($new_array[$group][$row['stantion']]['price_materials_month_plan_10'])) {
				$new_array[$group][$row['stantion']]['price_materials_month_plan_10'] = 0;
			}
			$new_array[$group][$row['stantion']]['price_materials_month_plan_10'] += ($row['month'] == 10) ? $row['price_total_no_vat'] / 1000 : 0;

			// Column BF
			if (!isset($new_array[$group][$row['stantion']]['quantity_materials_month_plan_10'])) {
				$new_array[$group][$row['stantion']]['quantity_materials_month_plan_10'] = 0;
			}
			$new_array[$group][$row['stantion']]['quantity_materials_month_plan_10'] = $new_array[$group][$row['stantion']]['price_materials_month_plan_10'] / $new_array[$group][$row['stantion']]['price_total'];

			// Column BL
			if (!isset($new_array[$group][$row['stantion']]['price_materials_month_plan_11'])) {
				$new_array[$group][$row['stantion']]['price_materials_month_plan_11'] = 0;
			}
			$new_array[$group][$row['stantion']]['price_materials_month_plan_11'] += ($row['month'] == 11) ? $row['price_total_no_vat'] / 1000 : 0;

			// Column BJ
			if (!isset($new_array[$group][$row['stantion']]['quantity_materials_month_plan_11'])) {
				$new_array[$group][$row['stantion']]['quantity_materials_month_plan_11'] = 0;
			}
			$new_array[$group][$row['stantion']]['quantity_materials_month_plan_11'] = $new_array[$group][$row['stantion']]['price_materials_month_plan_11'] / $new_array[$group][$row['stantion']]['price_total'];

			// Column BP
			if (!isset($new_array[$group][$row['stantion']]['price_materials_month_plan_12'])) {
				$new_array[$group][$row['stantion']]['price_materials_month_plan_12'] = 0;
			}
			$new_array[$group][$row['stantion']]['price_materials_month_plan_12'] += ($row['month'] == 12) ? $row['price_total_no_vat'] / 1000 : 0;

			// Column BN
			if (!isset($new_array[$group][$row['stantion']]['quantity_materials_month_plan_12'])) {
				$new_array[$group][$row['stantion']]['quantity_materials_month_plan_12'] = 0;
			}
			$new_array[$group][$row['stantion']]['quantity_materials_month_plan_12'] = $new_array[$group][$row['stantion']]['price_materials_month_plan_12'] / $new_array[$group][$row['stantion']]['price_total'];

			// Column BT
			if (!isset($new_array[$group][$row['stantion']]['price_materials_quarter_plan_04'])) {
				$new_array[$group][$row['stantion']]['price_materials_quarter_plan_04'] = 0;
			}
			$new_array[$group][$row['stantion']]['price_materials_quarter_plan_04'] = $new_array[$group][$row['stantion']]['price_materials_month_plan_10'] + $new_array[$group][$row['stantion']]['price_materials_month_plan_11'] + $new_array[$group][$row['stantion']]['price_materials_month_plan_12'];

			// Column BR
			if (!isset($new_array[$group][$row['stantion']]['quantity_materials_quarter_plan_04'])) {
				$new_array[$group][$row['stantion']]['quantity_materials_quarter_plan_04'] = 0;
			}
			$new_array[$group][$row['stantion']]['quantity_materials_quarter_plan_04'] = $new_array[$group][$row['stantion']]['price_materials_quarter_plan_04'] / $new_array[$group][$row['stantion']]['price_total'];

			// Column BX
			if (!isset($new_array[$group][$row['stantion']]['price_total_program'])) {
				$new_array[$group][$row['stantion']]['price_total_program'] = 0;
			}
			$new_array[$group][$row['stantion']]['price_total_program'] += $row['price_total_no_vat'] / 1000;

			if (!isset($new_array[$group][$row['stantion']]['start_quarter_1'])) {
				$new_array[$group][$row['stantion']]['start_quarter_1'] = [];
			}
			if (!isset($new_array[$group][$row['stantion']]['end_quarter_1'])) {
				$new_array[$group][$row['stantion']]['end_quarter_1'] = [];
			}

			if (!isset($new_array[$group][$row['stantion']]['start_quarter_2'])) {
				$new_array[$group][$row['stantion']]['start_quarter_2'] = [];
			}
			if (!isset($new_array[$group][$row['stantion']]['end_quarter_2'])) {
				$new_array[$group][$row['stantion']]['end_quarter_2'] = [];
			}

			if (!isset($new_array[$group][$row['stantion']]['start_quarter_3'])) {
				$new_array[$group][$row['stantion']]['start_quarter_3'] = [];
			}
			if (!isset($new_array[$group][$row['stantion']]['end_quarter_3'])) {
				$new_array[$group][$row['stantion']]['end_quarter_3'] = [];
			}
			if (!isset($new_array[$group][$row['stantion']]['start_quarter_4'])) {
				$new_array[$group][$row['stantion']]['start_quarter_4'] = [];
			}
			if (!isset($new_array[$group][$row['stantion']]['end_quarter_4'])) {
				$new_array[$group][$row['stantion']]['end_quarter_4'] = [];
			}

			if (!isset($new_array[$group][$row['stantion']]['start_work_quarter_1'])) {
				$new_array[$group][$row['stantion']]['start_work_quarter_1'] = '';
			}
			if (!isset($new_array[$group][$row['stantion']]['end_work_quarter_1'])) {
				$new_array[$group][$row['stantion']]['end_work_quarter_1'] = '';
			}

			if (!isset($new_array[$group][$row['stantion']]['start_work_quarter_2'])) {
				$new_array[$group][$row['stantion']]['start_work_quarter_2'] = '';
			}
			if (!isset($new_array[$group][$row['stantion']]['end_work_quarter_2'])) {
				$new_array[$group][$row['stantion']]['end_work_quarter_2'] = '';
			}

			if (!isset($new_array[$group][$row['stantion']]['start_work_quarter_3'])) {
				$new_array[$group][$row['stantion']]['start_work_quarter_3'] = '';
			}
			if (!isset($new_array[$group][$row['stantion']]['end_work_quarter_3'])) {
				$new_array[$group][$row['stantion']]['end_work_quarter_3'] = '';
			}

			if (!isset($new_array[$group][$row['stantion']]['start_work_quarter_4'])) {
				$new_array[$group][$row['stantion']]['start_work_quarter_4'] = '';
			}
			if (!isset($new_array[$group][$row['stantion']]['end_work_quarter_4'])) {
				$new_array[$group][$row['stantion']]['end_work_quarter_4'] = '';
			}

			// Column BZ && CA
			if ($row['month'] > 0 && $row['month'] <= 3) {
				array_push($new_array[$group][$row['stantion']]['start_quarter_1'], $start_month_to_array[$row['month']]);
				array_push($new_array[$group][$row['stantion']]['end_quarter_1'], $end_month_to_array[$row['month']]);
				$new_array[$group][$row['stantion']]['start_work_quarter_1'] = date('d.m.Y', strtotime(min($new_array[$group][$row['stantion']]['start_quarter_1'])));
				$new_array[$group][$row['stantion']]['end_work_quarter_1'] = date('d.m.Y', strtotime(max($new_array[$group][$row['stantion']]['end_quarter_1'])));
			}

			// Column CB && CC
			if ($row['month'] > 3 && $row['month'] <= 6) {
				array_push($new_array[$group][$row['stantion']]['start_quarter_2'], $start_month_to_array[$row['month']]);
				array_push($new_array[$group][$row['stantion']]['end_quarter_2'], $end_month_to_array[$row['month']]);
				$new_array[$group][$row['stantion']]['start_work_quarter_2'] = date('d.m.Y', strtotime(min($new_array[$group][$row['stantion']]['start_quarter_2'])));
				$new_array[$group][$row['stantion']]['end_work_quarter_2'] = date('d.m.Y', strtotime(max($new_array[$group][$row['stantion']]['end_quarter_2'])));
			}

			// Column CD && CE
			if ($row['month'] > 6 && $row['month'] <= 9) {
				array_push($new_array[$group][$row['stantion']]['start_quarter_3'], $start_month_to_array[$row['month']]);
				array_push($new_array[$group][$row['stantion']]['end_quarter_3'], $end_month_to_array[$row['month']]);
				$new_array[$group][$row['stantion']]['start_work_quarter_3'] = date('d.m.Y', strtotime(min($new_array[$group][$row['stantion']]['start_quarter_3'])));
				$new_array[$group][$row['stantion']]['end_work_quarter_3'] = date('d.m.Y', strtotime(max($new_array[$group][$row['stantion']]['end_quarter_3'])));
			}

			// Column CF && CG
			if ($row['month'] > 9 && $row['month'] <= 12) {
				array_push($new_array[$group][$row['stantion']]['start_quarter_4'], $start_month_to_array[$row['month']]);
				array_push($new_array[$group][$row['stantion']]['end_quarter_4'], $end_month_to_array[$row['month']]);
				$new_array[$group][$row['stantion']]['start_work_quarter_4'] = date('d.m.Y', strtotime(min($new_array[$group][$row['stantion']]['start_quarter_4'])));
				$new_array[$group][$row['stantion']]['end_work_quarter_4'] = date('d.m.Y', strtotime(max($new_array[$group][$row['stantion']]['end_quarter_4'])));
			}

			// Column CH
			$note = $row['note_for_contract'] ? ' - ' . $row['note_for_contract'] : NULL;
			$new_array[$group][$row['stantion']]['disp'][$row['oborud']][] = $row['disp'] . $note;
			$new_array[$group][$row['stantion']]['disp'][$row['oborud']] = array_unique($new_array[$group][$row['stantion']]['disp'][$row['oborud']]);

			$short_description = '';
			foreach ($new_array[$group][$row['stantion']]['disp'] as $k => $v) {
				$short_description .= $k . ': ' . implode(", ", array_unique($v)) . ', ';
			}

			$new_array[$group][$row['stantion']]['short_description'] = $row['repair_type'] . ' (' . $short_description . ')';
		}

		// echo "<pre>";
		// print_r($new_array['Капітальний ремонт_ГС_ ПС-150 кВ']);
		// print_r($data);
		// print_r(count($data));
		// echo "</pre>";
		// exit;
		return $new_array;
	}

	private function set_schedule_materials_add_data($post)
	{
		$data = [];

		foreach ($post as $key => $subarr) {
			if (is_array($subarr) || is_object($subarr)) {
				foreach ($subarr as $subkey => $subvalue) {
					$data[$subkey][$key] = $subvalue;
					$data[$subkey]['year_service'] = (date('Y') + 1);
					$data[$subkey]['is_extra'] = 1;
					$data[$subkey]['is_repair'] = 1;
					$data[$subkey]['created_by'] = $this->session->user->id;
					$data[$subkey]['updated_by'] = $this->session->user->id;
					$data[$subkey]['created_at'] = date('Y-m-d H:i:s');
					$data[$subkey]['updated_at'] = date('Y-m-d H:i:s');
				}
			}
		}
		return $data;
	}

	private function set_schedule_workers_add_data($post)
	{
		$data = [];

		foreach ($post as $key => $subarr) {
			if (is_array($subarr) || is_object($subarr)) {
				foreach ($subarr as $subkey => $subvalue) {

					$data[$subkey][$key] = $subvalue;
					$data[$subkey]['schedule_id'] = $post['schedule_id'];
					$data[$subkey]['year_service'] = (date('Y') + 1);
					$data[$subkey]['is_extra'] = 1;
					$data[$subkey]['is_repair'] = 1;
					$data[$subkey]['created_by'] = $this->session->user->id;
					$data[$subkey]['updated_by'] = $this->session->user->id;
					$data[$subkey]['created_at'] = date('Y-m-d H:i:s');
					$data[$subkey]['updated_at'] = date('Y-m-d H:i:s');
				}
			}
		}

		foreach ($data as $key => $value) {
			if (!$value['quantity']) {
				unset($data[$key]);
			}
		}
		return $data;
	}

	public function edit_avr_price_ajax()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}
		if ($this->session->user->group !== 'admin') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$data = json_decode(file_get_contents("php://input"), true);
		$data['year_service'] = (date('Y') + 1);
		$data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');

		$this->load->model('avr_price_model');

		$this->avr_price_model->delete_for_schedule_avr_price((date('Y') + 1));
		$this->avr_price_model->insert($data);

		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані записано', 'data' => $data], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function change_is_repair_ajax()
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

		$this->passport_model->change_value('is_repair', $this->input->post('value'), $this->input->post('id'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}






	public function clear_data()
	{
		$a = [1, 2, 3, 4, 5, 6, 7, 8, 9];

		$b = [1, 3, 5, 7, 9];

		foreach ($a as $item) {
			if (!in_array($item, $b)) {
				// Тоді видаляємо дані
				// echo $item . " iiiiiiiiiiiii<br>";
			}
		}

		$materials = $this->schedule_material_model->get_schedule_id();

		$workers = $this->schedule_worker_model->get_schedule_id();

		$technics = $this->schedule_technic_model->get_schedule_id();

		$notes = $this->schedule_note_model->get_schedule_id();

		$schedules_year = $this->schedule_year_model->get_schedule_id();

		$materials_simple = [];
		foreach ($materials as $item) {
			array_push($materials_simple, $item->id);
		}

		$workers_simple = [];
		foreach ($workers as $item) {
			array_push($workers_simple, $item->id);
		}

		$technics_simple = [];
		foreach ($technics as $item) {
			array_push($technics_simple, $item->id);
		}

		$notes_simple = [];
		foreach ($notes as $item) {
			array_push($notes_simple, $item->id);
		}

		$schedules_year_simple = [];
		foreach ($schedules_year as $item) {
			array_push($schedules_year_simple, $item->id);
		}

		foreach ($materials_simple as $item) {
			if (!in_array($item, $schedules_year_simple)) {
				// Тоді видаляємо дані
				echo $item . " material<br>";
			}
		}

		foreach ($workers_simple as $item) {
			if (!in_array($item, $schedules_year_simple)) {
				// Тоді видаляємо дані
				echo $item . " worker<br>";
			}
		}

		foreach ($technics_simple as $item) {
			if (!in_array($item, $schedules_year_simple)) {
				// Тоді видаляємо дані
				echo $item . " technic<br>";
			}
		}

		foreach ($notes_simple as $item) {
			if (!in_array($item, $schedules_year_simple)) {
				// Тоді видаляємо дані
				echo $item . " note<br>";
			}
		}
	}
}
