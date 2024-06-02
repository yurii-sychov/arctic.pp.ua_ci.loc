<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Users extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		if (!$this->session->user) {
			redirect('authentication/signin');
		}

		if ($this->session->user->group !== 'admin') {
			show_404();
		}
		$this->load->model('user_model');
		$this->load->model('subdivision_model');
		$this->load->model('complete_renovation_object_model');
		$this->load->model('users_complete_renovation_object_model');
	}

	public function index()
	{
		$data = [];
		$data['export_to_pdf'] = TRUE;
		$data['export_to_word'] = TRUE;
		$data['export_to_excel'] = TRUE;
		$data['title'] = 'Користувачі';
		$data['content'] = 'users/index';
		$data['page'] = 'users/index';
		$data['page_js'] = 'users';
		$data['datatables'] = TRUE;
		$data['title_heading'] = 'Користувачі';
		$data['title_heading_card'] = 'Користувачі';
		$subdivisions = $this->subdivision_model->get_data();
		$users = $this->user_model->get_data();
		$users_stantions = $this->users_complete_renovation_object_model->get_data();

		foreach ($users as $key => $value) {
			$value->stantions = [];

			foreach ($users_stantions as $k => $v) {
				if ($value->id == $v->user_id) {
					array_push($value->stantions, $v);
				}
			}

			$value->subdivision = '';
			foreach ($subdivisions as $k => $v) {
				if ($value->subdivision_id == $v->id) {
					$value->subdivision = $v->name;
				}
			}
		}
		$data['users'] = $users;

		$this->load->view('layout', $data);
	}
}
