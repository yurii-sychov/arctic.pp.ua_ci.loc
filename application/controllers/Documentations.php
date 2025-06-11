<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpWord\Style\Language;
use PhpOffice\PhpWord\Style\Tab;

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
			$this->Image($image_file, 20, 10, 20, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
			$this->write2DBarcode('Документ сгенеровано ' . date('d-m-Y року о H:i:s'), 'QRCODE,H', 260, 10, 29, 29, $style, 'N');
			// $this->write2DBarcode('Документ сгенеровано ' . date('d-m-Y року о H:i:s'), 'PDF417', 231, 10, 50, 50, $style, 'N');
			$this->SetFont('dejavusans', 'B', 16, '', true);
			$this->Cell(0, 15, 'ПрАТ "Кіровоградобленерго"', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		}
	}
}

class Documentations extends CI_Controller
{
	public $category_tree = [];

	public function __construct()
	{
		parent::__construct();

		if (!$this->session->master) {
			redirect('auth/signin');
		}

		$this->load->model('documentation_model');
		$this->load->model('documentation_master_model');
		$this->load->model('documentation_category_model');
		$this->load->model('documentation_plot_model');
	}

	public function index()
	{
		$data = [];
		$data['title'] = 'Документація';
		$data['content'] = 'documentations/index';
		$data['page'] = 'documentations';
		$data['page_js'] = 'documentations';
		$data['button'] = $this->session->master->master_group === 'admin'
			? ['name' => 'button_add', 'method' => 'onclick="addDocument(event);"', 'class' => NULL]
			: ['name' => 'button_add', 'method' => NULL, 'class' => 'disabled'];
		$data['datatables'] = TRUE;
		$data['title_heading'] = 'Документація';
		$data['title_heading_card'] = 'Перелік документації';

		$documentations = $this->documentation_model->get_data();
		$documentation_categories = $this->documentation_category_model->get_data();
		$category_tree = $this->recursive($documentation_categories, 0, 0);

		foreach ($documentations as $key => $v) {
			$documentations[$key]->category_tree = '';
			foreach ($category_tree as $val) {
				if ($v->documentation_category_id ==  $val['id']) {
					$documentations[$key]->category_tree = $val['path'];
				}
			}
		}

		$data['category_tree'] = $category_tree;
		$data['documentations'] = $documentations;
		$data['documentation_categories'] = $documentation_categories;

		$data['plots'] = $this->documentation_plot_model->get_data_for_master();
		$data['plot'] = $this->documentation_plot_model->get_data_row($this->input->get('plot_id'));

		$this->load->view('layout_md', $data);
	}

	public function my()
	{
		$data = [];
		$data['title'] = 'Моя документація';
		$data['content'] = 'documentations/my';
		$data['page'] = 'my_documentations';
		$data['page_js'] = 'my_documentations';
		// $data['button'] = ['name' => 'button_add', 'method' => 'onclick="addDocument(event);"'];
		// $data['ag_grid'] = TRUE;
		$data['datatables'] = FALSE;
		$data['title_heading'] = 'Моя документація';
		$data['title_heading_card'] = 'Перелік моєї документації';

		$this->load->view('layout_md', $data);
	}

	public function get_data_row_ajax($id = NULL)
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_status_header(400);
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!$id) {
			$this->output->set_status_header(400);
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'В запросі немає ідентифікатора!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!is_numeric($id)) {
			$this->output->set_status_header(400);
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Ідентифікатор повиненн бути цілим числом!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		// if ($this->session->user->group === 'user' || $this->session->user->group === 'head') {
		// 	$this->output->set_status_header(403);
		// 	$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
		// 	return;
		// }

		$document = $this->documentation_model->get_data_row($id);

		if (!isset($document)) {
			$this->output->set_status_header(400);
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Не вдалося отримати дані з бази даних!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані для зміни отримано', 'document' => $document], JSON_UNESCAPED_UNICODE));
	}

	public function get_data_doc_type_ajax($doc_type = NULL)
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_status_header(400);
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!$doc_type) {
			$this->output->set_status_header(400);
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'В запросі немає ідентифікатора!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!is_numeric($doc_type)) {
			$this->output->set_status_header(400);
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Ідентифікатор повиненн бути цілим числом!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		// if ($this->session->user->group === 'user' || $this->session->user->group === 'head') {
		// 	$this->output->set_status_header(403);
		// 	$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вам не дозволена ця операція!'], JSON_UNESCAPED_UNICODE));
		// 	return;
		// }

		$document = $this->documentation_model->get_data_doc_type_row($doc_type);

		if (!isset($document)) {
			$this->output->set_status_header(400);
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Не вдалося отримати дані з бази даних!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані для зміни отримано', 'document' => $document], JSON_UNESCAPED_UNICODE));
	}

	public function trash_row($id = NULL)
	{
		if ($this->session->master->master_group !== 'admin') {
			show_404();
		}

		if (!$id) {
			show_404();
		}

		if (!is_numeric($id)) {
			show_404();
		}

		$result = $this->documentation_model->trash_row($id, ['is_trash' => 1]);

		if ($result) {
			redirect('/documentations');
		}
	}

	public function untrash_row($id = NULL)
	{
		if ($this->session->master->master_group !== 'admin') {
			show_404();
		}

		if (!$id) {
			show_404();
		}

		if (!is_numeric($id)) {
			show_404();
		}

		$result = $this->documentation_model->untrash_row($id, ['is_trash' => 0]);

		if ($result) {
			redirect('/documentations');
		}
	}

	public function add_data_row_ajax()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_status_header(400);
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		// $this->form_validation->set_error_delimiters('', '');

		// $this->form_validation->set_rules('name', '<strong>Назва документа</strong>', 'required|trim|max_length[255]');
		// $this->form_validation->set_rules('number', '<strong>Номер документа</strong>', 'trim|max_length[255]');
		// $this->form_validation->set_rules('approval_document', '<strong>Документ про затвердження</strong>', 'trim|max_length[255]');
		// $this->form_validation->set_rules('document_date_start', '<strong>Дата затвердження документа</strong>', 'min_length[10]|max_length[10]');
		// $this->form_validation->set_rules('document_date_finish', '<strong>Дата закінчення документа</strong>', 'min_length[10]|max_length[10]');
		// $this->form_validation->set_rules('periodicity', '<strong>Періодичність перегляду документа, роки</strong>', 'numeric|min_length[1]|max_length[1]');
		// $this->form_validation->set_rules('document_type', '<strong>Тип документа</strong>', 'required');

		$this->set_rules();

		if ($this->form_validation->run() == FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => validation_errors()], JSON_UNESCAPED_UNICODE));
			return;
		}

		$data = $this->set_data_insert_row($this->input->post());

		if (!$data) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Дані для змін не встановлені!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$insert_id = $this->documentation_model->insert_row($data);

		if ($insert_id) {
			$data['insert_id'] = $insert_id;
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані додано!', 'data' => $data], JSON_UNESCAPED_UNICODE));
			return;
		}
	}

	public function edit_data_row_ajax($id = NULL)
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_status_header(400);
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!$id) {
			$this->output->set_status_header(400);
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'В запросі немає ідентифікатора!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!is_numeric($id)) {
			$this->output->set_status_header(400);
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Ідентифікатор повиненн бути цілим числом!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		// $this->form_validation->set_error_delimiters('', '');

		// $this->form_validation->set_rules('name', '<strong>Назва документа</strong>', 'required|trim|max_length[255]');
		// $this->form_validation->set_rules('number', '<strong>Номер документа</strong>', 'trim|max_length[255]');
		// $this->form_validation->set_rules('approval_document', '<strong>Документ про затвердження</strong>', 'trim|max_length[255]');
		// $this->form_validation->set_rules('document_date_start', '<strong>Дата затвердження документа</strong>', 'min_length[10]|max_length[10]');
		// $this->form_validation->set_rules('document_date_finish', '<strong>Дата закінчення документа</strong>', 'min_length[10]|max_length[10]');
		// $this->form_validation->set_rules('periodicity', '<strong>Періодичність перегляду документа, роки</strong>', 'numeric|min_length[1]|max_length[1]');
		// $this->form_validation->set_rules('document_type', '<strong>Тип документа</strong>', 'required');

		$this->set_rules();

		if ($this->form_validation->run() == FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => validation_errors()], JSON_UNESCAPED_UNICODE));
			return;
		}

		$data = $this->set_data_update_row($this->input->post());

		if (!$data) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Дані для змін не встановлені!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$result = $this->documentation_model->update_row($id, $data);

		if ($result) {
			$data['id'] = $id;
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!', 'data' => $data], JSON_UNESCAPED_UNICODE));
			return;
		}
	}

	public function add_doc_ajax()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->is_ajax_request()) {
			$this->output->set_status_header(400);
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$data = $this->set_data_add_doc($this->input->post());

		if (!$data) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Дані для змін не встановлені!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if ($this->input->post('checked') == 'true') {
			$result = $this->documentation_master_model->insert_row($data);
		} else {
			$result = $this->documentation_master_model->delete_for_master_row($this->input->post('documentation_id'), $this->input->post('plot_id'));
		}

		if ($result && $this->input->post('checked') == 'true') {
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Документ додано!', 'data' => $data], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Документ видалено!', 'data' => $data], JSON_UNESCAPED_UNICODE));
			return;
		}
	}

	public function list_pdf($docs = NULL, $plot = NULL)
	{
		if (!$docs || !$plot) {
			show_404();
		}

		if (!is_numeric($docs) || !is_numeric($plot)) {
			show_404();
		}

		if ($docs > 3) {
			show_404();
		}

		$documentations = $this->documentation_master_model->get_data($docs, $plot);

		if (!count($documentations)) {
			show_404();
		}

		$data = [];
		$data['documentations'] = $documentations;

		if ($docs == 1) {
			$doc_name = 'Інструкції з охорони праці';
		}
		if ($docs == 2) {
			$doc_name = 'Інструкції з пожежної безпеки';
		}
		if ($docs == 3) {
			$doc_name = 'Експлуатаційні інструкції';
		}

		$data['doc_name'] = $doc_name;
		$data['plot'] = $this->documentation_plot_model->get_data_row($plot);

		$html = $this->load->view('/documentations/docs_pdf', $data, TRUE);

		// Create new PDF document
		$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A3', true, 'UTF-8', false);

		// Set document information
		$pdf->setCreator(PDF_CREATOR);
		$pdf->setAuthor('Yurii Sychov');
		$pdf->setTitle('Перелік інструкцій з ОП');
		$pdf->setSubject('Перелік інструкцій з ОП');
		$pdf->setKeywords('TCPDF, PDF');

		// Set default header and footer data
		// $pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Перелік інструкцій з ОП', "by Yurii Sychov - Sychov.pp.ua\nwww.repair.pp.ua", array(0, 64, 255), array(0, 64, 128));
		$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

		// Set header and footer fonts
		// $pdf->setHeaderFont(array('dejavusans', '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// Set default monospaced font
		$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// Set margins
		$pdf->setMargins(19, PDF_MARGIN_TOP, 10);
		// $pdf->setHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->setFooterMargin(PDF_MARGIN_FOOTER);

		// Set auto page breaks
		// $pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// Set image scale factor
		// $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// Set some language-dependent strings (optional)
		// if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
		// 	require_once(dirname(__FILE__) . '/lang/eng.php');
		// 	$pdf->setLanguageArray($l);
		// }

		// ---------------------------------------------------------

		// Set default font subsetting mode
		$pdf->setFontSubsetting(true);

		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->setFont('dejavusans', '', 10, '', true);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();

		// Set text shadow effect
		$pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

		// Set some content to print
		// $html = $this->load->view('op');

		// Print text using writeHTMLCell()
		$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

		// ---------------------------------------------------------

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$pdf->Output(str_replace(['-', '/', '"'], [' ', '_', ''], $data['plot']->name) . ' (' . $doc_name . ').pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+
	}

	public function list_docs($plot = NULL)
	{
		if (!$plot) {
			show_404();
		}

		if (!is_numeric($plot)) {
			show_404();
		}

		header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
		header('Content-Disposition: attachment;filename="Перелік інструкцій з ОП.docx"');
		header('Cache-Control: max-age=0');

		$phpWord = new \PhpOffice\PhpWord\PhpWord();

		$phpWord->setDefaultFontName('Times New Roman');
		$phpWord->setDefaultFontSize(12);
		$phpWord->getSettings()->setZoom(100);
		$phpWord->getSettings()->setThemeFontLang(new \PhpOffice\PhpWord\Style\Language(Language::UK_UA));

		$sectionStyle = [
			'marginTop' => 566.929134,
			'marginLeft' => 1133.858268,
			'marginRight' => 566.929134,
			'marginBottom' => 566.929134,
			'headerHeight' => 0,
			'footerHeight' => 0,
			'pageSizeW' => '8419',
			'pageSizeH' => '11906',
			'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0),
			'spaceBefore' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0),
			'spacing' => 120,
			'lineHeight' => 1,
		];
		$section = $phpWord->addSection($sectionStyle);

		$section->addText('ЗАТВЕРДЖУЮ', ['bold' => TRUE], ['tabs' => 10]);
		$section->addText('Начальник СП', ['bold' => TRUE]);
		$section->addText('__________ Юрій СИЧОВ', ['bold' => TRUE]);
		$section->addText('"___"___________ 20 ___ рік', ['bold' => TRUE]);

		$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
		$objWriter->save('php://output');
	}

	private function set_rules()
	{
		return [
			$this->form_validation->set_rules('name', '<strong>Назва документа</strong>', 'required|trim|max_length[255]'),
			$this->form_validation->set_rules('number', '<strong>Номер документа</strong>', 'trim|max_length[255]'),
			$this->form_validation->set_rules('approval_document', '<strong>Документ про затвердження</strong>', 'trim|max_length[255]'),
			$this->form_validation->set_rules('document_date_start', '<strong>Дата затвердження документа</strong>', 'min_length[10]|max_length[10]'),
			$this->form_validation->set_rules('document revision date', '<strong>Дата затвердження документа</strong>', 'min_length[10]|max_length[10]'),
			$this->form_validation->set_rules('document_date_finish', '<strong>Дата закінчення документа</strong>', 'min_length[10]|max_length[10]'),
			$this->form_validation->set_rules('periodicity', '<strong>Періодичність перегляду документа, роки</strong>', 'numeric|min_length[1]|max_length[1]'),
			$this->form_validation->set_rules('term', '<strong>Термін зберігання документа</strong>', 'required'),
			$this->form_validation->set_rules('document_type', '<strong>Тип документа</strong>', 'required')
		];
	}

	private function set_data_insert_row($post)
	{
		$data = [];
		foreach ($post as $key => $value) {
			$data[$key] = $value;
		}
		$data['created_by'] = isset($this->session->master->id) ? $this->session->master->id : 1;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = isset($this->session->master->id) ? $this->session->master->id : 1;
		$data['updated_at'] = date('Y-m-d H:i:s');

		return $data;
	}

	private function set_data_update_row($post)
	{
		$data = [];
		foreach ($post as $key => $value) {
			$data[$key] = $value;
		}
		$data['updated_by'] = isset($this->session->master->id) ? $this->session->master->id : 1;
		$data['updated_at'] = date('Y-m-d H:i:s');

		return $data;
	}

	private function set_data_add_doc($post)
	{
		$data = [];

		$data['documentation_id'] = $this->input->post('documentation_id');
		$data['plot_id'] = $this->input->post('plot_id');
		$data['created_by'] = isset($this->session->master->id) ? $this->session->master->id : 1;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = isset($this->session->master->id) ? $this->session->master->id : 1;
		$data['updated_at'] = date('Y-m-d H:i:s');

		return $data;
	}

	private function recursive($data, $pid = 0, $level = 0, $path = "")
	{
		$arr = json_decode(json_encode($data), true);

		foreach ($arr as $row) {
			if ($row['parent_id'] == $pid) {
				$_row['id']    = $row['id'];
				$_row['name']   = $_row['name']   = str_pad('', $level * 3, '.') . $row['name'];
				$_row['parent_id']    = $row['parent_id'];
				$_row['path']   = $path ? $path . " (" . $row['name'] . ")" : $row['name'];
				$_row['level']  = $level;

				$this->category_tree[] = $_row;

				$this->recursive($data, $row['id'], $level + 1, $_row['path']);
			}
		}

		return $this->category_tree;
	}
}
