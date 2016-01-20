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
     * @param Curl $curl
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
     * Get Requested URI -- This is good for debugging purposes
     */
    public function getRequestedURI()
    {
        $endpoint = parse_url($this->apiEndpoint);

        // Replace Path of endpoint with requested path from curl
        $header = explode(' ', $this->curl->request_headers[0]);
        $endpoint['path'] = $header[1];

        return $endpoint['scheme'].'://'.$endpoint['host'].$endpoint['path'];
    }


    /**
     * Make API-call
     */
    private function call($method, $parameters = array())
    {
        $uri = $this->apiEndpoint . $method . '.json';

        // Filter empty params
        $parameters = array_filter(
            $parameters,
            (function ($var) {
                return !empty($var);
            })
        );

        // CURL Stuff
        $this->curl->reset();

        $this->curl->setUserAgent('Shopello-PHP API Client/1.0');
        $this->curl->setHeader('X-API-KEY', $this->apiKey);
        $this->curl->setOpt(CURLOPT_ENCODING, 'gzip');
        $this->curl->setOpt(CURLOPT_HEADER, false);
        $this->curl->setOpt(CURLOPT_NOBODY, false);
        $this->curl->setOpt(CURLOPT_CONNECTTIMEOUT, 3);
        $this->curl->setOpt(CURLOPT_TIMEOUT, 300);

        // Do Request
        $this->curl->get($uri, $parameters);

        if ($this->curl->curl_error_code == 28) {
            throw new \Exception('Connection timeout', 28);
        }

        $result = json_decode($this->curl->response);
        $error = $this->curl->error;

        if ($error) {
            throw new Exception($error . ' (HTTP CODE ' . $this->curl->http_status_code . ')');
        }

        return $result;
    }


    /**
     * Get Category from API
     *
     * @docs https://docs.shopelloapi.com/#category
     */
    public function getCategory($categoryId)
    {
        return $this->call('categories/'.$categoryId);
    }

    /**
     * Get Categories from API
     *
     * @docs https://docs.shopelloapi.com/#categories
     */
    public function getCategories()
    {
        return $this->call('categories');
    }

    /**
     * Get Parent Categories from API
     *
     * @docs https://docs.shopelloapi.com/#category-parents
     */
    public function getCategoryParents()
    {
        return $this->call('category_parents');
    }

    /**
     * Get Products from API
     *
     * @docs https://docs.shopelloapi.com/#products
     */
    public function getProducts($parameters = array())
    {
        return $this->call('products', $parameters);
    }

    /**
     * Get Product from API
     *
     * @docs https://docs.shopelloapi.com/#product
     */
    public function getProduct($productId)
    {
        return $this->call('products/'.$productId);
    }

    /**
     * Get ProductPrice History from API
     *
     * @docs https://docs.shopelloapi.com/#product-price-history
     */
    public function getProductPriceHistory($parameters)
    {
        return $this->call('price_history', $parameters);
    }

    /**
     * Get Related Products from API
     *
     * @docs https://docs.shopelloapi.com/#related-products
     */
    public function getRelatedProducts($productId)
    {
        return $this->call('related_products/'.$productId);
    }

    /**
     * Get Brands from API
     *
     * @docs https://docs.shopelloapi.com/#brands
     */
    public function getBrands()
    {
        return $this->call('attributes/brand');
    }

    /**
     * Get Stores from API
     *
     * @docs https://docs.shopelloapi.com/#stores
     */
    public function getStores()
    {
        return $this->call('stores');
    }

    /**
     * Get Store from API
     *
     * @docs https://docs.shopelloapi.com/#store
     */
    public function getStore($storeId)
    {
        return $this->call('stores/'.$storeId);
    }

    /**
     * Get Category Tree
     *
     * @docs https://docs.shopelloapi.com/#category-tree
     */
    public function getCategoryTree($categoryId = null)
    {
        $method = 'category_tree';

        if ($categoryId !== null) {
            $method .= '/'.$categoryId;
        }

        return $this->call($method);
    }
}
