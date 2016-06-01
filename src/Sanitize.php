<?php namespace SagePay;

/**
 * Class Sanitize
 * Removes any unwanted chars from the input
 *
 * @package SagePay
 */
abstract class Sanitize {

	public static function cardHolder($string) {
		return preg_replace('%([^&.\' \\\\/[:alpha:]-]+)%', ' ', $string);
	}

	public static function digits($string) {
		return preg_replace('%([^0-9]+)%', '', $string);
	}

	public static function names($string) {
		return preg_replace('%([^&,.\' \\\\/[:alnum:]-]+)%', ' ', $string);
	}

	public static function address($string) {
		return preg_replace('%([^-&,.\' \\\\/:+()[:alnum:]]+)%', ' ', $string);
	}

	public static function phone($string) {
		return preg_replace('%([^ ()+[:alnum:]-]+)%', ' ', $string);
	}

	public static function postcode($string) {
		return preg_replace('%([^ a-zA-Z0-9-]+)%', '', $string);
	}
}
