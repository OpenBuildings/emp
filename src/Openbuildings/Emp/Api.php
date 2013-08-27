<?php 

namespace Openbuildings\Emp;

/**
 * @package    Openbuildings\Emp
 * @author     Ivan Kerin
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Api {

	protected static $_instance;

	/**
	 * Configure the default instance (Api::instance())
	 * 
	 * @param  string $gateway_url url
	 * @param  string $client_id   
	 * @param  string $api_key     
	 */
	public static function configure($gateway_url, $client_id, $api_key)
	{
		self::$_instance = new Api($gateway_url, $client_id, $api_key);
	}
	
	/**
	 * return the default instance, you need to run Api::configure() to set it up
	 * @return Api 
	 */
	public static function instance()
	{
		if ( ! self::$_instance) 
			throw Exception('You need to run Api::configure to be able to use the global api instance');

		return self::$_instance;
	}

	protected $_client_id;
	protected $_api_key;
	protected $_gateway_url;

	/**
	 * Threatmatrix object, used to set thm_session_Id
	 * @var Threatmarix
	 */
	protected $_threatmatrix;

	/**
	 * Getter
	 * @return string 
	 */
	public function client_id()
	{
		return $this->_client_id;
	}

	/**
	 * Getter
	 * @return string 
	 */
	public function api_key()
	{
		return $this->_api_key;
	}

	/**
	 * Getter
	 * @return string
	 */
	public function gateway_url()
	{
		return $this->_gateway_url;
	}

	/**
	 * Getter / Setter of a Threatmatrix object
	 * @param  Threatmatrix $threatmatrix 
	 * @return Threatmatrix|$this               
	 */
	public function threatmatrix($threatmatrix = NULL)
	{
		if ($threatmatrix !== NULL)
		{
			$this->_threatmatrix = $threatmatrix;
			return $this;
		}
		return $this->_threatmatrix;
	}

	function __construct($gateway_url, $client_id, $api_key)
	{
		if ( ! filter_var($gateway_url, FILTER_VALIDATE_URL)) 
			throw Exception('Gateway url must be a proper url');
		
		$this->_client_id = $client_id;
		$this->_api_key = $api_key;
		$this->_gateway_url = $gateway_url;
	}

	/**
	 * Return the parameters required for authentication
	 * @return array 
	 */
	public function auth_params()
	{
		$params = array(
			'client_id' => $this->client_id(), 
			'api_key' => $this->api_key(),
		);

		if ($this->threatmatrix()) 
		{
			$params['thm_session_id'] = $this->threatmatrix()->session_id();
		}

		return $params;
	}

	/**
	 * Generate a url for an api request
	 * @param  string $endpoint 
	 * @param  array  $params   
	 * @return string           
	 */
	public function generate_url($endpoint, array $params)
	{
		$params = array_merge($this->auth_params(), $params);
		$url = $this->_gateway_url.$endpoint;

		return $url.'?'.http_build_query($params);
	}

	/**
	 * Perform an api request, return an array with result details
	 * 
	 * @param  string $endpoint 
	 * @param  array  $params   
	 * @throws Openbuildings\Emp\Exception If errors in request, api response or the card is declined
	 * @return array           
	 */
	public function request($endpoint, array $params)
	{
		$url = $this->generate_url($endpoint, $params);

		$response = Remote::get($url);

		$xml_response = new \SimpleXMLElement($response);
		
		if ($xml_response->errors)
		{
			$errors = array();
			foreach ($xml_response->errors as $error) 
			{
				$errors[] = '('.$error->error->code.') '.$error->error->text;
			}
				
			throw new Exception('Error sendig request to gateway: :errors', array(
				':errors' => join(', ', $errors)
			));
		}
		elseif ( (string) $xml_response->transaction->response === 'D')
		{
			throw new Exception('The transaction was declined: :errors', array(
				':errors' => (string) $xml_response->transaction->response_text
			));
		}
		
		return array(
			'order_id' => (string) $xml_response->order_id,
			'order_status' => (string) $xml_response->order_status,
			'transaction_response' => (string) $xml_response->transaction->response,
			'transaction_id' => (string) $xml_response->transaction->transaction_id,
			'raw' => json_decode(json_encode($xml_response), TRUE),
		);
	}
}
