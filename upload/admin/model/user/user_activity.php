<?php
class ModelUserUserActivity extends Model {
	public function addActivity($key, $data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "user_activity` SET `user_id` = '" . (int)$this->user->getId() . "', `key` = '" . $this->db->escape($key) . "', `data` = '" . $this->db->escape($data) . "', `ip` = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', `date_added` = NOW()");
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

		if (!empty($data['filter_user'])) {
			$implode[] = "CONCAT(u.firstname, ' ', u.lastname) LIKE '" . $this->db->escape($data['filter_user']) . "'";
		}

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

		if (!empty($data['filter_user'])) {
			$implode[] = "CONCAT(u.firstname, ' ', u.lastname) LIKE '" . $this->db->escape($data['filter_user']) . "'";
		}

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

	public function getUsers($data = array()) {
		$sql = "SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM `" . DB_PREFIX . "user`";

		if (!empty($data['filter_user'])) {
			$implode[] = "WHERE CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_user']) . "%'";
		}

		$sort_data = array(
			'username',
			'status',
			'date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY username";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

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

	public function uninstall() {
		$this->load->model('extension/event');

		$this->model_extension_event->deleteEvent('user_activity_login', 'admin/controller/common/login/index/after', 'model/user/user_activity/addActivity');

		$this->model_extension_event->deleteEvent('user_activity', 'admin/controller/common/login/index/after', 'admin/controller/user/user_activity/addActivity');
		
		$this->model_extension_event->deleteEvent('user_activity', 'admin/model/catalog/product/editProduct/after', 'admin/controller/user/user_activity/addActivity');
	}
}