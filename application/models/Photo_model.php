<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Photo_Model extends CI_Model
{
	public function insert_data($data)
	{
		$query = $this->db->insert('photos', $data);
		return $query;
	}

	public function get_row($id)
	{
		$this->db->select('*');
		$this->db->where('id', $id);
		$query = $this->db->get('photos');
		return $query->row();
	}

	public function get_rows($photo_album_id)
	{
		$this->db->select('*');
		$this->db->where('photo_album_id', $photo_album_id);
		$query = $this->db->get('photos');
		return $query->result();
	}

	public function delete($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('photos');
	}

	public function delete_photos($photo_album_id)
	{
		$this->db->where('photo_album_id', $photo_album_id);
		$this->db->delete('photos');
	}

	public function get_photos_for_passport($passport_id)
	{
		$this->db->select('photo_albums.id as photo_album_id, photo_albums.photo_album_date, photo_albums.photo_album_name, photos.*');
		$this->db->where('photos.photo_album_id = photo_albums.id');
		$this->db->where('passport_id', $passport_id);
		$this->db->order_by('photo_album_date', 'ASC');
		$this->db->order_by('photos.photo', 'ASC');
		$query = $this->db->get('photos, photo_albums');
		return $query->result();
	}
}
