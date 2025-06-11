<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MYPDF extends TCPDF
{
	public function Header()
	{
		if ($this->page === 1) {
			$style = array(
				'border' => false,
				'padding' => 0,
				'fgcolor' => array(47, 79, 79),
				'bgcolor' => false
			);
			$image_file = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/logo.png';
			$this->Image($image_file, 16, 10, 20, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
			$this->write2DBarcode('Документ сгенеровано ' . date('d-m-Y року о H:i:s'), 'QRCODE,H', 375, 10, 29, 29, $style, 'N');
			// $this->write2DBarcode('Документ сгенеровано ' . date('d-m-Y року о H:i:s'), 'PDF417', 231, 10, 50, 50, $style, 'N');
			$this->SetFont('dejavusans', 'B', 20, '', true);
			// $this->Cell(0, 15, 'ПрАТ "Кіровоградобленерго"', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		}
	}
}

class Complete_renovation_objects extends CI_Controller
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

		// if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer') {
		// 	show_404();
		// }

		$this->load->library('pagination');
		$this->load->library('user_agent');

		$this->load->model('subdivision_model');
		$this->load->model('operating_list_object_model');
		$this->load->model('complete_renovation_object_model');
		$this->load->model('type_service_model');
		$this->load->model('log_model');
	}

	public function index()
	{
		$this->load->library('form_validation');
		$data = [];

		$data['title'] = 'Енергетичні об`єкти';
		$data['content'] = 'complete_renovation_objects/index';
		$data['page'] = 'complete_renovation_objects/index';
		$data['page_js'] = 'complete_renovation_objects';
		$data['datatables'] = TRUE;
		$data['title_heading'] = 'Енергетичні об`єкти (Експлуатаційна відомість)';
		$data['title_heading_card'] = 'Енергетичні об`єкти';

		// $data['subdivisions'] = $this->subdivision_model->get_data_for_user();
		$data['subdivisions'] = $this->complete_renovation_object_model->get_subdivisions();
		if ($this->input->get('subdivision_id')) {
			$data['complete_renovation_objects'] = $this->complete_renovation_object_model->get_stantions();
		}
		$data['count_subdivisions'] = count($data['subdivisions']);
		$type_services = $this->type_service_model->get_data();

		// Start Pagination
		$link = '/complete_renovation_objects/index/';
		$total_rows = $this->complete_renovation_object_model->get_total_complete_renovation_objects();

		$this->load->helper('config_pagination');
		$config = get_config_pagination($link, $total_rows);
		$this->pagination->initialize($config);

		$per_page = $config['per_page'];
		$offset = $this->input->get('page') ? ($this->input->get('page') - 1) * $per_page : 0;
		$total_rows = $config['total_rows'];

		$data['per_page'] = !$this->input->get('page') ? 1 : (($this->input->get('page') - 1) * $per_page + 1);
		$data['offset'] = $this->input->get('page') ? $offset : $per_page;
		$data['total_rows'] = $total_rows;
		// End Pagination

		$stantions = $this->complete_renovation_object_model->get_data_with_subdivision_for_user($per_page, $offset);

		foreach ($stantions as $stantion) {
			$stantion->operating_data = $this->operating_list_object_model->get_data_for_object($stantion->id);
			foreach ($stantion->operating_data as $val) {
				$val->type_service_short_name = '-';
				$val->type_service_name = '-';
				foreach ($type_services as $type_service) {
					if ($val->type_service_id == $type_service->id) {
						$val->type_service_short_name = $type_service->short_name;
						$val->type_service_name = $type_service->name;
					}
				}
			}
		}

		$data['type_services'] = $type_services;
		$data['stantions'] = $stantions;

		$this->load->view('layout', $data);
	}

	public function edit($id = NULL)
	{
		if ($this->session->user->group !== 'admin') {
			show_404();
		}

		if (!is_numeric($id) || !isset($id)) {
			show_404();
		}

		$this->load->library('form_validation');
		$data = [];
		$data['title'] = 'Об`єкт';
		$data['content'] = 'complete_renovation_objects/form_object';
		$data['page'] = 'complete_renovation_objects/edit';
		$data['page_js'] = 'complete_renovation_objects';
		$data['datatables'] = TRUE;
		$data['title_heading'] = 'Об\'єкт';
		$data['title_heading_card'] = 'Об\'єкт';

		$data['stantion'] = $this->complete_renovation_object_model->get_row($id);

		$this->load->view('layout', $data);
	}

	public function fff()
	{
		$this->output->set_content_type('application/json');

		$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function add_operating_list_object()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Відсутні дані POST!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		$this->form_validation->set_rules('service_date', 'Дата обслуговування об`єкту', 'required|trim');
		$this->form_validation->set_rules('type_service_id', 'Тип обслуговування', 'required');
		$this->form_validation->set_rules('document_number', 'Номер документу', 'required|trim|min_length[3]|max_length[20]');
		$this->form_validation->set_rules('service_data', 'Дані з експлуатації по об`єкту', 'required|trim|min_length[3]|max_length[255]');
		$this->form_validation->set_rules('executor', 'Виконавець робіт', 'required|trim|min_length[3]|max_length[50]');

		if ($this->form_validation->run() == FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Щось пішло не так!', 'errors' => $this->form_validation->error_array()], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$operating_list_object_data = $this->set_oparating_list_object_add_data($this->input->post());

			$result = $this->operating_list_object_model->add_data($operating_list_object_data);
			$id = $this->db->insert_id();

			if ($result) {
				$log_data = $this->set_log_data("Створення експлуатаційних даних по об`єкту.", 'create', json_encode(NULL, JSON_UNESCAPED_UNICODE), json_encode($operating_list_object_data, JSON_UNESCAPED_UNICODE), 2);
				$this->log_model->insert_data($log_data);
			}

			if ($_FILES['document_scan']['error'] == 0) {
				$upload = $this->upload_file();
				if (isset($upload['error'])) {
					$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => $upload['error'], 'error' => TRUE], JSON_UNESCAPED_UNICODE));
					return;
				} else {
					$this->operating_list_object_model->edit_data_row(['document_scan' => $upload['upload_data']['file_name']], $id);
				}
			}

			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані додано!', 'result' => $result], JSON_UNESCAPED_UNICODE));
			return;
		}
	}

	public function edit_operating_list_object()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Відсутні дані POST!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!$this->input->post('is_edit')) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Необхідно активувати форму!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		$this->form_validation->set_rules('service_date', 'Дата обслуговування об`єкту', 'required|trim');
		$this->form_validation->set_rules('type_service_id', 'Тип обслуговування', 'required');
		$this->form_validation->set_rules('document_number', 'Номер документу', 'required|trim|min_length[3]|max_length[40]');
		$this->form_validation->set_rules('service_data', 'Дані з експлуатації по об`єкту', 'required|trim|min_length[3]|max_length[255]');
		$this->form_validation->set_rules('executor', 'Виконавець робіт', 'required|trim|min_length[3]|max_length[50]');

		if ($this->form_validation->run() == FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Щось пішло не так!', 'errors' => $this->form_validation->error_array()], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$operating_list_data_before = $this->operating_list_object_model->get_data_row($this->input->post('id'));
			$operating_list_data_after = $this->set_oparating_list_object_edit_data($this->input->post());

			$result = $this->operating_list_object_model->edit_data_row($operating_list_data_after, $this->input->post('id'));

			if ($result) {
				$log_data = $this->set_log_data("Зміна експлуатаційних даних по об`єкту.", 'update', json_encode($operating_list_data_before, JSON_UNESCAPED_UNICODE), json_encode($operating_list_data_after, JSON_UNESCAPED_UNICODE), 3);
				$this->log_model->insert_data($log_data);

				$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!', 'result' => $result], JSON_UNESCAPED_UNICODE));
				return;
			}
		}
	}

	public function gen_operating_list_object_pdf($id = NULL)
	{
		if (!is_numeric($id)) {
			show_404();
		}

		$data = [];
		$complete_renovation_object = $this->complete_renovation_object_model->get_row($id);
		if (!$complete_renovation_object) {
			show_404();
		}
		$type_services = $this->type_service_model->get_data();
		$operating_list = $this->operating_list_object_model->get_data_for_object($id);
		foreach ($operating_list as $item) {
			$item->type_service_short_name = NULL;
			$item->type_service_name = NULL;
			foreach ($type_services as $type_service) {
				if ($item->type_service_id == $type_service->id) {
					$item->type_service_short_name = $type_service->short_name;
					$item->type_service_name = $type_service->name;
				}
			}
		}
		$data['complete_renovation_object'] = $complete_renovation_object;
		$data['results'] = $operating_list;


		// echo "<pre>";
		// print_r($data);
		// print_r($operating_list);
		// print_r($equipments_group);
		// echo "</pre>";
		// exit;

		// create new PDF document
		$pdf = new MYPDF('L', PDF_UNIT, 'A3', true, 'UTF-8', false);

		// set document information
		$pdf->SetAuthor('Yurii Sychov');
		$pdf->SetTitle('Operating list Object');

		// set default header data
		// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
		// $pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

		// set header and footer fonts
		// $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		// $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set default font subsetting mode
		$pdf->setFontSubsetting(true);

		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->SetFont('dejavusans', '', 12, '', true);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();

		// set text shadow effect
		$pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

		// Set some content to print
		$html = $this->load->view('complete_renovation_objects/operating_list_object_pdf', $data, TRUE);

		// Print text using writeHTMLCell()
		$pdf->writeHTMLCell(0, 0, '', 15, $html, 0, 1, 0, true, '', true);

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$pdf->Output('operating_list_object.pdf', 'I');
	}

	public function gen_operating_list_object_excel($id = NULL)
	{
		if (!is_numeric($id)) {
			show_404();
		}

		$data = [];
		$complete_renovation_object = $this->complete_renovation_object_model->get_row($id);
		if (!$complete_renovation_object) {
			show_404();
		}
		$type_services = $this->type_service_model->get_data();
		$operating_list = $this->operating_list_object_model->get_data_for_object($id);
		foreach ($operating_list as $item) {
			$item->type_service_short_name = NULL;
			$item->type_service_name = NULL;
			foreach ($type_services as $type_service) {
				if ($item->type_service_id == $type_service->id) {
					$item->type_service_short_name = $type_service->short_name;
					$item->type_service_name = $type_service->name;
				}
			}
		}

		$spreadsheet = new Spreadsheet();

		$spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
		$spreadsheet->getActiveSheet()->getPageSetup()->setFitToHeight(0);
		$spreadsheet->getActiveSheet()->getPageMargins()->setTop(2);
		$spreadsheet->getActiveSheet()->getPageMargins()->setRight(1);
		$spreadsheet->getActiveSheet()->getPageMargins()->setLeft(1);
		$spreadsheet->getActiveSheet()->getPageMargins()->setBottom(1);

		$spreadsheet->getDefaultStyle()->getFont()->setName('Times NewRoman');
		$spreadsheet->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

		$sheet = $spreadsheet->getActiveSheet();

		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

		$spreadsheet->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$spreadsheet->getActiveSheet()->getStyle('A1:A2')->getFont()->setSize(16)->setBold(true);
		$spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
		$spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
		$sheet->mergeCells('A1:F1')->setCellValue('A1', 'Експлуатаційна відомість');
		$sheet->mergeCells('A2:F2')->setCellValue('A2', $complete_renovation_object->name);

		$spreadsheet->getActiveSheet()->getStyle('A3:F3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$spreadsheet->getActiveSheet()->getStyle('A3:F3')->getFont()->setSize(12)->setBold(true);
		$spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(15);


		$spreadsheet->getActiveSheet()->getStyle('A3:F3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
		$sheet->setCellValue('A3', '№ п/п');
		$sheet->setCellValue('B3', 'Дата');
		$sheet->setCellValue('C3', '№ документа');
		$sheet->setCellValue('D3', 'Вид обслуговування');
		$sheet->setCellValue('E3', 'Перелік робіт');
		$sheet->setCellValue('F3', 'Виконавець');

		$row = 4;
		$i = 1;
		foreach ($operating_list as $item) {
			$spreadsheet->getActiveSheet()->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
			$spreadsheet->getActiveSheet()->getStyle('A' . $row . ':D' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$spreadsheet->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue('A' . $row, $i);
			$sheet->setCellValue('B' . $row, date('d.m.Y', strtotime($item->service_date)));
			$sheet->setCellValue('C' . $row, $item->document_number);
			$sheet->setCellValue('D' . $row, $item->type_service_short_name ? $item->type_service_short_name : '-');
			$sheet->setCellValue('E' . $row, $item->service_data);
			$sheet->setCellValue('F' . $row, $item->executor);

			$row++;
			$i++;
		}

		$sheet->getStyle('A1');

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename=' . $complete_renovation_object->name . '.xlsx');
		header('Cache-Control: max-age=0');

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	public function get_value($value = NULL)
	{
		if (!$value) {
			show_404();
		}
		$data['values'] = $this->operating_list_object_model->get_value($value);
		$data['field'] = $value;
		$this->load->view('value', $data);
	}

	public function upload_document_scan()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Відсутні дані POST!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		// Завантажуємо новий файл
		$upload = $this->upload_file();
		if (isset($upload['error'])) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => $upload['error'], 'error' => TRUE], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			// Видаляємо старий файл
			$document_scan = $this->operating_list_object_model->get_data_row($this->input->post('id'))->document_scan;

			if ($document_scan && is_file('./uploads/acts/' . $document_scan)) {
				unlink('./uploads/acts/' . $document_scan);
			}

			$this->operating_list_object_model->edit_data_row(['document_scan' => $upload['upload_data']['file_name']], $this->input->post('id'));
		}

		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Файл завантажено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	private function set_oparating_list_object_add_data($post)
	{

		$data = [];

		$data['subdivision_id'] = $post['subdivision_id'];
		$data['complete_renovation_object_id'] = $post['complete_renovation_object_id'];
		$data['type_service_id'] = $post['type_service_id'] ? $post['type_service_id'] : 0;
		$data['service_date'] = date('Y-m-d', strtotime($post['service_date']));
		$data['document_number'] = $post['document_number'];
		$data['service_data'] = $post['service_data'];
		$data['executor'] = $post['executor'];
		$data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');

		return $data;
	}

	private function set_oparating_list_object_edit_data($post)
	{
		$data = [];

		$data['type_service_id'] = $post['type_service_id'];
		$data['service_date'] = date('Y-m-d', strtotime($post['service_date']));
		$data['document_number'] = $post['document_number'];
		$data['service_data'] = $post['service_data'];
		$data['executor'] = $post['executor'];
		$data['updated_by'] = $this->session->user->id;
		$data['updated_at'] = date('Y-m-d H:i:s');

		return $data;
	}

	private function set_log_data($action, $short_action, $data_before, $data_after, $importance)
	{
		$this->load->library('user_agent');
		$data = [];

		$data['user_id'] = $this->session->user->id;
		$data['link'] = uri_string();
		$data['action'] = $action;
		$data['short_action'] = $short_action;
		$data['data_before'] = $data_before;
		$data['data_after'] = $data_after;
		$data['browser'] = $this->agent->browser();
		$data['ip'] = $this->input->ip_address();
		$data['platform'] = $this->agent->platform();
		$data['importance'] = $importance;
		$data['created_at'] = date('Y-m-d H:i:s');

		return $data;
	}

	private function upload_file()
	{
		$config['upload_path'] = './uploads/acts';
		$config['allowed_types'] = 'pdf';
		$config['encrypt_name'] = TRUE;

		if (!file_exists('./uploads/acts')) {
			mkdir('./uploads/acts', 0755);
		}

		$this->load->library('upload', $config);
		$this->upload->initialize($config);

		if (!$this->upload->do_upload('document_scan')) {
			$error = array('error' => $this->upload->display_errors('', ''));
			return $error;
		} else {
			$data = array('upload_data' => $this->upload->data());
			return $data;
		}
	}

	public function get_complete_renovation_objects_ajax()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_status_header(400);
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!$this->input->get()) {
			$this->output->set_status_header(400);
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не GET запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$complete_renovation_objects = $this->complete_renovation_object_model->get_data_for_subdivision($this->input->get('subdivision_id'));

		if (!$complete_renovation_objects) {
			$this->output->set_status_header(200);
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Не вдалося отримати дані з реєстру!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'complete_renovation_objects' => $complete_renovation_objects], JSON_UNESCAPED_UNICODE));
	}

	public function change_is_repair_ajax()
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

		$result = $this->complete_renovation_object_model->change_value('is_repair', $this->input->post('value'), $this->input->post('id'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function edit_month_ajax()
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

		$this->complete_renovation_object_model->change_value('month', $this->input->post('value'), $this->input->post('id'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function edit_r3_number_ajax()
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

		$this->complete_renovation_object_model->change_value('r3_id', $this->input->post('value'), $this->input->post('id'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}
}
