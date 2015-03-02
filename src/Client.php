<?php
namespace Shopello\API;

/**
 * Shopello API wrapper
 *
 * @author Karl Laurentius Roos <karl.roos@produktion203.se>
 * @version 1.0
 */
class Client
{
    /**
     * API endpoint
     *
     * @access private
     */
    private $apiEndpoint = 'https://api.shopello.se/1/';

    /**
     * API key
     *
     * @access private
     */
    private $apiKey;

    /**
     * Constructor
     *
     * @param string Optional.
     * @return void
     */
    public function __construct($api_key = null)
    {
        if ($api_key !== null) {
            $this->setApiKey($api_key);
        }
    }

    /**
     * Set API endpoint
     *
     * @param string
     * @return void
     */
    public function setApiEndpoint($api_endpoint)
    {
        $this->apiEndpoint = $api_endpoint;
    }

    /**
     * Get API endpoint
     *
     * @return string
     */
    public function getApiEndpoint()
    {
        return $this->apiEndpoint;
    }

    /**
     * Set API key
     *
     * @param string
     * @return void
     */
    public function setApiKey($api_key)
    {
        $this->apiKey = $api_key;
    }

    /**
     * Get API key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Call
     *
     * @param string
     * @param array  Optional.
     * @return array
     */
    public function call($method, $params = array())
    {
        // Assemble the URL
        $url = $this->getApiEndpoint() . $method . '.json';

        // Add params
        if (count($params) > 0) {
            foreach ($params as $key => $val) {
                if (empty($val)) {
                    unset($params[$key]);
                }
            }

            $url .= '?' . http_build_query($params);
        }

        // Initialize cUrl
        $curl = curl_init();

        // Set the cURL parameters
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_NOBODY, false);
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'X-API-KEY: ' . $this->getApiKey()
        ));

        // Execute
        $result = curl_exec($curl);

        // Decode
        $data = json_decode($result);

        // Error? Exception!
        if (isset($data->error)) {
            throw new \Exception($data->error);
        }

        // Return data
        return $data;
    }

    /**
     * Products
     *
     * @param array|integer  Optional.
     * @param array          Optional.
     * @return array
     */
    public function products($product_id = null, $params = array())
    {
        $method = 'products';

        if (is_array($product_id)) {
            $params = $product_id;
        } else {
            $method .= '/' . $product_id;
        }

        return $this->call($method, $params);
    }

    /**
     * Related products
     *
     * @param integer
     * @return array
     */
    public function relatedProducts($product_id)
    {
        $method = 'related_products/' . $product_id;

        return $this->call($method, array());
    }

    /**
     * Attributes
     *
     * @param array|integer  Optional.
     * @param array          Optional.
     * @return array
     */
    public function attributes($attribute = null, $params = array())
    {
        $method = 'attributes';

        if (is_array($attribute)) {
            $params = $attribute;
        } else {
            $method .= '/' . $attribute;
        }

        return $this->call($method, $params);
    }

    /**
     * Stores
     *
     * @param array
     * @return array
     */
    public function stores($params = array())
    {
        return $this->call('stores', $params);
    }

    /**
     * Categories
     *
     * @param array   Optional.
     * @return array
     */
    public function categories($params = array())
    {
        return $this->call('categories', $params);
    }

    /**
     * Categories
     *
     * @param array   Optional.
     * @return array
     */
    public function categoryParents($params = array())
    {
        return $this->call('category_parents', $params);
    }

    /**
     * Customers
     *
     * @param array   Optional.
     * @return array
     */
    public function customers($params = array())
    {
        return $this->call('customers', $params);
    }

    /**
     * Batch
     *
     * @param array
     * @return array
     */
    public function batch($batch = array())
    {
        return $this->call('batch', array(
            'batch' => $batch
        ), true);
    }
}
