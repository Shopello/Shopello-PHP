<?php
namespace Shopello\API;

use \Curl\Curl;
use \Exception;

class ApiClient
{
    /**
     * @var Curl
     */
    private $curl;

    /**
     * Shopello API Settings
     */
    private $apiKey = null;
    private $apiEndpoint = null;


    /**
     * @param \Curl\Curl $curl
     * @return void
     */
    public function __construct(Curl $curl)
    {
        $this->curl = $curl;
    }


    /**
     * Set API Key
     *
     * @return void
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }


    /**
     * Set API Endpoint, for example: https://se.shopelloapi.com/1/
     *
     * @return void
     */
    public function setApiEndpoint($apiEndpoint)
    {
        $this->apiEndpoint = $apiEndpoint;
    }


    /**
     * Make API-call
     */
    private function call($method, $parameters = array())
    {
        $uri = $this->apiEndpoint . $method . '.json';

        // Filter empty params
        foreach ($parameters as $key => $value) {
            if (empty($value)) {
                unset($parameters[$key]);
            }
        }

        // CURL Stuff
        $this->curl->reset();

        $this->curl->setUserAgent('Shopello-PHP API Client/1.0');
        $this->curl->setHeader('X-API-KEY', $this->apiKey);
        $this->curl->setOpt(CURLOPT_ENCODING, 'gzip');
        $this->curl->setOpt(CURLOPT_HEADER, false);
        $this->curl->setOpt(CURLOPT_NOBODY, false);

        // Do Request
        $this->curl->get($uri, $parameters);

        $result = json_decode($this->curl->response);
        $error = $this->curl->error;

        if ($error) {
            throw new Exception($result->error . ' (HTTP CODE ' . $this->curl->http_status_code . ')');
        }

        return $result;
    }


    /**
     * Get product from API
     *
     * @param integer $product
     */
    public function getProduct($product)
    {
        return $this->call('products/'.$product);
    }
}
