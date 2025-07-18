<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Duty_schedule extends CI_Controller
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
		$this->load->model('shift_worker_model');
	}

	public function index()
	{
		$data = [];
		$data['title'] = 'Графіки чергувань';
		$data['content'] = 'duty_schedule/index';
		$data['page'] = 'duty_schedule';
		$data['page_js'] = 'duty_schedule';
		$data['title_heading'] = 'Графіки чергувань';
		$data['title_heading_card'] = 'Графіки чергувань';
		$data['datatables'] = FALSE;
		$data['forms'] = TRUE;

		$data['custom_js'] = ['daterangepicker'];

		$year = date('Y');
		$data['year'] = $year;

		for ($month = 1; $month <= 12; $month++) {
			$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
			$calendar_day[$month] = [];
			$calendar_day_full[$month] = [];
			$calendar_day_week[$month] = [];
			$calendar_day_off[$month] = [];

			$first_thursday = strtotime("first Thursday of {$year}-{$month}");
			$second_thursday = date('j', strtotime('+1 week', $first_thursday));

			for ($day = 1; $day <= $days_in_month; $day++) {
				$day_week = jddayofweek(gregoriantojd($month, $day, $year), 0);
				$calendar_day[$month][] = $day;
				$day_full = new DateTimeImmutable($year . '-' . $month . '-' . $day);
				$calendar_day_full[$month][] = $day_full->format('Y-m-d');
				$calendar_day_week[$month][] = $day_week;
				if ($day_week == 0 || $day_week == 6) {
					$calendar_day_off[$month][] = 1;
				} else {
					$calendar_day_off[$month][] = 0;
				}

				if ($day == $second_thursday) {
					$calendar_second_thursday[$month][] = 1;
				} else {
					$calendar_second_thursday[$month][] = 0;
				}
			}
		}

		$data['months'] = [1 => 'Січень', 2 => 'Лютий', 3 => 'Березень', 4 => 'Квітень', 5 => 'Травень', 6 => 'Червень', 7 => 'Липень', 8 => 'Серпень', 9 => 'Вересень', 10 => 'Жовтень', 11 => 'Листопад', 12 => 'Грудень'];
		$data['calendar_day'] = $calendar_day;
		$data['calendar_day_full'] = $calendar_day_full;
		$data['calendar_day_week'] = $calendar_day_week;
		$data['calendar_day_off'] = $calendar_day_off;
		$data['calendar_second_thursday'] = $calendar_second_thursday;

		$data['shift_workers'] = $this->shift_worker_model->get_data();

		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// exit;

		$this->load->view('layout_lte', $data);
	}
}
