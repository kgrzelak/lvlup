<?php
namespace kgrzelak\lvlup;

use kgrzelak\lvlup\Components;

class Payments extends Components {
	
	protected $apiKey = '';

	protected $components = '';
	
	protected $result = null;
	
	protected $request_payment = [
		'amount' => '',
		'redirectUrl' => '',
		'webhookUrl' => ''
	];
	
	public function __construct(string $apiKey) {
		$this->apiKey = $apiKey;

		$this->components = new Components();
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
	
	private function request_get($value, string $method = "GET", array $data = []) {
		return $this->components->request($data, 'https://api.lvlup.pro/v4/' . $value, $this->apiKey, $method);
	}
	
}
