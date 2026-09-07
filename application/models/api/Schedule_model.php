<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Schedule_Model extends CI_Model
{
	public function get_rows_current_year_sp()
	{
		$this->db->select('complete_renovation_objects.name as station, specific_renovation_objects.name as disp, (voltage_class.voltage/1000) as voltage, equipments.name as equipment, schedules_years.schedule_id, schedules_years.month_service, schedules_years.year_service, schedules.type_service_id, schedules_years.date_service_actual');
		$this->db->select('(SELECT GROUP_CONCAT(`is_photo` SEPARATOR "<br>") FROM `passports` WHERE `specific_renovation_objects`.`id` = `passports`.`specific_renovation_object_id` ORDER BY `place_id` )  as is_photo');
		$this->db->select('(SELECT GROUP_CONCAT(`type` SEPARATOR "<br>") FROM `passports` WHERE `specific_renovation_objects`.`id` = `passports`.`specific_renovation_object_id` ORDER BY `place_id`)  as type');
		$this->db->select('(SELECT GROUP_CONCAT(`short_type` SEPARATOR "<br>") FROM `passports` WHERE `specific_renovation_objects`.`id` = `passports`.`specific_renovation_object_id` ORDER BY `place_id`)  as short_type');
		$this->db->from('schedules_years, schedules, specific_renovation_objects, complete_renovation_objects, equipments, voltage_class');
		$this->db->where('schedules_years.year_service', date('Y'));
		$this->db->where('schedules_years.month_service <=', date('n'));
		$this->db->where('schedules_years.date_service_actual', 0000 - 00 - 00);
		$this->db->where('schedules_years.schedule_id = schedules.id');
		$this->db->where('specific_renovation_objects.subdivision_id', 1);
		$this->db->where('schedules.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('voltage_class.id = specific_renovation_objects.voltage_class_id');
		$this->db->where('specific_renovation_objects.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->order_by('complete_renovation_objects.name ASC, equipment ASC, specific_renovation_objects.name ASC');
		$query = $this->db->get();
		return $query->result();
	}
}
