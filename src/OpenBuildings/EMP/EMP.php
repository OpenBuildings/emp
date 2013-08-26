<?php

namespace OpenBuildings\EMP;

/**
 * EMP payment class
 * @author Ivan Kerin <ivan@openbuildings.com>
 * @author Haralan Dobrev <hdobrev@despark.com>
 * @copyright (c) 2013 OpenBuildings Inc.
 * @license http://spdx.org/licenses/BSD-3-Clause
 */
class EMP  {

	const GATEWAY = 'https://my.emerchantpay.com/';

	const ORDER_ENDPOINT = 'service/order/submit';

	protected $_instance;

	public static function instance($client_id, $api_key)
	{
		if ( ! self::$_instance)
		{
			self::$_instance = new static($client_id, $api_key);
		}

		return self::$_instance;
	}

	protected $_order = array();

	protected $_client_id;

	protected $_api_key;

	public function __construct($client_id, $api_key)
	{
		$this->client_id($client_id);
		$this->api_key($api_key);
	}

	public function default_params()
	{
		return array(
			'credit_card_trans_type' => 'sale',
			'payment_method' => 'creditcard',
		);
	}

	public function client_id($client_id = NULL)
	{
		if ($client_id === NULL)
			return $this->_client_id;
		
		$this->_client_id = $client_id;

		return $this;
	}

	public function api_key($api_key = NULL)
	{
		if ($api_key === NULL)
			return $this->_api_key;
		
		$this->_api_key = $api_key;

		return $this;
	}

	public function order(array $order = NULL)
	{
		if ($order === NULL)
			return $this->_order;
		
		$this->_order = $order;

		return $this;
	}

	public function execute()
	{
		if ( ! $this->_client_id)
			throw new Exception('Client ID MUST NOT be empty!');
			
		if ( ! $this->_api_key)
			throw new Exception('API key MUST NOT be empty!');

		if ( ! ($order = $this->order()))
			throw new Exception('Order MUST NOT be empty!');

		$params = array_merge(
			$order,
			$this->default_params(),
			array(
				'client_id' => $this->_client_id,
				'api_key' => $this->_api_key,
			)
		);

		if (empty($params['ip_address']) AND ! empty($_SERVER['REMOTE_ADDR']))
		{
			$params['ip_address'] = $_SERVER['REMOTE_ADDR'];
		}

		return $this->_parse(
			$this->_request(
				static::GATEWAY.static::ORDER_ENDPOINT.'?'.http_build_query($params)
			)
		);
	}

	protected function _request($url)
	{
		$curl = curl_init($url);
		if (($response = curl_exec($curl)) === FALSE)
		{
			$error_code = curl_errno($curl);
			$error_message = curl_error($curl);
			$status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);

			
			throw new Exception('Error sendig request to gateway, HTTP status code :status_code. :error_message (:error_code)', array(
				':status_code' => $status_code,
				':error_message' => $error_message,
				':error_code' => $error_code,
			));
		}

		curl_close($curl);

		return $response;
	}

	protected function _parse($response)
	{
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
		
		if ( (string) $xml_response->transaction->response === 'D')
			throw new Exception('Error sendig request to gateway: :errors', array(
				':errors' => (string) $xml_response->transaction->response_text
			));

		return array(
			'order_id' => (string) $xml_response->order_id,
			'order_status' => (string) $xml_response->order_status,
			'transaction_response' => (string) $xml_response->transaction->response,
			'transaction_id' => (string) $xml_response->transaction->transaction_id,
			'raw' => json_decode(json_encode($xml_response)),
		);
	}

}
