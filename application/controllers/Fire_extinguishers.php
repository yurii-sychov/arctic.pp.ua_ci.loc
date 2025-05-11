<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

// SELECT count(passports.id) as count, surname FROM `passports`, users_complete_renovation_objects, users WHERE passports.complete_renovation_object_id = users_complete_renovation_objects.object_id AND users_complete_renovation_objects.user_id = users.id GROUP BY user_id ORDER BY count DESC;

defined('BASEPATH') or exit('No direct script access allowed');

class Fire_extinguishers extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		if (!$this->session->user) {
			redirect('authentication/signin');
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'master' && $this->session->user->group !== 'user') {
			show_404();
		}

		$this->load->model('fire_extinguisher_model');
	}

	public function index()
	{
		$data = [];
		$data['title'] = 'Вогнегасники';
		$data['content'] = 'fire_extinguishers/index';
		$data['page'] = 'fire_extinguishers';
		$data['page_js'] = 'fire_extinguishers';
		$data['datatables'] = TRUE;
		$data['title_heading'] = 'Вогнегасники';
		$data['title_heading_card'] = 'Вогнегасники';

		$data['fire_extinguishers'] = $this->fire_extinguisher_model->get_data();
		$this->load->view('layout_lte', $data);
	}
}
