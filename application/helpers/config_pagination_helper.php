<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('get_config_pagination')) {
	function get_config_pagination($link, $total_rows)
	{
		$CI = &get_instance();

		$config['base_url'] = $link;
		$default = $CI->session->userdata('rows') ? $CI->session->userdata('rows') : 5;
		$config['per_page'] = $CI->session->userdata('rows') ? $CI->session->userdata('rows') : 5;
		$config['total_rows'] = $total_rows;

		$config['page_query_string'] = TRUE;
		$config['query_string_segment'] = 'page';
		$config['reuse_query_string'] = TRUE;

		$config['full_tag_open'] = '<nav><ul class="pagination justify-content-center">';
		$config['full_tag_close'] = '</ul></nav>';

		$config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="">';
		$config['cur_tag_close'] = '</a><li>';

		$config['first_link'] = $CI->agent->is_mobile() ? '<<' : 'На початок';
		$config['last_link'] = $CI->agent->is_mobile() ? '>>' : 'В кінець';

		$config['prev_tag_open'] = '<li class="page-item">';
		$config['prev_tag_close'] = '</li>';
		$config['prev_link'] = $CI->agent->is_mobile() ? '<' : 'Назад';

		$config['num_tag_open'] = '<li class="page-item">';
		$config['num_tag_close'] = '</li>';

		$config['next_tag_open'] = '<li class="page-item">';
		$config['next_tag_close'] = '</li>';
		$config['next_link'] = $CI->agent->is_mobile() ? '>' : 'Далі';

		$config['first_tag_open'] = '<li class="page-item">';
		$config['first_tag_close'] = '</li>';

		$config['last_tag_open'] = '<li class="page-item">';
		$config['last_tag_close'] = '</li>';

		$config['attributes'] = array('class' => 'page-link');
		$config['num_links'] = $CI->agent->is_mobile() ? 1 : 3;
		$config['use_page_numbers'] = TRUE;

		return $config;
	}
}
