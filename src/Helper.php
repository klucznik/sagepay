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
	}