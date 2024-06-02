<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

// SELECT count(passports.id) as count, surname FROM `passports`, users_complete_renovation_objects, users WHERE passports.complete_renovation_object_id = users_complete_renovation_objects.object_id AND users_complete_renovation_objects.user_id = users.id GROUP BY user_id ORDER BY count DESC;

defined('BASEPATH') or exit('No direct script access allowed');

class Protective_arsenal extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		if (!$this->session->user) {
			redirect('authentication/signin');
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'master') {
			show_404();
		}
		// $this->load->model('passport_model');
		// $this->load->model('schedule_model');
		// $this->load->model('specific_renovation_object_model');
	}

	public function index()
	{
		$data = [];
		$data['title'] = 'Захисні засоби';
		$data['content'] = 'protective_arsenal/index';
		$data['page'] = 'protective_arsenal/index';
		$data['page_js'] = 'protective_arsenal';
		$data['datatables'] = TRUE;
		$data['title_heading'] = 'Захисні засоби';
		$data['title_heading_card'] = 'Захисні засоби';
		$this->load->view('layout', $data);
	}
}
