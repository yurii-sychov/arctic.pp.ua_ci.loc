<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		if (!$this->session->user) {
			redirect('authentication/signin');
		}
		$this->load->model('user_model');
		$this->load->model('subdivision_model');
		$this->load->model('news_model');
	}

	public function index()
	{
		$user = $this->user_model->get_user($this->session->user->id);
		$data = [];
		$data['title'] = 'Профіль користувача';
		$data['content'] = 'profile/index';
		$data['page'] = 'profile/index';
		$data['page_js'] = 'profile';
		$data['datatables'] = FALSE;
		$data['title_heading'] = 'Кабінет користувача';
		$data['title_heading_card'] = 'Профіль користувача';
		$data['subdivisions'] = $this->subdivision_model->get_data();
		$data['user'] =  $user;
		$data['news'] = $this->news_model->get_data();
		$data['text_color'] = ['text-primary', 'text-secondary', 'text-success', 'text-danger', 'text-warning', 'text-info', 'text-dark'];
		$this->load->view('layout', $data);
	}

	public function update($id = NULL)
	{
		if (!is_numeric($id) || !$id) {
			show_404();
		}

		$user = $this->user_model->get_user($this->session->user->id == 1 ? $id : $this->session->user->id);

		if (!$user) {
			show_404();
		}
		echo $this->input->post('subdivision_id');
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');

		$data = [];
		$data['title'] = 'Редагування профілю';
		$data['content'] = 'profile/edit';
		$data['page'] = 'profile/edit';
		$data['page_js'] = 'profile';
		$data['datatables'] = FALSE;
		$data['title_heading'] = 'Кабінет користувача';
		$data['title_heading_card'] = 'Редагування профілю';
		$data['subdivisions'] = $this->subdivision_model->get_data();
		$data['user'] =  $user;

		$this->form_validation->set_rules('surname', 'Прізвище', 'trim|required');
		$this->form_validation->set_rules('name', 'Ім`я', 'trim|required');
		$this->form_validation->set_rules('patronymic', 'По батькові', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|required');
		$this->form_validation->set_rules('phone', 'Телефон', 'trim|required');
		$this->form_validation->set_rules('phone_mobile', 'Мобільний', 'trim|required');

		if ($this->form_validation->run() == FALSE) {
			$this->load->view('layout', $data);
		} else {
			$form_data = $this->set_data_user($this->input->post());
			$this->user_model->update_data($form_data, $user->id);
			$this->session->set_flashdata('message', 'Дані змінено.');
			redirect('/profile');
		}
	}

	public function send_message()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');

		$data = [];
		$data['title'] = 'Відправка повідомлення';
		$data['content'] = 'profile/send_message';
		$data['page'] = 'profile/send_message';
		$data['page_js'] = 'profile';
		$data['datatables'] = FALSE;
		$data['title_heading'] = 'Відправка повідомлення';
		$data['title_heading_card'] = 'Повідомлення';

		$this->form_validation->set_rules('name', 'Ваше ім`я', 'trim|required');
		$this->form_validation->set_rules('email', 'Ваш Email', 'trim|required');
		$this->form_validation->set_rules('subject', 'Тема', 'trim|required');
		$this->form_validation->set_rules('message', 'Повдомлення', 'trim|required');

		if ($this->form_validation->run() == FALSE) {
			$this->load->view('layout', $data);
		} else {
			$this->send($this->input->post());
			$this->session->set_flashdata('message', 'Ваше повдомлення відправлене.');
			redirect('/profile');
		}
	}

	private function set_data_user($post)
	{
		$data['surname'] = $post['surname'];
		$data['name'] = $post['name'];
		$data['patronymic'] = $post['patronymic'];
		$data['subdivision_id'] = $post['subdivision_id'];
		$data['gender'] = $post['gender'];
		$data['email'] = $post['email'];
		$data['phone'] = $post['phone'];
		$data['phone_mobile'] = $post['phone_mobile'];
		$data['rows'] = $post['rows'];
		return $data;
	}

	private function send($data)
	{
		$this->load->library('email');

		$config['protocol'] = 'smtp';
		$config['smtp_host'] = ($_SERVER['SERVER_ADDR'] == '127.0.1.1' or $_SERVER['SERVER_ADDR'] == '10.103.100.125') ? 's1.thehost.com.ua' : 'localhost';
		$config['smtp_user'] = 'yurii@sychov.pp.ua';
		$config['smtp_pass'] = '0910Yurasis';

		$this->email->initialize($config);

		$this->email->from($this->input->post('email', TRUE), $this->input->post('name', TRUE));
		$this->email->to('yurii@sychov.pp.ua');
		$this->email->subject($this->input->post('subject', TRUE));
		$this->email->message($this->input->post('message', TRUE));
		return $this->email->send();
	}

	// private function set_data_message($post)
	// {
	// 	$data['name'] = $post['name'];
	// 	$data['email'] = $post['email'];
	// 	$data['subject'] = $post['subject'];
	// 	$data['message'] = $post['message'];
	// 	return $data;
	// }
}
