<?php

namespace Openbuildings\Emp;

/**
 * @package    Openbuildings\Emp
 * @author     Ivan Kerin
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Threatmatrix implements \Serializable {

	protected static $_instance;

	/**
	 * Configure the default instance (Threatmatrix::instance())
	 * @param  string $org_id
	 * @param  string $client_id
	 */
	public static function configure($org_id, $client_id)
	{
		self::$_instance = new Threatmatrix($org_id, $client_id);
	}

	/**
	 * Get the default instance, must be configured with Threatmatrix::configure() first
	 * @return Threatmatrix
	 */
	public static function instance()
	{
		if ( ! self::$_instance)
			throw new Exception('You need to run Threatmatrix::configure to be able to use the global api instance');

		return self::$_instance;
	}

	protected $_org_id;
	protected $_session_id;
	protected $_session2;

	function __construct($org_id, $client_id)
	{
		$this->_org_id = $org_id;
		$this->_session_id = $client_id.date('Ymdhis').rand(100000,999999);
		$this->_session2 = md5(rand());
	}

	/**
	 * Return the tracking code, that has to be placed to the page where the payment form is
	 * @return string
	 */
	public function tracking_code()
	{
		$params = $this->tracking_params();

		return <<<TRACKING
<div style="position:absolute;left:0;bottom:0">
<p style="margin:0;background:url(https://h.online-metrix.net/fp/clear.png?{$params}&session2={$this->session2()}&m=1)"></p>
<img src="https://h.online-metrix.net/fp/clear.png?{$params}&m=2"/>
<script src="https://h.online-metrix.net/fp/check.js?{$params}"></script>
<object type="application/x-shockwave-flash" data="https://h.online-metrix.net/fp/fp.swf?{$params}" width="1" height="1" id="thm_fp"> 
<param name="movie" value="https://h.online-metrix.net/fp/fp.swf?{$params}" />
</object>
</div>
TRACKING;
	}

	/**
	 * Return a simple url to register the tracking with online matrix
	 * @return string
	 */
	public function tracking_url()
	{
		$params = $this->tracking_params();

		return "https://h.online-metrix.net/fp/clear.png?{$params}&m=2";
	}

	/**
	 * Getter
	 * @return string
	 */
	public function org_id()
	{
		return $this->_org_id;
	}

	/**
	 * Getter
	 * @return string
	 */
	public function session_id()
	{
		return $this->_session_id;
	}

	/**
	 * Getter
	 * @return string
	 */
	public function session2()
	{
		return $this->_session2;
	}

	/**
	 * Return query parameters for tracking
	 * @return string
	 */
	public function tracking_params()
	{
		return http_build_query(array(
			'org_id' => $this->org_id(),
			'session_id' => $this->session_id(),
		));
	}

	/**
	 * Implement Serializable
	 * @return string
	 */
	public function serialize()
	{
		$data = array(
			$this->org_id(),
			$this->session_id(),
			$this->session2(),
		);

		return serialize($data);
	}

	/**
	 * Implement Serializable
	 * @param  string $data
	 */
	public function unserialize($data)
	{
		$data = unserialize($data);
		$this->_org_id = $data[0];
		$this->_session_id = $data[1];
		$this->_session2 = $data[2];
	}

}
