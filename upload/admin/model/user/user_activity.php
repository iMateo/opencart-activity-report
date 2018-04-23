<?php
class ModelUserUserActivity extends Model {

	public function addActivity($key, $data) {
		if (isset($data['user_id'])) {
			$user_id = $data['user_id'];
		} else {
			$user_id = 0;
		}

		$this->db->query("INSERT INTO `" . DB_PREFIX . "user_activity` SET `user_id` = '" . (int)$user_id . "', `key` = '" . $this->db->escape($key) . "', `data` = '" . $this->db->escape(json_encode($data)) . "', `ip` = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', `date_added` = NOW()");
	}

	public function getTotalUserActivities($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "user_activity` ua LEFT JOIN " . DB_PREFIX . "user u ON (ua.user_id = u.user_id)";

		$implode = array();

		if (!empty($data['filter_date_start'])) {
			$implode[] = "DATE(ua.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$implode[] = "DATE(ua.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		//if (!empty($data['filter_username'])) {
		//	$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "'";
		//}

		if (!empty($data['filter_ip'])) {
			$implode[] = "ua.ip LIKE '" . $this->db->escape($data['filter_ip']) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

public function getUserActivities($data = array()) {
		$sql = "SELECT ua.user_activity_id, ua.user_id, ua.key, ua.data, ua.ip, ua.date_added FROM " . DB_PREFIX . "user_activity ua LEFT JOIN " . DB_PREFIX . "user u ON (ua.user_id = u.user_id)";

		$implode = array();

		if (!empty($data['filter_date_start'])) {
			$implode[] = "DATE(ua.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$implode[] = "DATE(ua.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		//if (!empty($data['filter_customer'])) {
		//	$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "'";
		//		}

		if (!empty($data['filter_ip'])) {
			$implode[] = "ua.ip LIKE '" . $this->db->escape($data['filter_ip']) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sql .= " ORDER BY ua.date_added DESC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}
	
	public function getUser($user_id) {
		$query = $this->db->query("SELECT *, (SELECT ug.name FROM `" . DB_PREFIX . "user_group` ug WHERE ug.user_group_id = u.user_group_id) AS user_group FROM `" . DB_PREFIX . "user` u WHERE u.user_id = '" . (int)$user_id . "'");

		return $query->row;
	}

	public function getUserByUsername($username) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "user` WHERE username = '" . $this->db->escape($username) . "'");

		return $query->row;
	}

	public function getUserByEmail($email) {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "user` WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function getUserByCode($code) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "user` WHERE code = '" . $this->db->escape($code) . "' AND code != ''");

		return $query->row;
	}


	public function getTotalUsers() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "user`");

		return $query->row['total'];
	}

	public function getTotalUsersByGroupId($user_group_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "user` WHERE user_group_id = '" . (int)$user_group_id . "'");

		return $query->row['total'];
	}

	public function getTotalUsersByEmail($email) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "user` WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row['total'];
	}

	public function install() {
		$this->load->model('extension/event');

		$this->model_extension_event->addEvent('user_activity', 'admin/model/setting/setting/editSetting/after', 'user/user_activity/addActivityEditSetting');
		
		$this->model_extension_event->addEvent('user_activity', 'admin/model/catalog/product/editProduct/after', 'user/user_activity/addActivityEditProduct');

		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "user_activity` (
			  	`user_activity_id` int(11) NOT NULL AUTO_INCREMENT,
  				`user_id` int(11) NOT NULL,
  				`key` varchar(64) NOT NULL,
  				`data` text NOT NULL,
  				`ip` varchar(40) NOT NULL,
  				`date_added` datetime NOT NULL,
				PRIMARY KEY (`user_activity_id`)
		) DEFAULT COLLATE=utf8_general_ci;");
	}

	public function uninstall() {
		$this->load->model('extension/event');

		$this->model_extension_event->deleteEvent('user_activity_login', 'admin/controller/common/login/index/after', 'model/user/user_activity/addActivity');

		$this->model_extension_event->deleteEvent('user_activity', 'admin/controller/common/login/index/after', 'admin/controller/user/user_activity/addActivity');
		
		$this->model_extension_event->deleteEvent('user_activity', 'admin/model/catalog/product/editProduct/after', 'admin/controller/user/user_activity/addActivity');		
	}
}