<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Passport_Model extends CI_Model
{

	public function get_data()
	{
		$this->db->select('passports.*, places.name as place');
		$this->db->from('passports');
		$this->db->join('places', 'places.id = passports.place_id');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_data_for_specific_renovation_object($subdivision_id, $complete_renovation_object_id)
	{
		$this->db->select('passports.*');
		$this->db->from('passports, users_complete_renovation_objects');
		$this->db->where('subdivision_id', $subdivision_id);
		$this->db->where('complete_renovation_object_id', $complete_renovation_object_id);
		$this->db->where('passports.complete_renovation_object_id = users_complete_renovation_objects.object_id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		$query = $this->db->get();
		return $query->result();
	}

	public function add_data($data)
	{
		$this->db->insert('passports', $data);
		return $this->db->insert_id();
	}

	public function edit_data($data, $id)
	{
		$this->db->where('id', $id);
		$query = $this->db->update('passports', $data);
		return $query;
	}

	public function edit_value($data, $id)
	{
		$this->db->where('id', $id);
		$query = $this->db->update('passports', $data);
		return $query;
	}

	public function delete_passport_full($passport_id)
	{
		// Видаляємо записи з дочірних таблиць
		// $this->db->where('passport_id', $passport_id);
		// $this->db->delete('operating_list');

		// $this->db->where('passport_id', $passport_id);
		// $this->db->delete('passport_properties');

		// $this->db->where('passport_id', $passport_id);
		// $this->db->delete('passport_schedules');

		// $this->db->where('passport_id', $passport_id);
		// $this->db->delete('passport_photos');

		// Видаляемо запис з батьківської таблиці
		$this->db->where('id', $passport_id);
		$query = $this->db->delete('passports');

		return $query;
	}

	public function get_passports($specific_renovation_object_id)
	{
		$this->db->select('passports.*, places.name');
		$this->db->from('passports');
		$this->db->join('places', 'places.id = passports.place_id');
		$this->db->where('specific_renovation_object_id', $specific_renovation_object_id);
		$this->db->order_by('places.name', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_passport($id)
	{
		$this->db->select('passports.*, DATE_FORMAT(passports.production_date, "%d.%m.%Y") as production_date, subdivisions.name as subdivision, complete_renovation_objects.name as complete_renovation_object, specific_renovation_objects.name as specific_renovation_object, places.name as place');
		$this->db->from('passports');
		$this->db->join('subdivisions', 'subdivisions.id = passports.subdivision_id');
		$this->db->join('complete_renovation_objects', 'complete_renovation_objects.id = passports.complete_renovation_object_id');
		$this->db->join('specific_renovation_objects', 'specific_renovation_objects.id = passports.specific_renovation_object_id');
		$this->db->join('places', 'places.id = passports.place_id');
		$this->db->where('passports.id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_data_datatables()
	{
		$this->db->select('passports.*, complete_renovation_objects.name as stantion, equipments.name as equipment, equipments.id as equipment_id, specific_renovation_objects.name as disp, places.name as place');
		$this->db->from('passports, complete_renovation_objects, specific_renovation_objects, places, equipments, users_complete_renovation_objects');
		$this->db->where('passports.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('passports.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('passports.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('passports.place_id=places.id');
		$this->db->where('passports.complete_renovation_object_id = users_complete_renovation_objects.object_id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		$query = $this->db->get();

		return $query->result();
	}

	public function get_records_filtered($post, $filter)
	{
		$this->db->select('passports.id');
		$this->db->from('passports, specific_renovation_objects, users_complete_renovation_objects');
		$this->db->where('specific_renovation_objects.id=passports.specific_renovation_object_id');
		$this->db->where('passports.complete_renovation_object_id = users_complete_renovation_objects.object_id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		if ($filter['complete_renovation_object_id']) {

			$this->db->where('passports.complete_renovation_object_id', rtrim(ltrim($filter['complete_renovation_object_id'], "^"), "$"));
		}
		if ($filter['equipment_id']) {
			$this->db->where('specific_renovation_objects.equipment_id', rtrim(ltrim($filter['equipment_id'], "^"), "$"));
		}
		if ($filter['insulation_type_id']) {
			$this->db->where('passports.insulation_type_id', rtrim(ltrim($filter['insulation_type_id'], "^"), "$"));
		}
		if ($filter['voltage_class_id']) {
			$this->db->where('specific_renovation_objects.voltage_class_id', rtrim(ltrim($filter['voltage_class_id'], "^"), "$"));
		}

		if ($post['search']['value']) {
			$this->db->like('specific_renovation_objects.name', $post['search']['value']);
		}

		$this->db->get();

		return $this->db->affected_rows();
	}

	public function get_data_datatables_server_side($post, $filter, $order_dir, $order_field)
	{
		$this->db->select('passports.*, complete_renovation_objects.name as stantion, equipments.name as equipment, equipments.id as equipment_id, specific_renovation_objects.name as disp, specific_renovation_objects.voltage_class_id as voltage_class_id, places.name as place');
		$this->db->from('passports, complete_renovation_objects, specific_renovation_objects, places, equipments, users_complete_renovation_objects');
		$this->db->where('passports.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('passports.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('passports.place_id=places.id');
		$this->db->where('passports.complete_renovation_object_id = users_complete_renovation_objects.object_id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		if ($filter['complete_renovation_object_id']) {

			$this->db->where('passports.complete_renovation_object_id', rtrim(ltrim($filter['complete_renovation_object_id'], "^"), "$"));
		}
		if ($filter['equipment_id']) {
			$this->db->where('specific_renovation_objects.equipment_id', rtrim(ltrim($filter['equipment_id'], "^"), "$"));
		}
		if ($filter['insulation_type_id']) {
			$this->db->where('passports.insulation_type_id', rtrim(ltrim($filter['insulation_type_id'], "^"), "$"));
		}
		if ($filter['voltage_class_id']) {
			$this->db->where('specific_renovation_objects.voltage_class_id', rtrim(ltrim($filter['voltage_class_id'], "^"), "$"));
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
		$this->db->select('COUNT(passports.id) as count');
		$this->db->from('passports, users_complete_renovation_objects');
		$this->db->where('passports.complete_renovation_object_id = users_complete_renovation_objects.object_id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		$query = $this->db->get();

		return (int) $query->row('count');
	}

	public function get_row($id)
	{
		$this->db->select('passports.*, complete_renovation_objects.name as stantion, equipments.name as equipment, equipments.id as equipment_id, specific_renovation_objects.name as disp, places.name as place');
		$this->db->from('passports, complete_renovation_objects, specific_renovation_objects, places, equipments');
		$this->db->where('passports.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('passports.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('passports.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('passports.place_id = places.id');
		$this->db->where('passports.id', $id);
		$query = $this->db->get();

		return $query->row();
	}

	public function get_rows($complete_renovation_object_id, $equipment_id = NULL)
	{
		$this->db->select('passports.*, complete_renovation_objects.name as stantion, equipments.name as equipment, equipments.id as equipment_id, specific_renovation_objects.name as disp, places.name as place, voltage_class.voltage as voltage, CONCAT(equipments.name, " ", (voltage_class.voltage/1000), " кВ") as equipment_voltage, insulation_type.insulation_type as insulation_type');
		$this->db->from('passports, complete_renovation_objects, specific_renovation_objects, places, equipments, voltage_class, insulation_type');
		$this->db->where('passports.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('specific_renovation_objects.voltage_class_id = voltage_class.id');
		$this->db->where('passports.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('passports.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('passports.place_id = places.id');
		$this->db->where('passports.insulation_type_id = insulation_type.id');
		$this->db->where('passports.complete_renovation_object_id', $complete_renovation_object_id);
		if ($equipment_id) {
			$this->db->where('equipments.id', $equipment_id);
		}

		$this->db->order_by('equipment', 'ASC');
		$this->db->order_by('disp', 'ASC');
		$this->db->order_by('place', 'ASC');
		// $this->db->limit(20);
		$query = $this->db->get();

		return $query->result();
	}

	public function get_places_for_specific_renovation_object($specific_renovation_object_id, $place_id)
	{
		$this->db->select('passports.id, passports.place_id, places.name as place');
		$this->db->from('passports, places');
		$this->db->where('passports.place_id = places.id');
		$this->db->where('specific_renovation_object_id', $specific_renovation_object_id);
		$this->db->where_not_in('place_id', $place_id);
		$this->db->order_by('place_id', 'ACS');
		$query = $this->db->get();

		return $query->result();
	}

	public function get_capital_repairs_of_transformers($per_page, $offset)
	{
		$this->db->select('passports.*, subdivisions.name as subdivision, complete_renovation_objects.name as stantion,specific_renovation_objects.name as disp, equipments.name as equipment');
		$this->db->from('passports, subdivisions, complete_renovation_objects, specific_renovation_objects, equipments');
		$this->db->where('passports.subdivision_id = subdivisions.id');
		$this->db->where('passports.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('passports.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('equipments.id = 19');
		$this->db->where('specific_renovation_objects.voltage_class_id >= 3');
		$this->db->where('specific_renovation_objects.voltage_class_id <= 5');
		if ($this->input->get('subdivision_id')) {
			$this->db->where('subdivisions.id', $this->input->get('subdivision_id'));
		}
		if ($this->input->get('stantion_id')) {
			$this->db->where('complete_renovation_objects.id', $this->input->get('stantion_id'));
		}
		if ($this->input->get('disp_id')) {
			$this->db->where('specific_renovation_objects.id', $this->input->get('disp_id'));
		}
		$this->db->order_by('stantion', 'ASC');
		$this->db->order_by('disp', 'ASC');
		$this->db->limit($per_page, $offset);
		$query = $this->db->get();

		return $query->result();
	}

	public function get_total_capital_repairs_of_transformers()
	{
		$this->db->select('passports.id');
		$this->db->from('passports, subdivisions, complete_renovation_objects, specific_renovation_objects, equipments');
		$this->db->where('passports.subdivision_id = subdivisions.id');
		$this->db->where('passports.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('passports.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('equipments.id = 19');
		$this->db->where('specific_renovation_objects.voltage_class_id >= 3');
		$this->db->where('specific_renovation_objects.voltage_class_id <= 5');
		if ($this->input->get('subdivision_id')) {
			$this->db->where('subdivisions.id', $this->input->get('subdivision_id'));
		}
		if ($this->input->get('stantion_id')) {
			$this->db->where('complete_renovation_objects.id', $this->input->get('stantion_id'));
		}
		if ($this->input->get('disp_id')) {
			$this->db->where('specific_renovation_objects.id', $this->input->get('disp_id'));
		}
		$this->db->get();

		return $this->db->affected_rows();
	}

	public function get_transformers($subdivision_id, $complete_renovation_object_id)
	{
		$this->db->select('passports.*, complete_renovation_objects.name as stantion, equipments.name as equipment, specific_renovation_objects.name as disp');
		$this->db->from('passports, complete_renovation_objects, specific_renovation_objects, equipments');
		$this->db->where('passports.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('passports.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('passports.subdivision_id', $subdivision_id);
		$this->db->where('passports.complete_renovation_object_id', $complete_renovation_object_id);
		$this->db->where('equipments.id = 19');
		$this->db->order_by('disp', 'ASC');
		$query = $this->db->get();

		return $query->result();
	}

	public function get_subdivisions()
	{
		$this->db->select('subdivisions.id, subdivisions.name');
		$this->db->from('subdivisions');
		$this->db->order_by('name', 'ASC');
		$query = $this->db->get();

		return $query->result();
	}

	public function get_stantions()
	{
		$this->db->select('complete_renovation_objects.id, complete_renovation_objects.name');
		$this->db->from('complete_renovation_objects');
		$this->db->where('subdivision_id', $this->input->get('subdivision_id'));
		$this->db->order_by('name', 'ASC');
		$query = $this->db->get();

		return $query->result();
	}

	public function get_disps()
	{
		$this->db->select('specific_renovation_objects.id, specific_renovation_objects.name');
		$this->db->from('specific_renovation_objects');
		$this->db->where('subdivision_id', $this->input->get('subdivision_id'));
		$this->db->where('complete_renovation_object_id', $this->input->get('stantion_id'));
		$this->db->where('specific_renovation_objects.equipment_id = 19');
		$this->db->order_by('name', 'ASC');
		$query = $this->db->get();

		return $query->result();
	}

	public function get_passport_for_donor($passport_id)
	{
		$this->db->select('passports.*, complete_renovation_objects.name as stantion, specific_renovation_objects.name as disp, places.name as place, equipments.name as equipment, equipments.id as equipment_id, passports.id as donor_passport_id');
		$this->db->from('passports, complete_renovation_objects, specific_renovation_objects, places, equipments');
		$this->db->where('passports.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('passports.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('passports.place_id = places.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('passports.id', $passport_id);
		$query = $this->db->get();

		return $query->row();
	}

	public function get_documents_for_zip_archive($passport_id)
	{
		$this->db->select('complete_renovation_objects.name as stantion');
		$this->db->select('specific_renovation_objects.name as disp');
		$this->db->select('passports.number as number');
		$this->db->select('documents.document_date, documents.document_description, documents.document_scan');
		$this->db->from('complete_renovation_objects, specific_renovation_objects, passports, documents');
		$this->db->where('complete_renovation_objects.id = passports.complete_renovation_object_id');
		$this->db->where('specific_renovation_objects.id = passports.specific_renovation_object_id');
		$this->db->where('passports.id = documents.passport_id');
		$this->db->where('passports.id', $passport_id);
		$query = $this->db->get();

		return $query->result();
	}

	public function get_photos_for_zip_archive($passport_id)
	{
		$this->db->select('complete_renovation_objects.name as stantion');
		$this->db->select('specific_renovation_objects.name as disp');
		$this->db->select('passports.number as number');
		$this->db->select('photos.photo_date, photos.photo_description, photos.photo');
		$this->db->from('complete_renovation_objects, specific_renovation_objects, passports, photos');
		$this->db->where('complete_renovation_objects.id = passports.complete_renovation_object_id');
		$this->db->where('specific_renovation_objects.id = passports.specific_renovation_object_id');
		$this->db->where('passports.id = photos.passport_id');
		$this->db->where('passports.id', $passport_id);
		$query = $this->db->get();

		return $query->result();
	}

	public function change_value($field, $value, $id)
	{
		$this->db->set($field, $value === '' ? NULL : $value);
		if ($this->session->user->group !== 'admin') {
			$this->db->set('updated_by', $this->session->user->id);
			$this->db->set('updated_at', date('Y-m-d H:i:s'));
		}
		$this->db->where('id', $id);
		$query = $this->db->update('passports');
		return $query;
	}

	public function get_data_for_excel($subdivision_id = NULL, $complete_renovation_object_id = NULL)
	{
		$this->db->select('passports.*');

		// $this->db->select('(SELECT `name` FROM `subdivisions` WHERE `id` = `subdivision_id`)  as subdivision');
		// $this->db->select('(SELECT `name` FROM `complete_renovation_objects` WHERE `id` = `complete_renovation_object_id`) as complete_renovation_object');
		// $this->db->select('(SELECT `short_class_voltage` FROM `complete_renovation_objects` WHERE `id` = `complete_renovation_object_id`) as short_class_voltage_cro');
		// $this->db->select('(SELECT `full_class_voltage` FROM `complete_renovation_objects` WHERE `id` = `complete_renovation_object_id`) as full_class_voltage_cro');
		// $this->db->select('(SELECT `name` FROM `specific_renovation_objects` WHERE `id` = `specific_renovation_object_id`) as specific_renovation_object');
		// $this->db->select('(SELECT `equipments`.`name` FROM `equipments`, `specific_renovation_objects` WHERE `specific_renovation_objects`.`equipment_id` = `equipments`.`id` AND `specific_renovation_objects`.`id` = `specific_renovation_object_id`)  as equipment');
		// $this->db->select('(SELECT `voltage_class`.`voltage` FROM `voltage_class`, `specific_renovation_objects` WHERE `specific_renovation_objects`.`voltage_class_id` = `voltage_class`.`id` AND `specific_renovation_objects`.`id` = `specific_renovation_object_id`)  as equipment_voltage');
		// $this->db->select('(SELECT `name` FROM `places` WHERE `id` = `place_id`)  as place');
		// $this->db->select('(SELECT `insulation_type` FROM `insulation_type` WHERE `id` = `insulation_type_id`) as insulation_type');
		// $this->db->from('passports');

		$this->db->simple_query('SET SESSION group_concat_max_len=50000');

		$this->db->select('subdivisions.name as subdivision');
		$this->db->select('complete_renovation_objects.name as complete_renovation_object');
		$this->db->select('complete_renovation_objects.short_class_voltage as short_class_voltage_cro');
		$this->db->select('complete_renovation_objects.full_class_voltage as full_class_voltage_cro');
		$this->db->select('specific_renovation_objects.name as specific_renovation_object');
		$this->db->select('places.name as place');
		$this->db->select('insulation_type.insulation_type as insulation_type');
		$this->db->select('equipments.name as equipment');
		$this->db->select('voltage_class.voltage as equipment_voltage');
		$this->db->select('(SELECT GROUP_CONCAT(CONCAT_WS(": ", `name`, `value`) SEPARATOR "|") FROM `passport_properties`, `properties` WHERE `properties`.`id` = `passport_properties`.`property_id` AND `passport_id` = `passports`.`id` ORDER BY `name`) as properties');

		$this->db->from('passports, subdivisions, complete_renovation_objects, specific_renovation_objects, equipments, voltage_class, places, insulation_type');
		$this->db->where('passports.subdivision_id = subdivisions.id');
		$this->db->where('passports.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('passports.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('passports.place_id = places.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('specific_renovation_objects.voltage_class_id = voltage_class.id');
		$this->db->where('passports.insulation_type_id = insulation_type.id');

		$this->db->where('passports.subdivision_id', $subdivision_id);
		$this->db->where('passports.complete_renovation_object_id', $complete_renovation_object_id);
		$this->db->order_by('equipment, equipment_voltage, place, complete_renovation_object, specific_renovation_object', 'ASC');
		$query = $this->db->get();

		return $query->result();
	}

	public function update_field($id, $data)
	{
		$this->db->where('id', $id);
		$query = $this->db->update('passports', $data);
		return $query;
	}
}
