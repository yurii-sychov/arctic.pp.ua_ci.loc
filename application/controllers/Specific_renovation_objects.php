<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Specific_renovation_objects extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		if (!$this->session->user) {
			redirect('authentication/signin');
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer' && $this->session->user->group !== 'master') {
			show_404();
		}

		$this->load->model('subdivision_model');
		$this->load->model('complete_renovation_object_model');
		$this->load->model('specific_renovation_object_model');
		$this->load->model('equipment_model');
		$this->load->model('voltage_class_model');
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
		$data['title'] = 'Диспетчерські найменування об\'єктів';
		$data['content'] = 'specific_renovation_objects/index';
		$data['page'] = 'specific_renovation_objects';
		$data['page_js'] = 'specific_renovation_objects';
		$data['title_heading'] = 'Диспетчерські найменування об\'єктів';
		$data['title_heading_card'] = 'Диспетчерські найменування об\'єктів';
		$data['datatables'] = TRUE;
		$data['forms'] = FALSE;

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
			$specific_renovation_objects = $this->specific_renovation_object_model->get_all_for_complete_renovation_object($subdivision_id, $complete_renovation_object_id);
			$equipments = $this->equipment_model->get_data();
			$data['equipments'] = $equipments;
			$voltage_class = $this->voltage_class_model->get_data();
			$users = $this->user_model->get_data();
			foreach ($specific_renovation_objects as $key => $specific_renovation_object) {
				foreach ($subdivisions as $subdivision) {
					if ($specific_renovation_object->subdivision_id == $subdivision->id) {
						$specific_renovation_objects[$key]->subdivision = $subdivision->name;
					}
				}
				foreach ($complete_renovation_objects as $сomplete_renovation_object) {
					if ($specific_renovation_object->complete_renovation_object_id == $сomplete_renovation_object->id) {
						$specific_renovation_objects[$key]->complete_renovation_object = $сomplete_renovation_object->name;
					}
				}
				foreach ($equipments as $equipment) {
					if ($specific_renovation_object->equipment_id == $equipment->id) {
						$specific_renovation_objects[$key]->equipment = $equipment->name;
					}
				}
				foreach ($voltage_class as $voltage) {
					if ($specific_renovation_object->voltage_class_id == $voltage->id) {
						$specific_renovation_objects[$key]->voltage_class = ($voltage->voltage / 1000) . ' кВ';
					}
				}
				foreach ($users as $user) {
					if ($specific_renovation_object->created_by == $user->id) {
						$specific_renovation_objects[$key]->created_by = $user->name . ' ' . $user->surname;
					}
					if ($specific_renovation_object->updated_by == $user->id) {
						$specific_renovation_objects[$key]->updated_by = $user->name . ' ' . $user->surname;
					}
				}
			}

			if ($equipment_id) {
				$specific_renovation_objects = array_filter($specific_renovation_objects, function ($row) {
					return $row->equipment_id == $this->uri->segment(5);
				});
			}

			$data['results'] = $specific_renovation_objects;
		}

		$this->load->view('layout_lte', $data);
	}

	public function create()
	{
		$data = [];
		$data['title'] = 'Створення диспетчерських найменуваннь об\'єктів';
		$data['content'] = 'specific_renovation_objects/form';
		$data['page'] = 'specific_renovation_objects';
		$data['page_js'] = 'specific_renovation_objects';
		$data['title_heading'] = 'Створення диспетчерських найменуваннь об\'єктів';
		$data['title_heading_card'] = 'Форма створення диспетчерських найменуваннь об\'єктів';
		$data['datatables'] = FALSE;
		$data['forms'] = TRUE;

		$this->load->view('layout_lte', $data);
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

		if ($this->session->user->group !== 'admin') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		$rules = ($this->input->post('field') === 'sort') ? 'required|numeric' : 'required';
		$this->form_validation->set_rules('value', '<strong>' . $this->input->post('field_name') . '</strong>', $rules);

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

		$result = $this->specific_renovation_object_model->update_field($this->input->post('id', TRUE), $data);

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

	public function change_year_commissioning_ajax()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}
		$result = $this->specific_renovation_object_model->change_value('year_commissioning', $this->input->post('value'), $this->input->post('id'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function change_year_repair_invest_ajax()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}
		$result = $this->specific_renovation_object_model->change_value('year_repair_invest', $this->input->post('value'), $this->input->post('id'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function change_year_plan_repair_invest_ajax()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}
		$result = $this->specific_renovation_object_model->change_value('year_plan_repair_invest', $this->input->post('value'), $this->input->post('id'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function change_disp_ajax()
	{
		// print_r($_POST);
		// exit;
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}
		$result = $this->specific_renovation_object_model->change_value('name', $this->input->post('value'), $this->input->post('id'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function get_specific_renovation_objects_ajax()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$specific_renovation_objects = $this->specific_renovation_object_model->get_data_for_cro_and_e_and_vc($this->input->get('complete_renovation_object_id'), $this->input->get('equipment_id'), $this->input->get('voltage_class_id'));

		if (!$specific_renovation_objects) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Не вдалося отримати дані з реєстру!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'specific_renovation_objects' => $specific_renovation_objects], JSON_UNESCAPED_UNICODE));
	}
}
