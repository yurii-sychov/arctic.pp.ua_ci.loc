<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Schedule_note_Model extends CI_Model
{
	public function insert($data)
	{
		$this->db->insert('schedules_notes', $data);
	}

	public function delete_for_schedule_id_and_year($schedule_id, $year_service)
	{
		$this->db->where('schedule_id', $schedule_id);
		$this->db->where('year_service', $year_service);
		$query = $this->db->delete('schedules_notes');
		return $query;
	}

	public function get_note_for_schedule_id($schedule_id)
	{
		$this->db->select('schedules_notes.*');
		$this->db->where('schedules_notes.schedule_id', $schedule_id);
		$this->db->where('schedules_notes.year_service', (date('Y') + 1));
		$this->db->from('schedules_notes');
		$query = $this->db->get();
		return $query->row();
	}

	public function get_schedule_id()
	{
		$this->db->select('schedule_id as id');
		$this->db->where('year_service', (date('Y') + 1));
		$this->db->from('schedules_notes');
		$query = $this->db->get();
		return $query->result();
	}

	// public function change_value($field, $value, $id)
	// {
	// 	$this->db->set($field, $value);
	// 	$this->db->set('updated_by', $this->session->user->id);
	// 	$this->db->set('updated_at', date('Y-m-d H:i:s'));
	// 	$this->db->where('schedule_id', $id);
	// 	$query = $this->db->update('schedules_years');
	// 	return $query;
	// }



	// public function delete($data)
	// {
	// 	$this->db->delete('schedules_years', $data);
	// }
}
