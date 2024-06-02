<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Schedules extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		// $this->output->set_header('Access-Control-Allow-Origin: *');
		// $this->output->set_header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Authorization');
		// $this->output->set_content_type('application/json', 'utf-8');
		$this->load->model('api/schedule_model');
	}

	public function index()
	{
		$data = [];

		$data = $this->schedule_model->get_rows();

		$new_data[] = [
			'repair_type' => 'Вид ремонту',
			'stantion' => 'Підстанція',
			'oborud' => 'Найменування обладнання',
			'disp' => 'Диспечерське найменування',
			'year_start' => 'Рік випуску обладнання',
			'class_voltage' => 'Клас напруги обладнання',
			'repair_year_last' => 'Дата останього обслуговування',
			'period' => 'Періодичність ремонту',
			'year_repair_invest' => 'Рік заходу в ІП фактично',
			'year_plan_repair_invest' => 'Плановий рік включення в ІП',
		];


		foreach ($data as $row) {
			$new_array['repair_type'] = $row->repair_type;
			$new_array['stantion'] = explode(" ", str_replace("\"", "", $row->stantion))[0] . " " . explode(" ", str_replace("\"", "", $row->stantion))[1];
			$new_array['oborud'] = $row->oborud;
			$new_array['disp'] = $row->disp;
			$new_array['year_start'] = $row->year_start;
			$new_array['class_voltage'] = $row->class_voltage;
			$new_array['repair_year_last'] = $row->repair_year_last;
			$new_array['period'] = $row->period;
			$new_array['year_repair_invest'] = $row->year_repair_invest;
			$new_array['year_plan_repair_invest'] = $row->year_plan_repair_invest;
			array_push($new_data, $new_array);
		}

		// echo "<pre>";
		// print_r($new_data);
		// print_r($data);
		// echo "</pre>";

		$xlsx = Shuchkin\SimpleXLSXGen::fromArray($new_data);
		$xlsx->saveAs('./uploads/multi_grafik_source.xlsx');

		redirect('multi_year_schedule');

		// $this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
	}
}
