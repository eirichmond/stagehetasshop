<?php

class ControllerPaymentSagepayV3 extends Controller {

	protected function index() {
		$this->language->load('payment/sagepay_v3');

		$this->data['button_confirm'] = $this->language->get('button_confirm');

		if ($this->config->get('sagepay_v3_test') == 'live') {
			$this->data['action'] = 'https://live.sagepay.com/gateway/service/vspform-register.vsp';
			$data['VPSProtocol'] = '3.00';
			$this->data['VPSProtocol'] = '3.00';
			$password = $this->config->get('sagepay_v3_password');
		} elseif ($this->config->get('sagepay_v3_test') == 'test') {
			$this->data['action'] = 'https://test.sagepay.com/gateway/service/vspform-register.vsp';
			$data['VPSProtocol'] = '3.00';
			$this->data['VPSProtocol'] = '3.00';
			$password = $this->config->get('sagepay_v3_password');
		} elseif ($this->config->get('sagepay_v3_test') == 'sim') {
			$this->data['action'] = 'https://test.sagepay.com/simulator/vspformgateway.asp';
			$data['VPSProtocol'] = '2.23';
			$this->data['VPSProtocol'] = '2.23';
			$password = 'Fdr4tKc0e3PKrp3l';
		}

		$vendor = $this->config->get('sagepay_v3_vendor');

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$data = array();

		$data['VendorTxCode'] = $this->session->data['order_id'];
		$data['ReferrerID'] = 'E511AF91-E4A0-42DE-80B0-09C981A3FB61';
		$data['Amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], false, false);
		$data['Currency'] = $order_info['currency_code'];
		$data['Description'] = sprintf($this->language->get('text_description'), date($this->language->get('date_format_short')), $this->session->data['order_id']);
		$data['SuccessURL'] = str_replace('&amp;', '&', $this->url->link('payment/sagepay_v3/success', 'order_id=' . $this->session->data['order_id']));
		$data['FailureURL'] = str_replace('&amp;', '&', $this->url->link('checkout/checkout', '', 'SSL'));

		$data['CustomerName'] = html_entity_decode($order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
		$data['SendEMail'] = '1';
		$data['CustomerEMail'] = $order_info['email'];
		$data['VendorEMail'] = $this->config->get('config_email');

		$data['BillingFirstnames'] = $order_info['payment_firstname'];
		$data['BillingSurname'] = $order_info['payment_lastname'];
		$data['BillingAddress1'] = $order_info['payment_address_1'];

		if ($order_info['payment_address_2']) {
			$data['BillingAddress2'] = $order_info['payment_address_2'];
		}

		$data['BillingCity'] = $order_info['payment_city'];
		$data['BillingPostCode'] = $order_info['payment_postcode'];
		$data['BillingCountry'] = $order_info['payment_iso_code_2'];

		if ($order_info['payment_iso_code_2'] == 'US') {
			$data['BillingState'] = $order_info['payment_zone_code'];
		}

		$data['BillingPhone'] = $order_info['telephone'];

		if ($this->cart->hasShipping()) {
			$data['DeliveryFirstnames'] = $order_info['shipping_firstname'];
			$data['DeliverySurname'] = $order_info['shipping_lastname'];
			$data['DeliveryAddress1'] = $order_info['shipping_address_1'];

			if ($order_info['shipping_address_2']) {
				$data['DeliveryAddress2'] = $order_info['shipping_address_2'];
			}

			$data['DeliveryCity'] = $order_info['shipping_city'];
			$data['DeliveryPostCode'] = $order_info['shipping_postcode'];
			$data['DeliveryCountry'] = $order_info['shipping_iso_code_2'];

			if ($order_info['shipping_iso_code_2'] == 'US') {
				$data['DeliveryState'] = $order_info['shipping_zone_code'];
			}

			$data['DeliveryPhone'] = $order_info['telephone'];
		} else {
			$data['DeliveryFirstnames'] = $order_info['payment_firstname'];
			$data['DeliverySurname'] = $order_info['payment_lastname'];
			$data['DeliveryAddress1'] = $order_info['payment_address_1'];

			if ($order_info['payment_address_2']) {
				$data['DeliveryAddress2'] = $order_info['payment_address_2'];
			}

			$data['DeliveryCity'] = $order_info['payment_city'];
			$data['DeliveryPostCode'] = $order_info['payment_postcode'];
			$data['DeliveryCountry'] = $order_info['payment_iso_code_2'];

			if ($order_info['payment_iso_code_2'] == 'US') {
				$data['DeliveryState'] = $order_info['payment_zone_code'];
			}

			$data['DeliveryPhone'] = $order_info['telephone'];
		}

		$data['AllowGiftAid'] = '0';

		if (!$this->config->get('sagepay_v3_transaction')) {
			$data['ApplyAVSCV2'] = '0';
		}

		$data['Apply3DSecure'] = '0';

		$this->data['transaction'] = $this->config->get('sagepay_v3_transaction');
		$this->data['vendor'] = $vendor;

		$crypt_data = array();

		foreach ($data as $key => $value) {
			$crypt_data[$key] = $value;
		}

		$queryStr = $this->arrayToQueryString($crypt_data);

		$this->data['crypt'] = $this->encryptAes($queryStr, $password);

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/sagepay_v3.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/sagepay_v3.tpl';
		} else {
			$this->template = 'default/template/payment/sagepay_v3.tpl';
		}

		$this->render();
	}

	public function success() {

		if (isset($this->request->get['crypt'])) {

			if ($this->config->get('sagepay_v3_test') == 'sim') {
				$password = 'Fdr4tKc0e3PKrp3l';
			} else {
				$password = $this->config->get('sagepay_v3_password');
			}

			$decrypt = $this->decryptAes($this->request->get['crypt'], $password);

			$data = $this->queryStringToArray($decrypt);

			if ($data && is_array($data)) {
				$this->load->model('checkout/order');

				$this->model_checkout_order->confirm($this->request->get['order_id'], $this->config->get('config_order_status_id'));

				$message = '';

				if (isset($data['VPSTxId'])) {
					$message .= 'VPSTxId: ' . $data['VPSTxId'] . "\n";
				}

				if (isset($data['TxAuthNo'])) {
					$message .= 'TxAuthNo: ' . $data['TxAuthNo'] . "\n";
				}

				if (isset($data['AVSCV2'])) {
					$message .= 'AVSCV2: ' . $data['AVSCV2'] . "\n";
				}

				if (isset($data['AddressResult'])) {
					$message .= 'AddressResult: ' . $data['AddressResult'] . "\n";
				}

				if (isset($data['PostCodeResult'])) {
					$message .= 'PostCodeResult: ' . $data['PostCodeResult'] . "\n";
				}

				if (isset($data['CV2Result'])) {
					$message .= 'CV2Result: ' . $data['CV2Result'] . "\n";
				}

				if (isset($data['3DSecureStatus'])) {
					$message .= '3DSecureStatus: ' . $data['3DSecureStatus'] . "\n";
				}

				if (isset($data['CAVV'])) {
					$message .= 'CAVV: ' . $data['CAVV'] . "\n";
				}

				if (isset($data['CardType'])) {
					$message .= 'CardType: ' . $data['CardType'] . "\n";
				}

				if (isset($data['Last4Digits'])) {
					$message .= 'Last4Digits: ' . $data['Last4Digits'] . "\n";
				}

				if ($data['Status'] == 'OK') {
					$this->model_checkout_order->update($this->request->get['order_id'], $this->config->get('sagepay_v3_order_status_id'), $message, false);
				} else {
					$this->model_checkout_order->update($this->request->get['order_id'], $this->config->get('config_order_status_id'), $message, false);
				}

				$this->redirect($this->url->link('checkout/success'));
			}
		}
	}

	public function logger($message) {
			$log = new Log('sagepay_v3.log');
			$log->write($message);
	}
	/**
	 * PHP's mcrypt does not have built in PKCS5 Padding, so we use this.
	 *
	 * @param string $input The input string.
	 *
	 * @return string The string with padding.
	 */
	protected function addPKCS5Padding($input) {
		$blockSize = 16;
		$padd = "";

		// Pad input to an even block size boundary.
		$length = $blockSize - (strlen($input) % $blockSize);
		for ($i = 1; $i <= $length; $i++) {
			$padd .= chr($length);
		}

		return $input . $padd;
	}

	/**
	 * Remove PKCS5 Padding from a string.
	 *
	 * @param string $input The decrypted string.
	 *
	 * @return string String without the padding.
	 * @throws SagepayApiException
	 */
	protected function removePKCS5Padding($input) {
		$blockSize = 16;
		$padChar = ord($input[strlen($input) - 1]);

		/* Check for PadChar is less then Block size */
		if ($padChar > $blockSize) {
			throw new Exception('Invalid encryption string');
		}
		/* Check by padding by character mask */
		if (strspn($input, chr($padChar), strlen($input) - $padChar) != $padChar) {
			throw new Exception('Invalid encryption string');
		}

		$unpadded = substr($input, 0, (-1) * $padChar);
		/* Chech result for printable characters */
		if (preg_match('/[[:^print:]]/', $unpadded)) {
			throw new Exception('Invalid encryption string');
		}
		return $unpadded;
	}

	/**
	 * Encrypt a string ready to send to SagePay using encryption key.
	 *
	 * @param  string  $string  The unencrypyted string.
	 * @param  string  $key     The encryption key.
	 *
	 * @return string The encrypted string.
	 */
	public function encryptAes($string, $key) {
		// AES encryption, CBC blocking with PKCS5 padding then HEX encoding.
		// Add PKCS5 padding to the text to be encypted.
		$string = $this->addPKCS5Padding($string);

		// Perform encryption with PHP's MCRYPT module.
		$crypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $string, MCRYPT_MODE_CBC, $key);

		// Perform hex encoding and return.
		return "@" . strtoupper(bin2hex($crypt));
	}

	/**
	 * Decode a returned string from SagePay.
	 *
	 * @param string $strIn         The encrypted String.
	 * @param string $password      The encyption password used to encrypt the string.
	 *
	 * @return string The unecrypted string.
	 * @throws SagepayApiException
	 */
	public function decryptAes($strIn, $password) {
		// HEX decoding then AES decryption, CBC blocking with PKCS5 padding.
		// Use initialization vector (IV) set from $str_encryption_password.
		$strInitVector = $password;

		// Remove the first char which is @ to flag this is AES encrypted and HEX decoding.
		$hex = substr($strIn, 1);

		// Throw exception if string is malformed
		if (!preg_match('/^[0-9a-fA-F]+$/', $hex)) {
			throw new Exception('Invalid encryption string');
		}
		$strIn = pack('H*', $hex);

		// Perform decryption with PHP's MCRYPT module.
		$string = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $password, $strIn, MCRYPT_MODE_CBC, $strInitVector);
		return $this->removePKCS5Padding($string);
	}

	/**
	 * Convert a data array to a query string ready to post.
	 *
	 * @param  array   $data        The data array.
	 * @param  string  $delimeter   Delimiter used in query string
	 * @param  boolean $urlencoded  If true encode the final query string
	 *
	 * @return string The array as a string.
	 */
	static public function arrayToQueryString(array $data, $delimiter = '&', $urlencoded = false) {
		$queryString = '';
		$delimiterLength = strlen($delimiter);

		// Parse each value pairs and concate to query string
		foreach ($data as $name => $value) {
			// Apply urlencode if it is required
			if ($urlencoded) {
				$value = urlencode($value);
			}
			$queryString .= $name . '=' . $value . $delimiter;
		}

		// remove the last delimiter
		return substr($queryString, 0, -1 * $delimiterLength);
	}

	/**
	 * Convert string to data array.
	 *
	 * @param string  $data       Query string
	 * @param string  $delimeter  Delimiter used in query string
	 *
	 * @return array
	 */
	static public function queryStringToArray($data, $delimeter = "&") {
		// Explode query by delimiter
		$pairs = explode($delimeter, $data);
		$queryArray = array();

		// Explode pairs by "="
		foreach ($pairs as $pair) {
			$keyValue = explode('=', $pair);

			// Use first value as key
			$key = array_shift($keyValue);

			// Implode others as value for $key
			$queryArray[$key] = implode('=', $keyValue);
		}
		return $queryArray;
	}

}

?>