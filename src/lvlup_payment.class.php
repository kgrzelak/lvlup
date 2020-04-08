<?php

namespace kgrzelak\lvlupPayment;

class lvlup_payment {
	
	protected $api_key = '';
	
	protected $result = null;
	
	protected $request_payment = [
		'amount' => '',
		'redirectUrl' => '',
		'webhookUrl' => ''
	];
	
	public function set(string $name, $data) {
		return $this->{$name} = $data;
	}
	
	public function set_payment(string $name, $data) {
		return $this->request_payment[$name] = $data;
	}
	
	public function transaction_generate() {
		
		if (!$api = $this->request_get('wallet/up', 'post', $this->request_payment)) {
			return false;
		}
		
		if (!isset($api->id)) {
			return false;
		}
		
		$this->result = $api;
		
		return true;
		
	}
	
	public function transaction_redirect() {
		
		if (!isset($this->result->url)) {
			return false;
		}
		
		return $this->result->url;
		
	}
	
	public function transaction_info(string $id) {
		
		$api = $this->request_get('', 'get', ['wallet', 'up', $id]);
		if ($api->payed) {
			return true;
		} else {
			return false;
		}
		
	}
	
	public function payments_get(int $limit = 10) {
		return $this->request_get('payments');
	}
	
	private function request(array $data, $url, string $method = "get") {
		$ch = curl_init();
		if ($method == "post") {
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		} else {
			$params = '';
			foreach ($data as $d) {
				if (!next($data)) {
					$params .= $d;
				} else {
					$params .= $d . '/';
				}
			}
			curl_setopt($ch, CURLOPT_URL, $url . $params);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer " . $this->api_key]);
		$call = curl_exec($ch);
		$response = json_decode($call);
		$error = curl_errno($ch);
		curl_close($ch);
		
		if ($error > 0) {
			//throw new RuntimeException('CURL ERROR Code: ' . $error);
			return false;
		}
		
		return $response;
	}
	
	private function request_get($value, string $method = "get", array $data = []) {
		return $this->request($data, 'https://api.lvlup.pro/v4/' . $value, $method);
	}
	
}