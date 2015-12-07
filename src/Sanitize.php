<?php namespace SagePay;

	abstract class Sanitize {

		public static function cardHolder($string) {
			return preg_replace('/^([[:alpha:]\-&.\' \\/]+)$/', '', $string);
		}

		public static function cardNumber($string) {
			if ( self::isLengthBeetween($string, 1, 20) === false )
				return false;

			return preg_match('/^(\d+)$/', $string);
		}

		public static function cardType($string) {
			return in_array($string, array_flip(CardType::$arrCardTypes) );
		}

		public static function issueNumber($string) {
			if ( self::isLengthBeetween($string, 0, 2) === false )
				return false;

			return preg_match('/^(\d+)$/', $string);
		}

		public static function cv2($string, $cardType) {
			//if ( self::isLengthBeetween($string, 0, ($strCardType == cardType::Amex) ? 4 : 3) == false )
			if ( self::isLengthBeetween($string, 0, 3) === false )
				return false;

			return preg_match('/^(\d+)$/', $string);
		}

		public static function date($string, $optional = false) {
			if ( self::isLengthBeetween($string, ($optional) ? 0 : 4, 4) === false )
				return false;

			return preg_match('/^(\d+)$/', $string);
		}

		public static function names($string) {
			if ( self::isLengthBeetween($string, 1, 20) === false )
				return false;

			$string = str_replace(';', ',', $string);

			return preg_match('/^([[:alpha:]\-&.\' ]+)$/', $string);
		}

		public static function address($string, $blnOptional = false) {
			if ( self::isLengthBeetween($string, ($blnOptional) ? 0 : 1, 100) === false )
				return false;

			$string = str_replace(';', ',', $string);

			return preg_match('/^([[:alnum:]&,.\' ()+:\-]+)$/', $string);
		}

		public static function city($string) {
			if ( self::isLengthBeetween($string, 1, 40) === false )
				return false;

			return preg_match('/^([[:alnum:]&,.\' ()+:\-]+)$/', $string);
		}

		public static function phone($string) {
			if ( self::isLengthBeetween($string, 0, 20) === false )
				return false;

			return preg_match('/^([[:alnum:]&,.\' ()+:\-]+)$/', $string);
		}

		public static function postcode($string) {
			if ( self::isLengthBeetween($string, 1, 10) === false )
				return false;

			return preg_match('/^([\w \-]+)$/', $string);
		}

		public static function country($string) {
			$iso3166 = new ISO3166;

			try {
				$iso3166->getByAlpha2($string);
			} catch(\RuntimeException $e) {
				return false;
			} catch(\InvalidArgumentException $e) {
				return false;
			}

			return true;
		}
	}