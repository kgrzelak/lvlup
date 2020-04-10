<?php

namespace kgrzelak\lvlup;

use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\NativeHttpClient;

/**
 * Main class of library.
 * 
 * @author kgrzelak <https://github.com/kgrzelak>
 * @author error56 <https://github.com/error56>
 *
 */
class Payments {
	
    private $apiKey = '';
    private $result = '';
    private $requestPayment = [];
    private $sandbox = false;
	
    /**
     * @param string $apiKey    Key generated from panel to access API.
     * @param bool   $sandbox   Info about using sandbox or production environment. Default production.
     */
    public function __construct(string $apiKey, bool $sandbox = false)
    {
        $this->apiKey  = $apiKey;
        $this->sandbox = $sandbox;
    }
	
    /**
     * Config method of creating new payment. You should call this
     * before creating new transaction and getting uri for payment.
     * 
     * @param string $amount        Payment amount in format 'XX.YY', e.g. '21.00'
     * @param string $redirectUri   User will be redirected after payment to this uri.
     * @param string $webhookUrl    Endpoint for receiving payment changes
     */
    public function setPaymentDetails(string $amount, string $redirectUri, string $webhookUrl): void
    {
        if (empty($amount) || empty($redirectUri) || empty($webhookUrl)) {
            throw new \Exception();
        }
        
        $this->requestPayment = [
            'amount' => $amount,
            'redirecturi' => $redirectUri,
            'webhookUrl' => $webhookUrl
        ];
    }
	
    /**
     * Generates transaction and returns address to payment.
     * Before calling this function you should call setPaymentDetails()
     *
     * @return string
     */
    public function generateTransaction(): ?string
    {
        $content = $this->request($this->requestPayment, 'wallet/up', 'POST');
        $array = json_decode($content, true);
        
        if (!isset($array['id'])) {
            return null;
        }
		
        return $array['url'];
    }
    
    /**
     * Returns payment status (payed - true/false)
     * 
     * @param string $id    ID of transaction
     * @return bool
     */
    public function getTransactionInfo(string $id): bool
    {
        $content = $this->request([], '/wallet/up/' . $id, 'GET');

        return $content;
    }
	
    /**
     * Returns last $limit payments.
     * 
     * @param int $limit    Limit of returned payments. Default 10.
     * @return array
     */
    public function getPayments(int $limit = 10): array
    {
        return json_decode($this->request([], 'payments'), true);
    }
    
    /**
     * Creates account and return credentials. Available only in sandbox mode.
     *
     * @return array
     */
    public function createAccount(): array
    {
        if (!$this->sandbox) {
            throw new \BadMethodCallException('Payments::createAccount available only in sandbox mode.');
        }
        
        $response = $this->request([], 'account/new', 'POST', false);
        
        return json_decode($response, true);
    }
    
    /**
     * Used to access API.
     * Uses curl client if extension exists, native streams otherwise.
     * 
     * @param array  $data                  Post data
     * @param string $url                   URL to access.
     * @param string $method                Request method. Currently support only GET and POST.
     * @param bool   $authorizationHeader   If authorization header will be sent with request.
     * @return string
     */
    private function request(array $data = [], string $url = '', string $method = 'GET', bool $authorizationHeader = true): ?string
    {
        $client;
        if (!\extension_loaded('curl')) {
            $client = new NativeHttpClient();
        } else {
            $client = new CurlHttpClient();
        }
        
        $url = $this->sandbox ? 'https://sandbox-api.lvlup.pro/v4/' . $url : 'https://api.lvlup.pro/v4/' . $url;
        
        if ('POST' === $method) {
            $requestData = [];
            $requestData['body'] = json_encode($data);
            
            if ($authorizationHeader) {
                $requestData['auth_bearer'] = $this->apiKey;
            }
            
            $response = $client->request('POST', $url, $requestData);
            
            return $response->getContent();
        } else {
            $requestData = [];
            $requestData['query'] = $data;
            
            if ($authorizationHeader) {
                $requestData['auth_bearer'] = $this->apiKey;
            }
            
            $response = $client->request('GET', $url, $requestData);
            
            return $response->getContent();
        }
    }
}
