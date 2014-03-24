<?php

namespace Openbuildings\Emp;

/**
 * @package    Openbuildings\Emp
 * @author     Ivan Kerin
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Remote {

	private static function execute(array $options)
	{
		$curl = curl_init();

		curl_setopt_array($curl, $options);

		$response = curl_exec($curl);

		if ( ! $response)
		{
			$code  = curl_errno($curl);
			$error = curl_error($curl);
			$url = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);

			throw new Exception('Request for :url returned :error', array(':error' => $error, ':url' => $url), $code);
		}

		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		if ($code < 200 OR $code >= 300)
		{
			throw new Exception('The server returned error code :code, :response', array(':code' => $code, ':response' => strip_tags($response)), $code);
		}

		return $response;
	}

	/**
	 * Perform a get HTTP request, verify ssl. Return the response.
	 * @throws Openbuildings\Emp\Exceptoion If request return something other than an OK request, or there were problems with curl
	 * @param  string $url
	 * @return string
	 */
	public static function get($url, array $custom_options = NULL)
	{
		if ( ! filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED))
			throw new Exception('Endpoint :url not a valid url', array(':url' => $url));

		$options = array(
			CURLOPT_SSL_VERIFYPEER => FALSE,
			CURLOPT_SSL_VERIFYHOST => FALSE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_FOLLOWLOCATION => TRUE,
			CURLOPT_MAXREDIRS      => 2,
			CURLOPT_URL            => $url,
			CURLOPT_USERAGENT      => 'Openbuildings\\Emp Api 0.1',
		);

		if ($custom_options)
		{
			$options = $options + $custom_options;
		}

		return self::execute($options);
	}

	public static function post($url, array $data, array $custom_options = NULL)
	{
		if ( ! filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED))
			throw new Exception('Endpoint :url not a valid url', array(':url' => $url));

		$options = array(
			CURLOPT_SSL_VERIFYPEER => FALSE,
			CURLOPT_SSL_VERIFYHOST => FALSE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_FOLLOWLOCATION => TRUE,
			CURLOPT_MAXREDIRS      => 2,
			CURLOPT_URL            => $url,
			CURLOPT_POST           => TRUE,
			CURLOPT_POSTFIELDS     => http_build_query($data),
			CURLOPT_USERAGENT      => 'Openbuildings\\Emp Api 0.1',
		);

		if ($custom_options)
		{
			$options = $options + $custom_options;
		}

		return self::execute($options);
	}
}
