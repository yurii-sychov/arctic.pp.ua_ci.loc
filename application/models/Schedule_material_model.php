<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Schedule_material_Model extends CI_Model
{
	public function add_data_batch($data)
	{
		$query = $this->db->insert_batch('schedules_materials', $data);
		return $query;
	}

	public function insert($data)
	{
		$this->db->insert('schedules_materials', $data);
		return $this->db->insert_id();
	}

	public function insert_batch($data)
	{
		$this->db->insert_batch('schedules_materials', $data);
		return true;
	}

	public function update_for_schedule_id($data, $schedule_id)
	{
		$this->db->set($data);
		$this->db->where('schedule_id', $schedule_id);
		$query = $this->db->update('schedules_materials');
		return $query;
	}

	public function get_materials_for_schedule_id($schedule_id)
	{
		// $this->db->select('(SELECT `materials`.`name` FROM `materials` WHERE materials.id = schedules_materials.material_id ORDER BY `materials`.`name` ASC) as name');
		// $this->db->select('(SELECT `materials`.`unit` FROM `materials` WHERE materials.id = schedules_materials.material_id) as unit');
		$this->db->select('schedules_materials.*');
		$this->db->select('materials.name');
		$this->db->select('materials.unit');
		$this->db->where('schedules_materials.material_id = materials.id');
		$this->db->where('schedules_materials.schedule_id', $schedule_id);
		$this->db->where('schedules_materials.year_service', (date('Y') + 1));
		$this->db->from('schedules_materials, materials');
		$this->db->order_by('schedules_materials.is_extra', 'DESC');
		$this->db->order_by('materials.name', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_prices_materials_for_schedule_id($schedule_id)
	{
		$this->db->select('SUM(`materials_prices`.`price` * `schedules_materials`.`quantity`) as `total_price`');
		$this->db->where('schedules_materials.material_id = materials_prices.material_id');
		$this->db->where('schedules_materials.schedule_id', $schedule_id);
		$this->db->where('schedules_materials.year_service', (date('Y') + 1));
		$this->db->from('schedules_materials, materials_prices');
		$query = $this->db->get();
		return $query->row('total_price');
	}

	public function get_count_rows_next_year($year_service)
	{
		$this->db->select('COUNT(`id`) as count');
		$this->db->where('year_service', $year_service);
		$this->db->from('schedules_materials');
		$query = $this->db->get();
		return $query->row('count');
	}

	public function get_is_material($schedule_id, $material_id, $year_service)
	{
		$this->db->select('schedules_materials.*');
		$this->db->where('schedules_materials.schedule_id', $schedule_id);
		$this->db->where('schedules_materials.material_id', $material_id);
		$this->db->where('schedules_materials.year_service', $year_service);
		$this->db->from('schedules_materials');
		$query = $this->db->get();
		return $query->row();
	}

	public function delete_materials_for_schedule($schedule_id, $year_service)
	{
		$this->db->where('is_extra', 0);
		$this->db->where('schedule_id', $schedule_id);
		$this->db->where('year_service', $year_service);
		$query = $this->db->delete('schedules_materials');
		return $query;
	}

	public function delete_for_schedule_id_and_year($schedule_id, $year_service)
	{
		$this->db->where('schedule_id', $schedule_id);
		$this->db->where('year_service', $year_service);
		$this->db->where('is_extra', 0);
		$query = $this->db->delete('schedules_materials');
		return $query;
	}

	public function delete_material($schedule_id, $material_id, $year_service)
	{
		$this->db->where('schedule_id', $schedule_id);
		$this->db->where('material_id', $material_id);
		$this->db->where('year_service', $year_service);
		$query = $this->db->delete('schedules_materials');
		return $query;
	}

	public function change_quantity($field, $value, $schedule_id, $material_id, $year_service)
	{
		$this->db->set('is_extra', 1);
		$this->db->set($field, $value === '' ? 0 : $value);
		$this->db->set('updated_by', $this->session->user->id);
		$this->db->set('updated_at', date('Y-m-d H:i:s'));
		$this->db->where('schedule_id', $schedule_id);
		$this->db->where('material_id', $material_id);
		$this->db->where('year_service', $year_service);
		$query = $this->db->update('schedules_materials');
		return $query;
	}

	public function get_materials_for_defect_list($complete_renovation_object_id, $type_service_id)
	{
		$this->db->select('schedules_materials.*');
		$this->db->select('materials.name');
		$this->db->select('materials.unit');
		$this->db->select('materials.r3_id as r3');
		$this->db->select('materials_prices.price');
		$this->db->select('equipments.name as equipment');
		$this->db->select('(voltage_class.voltage / 1000) as voltage');
		$this->db->select('schedules_years.month_service as month');
		$this->db->select('(SELECT COUNT(`id`) FROM `passports` WHERE `specific_renovation_object_id` = `specific_renovation_objects`.`id`) AS `amount`');

		$this->db->where('schedules_years.schedule_id = schedules_materials.schedule_id');
		$this->db->where('schedules_materials.material_id = materials.id');
		$this->db->where('schedules_materials.schedule_id = schedules.id');
		$this->db->where('schedules_years.schedule_id = schedules.id');
		$this->db->where('schedules.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('specific_renovation_objects.voltage_class_id = voltage_class.id');
		$this->db->where('materials.id = materials_prices.material_id');
		$this->db->where('schedules_materials.year_service', (date('Y') + 1));
		if ($type_service_id) {
			$this->db->where('schedules.type_service_id', $type_service_id);
		}
		$this->db->where('specific_renovation_objects.complete_renovation_object_id', $complete_renovation_object_id);
		$this->db->from('schedules_materials, materials, schedules, specific_renovation_objects, equipments, voltage_class, materials_prices, schedules_years');
		$this->db->order_by('schedules_materials.is_extra', 'DESC');
		$this->db->order_by('materials.name', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_materials_for_next_year_group()
	{
		$this->db->select('specific_renovation_objects.subdivision_id');
		$this->db->select('materials.name');
		$this->db->select('materials.r3_id as r3');
		$this->db->select('materials.unit');
		$this->db->select('(CASE WHEN `schedules`.`type_service_id` = 1 THEN \'КР\' WHEN `schedules`.`type_service_id` = 2 THEN \'ПР\' WHEN `schedules`.`type_service_id` = 3 THEN \'ТО\' END) as type_service');
		$this->db->select('(SUM(IF(specific_renovation_objects.subdivision_id = 1, quantity, 0))) as stantion_150');
		$this->db->select('(SUM(IF(specific_renovation_objects.subdivision_id > 1, quantity, 0))) as stantion_35');
		$this->db->where('schedules_materials.year_service', (date('Y') + 1));
		$this->db->where('schedules_materials.material_id = materials.id');
		$this->db->where('schedules_materials.schedule_id = schedules.id');
		$this->db->where('schedules.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('schedules_materials.schedule_id = schedules_years.schedule_id');
		$this->db->group_by('schedules_materials.material_id');
		$this->db->group_by('schedules.type_service_id');
		$this->db->order_by('materials.name', 'ASC');
		$this->db->order_by('schedules.type_service_id', 'ASC');
		$this->db->order_by('specific_renovation_objects.subdivision_id', 'ASC');
		$this->db->from('schedules_materials, materials, schedules, specific_renovation_objects, schedules_years');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_materials_for_specific_add($material_id)
	{
		$this->db->select('schedules_materials.schedule_id');
		$this->db->select('schedules_materials.quantity as plan_quantity');
		$this->db->select('schedules_materials.year_service as year_service');
		$this->db->select('complete_renovation_objects.name as stantion');
		$this->db->select('specific_renovation_objects.name as disp');
		$this->db->select('materials.unit as unit');
		$this->db->where('schedules_materials.year_service', (date('Y') + 1));
		$this->db->where('schedules_materials.schedule_id = schedules.id');
		$this->db->where('schedules.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('specific_renovation_objects.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('schedules_materials.schedule_id = schedules_years.schedule_id');
		$this->db->where('materials.id = schedules_materials.material_id');
		$this->db->where('schedules_materials.material_id', $material_id);
		$this->db->order_by('stantion', 'ASC');
		$this->db->order_by('disp', 'ASC');
		$this->db->from('schedules_materials, schedules, specific_renovation_objects, complete_renovation_objects, schedules_years, materials');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_summa($complete_renovation_object_id, $type_service_id, $month = NULL, $quarter = NULL)
	{
		$this->db->select('SUM(`price` * `quantity`) as summa');
		$this->db->where('schedules.id = schedules_materials.schedule_id');
		$this->db->where('specific_renovation_objects.id = schedules.specific_renovation_object_id');
		$this->db->where('schedules_materials.material_id = materials_prices.material_id');
		$this->db->where('schedules_materials.year_service', (date('Y') + 1));
		if ($quarter == 1) {
			$this->db->where('schedules.month BETWEEN 1 AND 3');
		}
		if ($quarter == 2) {
			$this->db->where('schedules.month BETWEEN 4 AND 6');
		}
		if ($quarter == 3) {
			$this->db->where('schedules.month BETWEEN 7 AND 9');
		}
		if ($quarter == 4) {
			$this->db->where('schedules.month BETWEEN 10 AND 12');
		}
		if ($month) {
			$this->db->where('schedules.month', $month);
		}
		if ($type_service_id) {
			$this->db->where('schedules.type_service_id', $type_service_id);
		}
		$this->db->where('specific_renovation_objects.complete_renovation_object_id', $complete_renovation_object_id);
		$this->db->from('schedules_materials, schedules, specific_renovation_objects, materials_prices');
		$query = $this->db->get();
		return $query->row('summa');
	}

	// public function get_summa_quarter_1($complete_renovation_object_id, $type_service_id)
	// {
	// 	$this->db->select('SUM(`price` * `quantity`) as summa');
	// 	$this->db->where('schedules.id = schedules_materials.schedule_id');
	// 	$this->db->where('specific_renovation_objects.id = schedules.specific_renovation_object_id');
	// 	$this->db->where('schedules_materials.material_id = materials_prices.material_id');
	// 	$this->db->where('schedules_materials.year_service', (date('Y') + 1));
	// 	if ($type_service_id) {
	// 		$this->db->where('schedules.type_service_id', $type_service_id);
	// 	}
	// 	$this->db->where('specific_renovation_objects.complete_renovation_object_id', $complete_renovation_object_id);
	// 	$this->db->where('schedules');
	// 	$this->db->from('schedules_material, schedules, specific_renovation_objects, materials_prices');
	// 	$query = $this->db->get();
	// 	return $query->row('summa');
	// }

	public function truncate()
	{
		$this->db->truncate('schedules_materials');
	}

	public function get_schedule_id()
	{
		$this->db->select('schedule_id as id');
		$this->db->where('year_service', (date('Y') + 1));
		$this->db->from('schedules_materials');
		$query = $this->db->get();
		return $query->result();
	}
}
