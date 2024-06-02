<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Buildings extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		if (!$this->session->user) {
			redirect('authentication/signin');
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer') {
			show_404();
		}
	}

	public function index()
	{
		$data = [];
		$data['title'] = 'Будівлі та споруди';
		$data['content'] = 'buildings/index';
		$data['page'] = 'buildings/index';
		$data['page_js'] = 'buildings';
		$data['datatables'] = TRUE;
		$data['title_heading'] = 'Будівлі та споруди';
		$data['title_heading_card'] = 'Будівлі та споруди';
		$this->load->view('layout', $data);
	}
}
