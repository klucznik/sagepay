<?php require_once __DIR__ . '/../vendor/autoload.php';

class TestExpired extends \SagePay\Test\TestBase {

	public function testExpiredCard() {
		$this->sagepay->execute();

		static::assertEquals(\SagePay\StatusType::INVALID, $this->sagepay->status);
		static::assertEquals('5013 : The card has expired.', $this->sagepay->error);
	}
}
