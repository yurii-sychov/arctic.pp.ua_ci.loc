<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		if (!$this->session->user) {
			redirect('authentication/signin');
		}

		// $this->load->model('equipment_model');
		// $this->load->model('property_model');
		// $this->load->model('passport_property_model');
		// $this->load->model('passport_photo_model');
	}

	public function index()
	{
		$data = [];
		$data['title'] = 'Статистика';
		$data['content'] = 'dashboard/index';
		$data['page'] = 'dashboard/index';
		$data['page_js'] = 'dashboard';
		$data['datatables'] = FALSE;
		$data['title_heading'] = 'Статистика';

		$this->load->view('layout_lte', $data);
	}
}
