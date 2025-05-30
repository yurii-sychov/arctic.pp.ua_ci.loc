<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Documentation_plot_Model extends CI_Model
{
	public function get_data()
	{
		$this->db->select('*');
		$this->db->from('documentations_plots');
		$this->db->order_by('sort ASC');
		$query = $this->db->get();

		return $query->result();
	}

	public function get_data_row($id)
	{
		$this->db->select('*');
		$this->db->where('id', $id);
		$this->db->from('documentations_plots');
		$query = $this->db->get();

		return $query->row();
	}

	public function get_data_for_master()
	{
		$this->db->select('*');
		$this->db->where('documentations_plots.id = documentations_masters_plots.plot_id');
		$this->db->where('documentations_masters_plots.master_id', $this->session->master->id);
		$this->db->from('documentations_plots, documentations_masters_plots');
		$this->db->order_by('sort ASC');
		$query = $this->db->get();

		return $query->result();
	}
}
