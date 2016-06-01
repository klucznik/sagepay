<?php require_once __DIR__ . '/../vendor/autoload.php';

class TestSuccess extends \SagePay\Test\TestBase {

	public function testOne() {
		$this->customerData->expiryDate = '1216';
		$this->sagepay->customerDetails = $this->customerData;
		$this->sagepay->execute();

		static::assertEquals(\SagePay\StatusType::OK, $this->sagepay->status);
		static::assertEquals('', $this->sagepay->error);
	}
}
