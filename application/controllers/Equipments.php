<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Equipments extends CI_Controller
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
		$this->load->model('equipment_model');
		$this->load->model('property_model');
		$this->load->model('passport_property_model');
		$this->load->model('passport_photo_model');
	}

	public function index($equipment_id = NULL)
	{
		if (!is_numeric($equipment_id) || !$equipment_id) {
			show_404();
		}

		$equipment = $this->equipment_model->get_data_row($equipment_id);

		if (!$equipment) {
			show_404();
		}

		$data = [];
		$data['export_to_excel'] = TRUE;
		$data['export_to_pdf'] = TRUE;
		$data['upload_photo'] =  ($this->session->user->group == 'admin') ? TRUE : FALSE;

		$passports = $this->equipment_model->get_data_for_equipment_id($equipment_id);
		$properties = $this->property_model->get_data_equipment($equipment_id);

		foreach ($passports as $item) {
			$item->passports_properties = $this->passport_property_model->get_data_for_passport($item->passport_id);
			$item->photo = $this->passport_photo_model->get_data_is_main_photo($item->passport_id);
		}
		// echo "<pre>";
		// print_r($passports);
		// echo "</pre>";
		$data['title'] = 'Обладнання ('.$equipment->plural_name.')';
		$data['content'] = file_exists(APPPATH.'/views/equipments/'.$equipment->id.'.php') ? 'equipments/'.$equipment->id : 'equipments/index';
		$data['page'] = 'equipments/index/'.$equipment->id;
		$data['page_js'] = 'equipments';
		$data['datatables'] = FALSE;
		$data['title_heading'] = $equipment->plural_name;
		$data['title_heading_card'] = $equipment->plural_name. ' ('.count($passports).' од.)';
		$data['equipment'] = $equipment;
		$data['passports'] = $passports;
		$data['properties'] = $properties;

		$this->load->view('layout', $data);
	}

	public function upload_photo($equipment_id = NULL) {
		if (!is_numeric($equipment_id) || !$equipment_id) {
			show_404();
		}
		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer') {
			show_404();
		}

		$equipment = $this->equipment_model->get_data_row($equipment_id);

		if (!$equipment) {
			show_404();
		}

		$data = [];
		$passports = $this->equipment_model->get_data_for_equipment_id($equipment_id);
		$type  = array_column($passports, 'type');
		array_multisort($type, SORT_ASC, $passports);
		$data['button_group'] = ['backToHome' => 'Назад до переліку'];
		$data['title'] = 'Додавання фото для обладнання';
		$data['content'] = 'equipments/form_photo';
		$data['page'] = 'equipments/form_photo';
		$data['page_js'] = 'equipments';
		$data['datatables'] = FALSE;
		$data['title_heading'] = 'Форма для додавання фото обладнання';
		$data['title_heading_card'] = 'Форма';
		$data['passports'] = $passports;

		if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 4) {
			$this->session->set_flashdata('action', 'error');
			$this->session->set_flashdata('message', 'Не вибране фото обладнання.');
		}

		if ($this->input->post()) {

			$upload = $this->upload_file();

			if ($upload['error']) {
				$this->session->set_flashdata('action', 'error');
				$this->session->set_flashdata('message', $upload['error']);
			}

			if (isset($upload['upload_data'])) {
				$data = $this->set_photos_add_data($this->input->post(), $upload['upload_data']['file_name']);
				$result = $this->passport_photo_model->add_data_batch($data);
				redirect('/equipments/index/1');
			}
			// print_r($upload);
			// print_r($_FILES);
			// print_r($this->input->post('passports'));
		}
		$this->load->view('layout', $data);
	}

	private function upload_file()
	{
		$config['upload_path'] = './uploads/passports/photos';
		$config['allowed_types'] = 'jpeg|jpg';
		$config['encrypt_name'] = TRUE;

		if (!file_exists('./uploads/passports')) {
			mkdir('./uploads/passports', 0755);
		}

		if (!file_exists('./uploads/passports/photos')) {
			mkdir('./uploads/passports/photos', 0755);
		}

		$this->load->library('upload', $config);
		$this->upload->initialize($config);

		if (!$this->upload->do_upload('photo')) {
			$error = array('error' => $this->upload->display_errors('', ''));
			return $error;
		} else {
			$data = array('upload_data' => $this->upload->data());
			return $data;
		}
	}

	private function set_photos_add_data($post, $photo)
	{
		$data = [];

		foreach ($post as $key => $subarr) {
			if (is_array($subarr) || is_object($subarr)) {
				foreach ($subarr as $subkey => $subvalue) {
					$data[$subkey]['passport_id'] = $subvalue;
					$data[$subkey]['photo'] = $photo;
					$data[$subkey]['is_main_photo'] = 1;
					$data[$subkey]['created_by'] = $this->session->user->id;
					$data[$subkey]['updated_by'] = $this->session->user->id;
					$data[$subkey]['created_at'] = date('Y-m-d H:i:s');
					$data[$subkey]['updated_at'] = date('Y-m-d H:i:s');
				}
			}
		}
		return $data;
	}
}
