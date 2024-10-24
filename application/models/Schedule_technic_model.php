<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Schedule_technic_Model extends CI_Model
{
	public function add_data_batch($data)
	{
		$query = $this->db->insert_batch('schedules_technics', $data);
		return $query;
	}

	public function insert($data)
	{
		$this->db->insert('schedules_technics', $data);
		return $this->db->insert_id();
	}

	public function update_for_schedule_id($data, $schedule_id)
	{
		$this->db->set($data);
		$this->db->where('schedule_id', $schedule_id);
		$query = $this->db->update('schedules_technics');
		return $query;
	}

	public function get_technics_for_schedule_id($schedule_id)
	{
		// $this->db->select('(SELECT `technics`.`name` FROM `technics` WHERE technics.id = schedules_technics.technic_id ORDER BY `technics`.`name` ASC) as name');
		// $this->db->select('(SELECT `technics`.`unit` FROM `technics` WHERE technics.id = schedules_technics.technic_id) as unit');
		$this->db->select('schedules_technics.*');
		$this->db->select('technics.name');
		$this->db->select('technics.unit');
		$this->db->where('schedules_technics.technic_id = technics.id');
		$this->db->where('schedules_technics.schedule_id', $schedule_id);
		$this->db->where('schedules_technics.year_service', (date('Y') + 1));
		$this->db->from('schedules_technics, technics');
		$this->db->order_by('schedules_technics.is_extra', 'DESC');
		$this->db->order_by('technics.name', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_count_rows_next_year($year_service)
	{
		$this->db->select('COUNT(`id`) as count');
		$this->db->where('year_service', $year_service);
		$this->db->from('schedules_technics');
		$query = $this->db->get();
		return $query->row('count');
	}

	public function get_is_technic($schedule_id, $technic_id, $year_service)
	{
		$this->db->select('schedules_technics.*');
		$this->db->where('schedules_technics.schedule_id', $schedule_id);
		$this->db->where('schedules_technics.technic_id', $technic_id);
		$this->db->where('schedules_technics.year_service', $year_service);
		$this->db->from('schedules_technics');
		$query = $this->db->get();
		return $query->row();
	}

	public function delete_technics_for_schedule($schedule_id, $year_service)
	{
		$this->db->where('is_extra', 0);
		$this->db->where('schedule_id', $schedule_id);
		$this->db->where('year_service', $year_service);
		$query = $this->db->delete('schedules_technics');
		return $query;
	}

	public function delete_for_schedule_id_and_year($schedule_id, $year_service)
	{
		$this->db->where('schedule_id', $schedule_id);
		$this->db->where('year_service', $year_service);
		$this->db->where('is_extra', 0);
		$query = $this->db->delete('schedules_technics');
		return $query;
	}

	public function delete_technic($schedule_id, $technic_id, $year_service)
	{
		$this->db->where('schedule_id', $schedule_id);
		$this->db->where('technic_id', $technic_id);
		$this->db->where('year_service', $year_service);
		$query = $this->db->delete('schedules_technics');
		return $query;
	}

	public function change_quantity($field, $value, $schedule_id, $technic_id, $year_service)
	{
		$this->db->set('is_extra', 1);
		$this->db->set($field, $value === '' ? 0 : $value);
		$this->db->set('updated_by', $this->session->user->id);
		$this->db->set('updated_at', date('Y-m-d H:i:s'));
		$this->db->where('schedule_id', $schedule_id);
		$this->db->where('technic_id', $technic_id);
		$this->db->where('year_service', $year_service);
		$query = $this->db->update('schedules_technics');
		return $query;
	}

	public function get_technics_for_defect_list($complete_renovation_object_id, $type_service_id)
	{
		$this->db->select('schedules_technics.*');
		$this->db->select('technics.name');
		$this->db->select('technics.unit');
		$this->db->select('technics.r3_id as r3');
		$this->db->select('technics_prices.price');
		$this->db->select('equipments.name as equipment');
		$this->db->select('(voltage_class.voltage / 1000) as voltage');
		$this->db->where('schedules_technics.technic_id = technics.id');
		$this->db->where('schedules_technics.schedule_id = schedules.id');
		$this->db->select('schedules.month');
		$this->db->where('schedules.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('specific_renovation_objects.voltage_class_id = voltage_class.id');
		$this->db->where('technics.id = technics_prices.technic_id');
		$this->db->where('schedules_technics.year_service', (date('Y') + 1));
		if ($type_service_id) {
			$this->db->where('schedules.type_service_id', $type_service_id);
		}
		$this->db->where('specific_renovation_objects.complete_renovation_object_id', $complete_renovation_object_id);
		$this->db->from('schedules_technics, technics, schedules, specific_renovation_objects, equipments, voltage_class, technics_prices');
		$this->db->order_by('schedules_technics.is_extra', 'DESC');
		$this->db->order_by('technics.name', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_summa($complete_renovation_object_id, $type_service_id, $month = NULL, $quarter = NULL)
	{
		$this->db->select('SUM(`price` * `quantity`) as summa');
		$this->db->where('schedules.id = schedules_technics.schedule_id');
		$this->db->where('specific_renovation_objects.id = schedules.specific_renovation_object_id');
		$this->db->where('schedules_technics.technic_id = technics_prices.technic_id');
		$this->db->where('schedules_technics.year_service', (date('Y') + 1));
		$this->db->where('technics_prices.price_year', (date('Y') + 1));
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
		$this->db->from('schedules_technics, schedules, specific_renovation_objects, technics_prices');
		$query = $this->db->get();
		return $query->row('summa');
	}

	public function truncate()
	{
		$this->db->truncate('schedules_technics');
	}

	public function get_schedule_id()
	{
		$this->db->select('schedule_id as id');
		$this->db->where('year_service', (date('Y') + 1));
		$this->db->from('schedules_technics');
		$query = $this->db->get();
		return $query->result();
	}
}
