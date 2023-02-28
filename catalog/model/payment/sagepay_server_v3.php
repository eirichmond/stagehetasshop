<?php
class ModelPaymentSagePayServerV3 extends Model {
	public function getMethod($address, $total) {
		$this->load->language('payment/sagepay_server_v3');

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE geo_zone_id = '" . (int)$this->config->get('sagepay_server_v3_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if ($this->config->get('sagepay_server_v3_total') > 0 && $this->config->get('sagepay_server_v3_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('sagepay_server_v3_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$method_data = array(
				'code' => 'sagepay_server_v3',
				'title' => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('sagepay_server_v3_sort_order')
			);
		}

		return $method_data;
	}

	public function getCards($customer_id) {

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "sagepay_server_v3_card` WHERE customer_id = '" . (int)$customer_id . "'");

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

	public function addCard($data) {
		$this->db->query("INSERT into `" . DB_PREFIX . "sagepay_server_v3_card` SET customer_id = '" . $this->db->escape($data['customer_id']) . "', token = '" . $this->db->escape($data['Token']) . "', digits = '" . $this->db->escape($data['Last4Digits']) . "', expiry = '" . $this->db->escape($data['ExpiryDate']) . "', type = '" . $this->db->escape($data['CardType']) . "'");
	}

	public function addOrder($order_info) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "sagepay_server_v3_order` SET `order_id` = '" . (int)$order_info['order_id'] . "', `VPSTxId` = '" . $this->db->escape($order_info['VPSTxId']) . "',  `VendorTxCode` = '" . $this->db->escape($order_info['VendorTxCode']) . "', `SecurityKey` = '" . $this->db->escape($order_info['SecurityKey']) . "', `created` = now(), `modified` = now(), `currency_code` = '" . $this->db->escape($order_info['currency_code']) . "', `total` = '" . $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) . "'");
	}

	public function getOrder($order_id) {
		$qry = $this->db->query("SELECT * FROM `" . DB_PREFIX . "sagepay_server_v3_order` WHERE `order_id` = '" . (int)$order_id . "' LIMIT 1");

		if ($qry->num_rows) {
			$order = $qry->row;
			$order['transactions'] = $this->getTransactions($order['sagepay_server_v3_order_id']);

			return $order;
		} else {
			return false;
		}
	}

	public function updateOrder($order_info, $VPSTxId, $TxAuthNo) {
		$this->db->query("UPDATE `" . DB_PREFIX . "sagepay_server_v3_order` SET `VPSTxId` = '" . $this->db->escape($VPSTxId) . "', `TxAuthNo` = '" . $this->db->escape($TxAuthNo) . "' WHERE `order_id` = '" . (int)$order_info['order_id'] . "'");
	}

	public function deleteOrder($order_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "sagepay_server_v3_order` WHERE order_id = '" . (int)$order_id . "'");
	}

	public function addTransaction($sagepay_server_v3_order_id, $type, $order_info) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "sagepay_server_v3_order_transaction` SET `sagepay_server_v3_order_id` = '" . (int)$sagepay_server_v3_order_id . "', `created` = now(), `type` = '" . $this->db->escape($type) . "', `amount` = '" . $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) . "'");
	}

	private function getTransactions($sagepay_server_v3_order_id) {
		$qry = $this->db->query("SELECT * FROM `" . DB_PREFIX . "sagepay_server_v3_order_transaction` WHERE `sagepay_server_v3_order_id` = '" . (int)$sagepay_server_v3_order_id . "'");

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
		if ($this->config->get('sagepay_server_v3_debug') == 1) {
			$log = new Log('sagepay_server_v3.log');
			$log->write($message);
		}
	}

}
