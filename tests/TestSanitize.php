<?php require_once __DIR__ . '/../vendor/autoload.php';

use SagePay\Sanitize;

class TestSanitize extends \PHPUnit_Framework_TestCase {

	public function testNames() {
		$string = 'Adam & Co';
		static::assertEquals($string, Sanitize::names($string));
		static::assertEquals('Adam ', Sanitize::names('Adam!'));
	}

	public function testAddress() {
		$string = 'Adam & Co 48 road.';
		static::assertEquals($string, Sanitize::address($string));
		static::assertEquals('Adam ', Sanitize::address('Adam!'));
		static::assertEquals(' & () +', Sanitize::address('!#$%^&*()_+'));
	}

	public function testDigits() {
		static::assertEquals('5543543254353', Sanitize::digits('5543543254353'));
		static::assertEquals('4929000000006', Sanitize::digits('4929 0000 0000 6'));
		static::assertEquals('4929000000006', Sanitize::digits('4929-0000-0000-6'));
	}

	public function testCardHolder() {
		$string = 'Card holder. &/-\\';
		static::assertEquals($string, Sanitize::cardHolder($string));
		//$this->assertEquals('Złomnik. &/-\\', Sanitize::cardHolder('Złomnik. &/-\\!!!!'));
	}

	public function testPostcode() {
		$string = 'Zlomnik -';
		static::assertEquals($string, Sanitize::postcode($string));
		$string = '00-079';
		static::assertEquals($string, Sanitize::postcode($string));
		$string = 'M22 4XE';
		static::assertEquals($string, Sanitize::postcode($string));
	}

	public function testPhone() {
		$string = '(+48) 604 453 543';
		static::assertEquals($string, Sanitize::phone($string));
		$string = 'miacha';
		static::assertEquals($string, Sanitize::phone($string));
	}
}
