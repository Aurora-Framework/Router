<?php

use Aurora\Router;

class RouterTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
   {
		$_SERVER['SCRIPT_NAME'] = '/index.php';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
	}

   public function testInit()
   {
		$this->assertInstanceOf('\Aurora\Router', new Router());
	}
}
