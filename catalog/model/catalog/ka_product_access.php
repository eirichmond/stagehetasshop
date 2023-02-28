<?php
/*
	Project : Restricted Product Access
	Author  : karapuz <support@ka-station.com>

	Version : 2 ($Revision: 12 $)

*/
class ModelCatalogKaProductAccess extends Model {		

	public function isInstalled() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE code = 'ka_product_access'");
		if (empty($query->num_rows)) {
			return false;
		}
		return true;
	}

	public function isProductAccessDenied($product_id) {

		$query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		if (empty($query->row)) {
			return false;
		}

		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$query = $this->db->query("SELECT customer_group_id FROM " . DB_PREFIX . "product_customer_group pcg
			WHERE product_id = '" . (int)$product_id . "'");

		// no records in the product customer group table. Product is available to all users
		//
		if (empty($query->row)) {
			return false;
		}

		foreach ($query->rows as $row) {
			if ($row['customer_group_id'] == $customer_group_id) {
				return false;
			}
		}
		
		return true;
	}


	public function isCategoryAccessDenied($category_id) {

		$query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "'");
		if (empty($query->row)) {
			return false;
		}

		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$query = $this->db->query("SELECT customer_group_id FROM " . DB_PREFIX . "category_customer_group ccg
			WHERE category_id = '" . (int)$category_id . "'");

		// no records in the category customer group table. Category is available to all users
		//
		if (empty($query->row)) {
			return false;
		}

		foreach ($query->rows as $row) {
			if ($row['customer_group_id'] == $customer_group_id) {
				return false;
			}
		}
		
		return true;
	}
	
	public function getPurchaseUrl($product_id) {
	
		$product_id = intval($product_id);
		$val = $this->config->get('ka_purchase_url');
		
		return $val;
	}
}
?>