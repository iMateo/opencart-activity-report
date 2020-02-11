<?php
	$this->load->model('extension/event');

	$this->model_extension_event->addEvent('UserActivityAddUser', 'admin/model/user/user/addUser/after', 'user/user_activity/addActivityAddUser');
	$this->model_extension_event->addEvent('UserActivityEditUser', 'admin/model/user/user/editUser/after', 'user/user_activity/addActivityEditUser');
	$this->model_extension_event->addEvent('UserActivityDeleteUser', 'admin/model/user/user/deleteUser/after', 'user/user_activity/addActivityDeleteUser');
	$this->model_extension_event->addEvent('UserActivityForgottenUser', 'admin/model/user/user/editCode/after', 'user/user_activity/addActivityForgottenUser');
	$this->model_extension_event->addEvent('UserActivityResetUser', 'admin/model/user/user/editPassword/after', 'user/user_activity/addActivityResetUser');
	$this->model_extension_event->addEvent('UserActivityLoginUser', 'admin/controller/common/login/after', 'user/user_activity/addActivityLoginUser');

	$this->model_extension_event->addEvent('UserActivityAddProduct', 'admin/model/catalog/product/addProduct/after', 'user/user_activity/addActivityAddProduct');
	$this->model_extension_event->addEvent('UserActivityAddCategory', 'admin/model/catalog/category/addCategory/after', 'user/user_activity/addActivityAddCategory');
	$this->model_extension_event->addEvent('UserActivityAddStore', 'admin/model/setting/store/addStore/after', 'user/user_activity/addActivityAddStore');

	$this->model_extension_event->addEvent('UserActivityEditProduct', 'admin/model/catalog/product/editProduct/after', 'user/user_activity/addActivityEditProduct');
	$this->model_extension_event->addEvent('UserActivityEditCategory', 'admin/model/catalog/category/editCategory/after', 'user/user_activity/addActivityEditCategory');
	$this->model_extension_event->addEvent('UserActivityEditStore', 'admin/model/setting/store/editStore/after', 'user/user_activity/addActivityEditStore');

	$this->model_extension_event->addEvent('UserActivityDeleteProduct', 'admin/model/catalog/product/deleteProduct/after', 'user/user_activity/addActivityDeleteProduct');
	$this->model_extension_event->addEvent('UserActivityDeleteCategory', 'admin/model/catalog/category/deleteCategory/after', 'user/user_activity/addActivityDeleteCategory');
	$this->model_extension_event->addEvent('UserActivityDeleteStore', 'admin/model/setting/store/deleteStore/after', 'user/user_activity/addActivityDeleteStore');

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

	$this->load->model('user/user_group');
	$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'user/user_activity');
	$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'user/user_activity');