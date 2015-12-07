<?php namespace SagePay;

	abstract class CardType {
		const Visa = 'VISA';
		const Mc = 'MC';
		const Delta = 'DELTA';
		const Solo = 'SOLO';
		const Maestro = 'MAESTRO';
		const Uke = 'UKE'; //Visa electron
		//const Amex = 'AMEX';
		//const Dc = 'DC';
		//const Jcb = 'JCB';
		//const Laser = 'LASER';

		public static $arrCardTypes = array(
			self::Visa => 'Visa',
			self::Mc => 'Mastercard',
			self::Delta => 'Delta',
			self::Solo => 'Solo',
			self::Maestro => 'Switch/Maestro',
			self::Uke => 'Visa Electron'
			//self::Amex => '',
			//self::Dc => '',
			//self::Jcb => '',
			//self::Laser => ''
		);
	}