<?php

use Openbuildings\Emp\Remote;

/**
 * @package 
 * @group   remote
 */
class RemoteTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers Openbuildings\Emp\Remote::get
	 */
	public function test_get()
	{
		$response = Remote::get('https://3533bfdb7f646acec3be-0cd42b8dee15b5017160a1d30c7ce549.ssl.cf3.rackcdn.com/test.txt');

		$this->assertEquals('test', $response);
	}

	/**
	 * @covers Openbuildings\Emp\Remote::get
	 * @expectedException Openbuildings\Emp\Exception
	 * @expectedExceptionCode 404
	 */
	public function test_get_404()
	{
		$response = Remote::get('https://3533bfdb7f646acec3be-0cd42b8dee15b5017160a1d30c7ce549.ssl.cf3.rackcdn.com/');
	}
	
	/**
	 * @covers Openbuildings\Emp\Remote::get
	 * @expectedException Openbuildings\Emp\Exception
	 * @expectedExceptionCode 6
	 */
	public function test_get_CURLE_COULDNT_RESOLVE_HOST()
	{
		$response = Remote::get('https://3533bfdb7f646acec3be-ssl.cf3.rackcdn.com/test.txt-not-existant');
	}

	/**
	 * @covers Openbuildings\Emp\Remote::get
	 * @expectedException Openbuildings\Emp\Exception
	 * @expectedExceptionMessage Endpoint mm://3 not a valid url
	 */
	public function test_get_wrong_url()
	{
		$response = Remote::get('mm://3');
	}
}