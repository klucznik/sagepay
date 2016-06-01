<?php namespace SagePay;

abstract class ModeType {
	const LIVE = 'live';
	const TEST = 'test';
	const SIMULATOR = 'simulator';

	public static $arrModeTypes = array(
		self::LIVE,
		self::TEST,
		self::SIMULATOR
	);
}