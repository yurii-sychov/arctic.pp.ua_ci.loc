<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Schedule_worker_Model extends CI_Model
{
	public function add_data_batch($data)
	{
		$query = $this->db->insert_batch('schedules_workers', $data);
		return $query;
	}

	public function insert($data)
	{
		$this->db->insert('schedules_workers', $data);
		return $this->db->insert_id();
	}

	public function update_for_schedule_id($data, $schedule_id)
	{
		$this->db->set($data);
		$this->db->where('schedule_id', $schedule_id);
		$query = $this->db->update('schedules_workers');
		return $query;
	}

	public function get_workers_for_schedule_id($schedule_id)
	{
		// $this->db->select('(SELECT `workers`.`name` FROM `workers` WHERE workers.id = schedules_workers.worker_id ORDER BY `workers`.`name` ASC) as name');
		// $this->db->select('(SELECT `workers`.`unit` FROM `workers` WHERE workers.id = schedules_workers.worker_id) as unit');
		$this->db->select('schedules_workers.*');
		$this->db->select('workers.name');
		$this->db->select('workers.unit');
		$this->db->where('schedules_workers.worker_id = workers.id');
		$this->db->where('schedules_workers.schedule_id', $schedule_id);
		$this->db->where('schedules_workers.year_service', (date('Y') + 1));
		$this->db->from('schedules_workers, workers');
		$this->db->order_by('schedules_workers.is_extra', 'DESC');
		$this->db->order_by('workers.name', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_count_rows_next_year($year_service)
	{
		$this->db->select('COUNT(`id`) as count');
		$this->db->where('year_service', $year_service);
		$this->db->from('schedules_workers');
		$query = $this->db->get();
		return $query->row('count');
	}

	public function get_is_worker($schedule_id, $worker_id, $year_service)
	{
		$this->db->select('schedules_workers.*');
		$this->db->where('schedules_workers.schedule_id', $schedule_id);
		$this->db->where('schedules_workers.worker_id', $worker_id);
		$this->db->where('schedules_workers.year_service', $year_service);
		$this->db->from('schedules_workers');
		$query = $this->db->get();
		return $query->row();
	}

	public function delete_workers_for_schedule($schedule_id, $year_service)
	{
		$this->db->where('is_extra', 0);
		$this->db->where('schedule_id', $schedule_id);
		$this->db->where('year_service', $year_service);
		$query = $this->db->delete('schedules_workers');
		return $query;
	}

	public function delete_for_schedule_id_and_year($schedule_id, $year_service)
	{
		$this->db->where('schedule_id', $schedule_id);
		$this->db->where('year_service', $year_service);
		$this->db->where('is_extra', 0);
		$query = $this->db->delete('schedules_workers');
		return $query;
	}

	public function delete_worker($schedule_id, $worker_id, $year_service)
	{
		$this->db->where('schedule_id', $schedule_id);
		$this->db->where('worker_id', $worker_id);
		$this->db->where('year_service', $year_service);
		$query = $this->db->delete('schedules_workers');
		return $query;
	}

	public function change_quantity($field, $value, $schedule_id, $worker_id, $year_service)
	{
		$this->db->set('is_extra', 1);
		$this->db->set($field, $value === '' ? 0 : $value);
		$this->db->set('updated_by', $this->session->user->id);
		$this->db->set('updated_at', date('Y-m-d H:i:s'));
		$this->db->where('schedule_id', $schedule_id);
		$this->db->where('worker_id', $worker_id);
		$this->db->where('year_service', $year_service);
		$query = $this->db->update('schedules_workers');
		return $query;
	}

	public function get_workers_for_defect_list($complete_renovation_object_id, $type_service_id)
	{
		$this->db->select('schedules_workers.*');
		$this->db->select('schedules.month');
		$this->db->select('schedules.type_service_id');
		$this->db->select('workers.name');
		$this->db->select('workers.unit');
		$this->db->select('specific_renovation_objects.name as disp');
		// $this->db->select('workers_prices.price');
		$this->db->select('equipments.name as equipment');
		$this->db->select('(voltage_class.voltage / 1000) as voltage');

// 		$this->db->select('ciphers.cipher');
        $this->db->select('(SELECT `cipher` FROM `ciphers` WHERE `id` = `schedules`.`cipher_id`) as cipher');

		// $this->db->select('passports.short_type as type');
		$this->db->select('(SELECT GROUP_CONCAT(`type` SEPARATOR "; ") FROM `passports` WHERE `specific_renovation_object_id` = `specific_renovation_objects`.`id`) as type');
		$this->db->select('(SELECT GROUP_CONCAT(`short_type` SEPARATOR "; ") FROM `passports` WHERE `specific_renovation_object_id` = `specific_renovation_objects`.`id`) as short_type');
		$this->db->select('(SELECT COUNT(`id`) FROM `passports` WHERE `specific_renovation_object_id` = `specific_renovation_objects`.`id`) as quantity_equipment');
// 		$this->db->where('schedules.cipher_id = ciphers.id');
		$this->db->where('schedules_workers.worker_id = workers.id');
		$this->db->where('schedules_workers.schedule_id = schedules.id');
		$this->db->where('schedules_years.schedule_id = schedules_workers.schedule_id');
		$this->db->where('schedules.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		// $this->db->where('specific_renovation_objects.id = passports.specific_renovation_object_id');
		// $this->db->where('specific_renovation_objects.id = schedules.specific_renovation_object_id');
		// $this->db->where('schedules.id = schedules_worker.schedule_id');
		// $this->db->where('schedules.specific_renovation_object_id = passports.specific_renovation_object_id');
		$this->db->where('specific_renovation_objects.voltage_class_id = voltage_class.id');
		// $this->db->where('workers.id = workers_prices.worker_id');
		$this->db->where('schedules_workers.year_service', (date('Y') + 1));
		if ($type_service_id) {
			$this->db->where('schedules.type_service_id', $type_service_id);
		}
		$this->db->where('specific_renovation_objects.complete_renovation_object_id', $complete_renovation_object_id);
		// $this->db->from('schedules_workers, workers, schedules, specific_renovation_objects, equipments, voltage_class, workers_prices, ciphers, passports');
		$this->db->from('schedules_workers, schedules_years, schedules, workers, specific_renovation_objects, equipments, voltage_class');
		if ($type_service_id == NULL) {
			$this->db->order_by('schedules.type_service_id', 'ASC');
		}
		$this->db->order_by('equipments.name', 'ASC');
		$this->db->order_by('specific_renovation_objects.name', 'ASC');

		$query = $this->db->get();
// 		echo count($query->result());
// 		exit;
		return $query->result();
	}

	public function get_summa($complete_renovation_object_id, $type_service_id, $month = NULL, $quarter = NULL)
	{
		$this->db->select('SUM(`price` * `quantity`) as summa');
		$this->db->where('schedules.id = schedules_workers.schedule_id');
		$this->db->where('specific_renovation_objects.id = schedules.specific_renovation_object_id');
		$this->db->where('schedules_workers.worker_id = workers_prices.worker_id');
		$this->db->where('schedules_workers.year_service', (date('Y') + 1));
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
		$this->db->from('schedules_workers, schedules, specific_renovation_objects, workers_prices');
		$query = $this->db->get();
		return $query->row('summa');
	}

	public function truncate()
	{
		$this->db->truncate('schedules_workers');
	}

	public function get_schedule_id()
	{
		$this->db->select('schedule_id as id');
		$this->db->where('year_service', (date('Y') + 1));
		$this->db->from('schedules_workers');
		$query = $this->db->get();
		return $query->result();
	}
}