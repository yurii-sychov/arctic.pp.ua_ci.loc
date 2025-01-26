<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Font, Border, Alignment};

class Multi_year_schedule extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		if (!$this->session->user) {
			redirect('authentication/signin');
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer' && $this->session->user->group !== 'master') {
			show_404();
		}
		$this->load->model('subdivision_model');
		$this->load->model('schedule_model');
		$this->load->model('schedule_material_model');
		$this->load->model('schedule_year_model');
		$this->load->model('passport_model');
		$this->load->model('complete_renovation_object_model');
		$this->load->model('specific_renovation_object_model');
		$this->load->model('equipment_model');
		$this->load->model('type_service_model');
		$this->load->model('voltage_class_model');
		$this->load->model('insulation_type_model');
		$this->load->model('cipher_model');
	}

	public function index()
	{
		$data = [];
		$data['title'] = 'Багаторічний графік';
		$data['content'] = 'multi_year_schedule/index';
		$data['page'] = 'multi_year_schedule';
		$data['page_js'] = 'multi_year_schedule';
		$data['datatables'] = TRUE;
		$data['title_heading'] = 'Багаторічний графік';
		$data['title_heading_card'] = 'Редагування багаторічного графіку';
		$data['subdivisions'] = $this->subdivision_model->get_data();
		$data['stantions'] = $this->complete_renovation_object_model->get_data_for_user();
		$data['equipments'] = $this->equipment_model->get_data();
		$data['type_services'] = $this->type_service_model->get_data();
		$data['voltage_class'] = $this->voltage_class_model->get_data();
		$data['insulation_type'] = $this->insulation_type_model->get_data();
		$this->load->view('layout', $data);
	}

	public function get_data_server_side()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		foreach ($this->input->post('columns') as $key => $value) {
			$filter[$value['data']] = $value['search']['value'];
			if ($key == $this->input->post('order')[0]['column']) {
				$order_dir = $this->input->post('order')[0]['dir'];
				$order_field = $this->input->post('columns')[$key]['data'];
			}
		}

		$specific_renovation_objects = $this->schedule_model->get_data_datatables_server_side($this->input->post(), $filter, $order_dir, $order_field);

		foreach ($specific_renovation_objects as $key => $value) {
			$passports = $this->passport_model->get_passports($value->specific_renovation_object_id);
			$places = [];
			$value->places = $places;
			$value->DT_RowData['user_group'] = $this->session->user->group;
			$value->DT_RowData['ciphers'] = $this->cipher_model->get_data();
			foreach ($passports as $k => $v) {
				if ($value->specific_renovation_object_id == $v->specific_renovation_object_id) {
					if ($v->place_id == 1) {
						$place_color = 'warning';
					} elseif ($v->place_id == 2) {
						$place_color = 'success';
					} elseif ($v->place_id == 3) {
						$place_color = 'danger';
					} else {
						$place_color = 'primary';
					}
					array_push($places, [
						'place_name' => $v->name,
						'place_color' => $place_color,
						'type' => $v->type,
						'number' => $v->number,
						'production_date' => $v->production_date,
					]);
					$value->places = $places;
				}
			}
		}

		$data['draw'] = $this->input->post('draw');
		$data['recordsTotal'] = $this->schedule_model->get_count_all();
		$data['recordsFiltered'] = $this->schedule_model->get_records_filtered($this->input->post(), $filter);
		$data['data'] = $specific_renovation_objects;
		// echo "<pre>";
		// print_r($specific_renovation_objects);
		// echo "</pre>";
		$data['user_group'] = $this->session->user->group;

		$data['post'] = $this->input->post();

		$this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
	}

	public function get_data()
	{
		// $this->output->set_content_type('application/json');

		// if (!$this->input->post()) {
		// 	$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
		// 	return;
		// }

		// $specific_renovation_objects = $this->schedule_model->get_data_datatables();
		// $passports = $this->passport_model->get_data();

		// foreach ($specific_renovation_objects as $key => $value) {
		// 	$passports = $this->passport_model->get_passports($value->specific_renovation_object_id);
		// 	$passport_filter = array_filter($passports, function ($arr) {
		// 		return $arr->specific_renovation_object_id == $arr->specific_renovation_object_id;
		// 	});
		// 	$places = [];
		// 	$value->places = $places;
		// 	foreach ($passports as $k => $v) {
		// 		if ($value->specific_renovation_object_id == $v->specific_renovation_object_id) {
		// 			if ($v->place_id == 1) {
		// 				$place_color = 'warning';
		// 			} elseif ($v->place_id == 2) {
		// 				$place_color = 'success';
		// 			} elseif ($v->place_id == 3) {
		// 				$place_color = 'danger';
		// 			} else {
		// 				$place_color = 'primary';
		// 			}
		// 			array_push($places, [
		// 				'place_name' => $v->place,
		// 				'place_color' => $place_color,
		// 				'type' => $v->type,
		// 				'number' => $v->number,
		// 				'production_date' => $v->production_date,
		// 			]);
		// 			$value->places = $places;
		// 		}
		// 	}
		// }

		// $data['passports'] = $pp;
		// $data['data'] = $specific_renovation_objects;

		// $this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
	}

	public function get_schedule_kr()
	{
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Графік капітального ремонту.xlsx"');
		header('Cache-Control: max-age=0');
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->mergeCells('A1:K1');
		$sheet->setCellValue("A1", "Багаторічний графік капітального ремонту обладнання СП");
		$sheet->getRowDimension(1)->setRowHeight(30);
		$sheet->getStyle('A1')->applyFromArray([
			'font' => [
				'name' => 'TimesNewRoman',
				'bold' => true,
				'italic' => false,
				'underline' => Font::UNDERLINE_DOUBLE,
				'strikethrough' => false,
				// 'color' => [
				// 	'rgb' => '000000'
				// ],
				'size' => 18
			],
			// 'borders' => [
			// 	'allBorders' => [
			// 		'borderStyle' => Border::BORDER_THIN,
			// 		'color' => [
			// 			'rgb' => '808080'
			// 		]
			// 	],
			// ],
			'alignment' => [
				'horizontal' => Alignment::HORIZONTAL_CENTER,
				'vertical' => Alignment::VERTICAL_CENTER,
				'wrapText' => true,
			]
		]);

		$header = [
			'A' => 'Підстанція',
			'B' => 'Обладнання',
			'C' => 'Дисп.',
			'D' => 'Тип',
			'E' => 'Період',
			'F' => 'Рік',
			'G' => date('Y') + 1,
			'H' => date('Y') + 2,
			'I' => date('Y') + 3,
			'J' => date('Y') + 4,
			'K' => date('Y') + 5,
		];
		foreach ($header as $k => $v) {
			$sheet->getColumnDimension($k)->setAutoSize(true);
			$sheet->setCellValue($k . "2", $v);
		}

		$data = [
			[
				'A' => 'ПС 1',
				'B' => 'Вимикач',
				'C' => 'Т-11',
				'D' => 'ВМПЭ-10',
				'E' => '6',
				'F' => '2017',
				'G' => '',
				'H' => 'KP',
				'I' => '',
				'J' => '',
				'K' => '',
			],
			[
				'A' => 'ПС 1',
				'B' => 'Вимикач',
				'C' => 'Т-12',
				'D' => 'ВМПЭ-10',
				'E' => '6',
				'F' => '2018',
				'G' => '',
				'H' => '',
				'I' => 'KP',
				'J' => '',
				'K' => '',
			],
		];
		foreach ($data as $key => $value) {

			foreach ($value as $k => $v) {
				$sheet->setCellValue($k . ($key + 3), $v);
			}
		}
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	public function get_schedule_pr()
	{
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Графік поточного ремонту.xlsx"');
		header('Cache-Control: max-age=0');
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue("A1", "Hello World !");
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	public function get_schedule_to()
	{
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Графік технічного обслуговування.xlsx"');
		header('Cache-Control: max-age=0');
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue("A1", "Hello World !");
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	public function get_data_for_multi_schedule()
	{
		$data = [];

		$data = $this->schedule_model->get_rows_for_sp();

		$new_data_mgr[] = [
			'repair_type' => 'Вид ремонту',
			'stantion' => 'Підстанція',
			'oborud' => 'Найменування обладнання',
			'disp' => 'Диспечерське найменування',
			'type' => 'Тип обладнання',
			'year_start' => 'Рік вводу в експлуатацію',
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
			$new_array['type'] = implode(', ', array_unique(explode(',', $row->type)));
			$new_array['year_start'] = $row->year_start;
			$new_array['class_voltage'] = $row->class_voltage;
			$new_array['repair_year_last'] = $row->repair_year_last;
			$new_array['period'] = $row->period;
			$new_array['year_repair_invest'] = $row->year_repair_invest;
			$new_array['year_plan_repair_invest'] = $row->year_plan_repair_invest;
			array_push($new_data_mgr, $new_array);
		}
		// echo "<pre>";
		// print_r($new_data_mgr);
		// echo "</pre>";
		$xlsx = Shuchkin\SimpleXLSXGen::fromArray($new_data_mgr);
		$xlsx->downloadAs('multi_grafik_source.xlsx');

		// redirect('multi_year_schedule');
	}

	public function get_data_for_schedule_sp()
	{
		$data = [];

		$data = $this->schedule_model->get_rows_for_sp();
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";

		$this->gen_xlsx_for_schedule($data);
	}

	public function get_data_for_schedule_srm()
	{
		$data = [];

		$data = $this->schedule_model->get_rows_for_srm();

		$this->gen_xlsx_for_schedule($data);
	}

	private function gen_xlsx_for_schedule($data)
	{
		$new_data_gr[] = [
			'cipher' => 'Шифр ремонту',
			'subdivision' => 'Дільниця',
			'stantion' => 'Підстанція',
			'disp' => 'Диспечерське найменування',
			'oborud' => 'Найменування обладнання',
			'class_voltage' => 'Клас напруги обладнання',
			'type' => 'Тип обладнання',
			'month' => 'Місяць ремонту',
			'repair_type' => 'Вид ремонту',
			'repair_method' => 'Спосіб ремонту',
			'quantity' => 'Кількість',
			'is_repair' => 'Ремонтуємо?',
			'year_start' => 'Рік вводу в експлуатацію',
			'note' => 'Примітка',
			'year_repair' => 'Рік ремонту',
			'note_contract' => 'Примітка для підряду',
		];

		foreach ($data as $row) {
			$new_array['cipher'] = $row->cipher;
			$new_array['subdivision'] = $row->user;
			$new_array['stantion'] = explode(" ", str_replace("\"", "", $row->stantion))[0] . " " . explode(" ", str_replace("\"", "", $row->stantion))[1];
			$new_array['disp'] = $row->disp;
			if ($row->insulation_type_id == 2 && $row->oborud === 'Вимикач') {
				$new_array['oborud'] = 'Елегазовий вимикач';
			} elseif ($row->insulation_type_id == 3 && $row->oborud === 'Вимикач') {
				$new_array['oborud'] = 'Вакуумний вимикач';
			} elseif ($row->insulation_type_id == 4 && $row->oborud === 'Вимикач') {
				$new_array['oborud'] = 'Масляний вимикач';
			} else {
				$new_array['oborud'] = $row->oborud;
			}
			$new_array['class_voltage'] = $row->class_voltage;
			$new_array['type'] = implode(', ', array_unique(explode(',', $row->type)));
			$new_array['month'] = 0;
			$new_array['repair_type'] = $row->repair_type;
			$new_array['repair_method'] = 'ГС';
			$new_array['quantity'] = 1;
			$new_array['is_repair'] = 1;
			$new_array['year_start'] = $row->year_start_min ? $row->year_start_min : $row->year_start;
			$new_array['note'] = '';
			$new_array['year_repair'] = date('Y') + 1;
			$new_array['note_contract'] = '';

			array_push($new_data_gr, $new_array);
		}

		// echo "<pre>";
		// print_r($data);
		// print_r($new_data_gr);
		// echo "</pre>";

		$xlsx = Shuchkin\SimpleXLSXGen::fromArray($new_data_gr);
		$xlsx->downloadAs('source_' . (date('Y') + 1) . '.xlsx');
	}

	public function change_cipher_ajax()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		$this->form_validation->set_rules('value', 'Шифр ремонту', 'numeric');

		if ($this->form_validation->run() == FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Щось пішло не так!'], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$result = $this->schedule_model->change_value('cipher_id', $this->input->post('value'), $this->input->post('id'));
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
			return;
		}
	}

	public function change_periodicity_ajax()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		$this->form_validation->set_rules('value', 'Періодичність', 'numeric');

		if ($this->form_validation->run() == FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Щось пішло не так!'], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			// $this->schedule_material_model->update_for_schedule_id(['is_extra' => 0], $this->input->post('id'));
			$result = $this->schedule_model->change_value('periodicity', $this->input->post('value'), $this->input->post('id'));
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
			return;
		}
	}

	public function change_year_service_ajax()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		// $this->schedule_material_model->update_for_schedule_id(['is_extra' => 0], $this->input->post('id'));
		$result = $this->schedule_model->change_value('year_last_service', $this->input->post('value'), $this->input->post('id'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function change_status_ajax()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		// $this->schedule_material_model->update_for_schedule_id(['is_extra' => 0], $this->input->post('id'));
		$this->schedule_model->change_value('status', $this->input->post('value'), $this->input->post('id'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function add_next_year_ajax()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		// $this->schedule_material_model->update_for_schedule_id(['is_extra' => 0], $this->input->post('id'));
		$this->schedule_model->change_value('will_add', $this->input->post('value'), $this->input->post('id'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function delete_next_year_ajax()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		// $this->schedule_material_model->update_for_schedule_id(['is_extra' => 0], $this->input->post('id'));
		$this->schedule_model->change_value('is_repair', $this->input->post('value'), $this->input->post('id'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function update_method_service_ajax()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->schedule_model->change_value('is_contract_method', $this->input->post('value'), $this->input->post('id'));
		$this->schedule_year_model->change_value('is_contract_method', $this->input->post('value'), $this->input->post('id'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function delete_specific_renovation_object($specific_renovation_object_id = NULL)
	{
		if ($this->session->user->group !== 'admin') {
			show_404();
		}

		if (!is_numeric($specific_renovation_object_id) || !isset($specific_renovation_object_id)) {
			show_404();
		}

		// $this->specific_renovation_object_model->delete_specific_renovation_object_full($specific_renovation_object_id);
		redirect('/multi_year_schedule');
	}

	public function fill_next_year()
	{
		$data = $this->schedule_year_model->get_current_year();

		// $schedules = $this->schedule_model->get_all();

		// $i = 0;
		// foreach ($schedules as $schedule) {
		// 	foreach ($data as $item) {
		// 		if ($schedule->id == $item->schedule_id) {
		// 			$i++;
		// 		}
		// 	}
		// }

		echo "<pre>";
		// echo $i;
		print_r($data);
		echo "</pre>";
	}
}
