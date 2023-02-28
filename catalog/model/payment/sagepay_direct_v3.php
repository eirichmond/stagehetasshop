<?php

class ModelPaymentSagePayDirectV3 extends Model {

	public function getMethod($address, $total) {
		$this->load->language('payment/sagepay_direct_v3');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('sagepay_direct_v3_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if ($this->config->get('sagepay_direct_v3_total') > 0 && $this->config->get('sagepay_direct_v3_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('sagepay_direct_v3_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$method_data = array(
				'code' => 'sagepay_direct_v3',
				'title' => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('sagepay_direct_v3_sort_order')
			);
		}

		return $method_data;
	}

	public function getCards($customer_id) {

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sagepay_direct_v3_card WHERE customer_id = '" . (int)$customer_id . "'");

		$card_data = array();

		$this->load->model('account/address');

		foreach ($query->rows as $row) {

			$card_data[] = array(
				'card_id' => $row['card_id'],
				'customer_id' => $row['customer_id'],
				'token' => $row['token'],
				'digits' => '**** ' . $row['digits'],
				'expiry' => $row['expiry'],
				'type' => $row['type'],
			);
		}
		return $card_data;
	}

	public function addFullCard($order_id, $card_data) {
		$this->db->query("INSERT into " . DB_PREFIX . "sagepay_direct_v3_card SET customer_id = '" . $this->db->escape($card_data['customer_id']) . "', order_id = '" . $this->db->escape($order_id) . "', digits = '" . $this->db->escape($card_data['Last4Digits']) . "', expiry = '" . $this->db->escape($card_data['ExpiryDate']) . "', type = '" . $this->db->escape($card_data['CardType']) . "', token = '" . $this->db->escape($card_data['Token']) . "'");
	}

	public function addCard($order_id, $card_data) {
		$this->db->query("INSERT into " . DB_PREFIX . "sagepay_direct_v3_card SET customer_id = '" . $this->db->escape($card_data['customer_id']) . "', order_id = '" . $this->db->escape($order_id) . "', digits = '" . $this->db->escape($card_data['Last4Digits']) . "', expiry = '" . $this->db->escape($card_data['ExpiryDate']) . "', type = '" . $this->db->escape($card_data['CardType']) . "'");
	}

	public function updateCard($order_id, $token) {
		$this->db->query("UPDATE " . DB_PREFIX . "sagepay_direct_v3_card SET token = '" . $this->db->escape($token) . "' WHERE order_id = '" . (int)$order_id . "'");
	}

	public function deleteCard($order_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "sagepay_direct_v3_card WHERE order_id = '" . (int)$order_id . "'");
	}

	public function addFullOrder($order_info, $data, $payment_data) {
		if (!isset($data['TxAuthNo'])) {
				$data['TxAuthNo'] = '';
			}
		
		$this->db->query("INSERT INTO `" . DB_PREFIX . "sagepay_direct_v3_order` SET `order_id` = '" . (int)$order_info['order_id'] . "', `VPSTxId` = '" . $this->db->escape($data['VPSTxId']) . "', `VendorTxCode` = '" . $this->db->escape($payment_data['VendorTxCode']) . "', `SecurityKey` = '" . $this->db->escape($data['SecurityKey']) . "', `TxAuthNo` = '" . $this->db->escape($data['TxAuthNo']) . "', `created` = now(), `modified` = now(), `currency_code` = '" . $this->db->escape($payment_data['Currency']) . "', `total` = '" . $this->currency->format($payment_data['Amount'], $payment_data['Currency'], false, false) . "'");

		return $this->db->getLastId();
	}

	public function addOrder($order_id, $payment_data) {		
		$this->db->query("INSERT INTO `" . DB_PREFIX . "sagepay_direct_v3_order` SET `order_id` = '" . (int)$order_id . "', `VendorTxCode` = '" . $this->db->escape($payment_data['VendorTxCode']) . "',`created` = now(), `modified` = now(), `currency_code` = '" . $this->db->escape($payment_data['Currency']) . "', `total` = '" . $this->currency->format($payment_data['Amount'], $payment_data['Currency'], false, false) . "'");
	}

	public function getOrder($order_id) {
		$qry = $this->db->query("SELECT * FROM `" . DB_PREFIX . "sagepay_direct_v3_order` WHERE `order_id` = '" . (int)$order_id . "' LIMIT 1");

		if ($qry->num_rows) {
			$order = $qry->row;
			$order['transactions'] = $this->getTransactions($order['sagepay_direct_v3_order_id']);

			return $order;
		} else {
			return false;
		}
	}

	public function updateOrder($order_info, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "sagepay_direct_v3_order` SET `SecurityKey` = '" . $this->db->escape($data['SecurityKey']) . "',  `VPSTxId` = '" . $this->db->escape($data['VPSTxId']) . "', `TxAuthNo` = '" . $this->db->escape($data['TxAuthNo']) . "' WHERE `order_id` = '" . (int)$order_info['order_id'] . "'");

		return $this->db->getLastId();
	}

	public function deleteOrder($VendorTxCode) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "sagepay_direct_v3_order` WHERE order_id = '" . $VendorTxCode . "'");
	}

	public function addTransaction($sagepay_direct_v3_order_id, $type, $order_info) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "sagepay_direct_v3_order_transaction` SET `sagepay_direct_v3_order_id` = '" . (int)$sagepay_direct_v3_order_id . "', `created` = now(), `type` = '" . $this->db->escape($type) . "', `amount` = '" . $this->currency->format($order_info['total'], $order_info['currency_code'], false, false) . "'");
}

	private function getTransactions($sagepay_direct_v3_order_id) {
		$qry = $this->db->query("SELECT * FROM `" . DB_PREFIX . "sagepay_direct_v3_order_transaction` WHERE `sagepay_direct_v3_order_id` = '" . (int)$sagepay_direct_v3_order_id . "'");

		if ($qry->num_rows) {
			return $qry->rows;
		} else {
			return false;
		}
	}
	public function sendCurl($url, $payment_data, $i = null) {
		$curl = curl_init($url);

		curl_setopt($curl, CURLOPT_PORT, 443);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($payment_data));

		$response = curl_exec($curl);

		curl_close($curl);

		$response_info = explode(chr(10), $response);

		foreach ($response_info as $string) {
			if (strpos($string, '=') && isset($i)) {
				$parts = explode('=', $string, 2);
				$data['RepeatResponseData_' . $i][trim($parts[0])] = trim($parts[1]);
			} elseif (strpos($string, '=')) {
				$parts = explode('=', $string, 2);
				$data[trim($parts[0])] = trim($parts[1]);
			}
		}
		return $data;
	}

	public function logger($message) {
		if ($this->config->get('sagepay_direct_v3_debug') == 1) {
			$log = new Log('sagepay_direct_v3.log');
			$log->write($message);
		}
	}
}
