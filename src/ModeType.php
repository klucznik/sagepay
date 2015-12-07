<?php namespace SagePay;

	abstract class ModeType {
		const Live = 'live';
		const Test = 'test';
		const Simulator = 'simulator';

		public static $arrModeTypes = array(
			self::Live,
			self::Test,
			self::Simulator
		);
	}