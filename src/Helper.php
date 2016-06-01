<?php namespace SagePay;

abstract class Helper {

	public static function isLengthBetween($string, $minimumLength, $maximumLength) {
		$stringLength = strlen($string);
		if (($stringLength < $minimumLength) || ($stringLength > $maximumLength)) {
			return false;
		} else {
			return true;
		}
	}

	public static function shortenString($string, $maximumLength) {
		$length = mb_strlen($string);

		if ($length <= $maximumLength) {
			return $string;
		}

		return mb_substr($string, 0, $maximumLength);
	}
}
