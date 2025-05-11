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
		$this->db->select('complete_renovation_objects.name as station, specific_renovation_objects.name as disp, equipments.name as equipment, schedules_years.schedule_id, schedules_years.month_service, schedules_years.year_service, schedules.type_service_id, schedules.year_last_service');
		$this->db->from('schedules_years, schedules, specific_renovation_objects, complete_renovation_objects, equipments');
		$this->db->where('schedules_years.year_service', date('Y'));
		$this->db->where('schedules_years.month_service <=', date('n'));
		$this->db->where('schedules.year_last_service <', date('Y'));
		$this->db->where('schedules_years.schedule_id = schedules.id');
		$this->db->where('specific_renovation_objects.subdivision_id', 1);
		$this->db->where('schedules.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('specific_renovation_objects.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->order_by('complete_renovation_objects.name ASC, equipment ASC, specific_renovation_objects.name ASC');
		$query = $this->db->get();
		return $query->result();
	}
}
