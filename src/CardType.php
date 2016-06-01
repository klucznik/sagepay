<?php namespace SagePay;

abstract class CardType {
	const VISA = 'VISA';
	const MC = 'MC';
	const DELTA = 'DELTA';
	const SOLO = 'SOLO';
	const MAESTRO = 'MAESTRO';
	const UKE = 'UKE'; //VISA electron
	const AMEX = 'AMEX';
	//const DC = 'DC';
	//const JCB = 'JCB';
	//const LASER = 'LASER';

	public static $arrCardTypes = array(
		self::VISA => 'VISA',
		self::MC => 'Mastercard',
		self::DELTA => 'DELTA',
		self::SOLO => 'SOLO',
		self::MAESTRO => 'Switch/MAESTRO',
		self::UKE => 'VISA Electron',
		self::AMEX => 'American Express'
		//self::DC => '',
		//self::JCB => '',
		//self::LASER => ''
	);
}
