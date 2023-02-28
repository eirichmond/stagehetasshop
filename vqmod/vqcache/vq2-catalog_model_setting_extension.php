<?php
class ModelSettingExtension extends Model {
	function getExtensions($type) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->db->escape($type) . "'");

		return $query->rows;
	}
	function getCustomerGroupPaymentMethods($customer_group_id) {
    	$query = $this->db->query("SELECT payment_methods FROM " . DB_PREFIX . "customer_group WHERE customer_group_id = '" . (int) $customer_group_id . "'");
		$payment_methods = unserialize($query->row['payment_methods']);
		if (is_array($payment_methods)) {
			return $payment_methods;
		} else {
			return array();
		}
	}

	function getCustomerGroupShippingMethods($customer_group_id) {
        $query = $this->db->query("SELECT shipping_methods FROM " . DB_PREFIX . "customer_group WHERE customer_group_id = '" . (int) $customer_group_id . "'");
		$shipping_methods =  unserialize($query->row['shipping_methods']);
		if (is_array($shipping_methods)) {
			return $shipping_methods;
		} else {
			return array();
		}
	}
}
?>