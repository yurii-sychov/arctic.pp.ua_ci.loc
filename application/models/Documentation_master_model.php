<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Documentation_master_Model extends CI_Model
{
	public function insert_row($data)
	{
		$query = $this->db->insert('documentations_masters', $data);
		return $query;
	}

	public function delete_for_master_row($documentation_id, $plot_id)
	{
		$this->db->where('documentation_id', $documentation_id);
		$this->db->where('plot_id', $plot_id);
		$query = $this->db->delete('documentations_masters');
		return $query;
	}

	public function get_data($document_type, $plot_id)
	{
		$this->db->select('documentations.*');

		$this->db->select('DATE_FORMAT(`documentations`.`document_date_start`, "%d-%m-%Y") as date_start_doc');
		$this->db->select('DATE_FORMAT(`documentations`.`document_date_finish`, "%d-%m-%Y") as date_finish_doc');
		$this->db->where('documentations_masters.documentation_id = documentations.id');
		$this->db->where('documentations.document_type', $document_type);
		$this->db->where('documentations_masters.plot_id', $plot_id);
		$this->db->from('documentations_masters, documentations');
		$query = $this->db->get();

		return $query->result();
	}
}
