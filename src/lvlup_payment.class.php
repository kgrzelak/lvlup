<?php

class lvlup_payment {
	
	protected $api_key = '';
	
	protected $result_json = null;
	
	protected $request_payment = [
		'amount' => '',
		'redirectUrl' => '',
		'webhookUrl' => ''
	];
	
	public function set($name, $data) {
		return $this->{$name} = $data;
	}
	
	public function set_payment($name, $data) {
		return $this->request_payment[$name] = $data;
	}
	
	public function transaction_generate() {
		
		$ch = curl_init('https://api.lvlup.pro/v4/wallet/up');
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->request_payment));
		curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer " . $this->api_key]);

		$result = curl_exec($ch);
		curl_close($ch);
		
		try {
			$resultDecode = json_decode($result);
			$this->result_json = $resultDecode;
			return true;
		} catch (Exception $err) {
			return false;
		}
		
		if (!isset($this->result_json->id)) {
			return false;
		}
		
	}
	
	public function transaction_redirect() {
		
		if (!isset($this->result_json->url)) {
			return false;
		}
		
		return $this->result_json->url;
		
	}
	
	public function transaction_info($id) {
		
		$api = $this->request_get('', 'get', ['wallet', 'up', $id]);
		if ($api['payed']) {
			return true;
		} else {
			return false;
		}
		
	}
	
	private function request($data, $url, $method = "get") {
		$ch = curl_init();
		if ($method == "post") {
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		} else {
			$i = 0;
			$params = '';
			foreach ($data as $d) {
				if (!next($data)) {
					$params .= $d;
				} else {
					$params .= $d . '/';
				}
				$i++;
			}
			curl_setopt($ch, CURLOPT_URL, $url . $params);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer " . $this->api_key]);
		$call = curl_exec($ch);
		$response = json_decode($call, true);
		$error = curl_errno($ch);
		curl_close($ch);
		
		if ($error > 0) {
			throw new RuntimeException('CURL ERROR Code: ' . $error);
		}
		
		return $response;
	}
	
	private function request_get($value, $method = "post", $data) {
		return $this->request($data, 'https://api.lvlup.pro/v4/' . $value, $method);
	}
	
}