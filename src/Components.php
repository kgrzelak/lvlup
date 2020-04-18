<?php
namespace kgrzelak\lvlup;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class Components {

	private function request(array $data, $url, string $apiKey, string $method = "GET") {
        
        $params = '';

        if ($method == "GET") {
            
            foreach ($data as $d) {
                if (!next($data)) {
                    $params .= $d;
                } else {
                    $params .= $d . '/';
                }
            }

            $array = [
                'allow_redirects' => false,
                'connect_timeout' => 4,
                'verify' => false,
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey
                ]
            ];

        } else {

            $array = [
                'body' => json_encode($data),
                'allow_redirects' => false,
                'connect_timeout' => 4,
                'verify' => false,
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey
                ]
            ];

        }

        $response = $this->client->request($method == "GET" ? "GET" : "POST", $url . $params, $array);

        if ($response->getStatusCode() != 200) {
			throw new RuntimeException('Response Code: ' . $response->getStatusCode());
		}
		
        return json_decode($response->getBody());
        
	}

}