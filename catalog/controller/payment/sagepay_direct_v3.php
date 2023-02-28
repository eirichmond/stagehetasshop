<?php
class ControllerPaymentSagepayDirectV3 extends Controller {
	public function index() {
		$this->language->load('payment/sagepay_direct_v3');

		$this->data['text_credit_card'] = $this->language->get('text_credit_card');
		$this->data['text_loading'] = $this->language->get('text_loading');
		$this->data['text_card_type'] = $this->language->get('text_card_type');
		$this->data['text_card_name'] = $this->language->get('text_card_name');
		$this->data['text_card_digits'] = $this->language->get('text_card_digits');
		$this->data['text_card_expiry'] = $this->language->get('text_card_expiry');

		$this->data['entry_card_existing'] = $this->language->get('entry_card_existing');
		$this->data['entry_card_new'] = $this->language->get('entry_card_new');
		$this->data['entry_card_save'] = $this->language->get('entry_card_save');
		$this->data['entry_cc_owner'] = $this->language->get('entry_cc_owner');
		$this->data['entry_cc_type'] = $this->language->get('entry_cc_type');
		$this->data['entry_cc_number'] = $this->language->get('entry_cc_number');
		$this->data['entry_cc_start_date'] = $this->language->get('entry_cc_start_date');
		$this->data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
		$this->data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');
		$this->data['entry_cc_issue'] = $this->language->get('entry_cc_issue');
		$this->data['entry_cc_choice'] = $this->language->get('entry_cc_choice');


		$this->data['help_start_date'] = $this->language->get('help_start_date');
		$this->data['help_issue'] = $this->language->get('help_issue');

		$this->data['button_confirm'] = $this->language->get('button_confirm');

		$this->data['cards'] = array();

		$this->data['cards'][] = array(
			'text' => 'Visa',
			'value' => 'VISA'
		);

		$this->data['cards'][] = array(
			'text' => 'MasterCard',
			'value' => 'MC'
		);

		$this->data['cards'][] = array(
			'text' => 'Visa Delta/Debit',
			'value' => 'DELTA'
		);

		$this->data['cards'][] = array(
			'text' => 'Solo',
			'value' => 'SOLO'
		);

		$this->data['cards'][] = array(
			'text' => 'Maestro',
			'value' => 'MAESTRO'
		);

		$this->data['cards'][] = array(
			'text' => 'Visa Electron UK Debit',
			'value' => 'UKE'
		);

		$this->data['cards'][] = array(
			'text' => 'American Express',
			'value' => 'AMEX'
		);

		$this->data['cards'][] = array(
			'text' => 'Diners Club',
			'value' => 'DC'
		);

		$this->data['cards'][] = array(
			'text' => 'Japan Credit Bureau',
			'value' => 'JCB'
		);

		$this->data['months'] = array();

		for ($i = 1; $i <= 12; $i++) {
			$this->data['months'][] = array(
				'text' => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)),
				'value' => sprintf('%02d', $i)
			);
		}

		$today = getdate();

		$this->data['year_valid'] = array();

		for ($i = $today['year'] - 10; $i < $today['year'] + 1; $i++) {
			$this->data['year_valid'][] = array(
				'text' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
			);
		}

		$this->data['year_expire'] = array();

		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$this->data['year_expire'][] = array(
				'text' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
			);
		}

		if ($this->config->get('sagepay_direct_v3_card') == '1') {
			$this->data['sagepay_direct_v3_card'] = true;
		} else {
			$this->data['sagepay_direct_v3_card'] = false;
		}

		$this->data['existing_cards'] = array();
		if ($this->customer->isLogged() && $this->data['sagepay_direct_v3_card']) {
			$this->load->model('payment/sagepay_direct_v3');
			$this->data['existing_cards'] = $this->model_payment_sagepay_direct_v3->getCards($this->customer->getId());
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/sagepay_direct_v3.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/sagepay_direct_v3.tpl';
		} else {
			$this->template = 'default/template/payment/sagepay_direct_v3.tpl';
		}
        
        $this->render();
	}

	public function send() {
		$this->language->load('payment/sagepay_direct_v3');
		$this->load->model('checkout/order');
		$this->load->model('payment/sagepay_direct_v3');

		$payment_data = array();

		if ($this->config->get('sagepay_direct_v3_test') == 'live') {
			$url = 'https://live.sagepay.com/gateway/service/vspdirect-register.vsp';
			$payment_data['VPSProtocol'] = '3.00';
		} elseif ($this->config->get('sagepay_direct_v3_test') == 'test') {
			$url = 'https://test.sagepay.com/gateway/service/vspdirect-register.vsp';
			$payment_data['VPSProtocol'] = '3.00';
		} elseif ($this->config->get('sagepay_direct_v3_test') == 'sim') {
			$url = 'https://test.sagepay.com/Simulator/VSPDirectGateway.asp';
			$payment_data['VPSProtocol'] = '2.23';
		}

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$payment_data['ReferrerID'] = 'E511AF91-E4A0-42DE-80B0-09C981A3FB61';
		$payment_data['Vendor'] = $this->config->get('sagepay_direct_v3_vendor');
		$payment_data['VendorTxCode'] = $this->session->data['order_id'] . 'SD' . strftime("%Y%m%d%H%M%S") . mt_rand(1, 999);
		$payment_data['Amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], false, false);
		$payment_data['Currency'] = $this->currency->getCode();
		$payment_data['Description'] = substr($this->config->get('config_name'), 0, 100);
		$payment_data['TxType'] = $this->config->get('sagepay_direct_v3_transaction');

		$payment_data['CV2'] = $this->request->post['cc_cvv2'];

		if (isset($this->request->post['Token'])) {
			$payment_data['Token'] = $this->request->post['Token'];
			$payment_data['StoreToken'] = 1;
		} else {
			$payment_data['CardHolder'] = $this->request->post['cc_owner'];
			$payment_data['CardNumber'] = $this->request->post['cc_number'];
			$payment_data['ExpiryDate'] = $this->request->post['cc_expire_date_month'] . substr($this->request->post['cc_expire_date_year'], 2);
			$payment_data['CardType'] = $this->request->post['cc_type'];
			$payment_data['StartDate'] = $this->request->post['cc_start_date_month'] . substr($this->request->post['cc_start_date_year'], 2);
			$payment_data['IssueNumber'] = $this->request->post['cc_issue'];
		}

		if (isset($this->request->post['CreateToken'])) {
			$payment_data['CreateToken'] = $this->request->post['CreateToken'];
			$payment_data['StoreToken'] = 1;
		}

		$payment_data['BillingSurname'] = substr($order_info['payment_lastname'], 0, 20);
		$payment_data['BillingFirstnames'] = substr($order_info['payment_firstname'], 0, 20);
		$payment_data['BillingAddress1'] = substr($order_info['payment_address_1'], 0, 100);

		if ($order_info['payment_address_2']) {
			$payment_data['BillingAddress2'] = $order_info['payment_address_2'];
		}

		$payment_data['BillingCity'] = substr($order_info['payment_city'], 0, 40);
		$payment_data['BillingPostCode'] = substr($order_info['payment_postcode'], 0, 10);
		$payment_data['BillingCountry'] = $order_info['payment_iso_code_2'];

		if ($order_info['payment_iso_code_2'] == 'US') {
			$payment_data['BillingState'] = $order_info['payment_zone_code'];
		}

		$payment_data['BillingPhone'] = substr($order_info['telephone'], 0, 20);

		if ($this->cart->hasShipping()) {
			$payment_data['DeliverySurname'] = substr($order_info['shipping_lastname'], 0, 20);
			$payment_data['DeliveryFirstnames'] = substr($order_info['shipping_firstname'], 0, 20);
			$payment_data['DeliveryAddress1'] = substr($order_info['shipping_address_1'], 0, 100);

			if ($order_info['shipping_address_2']) {
				$payment_data['DeliveryAddress2'] = $order_info['shipping_address_2'];
			}

			$payment_data['DeliveryCity'] = substr($order_info['shipping_city'], 0, 40);
			$payment_data['DeliveryPostCode'] = substr($order_info['shipping_postcode'], 0, 10);
			$payment_data['DeliveryCountry'] = $order_info['shipping_iso_code_2'];

			if ($order_info['shipping_iso_code_2'] == 'US') {
				$payment_data['DeliveryState'] = $order_info['shipping_zone_code'];
			}

			$payment_data['CustomerName'] = substr($order_info['firstname'] . ' ' . $order_info['lastname'], 0, 100);
			$payment_data['DeliveryPhone'] = substr($order_info['telephone'], 0, 20);
		} else {
			$payment_data['DeliveryFirstnames'] = $order_info['payment_firstname'];
			$payment_data['DeliverySurname'] = $order_info['payment_lastname'];
			$payment_data['DeliveryAddress1'] = $order_info['payment_address_1'];

			if ($order_info['payment_address_2']) {
				$payment_data['DeliveryAddress2'] = $order_info['payment_address_2'];
			}

			$payment_data['DeliveryCity'] = $order_info['payment_city'];
			$payment_data['DeliveryPostCode'] = $order_info['payment_postcode'];
			$payment_data['DeliveryCountry'] = $order_info['payment_iso_code_2'];

			if ($order_info['payment_iso_code_2'] == 'US') {
				$payment_data['DeliveryState'] = $order_info['payment_zone_code'];
			}

			$payment_data['DeliveryPhone'] = $order_info['telephone'];
		}

		$payment_data['CustomerEMail'] = substr($order_info['email'], 0, 255);
		$payment_data['Apply3DSecure'] = '0';
		$payment_data['ClientIPAddress'] = $this->request->server['REMOTE_ADDR'];

		$response_data = $this->model_payment_sagepay_direct_v3->sendCurl($url, $payment_data);

		$json = array();

		if ($response_data['Status'] == '3DAUTH') {
			$json['ACSURL'] = $response_data['ACSURL'];
			$json['MD'] = $response_data['MD'];
			$json['PaReq'] = $response_data['PAReq'];
			$this->model_payment_sagepay_direct_v3->addOrder($this->session->data['order_id'], $payment_data);
			
			if (!empty($payment_data['CreateToken']) && $this->customer->isLogged()) {
				$card_data = array();
				$card_data['customer_id'] = $this->customer->getId();
				$card_data['Last4Digits'] = substr(str_replace(' ', '', $payment_data['CardNumber']), -4, 4);
				$card_data['ExpiryDate'] = $this->request->post['cc_expire_date_month'] .'/'. substr($this->request->post['cc_expire_date_year'], 2);
				$card_data['CardType'] = $payment_data['CardType'];
				$this->model_payment_sagepay_direct_v3->addCard($this->session->data['order_id'], $card_data);
			}
			
			$json['TermUrl'] = $this->url->link('payment/sagepay_direct_v3/callback', '', 'SSL');
		} elseif ($response_data['Status'] == 'OK' || $response_data['Status'] == 'AUTHENTICATED' || $response_data['Status'] == 'REGISTERED') {
			$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));

			$message = '';

			if (isset($response_data['TxAuthNo'])) {
				$message .= 'TxAuthNo: ' . $response_data['TxAuthNo'] . "\n";
			}

			if (isset($response_data['AVSCV2'])) {
				$message .= 'AVSCV2: ' . $response_data['AVSCV2'] . "\n";
			}

			if (isset($response_data['AddressResult'])) {
				$message .= 'AddressResult: ' . $response_data['AddressResult'] . "\n";
			}

			if (isset($response_data['PostCodeResult'])) {
				$message .= 'PostCodeResult: ' . $response_data['PostCodeResult'] . "\n";
			}

			if (isset($response_data['CV2Result'])) {
				$message .= 'CV2Result: ' . $response_data['CV2Result'] . "\n";
			}

			if (isset($response_data['3DSecureStatus'])) {
				$message .= '3DSecureStatus: ' . $response_data['3DSecureStatus'] . "\n";
			}

			if (isset($response_data['CAVV'])) {
				$message .= 'CAVV: ' . $response_data['CAVV'] . "\n";
			}

			$sagepay_direct_v3_order_id = $this->model_payment_sagepay_direct_v3->addFullOrder($order_info, $response_data, $payment_data);

			$this->model_payment_sagepay_direct_v3->addTransaction($sagepay_direct_v3_order_id, $this->config->get('sagepay_direct_v3_transaction'), $order_info);

			$this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('sagepay_direct_v3_order_status_id'), $message, false);

			if (!empty($response_data['Token']) && $this->customer->isLogged()) {
				$card_data = array();
				$card_data['customer_id'] = $this->customer->getId();
				$card_data['Token'] = $response_data['Token'];
				$card_data['Last4Digits'] = substr(str_replace(' ', '', $payment_data['CardNumber']), -4, 4);
				$card_data['ExpiryDate'] = $this->request->post['cc_expire_date_month'] .'/'. substr($this->request->post['cc_expire_date_year'], 2);
				$card_data['CardType'] = $payment_data['CardType'];
				$this->model_payment_sagepay_direct_v3->addFullCard($this->session->data['order_id'], $card_data);
			}

			$json['redirect'] = $this->url->link('checkout/success', '', 'SSL');
		} else {
			$json['error'] = $response_data['Status'] . ': ' . $response_data['StatusDetail'];
			$this->model_payment_sagepay_direct_v3->logger('Response data: ' . print_r($response_data['Status'] . ': ' . $response_data['StatusDetail'], 1));
		}

		$this->response->setOutput(json_encode($json));
	}

	public function callback() {

		$this->load->model('payment/sagepay_direct_v3');
		$this->language->load('payment/sagepay_direct_v3');
		$this->load->model('checkout/order');

		if (isset($this->session->data['order_id'])) {
			if ($this->config->get('sagepay_direct_v3_test') == 'live') {
				$url = 'https://live.sagepay.com/gateway/service/direct3dcallback.vsp';
			} elseif ($this->config->get('sagepay_direct_v3_test') == 'test') {
				$url = 'https://test.sagepay.com/gateway/service/direct3dcallback.vsp';
			} elseif ($this->config->get('sagepay_direct_v3_test') == 'sim') {
				$url = 'https://test.sagepay.com/Simulator/VSPDirectCallback.asp';
			}

			$response_data = $this->model_payment_sagepay_direct_v3->sendCurl($url, $this->request->post);

			if ($response_data['Status'] == 'OK' || $response_data['Status'] == 'AUTHENTICATED' || $response_data['Status'] == 'REGISTERED') {


				$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));

				$message = '';

				if (isset($response_data['TxAuthNo'])) {
					$message .= 'TxAuthNo: ' . $response_data['TxAuthNo'] . "\n";
				}

				if (isset($response_data['AVSCV2'])) {
					$message .= 'AVSCV2: ' . $response_data['AVSCV2'] . "\n";
				}

				if (isset($response_data['AddressResult'])) {
					$message .= 'AddressResult: ' . $response_data['AddressResult'] . "\n";
				}

				if (isset($response_data['PostCodeResult'])) {
					$message .= 'PostCodeResult: ' . $response_data['PostCodeResult'] . "\n";
				}

				if (isset($response_data['CV2Result'])) {
					$message .= 'CV2Result: ' . $response_data['CV2Result'] . "\n";
				}

				if (isset($response_data['3DSecureStatus'])) {
					$message .= '3DSecureStatus: ' . $response_data['3DSecureStatus'] . "\n";
				}

				if (isset($response_data['CAVV'])) {
					$message .= 'CAVV: ' . $response_data['CAVV'] . "\n";
				}

				$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

				$this->model_payment_sagepay_direct_v3->updateOrder($order_info, $response_data);

				$sagepay_order_info = $this->model_payment_sagepay_direct_v3->getOrder($this->session->data['order_id']);
				$this->model_payment_sagepay_direct_v3->logger('sagepay_direct_v3_order_id: ' . print_r($sagepay_order_info['sagepay_direct_v3_order_id'], 1));

				$this->model_payment_sagepay_direct_v3->logger('$order_info: ' . print_r($order_info, 1));

				
				$this->model_payment_sagepay_direct_v3->addTransaction($sagepay_order_info['sagepay_direct_v3_order_id'], $this->config->get('sagepay_direct_v3_transaction'), $order_info);

				$this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('sagepay_direct_v3_order_status_id'), $message, false);

				if (!empty($response_data['Token']) && $this->customer->isLogged()) {
					$this->model_payment_sagepay_direct_v3->updateCard($this->session->data['order_id'], $response_data['Token']);
				} else {
					$this->model_payment_sagepay_direct_v3->deleteCard($this->session->data['order_id']);
				}

				$this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
			} else {
				$this->session->data['error'] = $response_data['StatusDetail'];

				$this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
			}
		} else {
			$this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}
	}

}
