<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Documentation_Model extends CI_Model
{

	public function get_data()
	{
		$plot_id = $this->input->get('plot_id') ?  $this->input->get('plot_id') : 0;
		$this->db->select('*');
		$this->db->select(
			'(CASE
				WHEN `document_type` = 1 THEN "ОП"
				WHEN `document_type` = 2 THEN "ПБ"
				WHEN `document_type` = 3 THEN "ТЕ"
				ELSE "Інше"
			END) as `document_type_text`'
		);
		$this->db->select('DATE_FORMAT(`document_date_start`, "%d-%m-%Y") as date_start_doc');
		$this->db->select('DATE_FORMAT(`document_date_finish`, "%d-%m-%Y") as date_finish_doc');
		$this->db->select('(SELECT `documentation_id` FROM `documentations_masters` WHERE `documentation_id` = `id` AND `plot_id` = ' . $plot_id . ' limit 1) as my_docs');
		// $this->db->select('(SELECT `parent_id` FROM `documentation_groups` WHERE `id` = `documentations`.`documentation_group_id`) as parent_id_group');
		// $this->db->select('(SELECT `name` FROM `documentation_groups` WHERE `id` = `documentations`.`documentation_group_id`) as name_subgroup');
		$this->db->from('documentations');
		$query = $this->db->get();

		return $query->result();
	}

	public function get_data_row($id)
	{
		$this->db->select('*');
		$this->db->select(
			'(CASE
				WHEN `document_type` = 1 THEN "ОП"
				WHEN `document_type` = 2 THEN "ПБ"
				WHEN `document_type` = 3 THEN "ТЕ"
				ELSE "Інше"
			END) as `document_type_text`'
		);
		$this->db->select('DATE_FORMAT(`document_date_start`, "%d-%m-%Y") as date_start_doc');
		$this->db->select('DATE_FORMAT(`document_date_finish`, "%d-%m-%Y") as date_finish_doc');
		// $this->db->select('(SELECT `parent_id` FROM `documentation_categories` WHERE `id` = `documentations`.`documentation_category_id`) as parent_id_category');
		// $this->db->select('(SELECT `name` FROM `documentation_categories` WHERE `id` = `documentations`.`documentation_category_id`) as name_subcategory');
		$this->db->where('id', $id);
		$this->db->from('documentations');
		$query = $this->db->get();

		return $query->row();
	}

	public function get_data_doc_type_row($doc_type)
	{
		$this->db->select('*');
		$this->db->select(
			'(CASE
				WHEN `document_type` = 1 THEN "ОП"
				WHEN `document_type` = 2 THEN "ПБ"
				WHEN `document_type` = 3 THEN "ТЕ"
				ELSE "Інше"
			END) as `document_type_text`'
		);
		$this->db->select('DATE_FORMAT(`document_date_start`, "%d-%m-%Y") as date_start_doc');
		$this->db->select('DATE_FORMAT(`document_date_finish`, "%d-%m-%Y") as date_finish_doc');

		$this->db->where('document_type', $doc_type);
		$this->db->from('documentations');
		$query = $this->db->get();

		return $query->result();
	}

	public function insert_row($data)
	{
		$this->db->insert('documentations', $data);
		$query = $this->db->insert_id();

		return $query;
	}

	public function update_row($id, $data)
	{
		$this->db->where('id', $id);
		$query = $this->db->update('documentations', $data);

		return $query;
	}

	public function trash_row($id, $data)
	{
		$this->db->where('id', $id);
		$query = $this->db->update('documentations', $data);

		return $query;
	}

	public function untrash_row($id, $data)
	{
		$this->db->where('id', $id);
		$query = $this->db->update('documentations', $data);

		return $query;
	}
}
