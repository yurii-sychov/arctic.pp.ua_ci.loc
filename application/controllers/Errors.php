<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Errors extends CI_Controller
{

	public function index()
	{
		$this->output->set_content_type('application/json');

		$this->output->set_status_header(404);

		$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Сторінка не існує!'], JSON_UNESCAPED_UNICODE));

		return;
	}
}
