<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Schedule_Model extends CI_Model
{

	public function get_all()
	{
		$this->db->select('schedules.*');
		$this->db->from('schedules');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_records_filtered($post, $filter)
	{
		$this->db->select('schedules.id');
		$this->db->from('schedules, complete_renovation_objects, specific_renovation_objects, type_services, equipments, voltage_class, users_complete_renovation_objects');
		$this->db->where('specific_renovation_objects.id = schedules.specific_renovation_object_id');
		$this->db->where('type_services.id=schedules.type_service_id');
		$this->db->where('complete_renovation_objects.id = specific_renovation_objects.complete_renovation_object_id');
		$this->db->where('equipments.id=specific_renovation_objects.equipment_id');
		$this->db->where('voltage_class.id=specific_renovation_objects.voltage_class_id');
		$this->db->where('complete_renovation_objects.id = users_complete_renovation_objects.object_id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		if ($filter['complete_renovation_object_id']) {
			$this->db->where('specific_renovation_objects.complete_renovation_object_id', rtrim(ltrim($filter['complete_renovation_object_id'], "^"), "$"));
		}
		if ($filter['equipment_id']) {
			$this->db->where('specific_renovation_objects.equipment_id', rtrim(ltrim($filter['equipment_id'], "^"), "$"));
		}
		if ($filter['insulation_type_id']) {
			$this->db->where('(SELECT `insulation_type_id` FROM `passports` WHERE `specific_renovation_object_id` = `schedules`.`specific_renovation_object_id` LIMIT 1) = ', rtrim(ltrim($filter['insulation_type_id'], "^"), "$"));
		}
		if ($filter['type_service_id']) {
			$this->db->where('schedules.type_service_id', rtrim(ltrim($filter['type_service_id'], "^"), "$"));
		}
		if ($filter['voltage_id']) {
			$this->db->where('specific_renovation_objects.voltage_class_id', rtrim(ltrim($filter['voltage_id'], "^"), "$"));
		}
		if ($filter['status']) {
			$this->db->where('schedules.status', rtrim(ltrim($filter['status'], "^"), "$"));
		}

		if ($post['search']['value']) {
			$this->db->like('specific_renovation_objects.name', $post['search']['value']);
		}

		$this->db->get();
		return $this->db->affected_rows();
	}

	public function get_data_datatables_server_side($post, $filter, $order_dir, $order_field)
	{
		$this->db->select('schedules.*,
		schedules.year_last_service as year_service,
		specific_renovation_objects.complete_renovation_object_id,
		specific_renovation_objects.equipment_id,
		specific_renovation_objects.name as disp,
		specific_renovation_objects.voltage_class_id as voltage_id,
		type_services.name as type_service, type_services.short_name as short_type_service,
		complete_renovation_objects.name as stantion,
		equipments.name as equipment,
		concat(equipments.name, " ", (ROUND(voltage_class.voltage/1000, 1)), " кВ") as equipment_with_voltage,
		concat( ROUND((voltage_class.voltage/1000), 1), " кВ") as voltage,
		specific_renovation_objects.year_repair_invest as year_repair_invest,
		specific_renovation_objects.year_plan_repair_invest as year_plan_repair_invest,
		specific_renovation_objects.year_commissioning as year_commissioning,
		(SELECT `insulation_type_id` FROM `passports` WHERE `specific_renovation_object_id` = `schedules`.`specific_renovation_object_id` LIMIT 1) as `insulation_type_id`');
		$this->db->from('schedules, complete_renovation_objects, specific_renovation_objects, type_services, equipments, voltage_class, users_complete_renovation_objects');
		$this->db->where('specific_renovation_objects.id = schedules.specific_renovation_object_id');
		$this->db->where('type_services.id=schedules.type_service_id');
		$this->db->where('complete_renovation_objects.id = specific_renovation_objects.complete_renovation_object_id');
		$this->db->where('equipments.id=specific_renovation_objects.equipment_id');
		$this->db->where('voltage_class.id=specific_renovation_objects.voltage_class_id');
		$this->db->where('complete_renovation_objects.id = users_complete_renovation_objects.object_id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		if ($filter['complete_renovation_object_id']) {
			$this->db->where('specific_renovation_objects.complete_renovation_object_id', rtrim(ltrim($filter['complete_renovation_object_id'], "^"), "$"));
		}
		if ($filter['equipment_id']) {
			$this->db->where('specific_renovation_objects.equipment_id', rtrim(ltrim($filter['equipment_id'], "^"), "$"));
		}
		if ($filter['insulation_type_id']) {
			$this->db->where('(SELECT `insulation_type_id` FROM `passports` WHERE `specific_renovation_object_id` = `schedules`.`specific_renovation_object_id` LIMIT 1) = ', rtrim(ltrim($filter['insulation_type_id'], "^"), "$"));
		}
		if ($filter['type_service_id']) {
			$this->db->where('schedules.type_service_id', rtrim(ltrim($filter['type_service_id'], "^"), "$"));
		}
		if ($filter['voltage_id']) {
			$this->db->where('specific_renovation_objects.voltage_class_id', rtrim(ltrim($filter['voltage_id'], "^"), "$"));
		}
		if ($filter['status']) {
			$this->db->where('schedules.status', rtrim(ltrim($filter['status'], "^"), "$"));
		}

		if ($post['search']['value']) {
			$this->db->like('specific_renovation_objects.name', $post['search']['value']);
		}

		$this->db->order_by($order_field, $order_dir);
		$this->db->limit($post['length'], $post['start']);
		$query = $this->db->get();

		return $query->result();
	}

	public function get_count_all()
	{
		$this->db->select('COUNT(schedules.id) as count');
		$this->db->from('schedules, specific_renovation_objects, complete_renovation_objects, users_complete_renovation_objects');
		$this->db->where('specific_renovation_objects.id = schedules.specific_renovation_object_id');
		$this->db->where('complete_renovation_objects.id = specific_renovation_objects.complete_renovation_object_id');
		$this->db->where('complete_renovation_objects.id = users_complete_renovation_objects.object_id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		$query = $this->db->get();

		return (int) $query->row('count');
	}

	public function change_value($field, $value, $id)
	{
		$this->db->set($field, $value === '' ? NULL : $value);
		$this->db->set('updated_by', $this->session->user->id);
		$this->db->set('updated_at', date('Y-m-d H:i:s'));
		$this->db->where('id', $id);
		$query = $this->db->update('schedules');
		return $query;
	}

	public function get_schedules($specific_renovation_object_id)
	{
		$this->db->select('schedules.*, type_services.name, type_services.short_name');
		$this->db->from('schedules');
		$this->db->join('type_services', 'type_services.id = schedules.type_service_id');
		$this->db->where('specific_renovation_object_id', $specific_renovation_object_id);
		$this->db->order_by('type_service_id', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_schedules_for_complete_renovation_object_sp($subdivision_id, $complete_renovation_object_id)
	{
		$this->db->select('schedules.*');
		$this->db->select('(`periodicity` + `year_last_service`) as year_repair');
		$this->db->select('(SELECT COUNT(`id`) FROM `passports` WHERE `specific_renovation_object_id` = `specific_renovation_objects`.`id`) AS `amount`');
		$this->db->where('specific_renovation_objects.id = schedules.specific_renovation_object_id');
		$this->db->where('specific_renovation_objects.subdivision_id', (int)$subdivision_id);
		$this->db->where('specific_renovation_objects.complete_renovation_object_id', (int)$complete_renovation_object_id);
		$this->db->where('((`schedules`.`status` = 1 AND `schedules`.`is_repair` = 1) OR schedules.will_add = 1)');
		$this->db->from('schedules, specific_renovation_objects');
		$this->db->order_by('schedules.type_service_id', 'ASC');
		$this->db->order_by('id', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_schedules_for_complete_renovation_object_srm($subdivision_id, $complete_renovation_object_id)
	{
		$this->db->select('schedules.*');
		$this->db->select('(`periodicity` + `year_last_service`) as year_repair');
		$this->db->select('(SELECT COUNT(`id`) FROM `passports` WHERE `specific_renovation_object_id` = `specific_renovation_objects`.`id`) AS `amount`');
		$this->db->where('specific_renovation_objects.id = schedules.specific_renovation_object_id');
		$this->db->where('specific_renovation_objects.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('complete_renovation_objects.is_repair', 1);
		$this->db->where('specific_renovation_objects.subdivision_id', (int)$subdivision_id);
		$this->db->where('specific_renovation_objects.complete_renovation_object_id', (int)$complete_renovation_object_id);
		$this->db->where('schedules.status', 1);
		$this->db->from('schedules, specific_renovation_objects, complete_renovation_objects');
		$this->db->order_by('schedules.type_service_id', 'ASC');
		$this->db->order_by('id', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_row($id)
	{
		$this->db->select('schedules.*');
		$this->db->where('schedules.id', $id);
		$this->db->from('schedules');
		$query = $this->db->get();
		return $query->row('is_repair');
	}

	public function update($data, $id)
	{
		$this->db->set($data);
		if ($id) {
			$this->db->where('id', $id);
		}
		$query = $this->db->update('schedules');
		return $query;
	}

	public function update_for_complete_renovation_object($data, $id)
	{
		$this->db->set($data);
		$this->db->where('id', $id);
		$query = $this->db->update('schedules');
		return $query;
	}

	public function get_count()
	{
		$query = $this->db->count_all('schedules');
		return $query;
	}

	public function add_data($data)
	{
		$this->db->insert('schedules', $data);
		return $this->db->insert_id();
	}

	public function get_rows_for_sp()
	{
		$this->db->select('type_services.short_name as repair_type');
		$this->db->select('complete_renovation_objects.name as stantion');
		$this->db->select('equipments.name as oborud');
		$this->db->select('specific_renovation_objects.name as disp');
		$this->db->select('(SELECT GROUP_CONCAT(`short_type`) from `passports` WHERE `passports`.`specific_renovation_object_id` = `specific_renovation_objects`.`id`) as `type`');
		$this->db->select('(SELECT MIN(`commissioning_year`) from `passports` WHERE `passports`.`specific_renovation_object_id` = `specific_renovation_objects`.`id`) as `year_start_min`');
		$this->db->select('specific_renovation_objects.year_commissioning as year_start');
		$this->db->select('specific_renovation_objects.year_repair_invest as year_repair_invest');
		$this->db->select('specific_renovation_objects.year_plan_repair_invest as year_plan_repair_invest');
		$this->db->select('(`voltage_class`.`voltage`/1000) as `class_voltage`');
		$this->db->select('schedules.year_last_service as repair_year_last');
		$this->db->select('schedules.periodicity as period');
		$this->db->select('(SELECT `cipher` from `ciphers` WHERE `id` = `schedules`.`cipher_id`) as `cipher`');
		$this->db->select('(SELECT `insulation_type_id` from `passports` WHERE `passports`.`specific_renovation_object_id` = `specific_renovation_objects`.`id` AND specific_renovation_objects.equipment_id = 3 LIMIT 1) as `insulation_type_id`');
		$this->db->select('(SELECT `surname` from `users` WHERE `id` = `complete_renovation_objects`.`user_id`) as `user`');
		$this->db->from('schedules, type_services, complete_renovation_objects, equipments, specific_renovation_objects, voltage_class');
		$this->db->where('specific_renovation_objects.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('schedules.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('schedules.type_service_id = type_services.id');
		$this->db->where('specific_renovation_objects.voltage_class_id = voltage_class.id');
		$this->db->where('status', 1);
		$query = $this->db->get();
		return $query->result();
	}

	public function get_rows_for_srm()
	{
		$this->db->select('type_services.short_name as repair_type');
		$this->db->select('complete_renovation_objects.name as stantion');
		$this->db->select('equipments.name as oborud');
		$this->db->select('specific_renovation_objects.name as disp');
		$this->db->select('(SELECT GROUP_CONCAT(`short_type`) from `passports` WHERE `passports`.`specific_renovation_object_id` = `specific_renovation_objects`.`id`) as `type`');
		$this->db->select('(SELECT MIN(`commissioning_year`) from `passports` WHERE `passports`.`specific_renovation_object_id` = `specific_renovation_objects`.`id`) as `year_start_min`');
		$this->db->select('specific_renovation_objects.year_commissioning as year_start');
		$this->db->select('specific_renovation_objects.year_repair_invest as year_repair_invest');
		$this->db->select('specific_renovation_objects.year_plan_repair_invest as year_plan_repair_invest');
		$this->db->select('(`voltage_class`.`voltage`/1000) as `class_voltage`');
		$this->db->select('schedules.year_last_service as repair_year_last');
		$this->db->select('schedules.periodicity as period');
		$this->db->select('(SELECT `cipher` from `ciphers` WHERE `id` = `schedules`.`cipher_id`) as `cipher`');
		$this->db->select('(SELECT `insulation_type_id` from `passports` WHERE `passports`.`specific_renovation_object_id` = `specific_renovation_objects`.`id` AND specific_renovation_objects.equipment_id = 3 LIMIT 1) as `insulation_type_id`');
		$this->db->select('(SELECT `surname` from `users` WHERE `id` = `complete_renovation_objects`.`user_id`) as `user`');
		$this->db->from('schedules, type_services, complete_renovation_objects, equipments, specific_renovation_objects, voltage_class');
		$this->db->where('specific_renovation_objects.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('schedules.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('schedules.type_service_id = type_services.id');
		$this->db->where('specific_renovation_objects.voltage_class_id = voltage_class.id');
		$this->db->where('status', 1);
		$this->db->where('complete_renovation_objects.is_repair', 1);
		$query = $this->db->get();
		return $query->result();
	}

	public function get_schedules_id_for_complete_renovation_object($subdivision_id = NULL, $complete_renovation_object_id = NULL)
	{
		$this->db->select('schedules.id');
		$this->db->where('specific_renovation_objects.id = schedules.specific_renovation_object_id');
		$this->db->where('schedules.id = schedules_years.schedule_id');
		$this->db->where('schedules_years.year_service', (date('Y') + 1));
		if ($subdivision_id && $complete_renovation_object_id) {
			$this->db->where('specific_renovation_objects.subdivision_id', $subdivision_id);
			$this->db->where('specific_renovation_objects.complete_renovation_object_id', $complete_renovation_object_id);
		}
		$this->db->from('schedules, specific_renovation_objects, schedules_years');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_schedules_for_complete_renovation_object($subdivision_id, $complete_renovation_object_id, $per_page = NULL, $offset = NULL, $pagination = NULL)
	{
		$this->db->select('schedules.*');
		$this->db->select('(periodicity + year_last_service) as year_repair');
		$this->db->select('specific_renovation_objects.name as dno');
		$this->db->select('specific_renovation_objects.equipment_id as equipment_id');
		$this->db->select('type_services.short_name as type_service');
		$this->db->select('equipments.name as equipment');
		$this->db->select('ROUND((voltage_class.voltage)) as voltage');
		$this->db->select('equipments.plural_name as equipment_plural_name');
		$this->db->where('specific_renovation_objects.id = schedules.specific_renovation_object_id');
		$this->db->where('specific_renovation_objects.voltage_class_id = voltage_class.id');
		$this->db->where('type_services.id = schedules.type_service_id');
		$this->db->where('equipments.id = specific_renovation_objects.equipment_id');
		$this->db->where('specific_renovation_objects.subdivision_id', $subdivision_id);
		$this->db->where('specific_renovation_objects.complete_renovation_object_id', $complete_renovation_object_id);
		// $this->db->where('schedules.is_repair', 1);
		$this->db->where('schedules.id = schedules_years.schedule_id');
		$this->db->where('schedules_years.year_service', (date('Y') + 1));
		$this->db->where('specific_renovation_objects.complete_renovation_object_id = users_complete_renovation_objects.object_id');
		$this->db->where('complete_renovation_objects.id = users_complete_renovation_objects.object_id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		$this->db->from('schedules, specific_renovation_objects, type_services, equipments, complete_renovation_objects, users_complete_renovation_objects, schedules_years, voltage_class');

		if ($this->input->get('field') === 'dno' && $this->input->get('sort')) {
			$this->db->order_by('specific_renovation_objects.name', $this->input->get('sort'));
		} elseif ($this->input->get('field') === 'equipment' && $this->input->get('sort')) {
			$this->db->order_by('equipments.name', $this->input->get('sort'));
		} elseif ($this->input->get('field') === 'type_service' && $this->input->get('sort')) {
			$this->db->order_by('type_services.short_name', $this->input->get('sort'));
		} elseif ($this->input->get('field') === 'is_contract_method' && $this->input->get('sort')) {
			$this->db->order_by('schedules.is_contract_method', $this->input->get('sort'));
		} elseif ($this->input->get('field') === 'month' && $this->input->get('sort')) {
			$this->db->order_by('schedules.month', $this->input->get('sort'));
		} else {
			$this->db->order_by('schedules.type_service_id', 'ASC');
			$this->db->order_by('equipments.name', 'ASC');
			$this->db->order_by('specific_renovation_objects.name', 'ASC');
		}
		if ($pagination) {
			$this->db->limit($per_page, $offset);
		}
		$query = $this->db->get();
		return $query->result();
	}

	public function get_total_schedules_for_complete_renovation_object($subdivision_id, $complete_renovation_object_id)
	{
		$this->db->select('schedules.*');
		$this->db->select('(periodicity + year_last_service) as year_repair');
		$this->db->select('specific_renovation_objects.name as dno');
		$this->db->select('specific_renovation_objects.equipment_id as equipment_id');
		$this->db->select('type_services.short_name as type_service');
		$this->db->select('equipments.name as equipment');
		$this->db->select('ROUND((voltage_class.voltage)) as voltage');
		$this->db->select('equipments.plural_name as equipment_plural_name');
		$this->db->where('specific_renovation_objects.id = schedules.specific_renovation_object_id');
		$this->db->where('specific_renovation_objects.voltage_class_id = voltage_class.id');
		$this->db->where('type_services.id = schedules.type_service_id');
		$this->db->where('equipments.id = specific_renovation_objects.equipment_id');
		$this->db->where('specific_renovation_objects.subdivision_id', $subdivision_id);
		$this->db->where('specific_renovation_objects.complete_renovation_object_id', $complete_renovation_object_id);
		// $this->db->where('schedules.is_repair', 1);
		$this->db->where('schedules.id = schedules_years.schedule_id');
		$this->db->where('schedules_years.year_service', (date('Y') + 1));
		$this->db->where('specific_renovation_objects.complete_renovation_object_id = users_complete_renovation_objects.object_id');
		$this->db->where('complete_renovation_objects.id = users_complete_renovation_objects.object_id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		$this->db->from('schedules, specific_renovation_objects, type_services, equipments, complete_renovation_objects, users_complete_renovation_objects, schedules_years, voltage_class');
		$query = $this->db->get();
		return $this->db->affected_rows();
	}

	public function get_row_kr($specific_renovation_object_id)
	{
		$this->db->select('schedules.*');
		$this->db->where('schedules.specific_renovation_object_id', $specific_renovation_object_id);
		$this->db->where('schedules.id = schedules_years.schedule_id');
		$this->db->where('schedules_years.year_service', (date('Y') + 1));
		$this->db->where('schedules.type_service_id', 1);
		$this->db->from('schedules, schedules_years');
		$query = $this->db->get();
		return $query->row();
	}

	// public function get_row_kr_old($specific_renovation_object_id)
	// {
	// 	$this->db->select('schedules.*');
	// 	$this->db->where('schedules.specific_renovation_object_id', $specific_renovation_object_id);
	// 	$this->db->where('schedules.is_repair', 1);
	// 	$this->db->where('schedules.type_service_id', 1);
	// 	$this->db->from('schedules');
	// 	$query = $this->db->get();
	// 	return $query->row();
	// }

	public function get_id_for_specific_renovation_object($specific_renovation_object_id)
	{
		$this->db->select('schedules.id');
		$this->db->where('schedules.specific_renovation_object_id', $specific_renovation_object_id);
		$this->db->from('schedules');
		$query = $this->db->get();
		return $query->result();
	}
}
