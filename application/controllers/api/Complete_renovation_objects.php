<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */
// $key = 81809f82e2074b59448635de2fcc121aaec62890;

defined('BASEPATH') or exit('No direct script access allowed');

class Complete_renovation_objects extends CI_Controller
{
	public $key = '81809f82e2074b59448635de2fcc121aaec62890';

	public function __construct()
	{
		parent::__construct();
		$this->output->set_header("Access-Control-Allow-Origin: *");
		$this->output->set_header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS, *");
		$this->output->set_header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization, *");
		// $this->output->set_header("Access-Control-Max-Age: 84000");
		$this->output->set_content_type('application/json', 'utf-8');

		$this->load->model('api/complete_renovation_object_model');
	}

	public function index($offset = NULL, $limit = NULL)
	{
		if ($this->input->get_request_header('Key-Api') !== $this->key) {
			$data['info']['status'] = 'ERROR';
			$data['info']['message'] = 'Не вірний Api-Key!';

			return $this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
		}

		$data = [];

		$result = $this->complete_renovation_object_model->get_data($this->input->get('offset'), $this->input->get('limit'));
		$data['data'] = $result;
		$data['info']['total'] = count($result);
		$data['info']['status'] = 'SUCCESS';
		$data['info']['message'] = 'Дані отримані!';

		return $this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
	}

	public function create()
	{
		if ($this->input->get_request_header('Key-Api') !== $this->key) {
			$data['info']['status'] = 'ERROR';
			$data['info']['message'] = 'Не вірний Api-Key!';

			return $this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
		}

		$data = json_decode(file_get_contents("php://input"), true);

		if ($data) {
			foreach ($data as $key => $value) {
				$_POST[$key] = $value;
			}

			$this->load->library('form_validation');

			$this->form_validation->set_rules('subdivision_id', 'Підрозділ', 'required|trim');
			$this->form_validation->set_rules('name', 'Енергетичний об\'єкт', 'required|trim');
			$this->form_validation->set_rules('year_commissioning', 'Рік вводу в експлуатацію', 'required|min_length[4]|max_length[4]|trim');
			$this->form_validation->set_rules('r3_id', 'Номер в SAP/R3', 'required|numeric|trim');
		}

		if ($this->form_validation->run() == FALSE) {
			$data['info']['status'] = 'ERROR';
			$data['info']['message_array'] = $this->form_validation->error_array();
			$data['info']['message_string'] = $this->form_validation->error_string();
			$data['info']['message'] = 'У Вас є помилка!';

			return $this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
		}

		if ($this->form_validation->run() !== FALSE) {
			$data['created_by'] = 1;
			$data['updated_by'] = 1;
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = date('Y-m-d H:i:s');

			$result = $this->complete_renovation_object_model->insert($data);
		}

		if (isset($result)) {

			$data['id'] = $result;
			$return_data['data'] = $data;
			$return_data['info']['status'] = 'SUCCESS';
			$return_data['info']['message'] = 'Дані створені!';

			return $this->output->set_output(json_encode($return_data, JSON_UNESCAPED_UNICODE));
		}
	}

	public function update_field($field, $id)
	{
		if ($this->input->get_request_header('Key-Api') !== $this->key) {
			$data['info']['status'] = 'ERROR';
			$data['info']['message'] = 'Не вірний Api-Key!';

			return $this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
		}

		$data = json_decode(file_get_contents("php://input"), true);

		if ($data) {
			$_POST['value'] = $data['value'];

			$this->load->library('form_validation');

			if ($field === 'name') {
				$this->form_validation->set_rules('value', 'Енергетичний об\'єкт', 'required|trim');
			} elseif ($field === 'year_commissioning') {
				$this->form_validation->set_rules('value', 'Рік вводу', 'required|min_length[4]|max_length[4]|numeric|trim');
			} elseif ($field === 'r3_id') {
				$this->form_validation->set_rules('value', 'Номер в SAP/R3', 'required|numeric|trim');
			}
		}

		if ($this->form_validation->run() == FALSE) {
			$data['info']['status'] = 'ERROR';
			$data['info']['message_array'] = $this->form_validation->error_array();
			$data['info']['message_string'] = $this->form_validation->error_string();
			$data['info']['message'] = 'У Вас є помилка!';

			return $this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
		}

		if ($this->form_validation->run() !== FALSE) {
			$data['updated_by'] = 1;
			$data['updated_at'] = date('Y-m-d H:i:s');

			$result = $this->complete_renovation_object_model->update_field($field, $this->input->post('value'), $id);
		}

		if (isset($result)) {

			$data['id'] = $id;
			$return_data['data'] = $data;
			$return_data['info']['status'] = 'SUCCESS';
			$return_data['info']['message'] = 'Дані оновлені!';

			return $this->output->set_output(json_encode($return_data, JSON_UNESCAPED_UNICODE));
		}
	}

	public function delete($id)
	{
		if ($this->input->get_request_header('Key-Api') !== $this->key) {
			$data['info']['status'] = 'ERROR';
			$data['info']['message'] = 'Не вірний Api-Key!';

			return $this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
		}

		if ($id) {
			$this->complete_renovation_object_model->delete($id);

			$return_data['id'] = $id;
			$return_data['info']['status'] = 'SUCCESS';
			$return_data['info']['message'] = 'Дані видалені!';

			return $this->output->set_output(json_encode($return_data, JSON_UNESCAPED_UNICODE));
		}
	}

	public function update($id)
	{
		if ($this->input->get_request_header('Key-Api') !== $this->key) {
			$data['info']['status'] = 'ERROR';
			$data['info']['message'] = 'Не вірний Api-Key!';

			return $this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
		}

		$data = [];

		$data = json_decode(file_get_contents("php://input"), true);

		$data['info']['status'] = 'SUCCESS';
		$data['info']['message'] = 'Дані оновлені!';

		return $this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
	}
}
