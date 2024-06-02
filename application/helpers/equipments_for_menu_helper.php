<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('get_equipments_for_menu')) {
	function get_equipments_for_menu()
	{
		$equipments = [];

		$CI = &get_instance();

		$CI->load->model('equipment_model');

		$equipments = $CI->equipment_model->get_data_is_menu_show();

		return $equipments;
	}
}
