<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpWord\Style\Language;

class Acts extends CI_Controller
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

		$this->load->model('complete_renovation_object_model');
	}

	public function index()
	{
		$data = [];
		$data['title'] = 'Акти приймання здавання';
		$data['content'] = 'acts/index';
		$data['page'] = 'acts';
		$data['page_js'] = 'acts';
		$data['title_heading'] = 'Акти приймання здавання';
		$data['title_heading_card'] = 'Акти приймання здавання';
		$data['datatables'] = TRUE;
		$data['forms'] = FALSE;

		$id = '1Ctg89Q2_KqoG6OmZ21p8yqUotCVENOBva9qrTXB4Myw';
		$gid = 1449075880;

		$csv = file_get_contents('https://docs.google.com/spreadsheets/d/' . $id . '/export?format=csv&gid=' . $gid);
		$csv = explode("\r\n", $csv);
		$array = array_map('str_getcsv', $csv);

		$new_array = [];
		foreach ($array as $key => $value) {
			if ($key > 1) {
				array_push($new_array, array_combine($array[0], $value));
			}
		}
		$data['results'] = $new_array;

		// echo "<pre>";
		// print_r($new_array);
		// var_export($new_array);
		// echo "</pre>";

		$this->load->view('layout_lte', $data);
	}

	public function create($quantity = NULL)
	{
		if (!is_numeric($quantity)) {
			$quantity = 1;
		}
		$data = [];
		$data['title'] = 'Створення акту приймання здавання';
		$data['content'] = 'acts/form';
		$data['page'] = 'acts';
		$data['page_js'] = 'acts';
		$data['title_heading'] = 'Створення акту приймання здавання';
		$data['title_heading_card'] = 'Форма для створення акту приймання здавання';
		$data['datatables'] = FALSE;
		$data['forms'] = TRUE;
		$data['quantity'] = $quantity ? $quantity : 1;
		$id = '1Ctg89Q2_KqoG6OmZ21p8yqUotCVENOBva9qrTXB4Myw';
		$gid = 1422538028;

		$csv = file_get_contents('https://docs.google.com/spreadsheets/d/' . $id . '/export?format=csv&gid=' . $gid);
		$csv = explode("\r\n", $csv);
		$array_data = array_map('str_getcsv', $csv);
		$data['array_data'] = $array_data;

		$new_array = [];
		foreach ($array_data as $key => $value) {
			if ($key > 1) {
				array_push($new_array, array_combine($array_data[0], $value));
			}
		}

		$stations = array_column($new_array, 'station');
		$data['stations'] = array_filter($stations, function ($row) {
			return $row !== "";
		});

		$commission_members = array_column($new_array, 'commission_member');
		$data['commission_members'] = array_filter($commission_members, function ($row) {
			return $row !== "";
		});

		$work_heads = array_column($new_array, 'work_head');
		$data['work_heads'] = array_filter($work_heads, function ($row) {
			return $row !== "";
		});

		$work_members = array_column($new_array, 'work_member');
		$data['work_members'] = array_filter($work_members, function ($row) {
			return $row !== "";
		});

		$this->load->view('layout_lte', $data);
	}

	public function act_generation($row_id)
	{
		$id = '1Ctg89Q2_KqoG6OmZ21p8yqUotCVENOBva9qrTXB4Myw';
		$gid = 1449075880;

		$csv = file_get_contents('https://docs.google.com/spreadsheets/d/' . $id . '/export?format=csv&gid=' . $gid);
		$csv = explode("\r\n", $csv);
		$array = array_map('str_getcsv', $csv);
		$new_array = [];

		foreach ($array as $key => $value) {
			if ($key > 1) {
				array_push($new_array, array_combine($array[0], $value));
			}
		}
		$new_array = array_filter($new_array, function ($row) {
			return $row['id'] === $this->uri->segment(3);
		});
		foreach ($new_array as $row) {
			$row['plan_start_array'] = explode(", ", $row['plan_start']);
			$row['plan_end_array'] = explode(", ", $row['plan_end']);
			$row['fact_start_array'] = explode(", ", $row['fact_start']);
			$row['fact_end_array'] = explode(", ", $row['fact_end']);
			$row['plan'] = "";
			$row['fact'] = "";
			foreach ($row['plan_start_array'] as $k => $item) {
				$row['plan'] .= ' з ' . $row['plan_start_array'][$k] . ' року до ' . $row['plan_end_array'][$k] . ' року, ';
				$row['fact'] .= ' з ' . $row['fact_start_array'][$k] . ' року до ' . $row['fact_end_array'][$k] . ' року, ';
			}
			$row['plan'] = substr($row['plan'], 0, -2);
			$row['fact'] = substr($row['fact'], 0, -2);
			$result = $row;
		}

		$commission_members = explode(',', $result['commission_members']);
		// echo "<pre>";
		// print_r($result);
		// echo "</pre>";
		// exit;

		// Creating the new document...
		$phpWord = new \PhpOffice\PhpWord\PhpWord();

		$phpWord->setDefaultFontName('Times New Roman');
		$phpWord->setDefaultFontSize(6);
		$phpWord->getSettings()->setZoom(100);
		$phpWord->getSettings()->setThemeFontLang(new \PhpOffice\PhpWord\Style\Language(Language::UK_UA));

		// $documentProtection = $phpWord->getSettings()->getDocumentProtection();
		// $documentProtection->setEditing(PhpOffice\PhpWord\SimpleType\DocProtect::READ_ONLY);

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


		// $header = $section->addHeader();
		// $header->addWatermark('./assets/images/logo.png', array('marginTop' => 200, 'marginLeft' => 55));
		// $header->addTextBreak();
		// $header->addImage('./assets/images/logo.png', ['width' => 30, 'align' => 'right']);

		// $footer = $section->addFooter();
		// $footer->addPreserveText('Сторінка {PAGE} з {NUMPAGES}', ['bold' => FALSE], ['align' => 'center']);

		$section->addText('ПрАТ "Кіровоградобленерго"', ['bold' => TRUE]);
		$textrun = $section->addTextRun();
		$textrun->addText('Підрозділ:');
		$textrun->addText('СП', ['underline' => 'single']);

		$section->addTextBreak(1);
		$section->addText('АКТ', ['bold' => TRUE], ['alignment' => 'center']);
		$section->addText('приймання-здавання електричної мережі з капітального ремонту', ['bold' => TRUE], ['alignment' => 'center']);
		$section->addText($result['acceptance_date'] . ' року', ['bold' => TRUE], ['alignment' => 'center']);
		$section->addTextBreak(1);
		$section->addText('Комісія призначена наказом по ПрАТ "Кіровоградобленерго" ' . $result['order'] . ' у складі:', ['marginLeft' => '50px'], ['alignment' => 'both']);
		$section->addTextBreak(1);
		$textrun = $section->addTextRun(['alignment' => 'both']);
		$textrun->addText('Голова комісії: ');
		$textrun->addText($result['commission_head'], ['underline' => 'single']);
		$textrun = $section->addTextRun(['alignment' => 'both']);
		$textrun->addText('Члени комісії: ');
		$textrun->addText($result['commission_members'], ['underline' => 'single']);
		$section->addTextBreak(1);
		$textrun = $section->addTextRun(['alignment' => 'both']);
		$textrun->addText('здійснила приймання в експлуатацію закінченої капітальним ремонтом ');
		$textrun->addText($result['dno'], ['underline' => 'single']);
		$section->addText('При приймані встановлено:');
		$phpWord->addNumberingStyle(
			'multilevel',
			[
				'type' => 'multilevel',
				'levels' => [
					['format' => 'decimal', 'text' => '%1.', 'left' => 160, 'hanging' => 160, 'tabPos' => 160],
				]
			]
		);
		$section->addListItem('Ремонт виконувався в період ' . $result['fact'] . ' при плановому терміні ' . $result['plan'], 0, null, 'multilevel', ['alignment' => 'both']);
		$textrun = $section->addTextRun(['alignment' => 'both']);
		$textrun->addText('Відповідальний керівник: ');
		$textrun->addText($result['work_head'], ['underline' => 'single']);
		$textrun = $section->addTextRun(['alignment' => 'both']);
		$textrun->addText('Відповідальний виконавець: ');
		$textrun->addText($result['work_members'], ['underline' => 'single']);

		$listItemRun = $section->addListItemRun(0, 'multilevel', ['alignment' => 'both']);
		$listItemRun->addText('Ремонт виконаний на основі договору від ');
		$listItemRun->addText($result['contract_date'] ? $result['contract_date'] . ' року' : '__________', ['underline' => 'single']);
		$listItemRun->addText(' № ');
		$listItemRun->addText($result['contract_number'] ? $result['contract_number'] : '__________', ['underline' => 'single']);
		$listItemRun->addText(' згідно плану капітального ремонту на ' . date('Y') . ' рік');

		$listItemRun = $section->addListItemRun(0, 'multilevel', ['alignment' => 'both']);
		$listItemRun->addText('Роботи виконані згідно або без відхилень від проекту: ');
		$listItemRun->addText($result['deviation'], ['underline' => 'single']);

		$listItemRun = $section->addListItemRun(0, 'multilevel', ['alignment' => 'both']);
		$listItemRun->addText('Під час ремонту виконані основні наступні роботи: ');
		$listItemRun->addText($result['work_complete'], ['underline' => 'single']);

		$listItemRun = $section->addListItemRun(0, 'multilevel', ['alignment' => 'both']);
		$listItemRun->addText('Кошторисна вартість ремонту згідно з затвердженною кошторисною документацією ');
		$listItemRun->addText($result['plan_sum'] . ' грн.', ['underline' => 'single']);
		$listItemRun->addText(' , фактична ');
		$listItemRun->addText($result['fact_sum'] . ' грн.', ['underline' => 'single']);

		$listItemRun = $section->addListItemRun(0, 'multilevel', ['alignment' => 'both']);
		$listItemRun->addText('Комісія перевірила наявність і зміст наступних документів з ремонту: ');
		$listItemRun->addText($result['documents'], ['underline' => 'single']);

		$listItemRun = $section->addListItemRun(0, 'multilevel', ['alignment' => 'both']);
		$listItemRun->addText('Недоробки, що не заважають нормальній експлуатації відремонтованої мережі, вказані у додатку до акту із терміном їх усунення: ');
		$listItemRun->addText($result['defects'], ['underline' => 'single']);

		$listItemRun = $section->addListItemRun(0, 'multilevel', ['alignment' => 'both']);
		$listItemRun->addText('Проведені роботи призводять до (відновлення початкових експлуатаційних характеристик / поліпшення експлуатаційних характеристик об\'єкту): ');
		$listItemRun->addText($result['result_repair'], ['underline' => 'single']);

		$section->addTextBreak(1);
		$section->addText('Рішення комісії:', ['bold' => TRUE]);
		$textrun = $section->addTextRun(['alignment' => 'both']);
		$textrun->addText('Надана до здавання мережа ');
		$textrun->addText($result['station'] . ' (' . $result['dno'] . ')', ['underline' => 'single']);
		$textrun = $section->addTextRun(['alignment' => 'both']);
		$textrun->addText('приймається в експлуатацію ');
		$textrun->addText($result['acceptance_date'] . ' року', ['underline' => 'single']);
		$textrun = $section->addTextRun(['alignment' => 'both']);
		$textrun->addText('з оцінкою якості відремонтованної мережі: ');
		$textrun->addText($result['result'], ['underline' => 'single']);
		$section->addTextBreak(2);
		$section->addText('Голова комісії ____________________ ' . $result['commission_head']);
		$section->addTextBreak(1);
		foreach ($commission_members as $member) {
			$section->addText('член комісії     ____________________ ' . $member);
			$section->addTextBreak(1);
		}

		header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
		header('Content-Disposition: attachment;filename="Акт приймання здавання.docx"');
		header('Cache-Control: max-age=0');

		// Saving the document as OOXML file...
		$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
		$objWriter->save('php://output');
	}
}
