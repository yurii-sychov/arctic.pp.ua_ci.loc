<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Specific_renovation_object_Model extends CI_Model
{

	public function get_all()
	{
		$this->db->select('specific_renovation_objects.*');
		$this->db->from('specific_renovation_objects');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_all_for_complete_renovation_object($subdivision_id, $complete_renovation_object_id)
	{
		$this->db->select('*');
		$this->db->where('subdivision_id', $subdivision_id);
		$this->db->where('complete_renovation_object_id', $complete_renovation_object_id);
		$this->db->from('specific_renovation_objects');
		$this->db->order_by('name', 'asc');
		$query = $this->db->get();
		return $query->result();
	}

	public function add_data($data)
	{
		$this->db->insert('specific_renovation_objects', $data);
		return $this->db->insert_id();
	}

	public function is_specific_renovation_object($complete_renovation_object_id, $name, $equipment_id)
	{
		$this->db->select('*');
		$this->db->where('complete_renovation_object_id', $complete_renovation_object_id);
		$this->db->where('name', $name);
		$this->db->where('equipment_id', $equipment_id);
		$query = $this->db->get('specific_renovation_objects');
		return $query->row();
	}

	public function get_specific_renovation_object($id)
	{
		$this->db->select('specific_renovation_objects.*');
		$this->db->from('specific_renovation_objects');
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_data_for_cro_and_e_and_vc($complete_renovation_object_id, $equipment_id, $voltage_class_id)
	{
		$this->db->select('*');
		$this->db->from('specific_renovation_objects');
		$this->db->where('complete_renovation_object_id', $complete_renovation_object_id);
		$this->db->where('equipment_id', $equipment_id);
		$this->db->where('voltage_class_id', $voltage_class_id);
		$this->db->order_by('name', 'asc');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_search($name, $equipment_id)
	{
		$this->db->select('specific_renovation_objects.*, complete_renovation_objects.name as complete_renovation_object, equipments.name as equipment, voltage_class.voltage');
		$this->db->from('specific_renovation_objects, complete_renovation_objects, equipments, voltage_class');
		$this->db->where('specific_renovation_objects.complete_renovation_object_id=complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id=equipments.id');
		$this->db->where('specific_renovation_objects.voltage_class_id=voltage_class.id');
		$this->db->like('specific_renovation_objects.name', $name);
		$this->db->like('specific_renovation_objects.equipment_id', $equipment_id);
		$this->db->order_by('specific_renovation_objects.id', 'ASC');
		// $this->db->limit(10);
		$query = $this->db->get();
		return $query->result();
	}

	public function get_specific_renovation_object_for_copy($donor)
	{
		$this->db->select('passports.id, passports.type, places.name as place, specific_renovation_objects.name as disp, complete_renovation_objects.name as stantion');
		$this->db->from('places, specific_renovation_objects, complete_renovation_objects, passports');
		$this->db->where('specific_renovation_objects.id = passports.specific_renovation_object_id');
		$this->db->where('specific_renovation_objects.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('places.id = passports.place_id');
		$this->db->where('passports.type', $donor->type);
		if ($this->session->user->group !== 'admin') {
			$this->db->where('passports.complete_renovation_object_id', $donor->complete_renovation_object_id);
		}
		$this->db->where_not_in('passports.id', $donor->id);
		$query = $this->db->get();
		return $query->result();
	}

	public function delete_specific_renovation_object_full($specific_renovation_object_id)
	{
		$this->db->where('id', $specific_renovation_object_id);
		$query = $this->db->delete('specific_renovation_objects');

		return $query;
	}

	public function change_value($field, $value, $id)
	{
		$this->db->set($field, $value === '' ? NULL : $value);
		$this->db->set('updated_by', $this->session->user->id);
		$this->db->set('updated_at', date('Y-m-d H:i:s'));
		$this->db->where('id', $id);
		$query = $this->db->update('specific_renovation_objects');
		return $query;
	}

	public function get_id_for_complete_renovation_object($subdivision_id, $complete_renovation_object_id)
	{
		$this->db->select('id');
		$this->db->where('subdivision_id', $subdivision_id);
		$this->db->where('complete_renovation_object_id', $complete_renovation_object_id);
		$this->db->from('specific_renovation_objects');
		$this->db->order_by('name', 'asc');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_data_for_complete_renovation_object($complete_renovation_object)
	{
		$this->db->select('specific_renovation_objects.*');
		$this->db->select('(SELECT `name` FROM `equipments` WHERE `id` = `specific_renovation_objects`.`equipment_id`) as `equipment`');
		$this->db->select('(SELECT `voltage` FROM `voltage_class` WHERE `id` = `specific_renovation_objects`.`voltage_class_id`) as `voltage_class`');
		$this->db->from('specific_renovation_objects');
		$this->db->where('specific_renovation_objects.complete_renovation_object_id', $complete_renovation_object);
		$query = $this->db->get();
		return $query->result();
	}

	public function get_data_for_multi_year_schedule($complete_renovation_object_id, $type_service_id)
	{
		$this->db->select('schedules.type_service_id as repair_type');
		$this->db->select('complete_renovation_objects.name as station');
		$this->db->select('equipments.name as oborud');
		$this->db->select('specific_renovation_objects.name as disp');
		$this->db->select('specific_renovation_objects.year_commissioning as year_start');
		$this->db->select('(voltage_class.voltage / 1000) as class_voltage');
		$this->db->select('schedules.year_last_service as repair_year_last');
		$this->db->select('schedules.periodicity as period');
		$this->db->select('specific_renovation_objects.year_plan_repair_invest');
		$this->db->select('specific_renovation_objects.year_repair_invest as invest_year_last');

		$this->db->select('specific_renovation_objects.year_plan_repair_invest as invest_year_plan');
		$this->db->from('specific_renovation_objects, complete_renovation_objects, schedules, voltage_class, equipments');
		$this->db->where('specific_renovation_objects.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.id = schedules.specific_renovation_object_id');
		$this->db->where('specific_renovation_objects.voltage_class_id = voltage_class.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('schedules.type_service_id', $type_service_id);
		$this->db->where('schedules.status', 1);
		$this->db->where('complete_renovation_objects.id', $complete_renovation_object_id);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function update_field($id, $data)
	{
		$this->db->where('id', $id);
		$query = $this->db->update('specific_renovation_objects', $data);
		return $query;
	}
}
