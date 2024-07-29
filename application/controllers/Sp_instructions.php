<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpWord\Style\Language;

class Sp_instructions extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		// if (!$this->session->user) {
		// 	redirect('authentication/signin');
		// }

		// if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer' && $this->session->user->group !== 'master') {
		// 	show_404();
		// }

		// $this->load->model('complete_renovation_object_model');
	}

	public function index()
	{
		$data = [];
		$data['title'] = 'Перелік інструкцій з охорони праці';
		$data['content'] = 'sp_instructions/index';
		$data['page'] = 'sp_instructions';
		$data['page_js'] = 'sp_instructions';
		$data['title_heading'] = 'Перелік інструкцій з охорони праці';
		$data['title_heading_card'] = 'Перелік інструкцій з охорони праці';
		$data['datatables'] = False;
		$data['forms'] = FALSE;

		$id = '1tgHOfDShGaj5VNojtTDsZHcwHPsQEIudXEeq6wGbw3c';
		$gid = 0;
		$range = 'A:D';

		$csv = file_get_contents('https://docs.google.com/spreadsheets/d/' . $id . '/export?format=csv&gid=' . $gid . '&range=' . $range);
		$csv = explode("\r\n", $csv);
		$array = array_map('str_getcsv', $csv);

		$new_array = [];
		foreach ($array as $key => $value) {
			if ($key > 1) {
				array_push($new_array, array_combine($array[0], $value));
			}
		}

		$instructions = [];
		foreach ($new_array as $item) {
			// echo "<pre>";
			// print_r(explode(";", $item['objects']));
			// echo "</pre>";
			$item[]['objects'] = explode(";", $item['objects']);
			$arr['name'] = $item['name'];
			$arr['number'] = $item['number'];
			$arr['date_start'] = $item['date_start'];
			// $arr['period'] = $item['period'];
			$arr['objects'] = explode(";", $item['objects']);
			array_push($instructions, $arr);
		}

		$new_data = [];
		foreach ($instructions as $item) {
			// echo "<pre>";
			// print_r($item);
			// echo "</pre>";
			$min_date_arr = [];
			$max_date_arr = [];
			foreach ($item['objects'] as $val) {
				$arr = [];
				$arr['station'] = $val;
				$arr['name'] = $item['name'];
				$arr['number'] = $item['number'];
				$arr['date_start'] = $item['date_start'];
				$arr['date_start_unix'] = strtotime($item['date_start']);
				// $arr['period'] = $item['period'];
				// $arr['date_end'] = date_format(date_modify(date_create($item['date_start']), $item['period'] . ' year'), 'd.m.Y');
				array_push($new_data, $arr);
			}
		}

		$instructions = [];
		foreach ($new_data as $item) {
			$instructions[$item['station']][] = $item;
		}
		$data['results'] = $instructions;

		$this->act_generation($instructions);

		// echo "<pre>";
		// print_r($new_array[0]);
		// print_r($instructions);
		// var_export($new_array);
		// echo "</pre>";

		// $this->load->view('layout_lte', $data);
	}

	private function act_generation($instructions)
	{
		// Creating the new document...
		$phpWord = new \PhpOffice\PhpWord\PhpWord();

		$phpWord->setDefaultFontName('Times New Roman');
		$phpWord->setDefaultFontSize(10);
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
			// 'pageSizeW' => '8419',
			// 'pageSizeH' => '11906',
			'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0),
			'spaceBefore' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0),
			'spacing' => 120,
			'lineHeight' => 1,
			'breakType' => 'nextPage'
		];

		foreach ($instructions as $station => $instruction) {

			$min_date = date('d.m.Y', min(array_column($instruction, 'date_start_unix')));
			$max_date = date_format(date_modify(date_create($min_date), '3 year'), 'd.m.Y');

			$section = $phpWord->addSection($sectionStyle);

			$section->addText('ЗАТВЕРДЖУЮ', ['bold' => TRUE]);
			$section->addText('Начальник СП', ['bold' => TRUE]);
			$section->addText('__________ Сичов Ю.М.', ['bold' => TRUE]);

			$section->addTextBreak(2);

			$section->addText('ПЕРЕЛІК', ['bold' => TRUE], ['alignment' => 'center']);
			$section->addText('інструкцій з ОП', ['bold' => TRUE], ['alignment' => 'center']);

			$section->addTextBreak(1);
			$section->addText($station . ' і термін їх зберігання', ['bold' => FALSE], ['alignment' => 'center']);

			$section->addTextBreak(1);

			$section->addText('Термін дії встановлений:', ['bold' => FALSE], ['alignment' => 'right']);
			$section->addText('з ' . $min_date . ' р.', ['bold' => FALSE], ['alignment' => 'right']);
			$section->addText('до ' . $max_date . ' р.', ['bold' => FALSE], ['alignment' => 'right']);

			$section->addTextBreak(1);

			$fancyTableCellStyle = ['valign' => 'center'];

			$tableStyle = array(
				'borderColor' => '000000',
				'borderSize'  => 1,
				'cellMargin'  => 10,
				'width' => 50 * 100,
				'unit' => 'pct',
				'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
			);

			$table = $section->addTable($tableStyle);
			$table->addRow();
			$table->addCell(566.929134, $fancyTableCellStyle)->addText('№ п/п', ['bold' => true], ['align' => 'center', 'lineHeight' => 1, 'spaceAfter' => 0]);
			$table->addCell(6436.220473, $fancyTableCellStyle)->addText('Інструкції з охорони праці', ['bold' => true], ['align' => 'center', 'lineHeight' => 1, 'spaceAfter' => 0]);
			$table->addCell(1584.251969, $fancyTableCellStyle)->addText('Термін зберігання', ['bold' => true], ['align' => 'center', 'lineHeight' => 1, 'spaceAfter' => 0]);

			$i = 1;
			foreach ($instruction as $item) {
				$table->addRow();
				$table->addCell(566.929134, $fancyTableCellStyle)->addText($i, ['bold' => false], ['align' => 'center', 'lineHeight' => 1, 'spaceAfter' => 0]);
				$table->addCell(6436.220473, $fancyTableCellStyle)->addText($item['name'] . ' ' . $item['number'], ['bold' => false], ['align' => 'left', 'lineHeight' => 1, 'spaceAfter' => 0]);
				$table->addCell(1584.251969, $fancyTableCellStyle)->addText('постійно', ['bold' => false], ['align' => 'center', 'lineHeight' => 1, 'spaceAfter' => 0]);
				$i++;
			}

			$section->addTextBreak(2);
			$section->addText('Начальник СП                    Сичов Ю.М.', ['bold' => TRUE], ['alignment' => 'center']);
		}


		header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
		header('Content-Disposition: attachment;filename="Перелік інструкцій з ОП.docx"');
		header('Cache-Control: max-age=0');

		// Saving the document as OOXML file...
		$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
		$objWriter->save('php://output');
	}
}
