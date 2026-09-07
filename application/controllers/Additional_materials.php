<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Additional_materials extends CI_Controller
{
	private const PAGE = 'additional_materials';

	public function __construct()
	{
		parent::__construct();

		$this->checkAuthentication();

		$this->load->model('schedule_material_model');
	}

	/**
	 * Головна сторінка додаткових матеріалів.
	 */
	public function index()
	{
		$data = [
			'title'              => 'Додаткові матеріали',
			'content'            => self::PAGE . '/index',
			'page'               => self::PAGE,
			'page_js'            => self::PAGE,
			'datatables'         => TRUE,
			'datatables_button'  => TRUE,
			'title_heading'      => 'Додаткові матеріали',
			'title_heading_card' => 'Додаткові матеріали',
			'materials'          => $this->getGroupedMaterials(),
		];

		$this->load->view('layout', $data);
	}

	/**
	 * Перевірка авторизації користувача.
	 */
	private function checkAuthentication(): void
	{
		if (!$this->session->user) {
			redirect('authentication/signin');
		}
	}

	/**
	 * Отримання та групування додаткових матеріалів.
	 *
	 * Матеріали групуються за:
	 * - станцією;
	 * - диспетчерським найменуванням;
	 * - типом послуги.
	 *
	 * @return array
	 */

	private function getGroupedMaterials(): array
	{
		$additionalMaterials =
			$this->schedule_material_model->get_additional_materials_for_next_year();

		$materials = [];

		foreach ($additionalMaterials as $material) {
			$groupKey = $this->buildGroupKey(
				$material->stantion,
				$material->disp,
				$material->type_service
			);

			if (!isset($materials[$groupKey])) {
				$materials[$groupKey] = [
					'stantion'     => $material->stantion,
					'disp'         => $material->disp,
					'type_service' => $material->type_service,
					'materials'    => [],
				];
			}

			$materials[$groupKey]['materials'][] = [
				'name'     => $material->name,
				'quantity' => $material->quantity,
				'unit'     => $material->unit,
				'r3'       => $material->r3,
			];
		}

		return $materials;
	}

	/**
	 * Формування унікального ключа групи.
	 */
	private function buildGroupKey(
		string $stantion,
		string $disp,
		string $typeService
	): string {
		return implode('_', [
			$stantion,
			$disp,
			$typeService,
		]);
	}
}

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

// defined('BASEPATH') or exit('No direct script access allowed');

// class Additional_materials extends CI_Controller
// {
// 	private const PAGE = 'additional_materials';

// 	public function __construct()
// 	{
// 		parent::__construct();

// 		$this->checkAuthentication();

// 		$this->load->model('schedule_material_model');
// 	}

// 	public function index()
// 	{
// 		$data = [];
// 		$data['title'] = 'Додаткові матеріали';
// 		$data['content'] = 'additional_materials/index';
// 		$data['page'] = 'additional_materials';
// 		$data['page_js'] = 'additional_materials';
// 		$data['datatables'] = TRUE;
// 		$data['datatables_button'] = TRUE;
// 		$data['title_heading'] = 'Додаткові матеріали';
// 		$data['title_heading_card'] = 'Додаткові матеріали';
// 		$additional_materials = $this->schedule_material_model->get_additional_materials_for_next_year();
// 		$materials = [];

// 		foreach ($additional_materials as $key => $item) {
// 			$group = $item->stantion . '_' . $item->disp . '_' . $item->type_service;
// 			$materials[$group]['stantion'] = $item->stantion;
// 			$materials[$group]['disp'] = $item->disp;
// 			$materials[$group]['type_service'] = $item->type_service;
// 			$materials[$group]['materials']['name'][] = $item->name;
// 			$materials[$group]['materials']['quantity'][] = $item->quantity;
// 			$materials[$group]['materials']['unit'][] = $item->unit;
// 			$materials[$group]['materials']['r3'][] = $item->r3;
// 		}
// 		$data['materials'] = $materials;
// 		$this->load->view('layout', $data);
// 	}

// 	/**
// 	 * Перевірка авторизації користувача.
// 	 */
// 	private function checkAuthentication(): void
// 	{
// 		if (!$this->session->user) {
// 			redirect('authentication/signin');
// 		}
// 	}
// }
