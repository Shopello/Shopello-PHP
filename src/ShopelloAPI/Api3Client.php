<?php
namespace Shopello\API;

use \Curl\Curl;

class Api3Client
{
    /**
     * @var Curl
     */
    private $curl;

    /**
     * Shopello API Settings
     */
    private $apiUsername = null;
    private $apiPassword = null;
    private $apiEndpoint = null;


    /**
     * Get Params to send to API in all requests
     */
    private $apiGetParams = array();


    /**
     * Persistent curl options
     */
    private $curlOptions = array();


    /**
     * @param Curl $curl
     * @return void
     */
    public function __construct(Curl $curl)
    {
        $this->curl = $curl;
    }


    /**
     * Set API Credentials
     *
     * @var string $apiUsername
     * @var string $apiPassword
     * @return void
     */
    public function setApiCredentials($apiUsername, $apiPassword)
    {
        $this->apiUsername = $apiUsername;
        $this->apiPassword = $apiPassword;
    }


    /**
     * Set API Key
     *
     * @var string $apiKey
     * @return void
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }


    /**
     * Set API Endpoint, for example: https://se.shopelloapi.com/v3/
     *
     * @var string $apiEndpoint
     * @return void
     */
    public function setApiEndpoint($apiEndpoint)
    {
        $this->apiEndpoint = $apiEndpoint;
    }


    /**
     * Set GET Params to always append to URI (JSONP for now)
     *
     * @param array('callback' => 'jsonp_callback_name') $params
     * @return void
     */
    public function setGetParams($params)
    {
        $this->apiGetParams = $params;
    }


    /**
     * Set persistent cURL options
     *
     * @param array(<curlopt_constant> => <value>) $options
     */
    public function setCurlOptions($options)
    {
        $this->curlOptions = $options;
    }


    /**
     * Apply cURL options
     */
    private function applyCurlOptions($authMethod)
    {
        $this->curl->reset();

        $this->curl->setUserAgent('Shopello-PHP API Client/1.0');

        $this->curl->setOpt(CURLOPT_ENCODING, 'gzip');
        $this->curl->setOpt(CURLOPT_HEADER, false);
        $this->curl->setOpt(CURLOPT_NOBODY, false);
        $this->curl->setOpt(CURLOPT_CONNECTTIMEOUT, 3);
        $this->curl->setOpt(CURLOPT_TIMEOUT, 300);

        $this->setAuthMethod($authMethod);

        foreach ($this->curlOptions as $key => $value) {
            $this->curl->setOpt($key, $value);
        }
    }


    /**
     * Set auth method for curl request
     *
     * @param string "apikey" or "basic"
     */
    private function setAuthMethod($authMethod)
    {
        switch ($authMethod) {
            case 'apikey':
                $this->curl->setHeader('X-API-KEY', $this->apiKey);
                break;

            case 'basic':
            default:
                $this->curl->setBasicAuthentication($this->apiUsername, $this->apiPassword);
                break;
        }
    }


    /**
     * Make API-call
     */
    private function call($method, $uri, $getParams = array(), $postParams = array(), $authMethod = 'basic')
    {
        $uri = $this->apiEndpoint.$uri;

        $getParams = array_merge($this->apiGetParams, $getParams);

        // Filter empty params
        $getParams = array_filter(
            $getParams,
            (function ($var) {
                return !empty($var);
            })
        );

        // CURL Stuff
        $this->applyCurlOptions($authMethod);

        // Do Request
        switch ($method) {
            case 'get':
                $this->curl->get($uri, $getParams);
                break;

            case 'post':
                $this->curl->post($uri.'?'.http_build_query($getParams), $postParams);
                break;

            case 'delete':
                $this->curl->delete($uri, $getParams);
                break;

            case 'put':
                $this->curl->put($uri.'?'.http_build_query($getParams), $postParams);
                break;

            default:
                throw new \Exception('Requested method behaviour is not defined yet');
        }

        if ($this->curl->curl_error_code == 28) {
            throw new \Exception('Connection timeout', 28);
        }

        $error = $this->curl->error;

        if ($error) {
            throw new \Exception($error . ' (HTTP CODE ' . $this->curl->http_status_code . ')');
        }

        // If JSONP is activated, return instantly
        if (isset($extraParams['callback'])) {
            return $this->curl->response;
        }

        // Otherwise decode and return
        $result = json_decode($this->curl->response);

        return $result;
    }

    /*******************************************************************************************************************
     * Channel related methods
     */
    public function getChannels()
    {
        return $this->call('get', 'channel/');
    }

    public function createChannel($name, $uri = null)
    {
        $data = array(
            'name' => $name
        );

        if ($uri) {
            $data['uri'] = $uri;
        }

        return $this->call('post', 'channel/', array(), $data);
    }

    public function updateChannel($id, $params = array())
    {
        return $this->call('post', 'channel/'.$id.'/', array(), $params);
    }

    public function deleteChannel($id)
    {
        return $this->call('delete', 'channel/'.$id.'/');
    }

    public function getChannelRevenue($id, $startDate, $endDate)
    {
        return $this->call('get', 'channel/revenue/'.$id.'/'.$startDate.'/'.$endDate.'/');
    }

    /*******************************************************************************************************************
     * Consumer related methods
     */
    public function getConsumerRevenue($startDate, $endDate)
    {
        return $this->call('get', 'consumer/revenue/'.$startDate.'/'.$endDate.'/');
    }

    /*******************************************************************************************************************
     * Consumer Secret methods
     */
    public function getConsumerSecret()
    {
        return $this->call('get', 'consumer/secret/');
    }

    public function generateNewConsumerSecret()
    {
        return $this->call('put', 'consumer/secret/');
    }

    /*******************************************************************************************************************
     * Deeplink generator
     */
    public function createDeepLink($uri, $params = array())
    {
        return $this->call('post', 'deepLinkGenerator/', $params, array('uri' => $uri), 'apikey');
    }
}
