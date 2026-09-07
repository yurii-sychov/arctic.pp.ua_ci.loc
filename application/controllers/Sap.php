<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpWord\Style\Language;

use Shuchkin\SimpleXLSXGen;

class Sap extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		if (!$this->session->user) {
			redirect('authentication/signin');
		}
	}

	public function index($napruga = NULL)
	{
		include('./uploads/packages/simplehtmldom/simple_html_dom.php');

		$segment = (int)$this->uri->segment(3, 0);
		$year = date('Y') - 1;

		// Визначення директорії
		$dirs = [
			150 => "150/",
			35  => "35/",
			0   => ""
		];

		$dir = "./uploads/packages/simplehtmldom/sapr3/{$year}/" . ($dirs[$segment] ?? '');

		// Отримання списку файлів
		$files = [];
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

		foreach ($iterator as $file) {
			if (!$file->isDir()) {
				$files[] = $file->getPathname();
			}
		}

		$materials = [];

		foreach ($files as $file) {
			$html = str_get_html(
				iconv('Windows-1251', 'UTF-8//IGNORE', file_get_contents($file))
			);

			if (!$html) continue;

			// Один раз дістаємо вузли
			$node_l0014002 = $html->find('#l0014002', 0);
			$node_l0012002 = $html->find('#l0012002', 0);
			$node_l0010002 = $html->find('#l0010002', 0);

			$text_4002 = $node_l0014002->plaintext ?? '';
			$text_2002 = $node_l0012002->plaintext ?? '';
			$text_0002 = $node_l0010002->plaintext ?? '';

			// Регулярки (менше викликів preg_match)
			preg_match('/Поточний ремонт|Капітальний ремонт|Неплановий ремонт/u', $text_4002, $m1);
			preg_match('/ПС\s+(?:150\/)?35\/6-10кВ/u', $text_4002, $m2);
			preg_match('/\((\d+)\)/u', $text_2002, $m3);
			preg_match('/Тип\s+пересування\s+(\d+)/u', $text_0002, $m4);

			$vid_remontu = $m1[0] ?? NULL;
			$class_voltage = $m2[0] ?? NULL;
			$transaction_number = $m3[1] ?? NULL;
			$type_move = $m4[1] ?? NULL;

			// Перша таблиця (об'єкт)
			$firstTable = $html->find('table', 0);
			$name_object = $number_object = NULL;

			if ($firstTable) {
				$rows = $firstTable->find('tr');
				$name_object = $rows[1]->find('td', 0)->plaintext ?? NULL;
				$number_object = $rows[2]->find('td', 0)->plaintext ?? NULL;
			}

			// Обробка таблиць матеріалів
			foreach ($html->find('table') as $table) {

				foreach ($table->find('tr td') as $td) {
					if (mb_stripos($td->plaintext, 'Код роботи') !== FALSE || mb_stripos($td->plaintext, 'Всього матеріальних витрат') !== FALSE) {

						foreach ($table->find('tr') as $row) {
							$cols = $row->find('td');

							if (count($cols) < 7) continue;

							$title = trim($cols[0]->plaintext);

							if ($title === 'Код роботи' || $title === 'Всього матеріальних витрат') {
								continue;
							}

							$qty = $this->toFloat($cols[5]->plaintext ?? 0);
							$price = $this->toFloat($cols[6]->plaintext ?? 0);
							$sum = $this->toFloat($cols[7]->plaintext ?? 0);
							// $sum = $qty * $price;

							$materials[] = [
								'Найменування об\'єкта' => $name_object,
								'Клас напруги' => $class_voltage,
								'Номер об\'єкта' => '<center>' . $number_object . '</center>',
								'Вид ремонту' => $vid_remontu,
								'Тип пересування' => '<center>' . $type_move . '</center>',
								'Номер проводки' => '<center>' . $transaction_number . '</center>',
								'Кoд мат-лу' => '<center>' . trim($cols[1]->plaintext ?? '') . '</center>',
								'Назва матеріалу' => trim($cols[2]->plaintext ?? ''),
								'ОВ' => '<center>' . trim($cols[4]->plaintext ?? '') . '</center>',
								'Кількість' => '<center>' . number_format($qty, 3, '.', '') . '</center>',
								'Ціна' => '<center>' . number_format($price, 2, '.', '') . '</center>',
								'Сума' => '<center>' . number_format($sum, 2, '.', '') . '</center>',
							];
						}
						break;
					}
				}
			}

			$html->clear();
			unset($html);
		}

		// Заголовок
		array_unshift($materials, [
			'Найменування об\'єкта' => '<center><b>Найменування об\'єкта</b></center>',
			'Клас напруги' => '<center><b>Клас напруги</b></center>',
			'Номер об\'єкта' => '<center><b>Номер об\'єкта</b></center>',
			'Вид ремонту' => '<center><b>Вид ремонту</b></center>',
			'Тип пересування' => '<center><b>Тип пересування</b></center>',
			'Номер проводки' => '<center><b>Номер проводки</b></center>',
			'Кoд мат-лу' => '<center><b>Кoд мат-лу</b></center>',
			'Назва матеріалу' => '<center><b>Назва матеріалу</b></center>',
			'ОВ' => '<center><b>ОВ</b></center>',
			'Кількість' => '<center><b>Кількість</b></center>',
			'Ціна' => '<center><b>Ціна</b></center>',
			'Сума' => '<center><b>Сума</b></center>',
		]);

		// Назва файлу
		$reports = [
			150 => 'materials_150.xlsx',
			35  => 'materials_35.xlsx',
			0   => 'materials.xlsx'
		];

		$report = $reports[$segment] ?? 'materials.xlsx';

		Shuchkin\SimpleXLSXGen::fromArray($materials)->freezePanes('A2')->downloadAs($report);
		exit;
	}

	// --- Helper ---
	private function toFloat($value)
	{
		if (!$value) return 0;
		return (float)str_replace(',', '.', str_replace('.', '', $value));
	}
}
