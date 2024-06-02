<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Authentication extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('authentication_model');
		$this->load->model('log_model');
	}

	public function signin_ajax()
	{
		$this->output->set_content_type('application/json', 'utf-8');

		// Якщо це не Ajax-запрос
		if ($this->input->is_ajax_request() === FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Это не Ajax-запрос!']));
			return;
		}

		$error = [];

		if (!$this->input->post('login')) {
			array_push($error, ['message' => 'Будь ласка введіть логін.']);
		}

		if (!$this->input->post('password')) {
			array_push($error, ['message' => 'Будь ласка введіть пароль.']);
		}

		if ($error) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => $error]));
			return;
		}

		$result = $this->authentication_model->get_user($this->input->post('login'), $this->input->post('password'));

		if ($result) {
			unset($result->password);
			unset($result->password_sha1);

			$this->session->set_userdata('user', $result);

			//$this->send_mail();
			$log_data = $this->set_log_data("Вхід в систему.", 'signin', json_encode(NULL, JSON_UNESCAPED_UNICODE), json_encode(NULL, JSON_UNESCAPED_UNICODE), 5);
			$this->log_model->insert_data($log_data);

			$this->output->set_output(json_encode([
				'status' => 'SUCCESS',
				'message' => 'Чекайте. Зараз ви будете перенаправлені в кабінет користувача.',
				// 'data' => $result,
				// 'user_id' => $this->session->user->id
			]));
			return;
		}

		array_push($error, ['message' => 'Неправильний логін або пароль. Можливо ви не активовані.']);
		$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => $error]));
		return;
	}

	public function signup_ajax()
	{
		$this->output->set_content_type('application/json', 'utf-8');

		// Якщо це не Ajax-запрос
		if ($this->input->is_ajax_request() === FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Это не Ajax-запрос!']));
			return;
		}

		$is_email = $this->authentication_model->is_email($this->input->post('email'));
		$is_login = $this->authentication_model->is_login($this->input->post('login'));

		$error = [];

		if ($is_email) {
			array_push($error, ['message' => '<strong>Користувач з таким email вже існує.</strong>']);
		}

		if ($is_login) {
			array_push($error, ['message' => '<strong>Користувач з таким логіном вже існує.</strong>']);
		}

		if (!$this->input->post('email')) {
			array_push($error, ['message' => 'Будь ласка введіть email.']);
		}

		if (!$this->input->post('login')) {
			array_push($error, ['message' => 'Будь ласка введіть логін.']);
		}

		if (!$this->input->post('password')) {
			array_push($error, ['message' => 'Будь ласка введіть пароль.']);
		}

		// if ( ! $this->input->post('re_password')) {
		// 	array_push($error, ['message' => 'Будь ласка повторіть введений пароль.']);
		// }

		if (!$this->input->post('surname')) {
			array_push($error, ['message' => 'Будь ласка введіть прізвище.']);
		}

		if (!$this->input->post('name')) {
			array_push($error, ['message' => 'Будь ласка введіть ім`я.']);
		}

		if ($error) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => $error]));
			return;
		}

		$data['email'] = $this->input->post('email');
		$data['login'] = $this->input->post('login');
		$data['password_sha1'] = sha1($this->input->post('password'));
		$data['surname'] = $this->input->post('surname');
		$data['name'] = $this->input->post('name');
		$data['group'] = 'user';
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');

		$result = $this->authentication_model->create_user($data);

		if ($result) {

			$this->output->set_output(json_encode([
				'status' => 'SUCCESS',
				'message' => 'Чекайте. Зараз ви будете перенаправлені на сторінку входу.',
			]));
			return;
		}

		$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => $error]));
		return;
	}

	public function signin()
	{
		if ($this->session->user) {
			redirect('/');
		}

		$data['title'] = 'Вхід до системи';
		$data['content'] = 'authentication/signin';
		$data['page'] = 'signin';
		$this->load->view('authentication/layout', $data);
	}

	public function signup()
	{
		$data['title'] = 'Реєстрація в системі';
		$data['content'] = 'authentication/signup';
		$data['page'] = 'signup';
		$this->load->view('authentication/layout', $data);
	}

	public function forgot()
	{
		$data['title'] = 'Відновлення паролю';
		$data['content'] = 'authentication/forgot';
		$data['page'] = 'forgot';
		$this->load->view('authentication/layout', $data);
	}

	public function logout()
	{
		if ($this->session->user) {
			$log_data = $this->set_log_data("Вихід з системи.", 'logout', json_encode(NULL, JSON_UNESCAPED_UNICODE), json_encode(NULL, JSON_UNESCAPED_UNICODE), 5);
			$this->log_model->insert_data($log_data);
		}

		$this->session->sess_destroy();

		redirect('/authentication/signin');
	}

	public function jump($id)
	{
		if ($this->session->user->id != 1) {
			redirect('/profile');
		}

		$log_data = $this->set_log_data("Вхід адміна до кабінету користувача з id #" . $id . ".", 'jump', json_encode(NULL, JSON_UNESCAPED_UNICODE), json_encode(NULL, JSON_UNESCAPED_UNICODE), 5);
		$this->log_model->insert_data($log_data);

		$result = $this->authentication_model->get_user_id($id);

		if ($result) {
			unset($result->password);
			unset($result->password_sha1);

			$this->session->set_userdata('user', $result);

			redirect('/profile');
		}
	}

	private function send_mail()
	{
		$this->load->library('email');

		$config['protocol'] = 'smtp';
		$config['smtp_host'] = ($_SERVER['SERVER_ADDR'] == '127.0.1.1' or $_SERVER['SERVER_ADDR'] == '10.103.100.125') ? 's1.thehost.com.ua' : 'localhost';
		$config['smtp_user'] = 'yurii@sychov.pp.ua';
		$config['smtp_pass'] = '0910Yurasis';

		$this->email->initialize($config);

		$this->email->from($this->session->user->email, $this->session->user->surname . ' ' . $this->session->user->name . ' ' . $this->session->user->patronymic);
		$this->email->to('yurii@sychov.pp.ua');
		$this->email->subject('!!!_Enter to arctic.pp.ua_IP:' . $this->input->ip_address() . '!!!');
		$this->email->message('Enter to site!!!');
		return $this->email->send();
	}

	private function set_log_data($action, $short_action, $data_before, $data_after, $importance)
	{
		$this->load->library('user_agent');
		$data = [];

		$data['user_id'] = $this->session->user->id;
		$data['link'] = uri_string();
		$data['action'] = $action;
		$data['short_action'] = $short_action;
		$data['data_before'] = $data_before;
		$data['data_after'] = $data_after;
		$data['browser'] = $this->agent->browser();
		$data['ip'] = $this->input->ip_address();
		$data['platform'] = $this->agent->platform();
		$data['importance'] = $importance;
		$data['created_at'] = date('Y-m-d H:i:s');

		return $data;
	}
}
