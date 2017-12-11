<?php namespace SagePay;

use \League\ISO3166\ISO3166;

abstract class Validate {

	const CARD_HOLDER_MAX_LENGTH = 50;
	const CARD_NUMBER_MAX_LENGTH = 20;
	const NAMES_MAX_LENGTH = 20;
	const ADDRESS_MAX_LENGTH = 100;
	const CITY_MAX_LENGTH = 40;
	const POSTCODE_MAX_LENGTH = 10;
	const PHONE_MAX_LENGTH = 20;
	const STATE_MAX_LENGTH = 2;

	public static function cardHolder($string) {
		if ( Helper::isLengthBetween($string, 1, self::CARD_HOLDER_MAX_LENGTH) === false ) {
			return false;
		}

		return ($string === Sanitize::cardHolder($string));
	}

	public static function cardNumber($string) {
		if ( Helper::isLengthBetween($string, 1, self::CARD_NUMBER_MAX_LENGTH) === false ) {
			return false;
		}

		return ($string === Sanitize::digits($string));
	}

	public static function cardType($string) {
		return in_array($string, array_flip(CardType::$arrCardTypes), false );
	}

	public static function issueNumber($string) {
		if ( Helper::isLengthBetween($string, 0, 2) === false ) {
			return false;
		}

		return ($string === Sanitize::digits($string));
	}

	public static function cv2($string, $cardType = null) {
		if ( $cardType !== null && $cardType === CardType::AMEX && Helper::isLengthBetween($string, 0, 4) === false) {
			return false;
		}

		if ( Helper::isLengthBetween($string, 0, 3) === false ) {
			return false;
		}

		return ($string === Sanitize::digits($string));
	}

	public static function date($string, $optional = false) {
		if ( Helper::isLengthBetween($string, $optional ? 0 : 4, 4) === false ) {
			return false;
		}

		return preg_match('/^(\d+)$/', $string);
	}

	public static function names($string) {
		if ( Helper::isLengthBetween($string, 1, self::NAMES_MAX_LENGTH) === false ) {
			return false;
		}

		$string = str_replace(';', ',', $string);

		return ($string === Sanitize::names($string));
	}

	public static function address($string, $blnOptional = false) {
		if ( Helper::isLengthBetween($string, $blnOptional ? 0 : 1, self::ADDRESS_MAX_LENGTH) === false ) {
			return false;
		}

		$string = str_replace(';', ',', $string);

		return ($string === Sanitize::address($string));
	}

	public static function city($string) {
		if ( Helper::isLengthBetween($string, 1, self::CITY_MAX_LENGTH) === false ) {
			return false;
		}

		return ($string === Sanitize::address($string));
	}

	public static function phone($string) {
		if ( Helper::isLengthBetween($string, 0, self::PHONE_MAX_LENGTH) === false ) {
			return false;
		}

		return ($string === Sanitize::phone($string));
	}

	public static function postcode($string) {
		if ( Helper::isLengthBetween($string, 1, self::POSTCODE_MAX_LENGTH) === false ) {
			return false;
		}

		return ($string === Sanitize::postcode($string));
	}

	public static function country($string) {
		$iso3166 = new ISO3166();

		try {
			$iso3166->getByAlpha2($string);
		} catch(\RuntimeException $e) {
			return false;
		} catch(\DomainException $e) {
			return false;
		} catch(\InvalidArgumentException $e) {
			return false;
		}

		return true;
	}
}
