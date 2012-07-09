<?php
/**
 * Shopello API wrapper
 *
 * @author Karl Laurentius Roos <karl.roos@produktion203.se>
 * @version 1.0
 */
class Shopello
{
	/**
	 * API endpoint
	 *
	 * @access private
	 */
	private $_api_endpoint = 'http://api.shopello.se/1/';
	 
	/**
	 * API key
	 *
	 * @access private
	 */
	private $_api_key;
	
	/**
	 * Constructor
	 *
	 * @param string	Optional.
	 * @return void
	 */
	public function __construct($api_key = null){
		if($api_key !== null){
			$this->set_api_key($api_key);
		}
	}
	
	/**
	 * Set API endpoint
	 *
	 * @param string
	 * @return void
	 */
	public function set_api_endpoint($api_endpoint){
		$this->_api_endpoint = $api_endpoint;
	}
	
	/**
	 * Get API endpoint
	 *
	 * @return string
	 */
	public function get_api_endpoint(){
		return $this->_api_endpoint;
	}
	
	/**
	 * Set API key
	 *
	 * @param string
	 * @return void
	 */
	public function set_api_key($api_key){
		$this->_api_key = $api_key;
	}
	
	/**
	 * Get API key
	 *
	 * @return string
	 */
	public function get_api_key(){
		return $this->_api_key;
	}
	
	/**
	 * Call
	 *
	 * @param string
	 * @param array		Optional.
	 * @return array
	 */
	public function call($method, $params = array()){
		// Assemble the URL
		$url = $this->get_api_endpoint() . $method . '.json';
		
		// Add params
		if(count($params) > 0){
			$url .= '?' . http_build_query($params);
		}
		
		// Initialize cUrl
		$curl = curl_init();
		
		// Set the cURL parameters
		curl_setopt($curl, CURLOPT_URL, $url); 
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_NOBODY, false);
		curl_setopt($curl, CURLOPT_ENCODING , 'gzip');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'X-API-KEY: ' . $this->get_api_key()
		));
		
		// Execute
		$result = curl_exec($curl);
		
		// Decode
		return json_decode($result);
	}
	
	/**
	 * Products
	 *
	 * @param array		Optional.
	 * @return array
	 */
	public function products($params = array()){
		return $this->call('products', $params);
	}
	
	/**
	 * Categories
	 *
	 * @param array		Optional.
	 * @return array
	 */
	public function categories($params = array()){
		return $this->call('categories', $params);
	}
	
	/**
	 * Customers
	 *
	 * @param array		Optional.
	 * @return array
	 */
	public function customers($params = array()){
		return $this->call('customers', $params);
	}
}