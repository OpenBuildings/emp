<?php

use Openbuildings\Emp\Threatmatrix;

/**
 * @package 
 * @group   threatmatrix
 */
class ThreatmatrixTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers Openbuildings\Emp\Threatmatrix::configure
	 * @covers Openbuildings\Emp\Threatmatrix::instance
	 */
	public function test_initialize()
	{
		Threatmatrix::configure('myorgid-12', 'client-id-1');

		$instance = Threatmatrix::instance();

		$this->assertEquals('myorgid-12', $instance->org_id());
		$this->assertContains('client-id-1', $instance->session_id());
	}

	/**
	 * @covers Openbuildings\Emp\Threatmatrix::__construct
	 * @covers Openbuildings\Emp\Threatmatrix::org_id
	 * @covers Openbuildings\Emp\Threatmatrix::session_id
	 * @covers Openbuildings\Emp\Threatmatrix::session2
	 */
	public function test_construct()
	{
		$instance = new Threatmatrix('myorg1', 'client-id-2');
		$this->assertEquals('myorg1', $instance->org_id());
		$this->assertContains('client-id-2', $instance->session_id());
		$this->assertNotNull($instance->session2());
	}

	/**
	 * @covers Openbuildings\Emp\Threatmatrix::serialize
	 * @covers Openbuildings\Emp\Threatmatrix::unserialize
	 */
	public function test_serializable()
	{
		$instance = new Threatmatrix('myorg2', 'client-id-3');
		$string = serialize($instance);
		
		$instance2 = unserialize($string);

		$this->assertEquals($instance, $instance2);
	}

	/**
	 * @covers Openbuildings\Emp\Threatmatrix::tracking_params
	 */
	public function test_traking_params()
	{
		$instance = new Threatmatrix('myorg3', 'client-id-4');

		$params = array();
		parse_str($instance->tracking_params(), $params);

		$this->assertEquals('myorg3', $params['org_id']);
		$this->assertEquals($instance->session_id(), $params['session_id']);
	}

	/**
	 * @covers Openbuildings\Emp\Threatmatrix::tracking_url
	 */
	public function test_tracking_url()
	{
		$instance = new Threatmatrix('myorg3', 'client-id-4');

		$url = $instance->tracking_url();

		$this->assertContains($instance->tracking_params(), $url);
	}

	/**
	 * @covers Openbuildings\Emp\Threatmatrix::tracking_code
	 */
	public function test_tracking_code()
	{
		$instance = new Threatmatrix('myorg3', 'client-id-4');

		$code = $instance->tracking_code();

		$this->assertSelectCount('img', 1, $code);
		$this->assertSelectCount('object', 1, $code);
		$this->assertSelectCount('script', 1, $code);

		$this->assertContains($instance->tracking_params(), $code);
	}
}