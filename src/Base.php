<?php namespace SagePay;

class Base {

	public function __set($name, $value) {
		$trace = debug_backtrace();
		trigger_error(
			'Undefined set property: ' . $name .
			' in ' . $trace[1]['file'] .
			' on line ' . $trace[1]['line'],
			E_USER_NOTICE);
	}

	public function __get($name) {
		$trace = debug_backtrace();
		trigger_error(
			'Undefined get property: ' . $name .
			' in ' . $trace[1]['file'] .
			' on line ' . $trace[1]['line'],
			E_USER_NOTICE);
		return null;
	}
}
