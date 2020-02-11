<?php
class ControllerUserUserActivity extends Controller {
	public function __construct($registry) {
		parent::__construct($registry);

		$this->load->model('user/user_activity');
	}

	public function index() {
		$this->load->language('user/user_activity');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['filter_user'])) {
			$filter_user = $this->request->get['filter_user'];
		} else {
			$filter_user = null;
		}

		if (isset($this->request->get['filter_ip'])) {
			$filter_ip = $this->request->get['filter_ip'];
		} else {
			$filter_ip = null;
		}

		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = '';
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = '';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_user'])) {
			$url .= '&filter_user=' . urlencode($this->request->get['filter_user']);
		}

		if (isset($this->request->get['filter_ip'])) {
			$url .= '&filter_ip=' . $this->request->get['filter_ip'];
		}

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true),
			'text' => $this->language->get('text_home')
		);

		$data['breadcrumbs'][] = array(
			'href' => $this->url->link('user/user_activity', 'token=' . $this->session->data['token'] . $url, true),
			'text' => $this->language->get('heading_title')
		);

		$data['activities'] = array();

		$filter_data = array(
			'filter_user'       => $filter_user,
			'filter_ip'         => $filter_ip,
			'filter_date_start'	=> $filter_date_start,
			'filter_date_end'	=> $filter_date_end,
			'start'             => ($page - 1) * 20,
			'limit'             => 20
		);

		$activity_total = $this->model_user_user_activity->getTotalUserActivities($filter_data);

		$results = $this->model_user_user_activity->getUserActivities($filter_data);

		foreach ($results as $result) {
			$username = '';

			$this->load->model('user/user');

			$user_info = $this->model_user_user->getUser($result['user_id']);

			if ($user_info && $user_info['username']) {
				$username = $user_info['username'];
			}

			$comment = sprintf($this->language->get('text_' . $result['key']), $result['user_id'], $username, $result['data']);

			$find = array(
				'user_id=',
				'product_id=',
				'category_id=',
				'store_id=',
			);

			$replace = array(
				$this->url->link('user/user/edit', 'token=' . $this->session->data['token'] . '&user_id=', true),
				$this->url->link('catalog/product/edit', 'token=' . $this->session->data['token'] . '&product_id=', true),
				$this->url->link('catalog/category/edit', 'token=' . $this->session->data['token'] . '&category_id=', true),
				$this->url->link('setting/store/edit', 'token=' . $this->session->data['token'] . '&store_id=', true),
			);

			$data['activities'][] = array(
				'comment'    => str_replace($find, $replace, $comment),
				'ip'         => $result['ip'],
				'date_added' => date($this->language->get('datetime_format'), strtotime($result['date_added']))
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_comment'] = $this->language->get('column_comment');
		$data['column_ip'] = $this->language->get('column_ip');
		$data['column_date_added'] = $this->language->get('column_date_added');

		$data['entry_user'] = $this->language->get('entry_user');
		$data['entry_ip'] = $this->language->get('entry_ip');
		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');

		$data['button_filter'] = $this->language->get('button_filter');

		$data['token'] = $this->session->data['token'];

		$url = '';

		if (isset($this->request->get['filter_user'])) {
			$url .= '&filter_user=' . urlencode($this->request->get['filter_user']);
		}

		if (isset($this->request->get['filter_ip'])) {
			$url .= '&filter_ip=' . $this->request->get['filter_ip'];
		}

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		$pagination = new Pagination();
		$pagination->total = $activity_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('user/user_activity', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($activity_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($activity_total - $this->config->get('config_limit_admin'))) ? $activity_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $activity_total, ceil($activity_total / $this->config->get('config_limit_admin')));

		$data['filter_user'] = $filter_user;
		$data['filter_ip'] = $filter_ip;
		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('user/user_activity', $data));
	}

	public function addActivityAddUser(&$route, &$args = null, &$output = null) {
		if (isset($output) && !empty($output)) {
			$this->model_user_user_activity->addActivity('create_user', $output);
		}
	}

	public function addActivityEditUser(&$route, &$args = null, &$output = null) {
		if (isset($args[0]) && !empty($args[0])) {
			$this->model_user_user_activity->addActivity('edit_user', $args[0]);
		}
	}

	public function addActivityDeleteUser(&$route, &$args = null, &$output = null) {
		if (isset($args[0]) && !empty($args[0])) {
			$this->model_user_user_activity->addActivity('delete_user', $args[0]);
		}
	}

	public function addActivityForgottenUser(&$route, &$args = null, &$output = null) {
		if (isset($args[0]) && !empty($args[0])) {
			$this->load->model('user/user');

			$user_info = $this->model_user_user->getUserByEmail($args[0]);

			if ($user_info && $user_info['user_id']) {
				$this->model_user_user_activity->addActivity('forgotten_user', $user_info['user_id']);
			}
		}
	}

	public function addActivityResetUser(&$route, &$args = null, &$output = null) {
		if (isset($args[0]) && !empty($args[0])) {
			$this->model_user_user_activity->addActivity('reset_user', $args[0]);
		}
	}

	public function addActivityAddProduct(&$route, &$args = null, &$output = null) {
		if (isset($output) && !empty($output)) {
			$this->model_user_user_activity->addActivity('create_product', $output);
		}
	}

	public function addActivityAddCategory(&$route, &$args = null, &$output = null) {
		if (isset($output) && !empty($output)) {
			$this->model_user_user_activity->addActivity('create_category', $output);
		}
	}

	public function addActivityAddStore(&$route, &$args = null, &$output = null) {
		if (isset($output) && !empty($output)) {
			$this->model_user_user_activity->addActivity('create_store', $output);
		}
	}

	public function addActivityEditProduct(&$route, &$args = null, &$output = null) {
		if (isset($args[0]) && !empty($args[0])) {
			$this->model_user_user_activity->addActivity('update_product', $args[0]);
		}
	}

	public function addActivityEditCategory(&$route, &$args = null, &$output = null) {
		if (isset($args[0]) && !empty($args[0])) {
			$this->model_user_user_activity->addActivity('update_category', $args[0]);
		}
	}

	public function addActivityEditStore(&$route, &$args = null, &$output = null) {
		if (isset($args[0]) && ($args[0] == 'config')) {
			if (isset($args[2]) && !empty($args[2])) {
				$store_id = $args[2];
			} else {
				$store_id = 0;
			}

			$this->model_user_user_activity->addActivity('update_store', $store_id);
		}
	}

	public function addActivityDeleteProduct(&$route, &$args = null, &$output = null) {
		if (isset($args[0]) && !empty($args[0])) {
			$this->model_user_user_activity->addActivity('delete_product', $args[0]);
		}
	}

	public function addActivityDeleteCategory(&$route, &$args = null, &$output = null) {
		if (isset($args[0]) && !empty($args[0])) {
			$this->model_user_user_activity->addActivity('delete_category', $args[0]);
		}
	}

	public function addActivityDeleteStore(&$route, &$args = null, &$output = null) {
		if (isset($args[0]) && !empty($args[0])) {
			$this->model_user_user_activity->addActivity('delete_store', $args[0]);
		}
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				'start'        => 0,
				'limit'        => 5
			);

			$results = $this->model_user_user_activity->getUsers($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'user_id'       => $result['user_id'],
					'user_group_id' => $result['user_group_id'],
					'username'      => $result['username'],
					'name'          => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'firstname'     => $result['firstname'],
					'lastname'      => $result['lastname'],
					'email'         => $result['email'],
					'image'         => $result['image'],
					'ip'            => $result['ip'],
					'status'        => $result['status'],
					'date_added'    => date($this->language->get('date_format_short'), strtotime($result['date_added']))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function uninstall() {
		return $this->model_user_user_activity->uninstall();
	}
}