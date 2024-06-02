<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Document_Model extends CI_Model
{
	public function insert_data($data)
	{
		$query = $this->db->insert('documents', $data);
		return $query;
	}

	public function get_row($id)
	{
		$this->db->select('*');
		$this->db->where('id', $id);
		$query = $this->db->get('documents');
		return $query->row();
	}

	public function delete($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('documents');
	}

	public function get_documents_for_passport($passport_id)
	{
		$this->db->select('*');
		$this->db->where('passport_id', $passport_id);
		$this->db->order_by('document_date', 'ASC');
		$query = $this->db->get('documents');
		return $query->result();
	}

	public function get_value($field)
	{
		$this->db->select($field);
		$this->db->distinct();
		// $this->db->where('created_by', $this->session->user->id);
		$this->db->order_by($field);
		$query = $this->db->get('documents');
		return $query->result();
	}

	public function edit_data_row($data, $id)
	{
		$this->db->where('id', $id);
		$query = $this->db->update('documents', $data);
		return $query;
	}
}
