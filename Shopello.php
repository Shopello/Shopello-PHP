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
	private $_api_endpoint = 'https://api.shopello.se/1/';
	 
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
	 * @param bool		Optional.
	 * @return array
	 */
	public function call($method, $params = array(), $post = false){
		// Assemble the URL
		$url = $this->get_api_endpoint() . $method . '.json';
		
		// Add params
		if(!$post && count($params) > 0){
			foreach($params as $key => $val){
				if(empty($val)){
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
		curl_setopt($curl, CURLOPT_ENCODING , 'gzip');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'X-API-KEY: ' . $this->get_api_key()
		));
		
		// Post
		if($post){
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
		}
		
		// Execute
		$result = curl_exec($curl);
		
		// Decode
		$data = json_decode($result);
		
		// Error? Exception!
		if(isset($data->error)){
			throw new Exception($data->error);
		}
		
		// Return data
		return $data;
	}
	
	/**
	 * Products
	 *
	 * @param array|integer	Optional.
	 * @param array			Optional.
	 * @return array
	 */
	public function products($product_id = null, $params = array()){
		$method = 'products';
		
		if(is_array($product_id)){
			$params = $product_id;
		}
		else{
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
	public function related_products($product_id){
		$method = 'related_products/' . $product_id;
		
		return $this->call($method, array());
	}
	
	/**
	 * Attributes
	 *
	 * @param array|integer	Optional.
	 * @param array			Optional.
	 * @return array
	 */
	public function attributes($attribute = null, $params = array()){
		$method = 'attributes';
		
		if(is_array($attribute)){
			$params = $attribute;
		}
		else{
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
	public function stores($params = array()){
		return $this->call('stores', $params);
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
	 * Categories
	 *
	 * @param array		Optional.
	 * @return array
	 */
	public function category_parents($params = array()){
		return $this->call('category_parents', $params);
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
	
	/**
	 * Batch
	 *
	 * @param array
	 * @return array
	 */
	public function batch($batch = array()){
		return $this->call('batch', array(
			'batch' => $batch
		), true);
	}
}