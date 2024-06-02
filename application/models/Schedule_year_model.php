<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Schedule_year_Model extends CI_Model
{
	public function insert($data)
	{
		$this->db->insert('schedules_years', $data);
		return $this->db->insert_id();
	}

	public function delete_for_schedule_id_and_year($schedule_id, $year_service)
	{
		$this->db->where('schedule_id', $schedule_id);
		$this->db->where('year_service', $year_service);
		$query = $this->db->delete('schedules_years');
		return $query;
	}

	public function change_value($field, $value, $id)
	{
		$this->db->set($field, $value);
		$this->db->set('updated_by', $this->session->user->id);
		$this->db->set('updated_at', date('Y-m-d H:i:s'));
		$this->db->where('schedule_id', $id);
		$query = $this->db->update('schedules_years');
		return $query;
	}

	public function change_date_service_actual($field, $value, $schedule_id, $year_service, $is_contract_method)
	{
		$this->db->set($field, $value);
		$this->db->set('updated_by', $this->session->user->id);
		$this->db->set('updated_at', date('Y-m-d H:i:s'));
		$this->db->where('schedule_id', $schedule_id);
		$this->db->where('year_service', $year_service);
		$this->db->where('is_contract_method', $is_contract_method);
		$query = $this->db->update('schedules_years');
		return $query;
	}

	public function truncate()
	{
		$this->db->truncate('schedules_years');
	}

	public function get_data($complete_renovation_object_id)
	{
		$this->db->select('schedules_years.*');
		$this->db->select('(CASE WHEN `schedules`.`type_service_id` = 1 THEN "КР" WHEN `schedules`.`type_service_id` = 2 THEN "ПР" WHEN `schedules`.`type_service_id` = 3 THEN "ТО" END) as type_service');
		$this->db->select('specific_renovation_objects.name as disp');
		$this->db->select('equipments.name as equipment');
		$this->db->where('schedules_years.schedule_id = schedules.id');
		$this->db->where('schedules.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('schedules_years.year_service', date('Y'));
		$this->db->where('specific_renovation_objects.complete_renovation_object_id', $complete_renovation_object_id);
		$this->db->from('schedules_years, schedules, specific_renovation_objects, equipments');
		$query = $this->db->get();
		return $query->result();
	}


	public function get_data_for_schedule_year($complete_renovation_object_id, $type_service_id)
	{
		$this->db->select('schedules_years.*');
		$this->db->select('schedules.type_service_id');
		$this->db->select('schedules.is_contract_method');
		$this->db->select('(CASE WHEN `schedules`.`type_service_id` = 1 THEN "КР" WHEN `schedules`.`type_service_id` = 2 THEN "ПР" WHEN `schedules`.`type_service_id` = 3 THEN "ТО" END) as type_service');
		$this->db->select('specific_renovation_objects.name as disp');
		$this->db->select('specific_renovation_objects.year_commissioning as year_commissioning');
		// $this->db->select('materials.name');
		// $this->db->select('materials.unit');
		// $this->db->select('materials.r3_id as r3');
		// $this->db->select('materials_prices.price');
		$this->db->select('equipments.name as equipment');
		$this->db->select('(voltage_class.voltage / 1000) as voltage');
		$this->db->select('(SELECT SUM(`quantity`) FROM `schedules_workers` WHERE `schedule_id` = `schedules`.`id` AND `year_service` = ' . (date('Y') + 1) . ') AS `workers`');
		$this->db->select('(SELECT SUM(`materials_prices`.`price` * `schedules_materials`.`quantity`) / 1000 FROM `schedules_materials`, `materials_prices` WHERE `schedules_materials`.`material_id` = `materials_prices`.`material_id` AND `schedule_id` = `schedules`.`id` AND `year_service` = ' . (date('Y') + 1) . ') AS `materials`');
		// $this->db->select('schedules_years.month_service as month');
		// $this->db->where('schedules_materials.material_id = materials.id');
		// $this->db->where('schedules_materials.schedule_id = schedules.id');
		$this->db->where('schedules_years.schedule_id = schedules.id');
		$this->db->where('schedules.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('specific_renovation_objects.voltage_class_id = voltage_class.id');
		// $this->db->where('materials.id = materials_prices.material_id');
		$this->db->where('schedules_years.year_service', (date('Y') + 1));
		if ($type_service_id) {
			$this->db->where('schedules.type_service_id', $type_service_id);
		}
		$this->db->where('specific_renovation_objects.complete_renovation_object_id', $complete_renovation_object_id);
		$this->db->from('schedules_years, schedules, specific_renovation_objects, equipments, voltage_class');
		// $this->db->order_by('schedules_materials.is_extra', 'DESC');
		// $this->db->order_by('materials.name', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_prices_materials_for_next_year()
	{
		$this->db->select('(CASE WHEN `schedules`.`is_contract_method` = 1 THEN \'ПС\' WHEN `schedules`.`is_contract_method` = 0 THEN \'ГС\' END) as repair_method');
		$this->db->select('(CASE WHEN `schedules`.`type_service_id` = 1 THEN \'КР\' WHEN `schedules`.`type_service_id` = 2 THEN \'ПР\' WHEN `schedules`.`type_service_id` = 3 THEN \'ТО\' END) as repair_type');
		$this->db->select('complete_renovation_objects.name as stantion');
		$this->db->select('specific_renovation_objects.name as disp');
		$this->db->select('equipments.name as oborud');
		$this->db->select('(IF(`complete_renovation_objects`.`id` > 24, \'35\', \'150\')) as class_voltage');
		$this->db->select('schedules_years.month_service as month');
		$this->db->select('(SELECT `note` FROM `schedules_notes` WHERE `schedule_id` =  schedules_years.schedule_id) AS `note_for_contract`');
		$this->db->select('schedules_materials.quantity as quantity');
		$this->db->select('materials_prices.price as price');
		$this->db->select('(`schedules_materials`.`quantity` * `materials_prices`.`price`) as `price_total_no_vat`');
		$this->db->select('complete_renovation_objects.r3_id as inventar_number');

		$this->db->where('schedules_years.year_service', (date('Y') + 1));
		$this->db->where('materials_prices.price_year', (date('Y') + 1));
		$this->db->where('schedules.type_service_id != 3');
		$this->db->where('schedules_years.schedule_id = schedules_materials.schedule_id');
		$this->db->where('schedules_years.schedule_id = schedules.id');
		$this->db->where('schedules.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('specific_renovation_objects.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('schedules_materials.material_id = materials_prices.material_id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');

		$this->db->order_by('repair_method', 'ASC');
		$this->db->order_by('repair_type', 'ASC');
		$this->db->order_by('class_voltage', 'ASC');
		$this->db->order_by('stantion', 'ASC');

		$this->db->from('schedules_years, schedules_materials, schedules, specific_renovation_objects, complete_renovation_objects, materials_prices, equipments');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_data_for_simple_year($stantion_id, $current_year)
	{
		$this->db->select('equipments.name as oborud');
		$this->db->select('(voltage_class.voltage / 1000) as voltage');
		$this->db->select('specific_renovation_objects.name as disp');
		$this->db->select('(SELECT GROUP_CONCAT(`type`) from `passports` WHERE `passports`.`specific_renovation_object_id` = `specific_renovation_objects`.`id`) as `type`');
		$this->db->select('(CASE WHEN `schedules`.`type_service_id` = 1 THEN "КР" WHEN `schedules`.`type_service_id` = 2 THEN "ПР" WHEN `schedules`.`type_service_id` = 3 THEN "ТО" END) as type_service');
		$this->db->select('schedules_years.month_service as month');
		$this->db->select('schedules_years.date_service_actual as date_service_actual');

		$this->db->where('schedules_years.year_service', $current_year ? date('Y') : (date('Y') + 1));
		$this->db->where('specific_renovation_objects.complete_renovation_object_id', $stantion_id);
		$this->db->where('schedules_years.schedule_id = schedules.id');
		$this->db->where('schedules.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('specific_renovation_objects.voltage_class_id = voltage_class.id');


		// $this->db->order_by('repair_method', 'ASC');
		$this->db->order_by('voltage', 'ASC');
		$this->db->order_by('type_service', 'ASC');
		// $this->db->order_by('class_voltage', 'ASC');
		$this->db->order_by('disp', 'ASC');

		$this->db->from('schedules_years, schedules, specific_renovation_objects, equipments, voltage_class');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_schedule_id()
	{
		$this->db->select('schedule_id as id');
		$this->db->where('year_service', (date('Y') + 1));
		$this->db->from('schedules_years');
		$query = $this->db->get();
		return $query->result();
	}

	// public function delete($data)
	// {
	// 	$this->db->delete('schedules_years', $data);
	// }
}
