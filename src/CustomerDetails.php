<?php namespace SagePay;

	/**
	 * @property string $customerEmail
	 *
	 * @property string $cardHolder
	 * @property string $cardNumber
	 * @property string $expiryDate
	 * @property string $cv2
	 * @property string $cardType
	 *
	 * @property string $billingFirstnames
	 * @property string $billingSurname
	 * @property string $billingAddress1
	 * @property string $billingAddress2
	 * @property string $billingCity
	 * @property string $billingPostCode
	 * @property string $billingCountry
	 * @property string $billingState
	 * @property string $billingPhone
	 *
	 * @property string $deliveryFirstnames
	 * @property string $deliverySurname
	 * @property string $deliveryAddress1
	 * @property string $deliveryAddress2
	 * @property string $deliveryCity
	 * @property string $deliveryPostCode
	 * @property string $deliveryCountry
	 * @property string $deliveryState
	 * @property string $deliveryPhone
	 **/

	class CustomerDetails extends Base {

		protected $customerEmail;

		protected $cardHolder;
		protected $cardNumber;
		protected $expiryDate;
		protected $cv2;
		protected $cardType;

		protected $billingFirstnames;
		protected $billingSurname;
		protected $billingAddress1;
		protected $billingAddress2;
		protected $billingCity;
		protected $billingPostCode;
		protected $billingCountry;
		protected $billingState;
		protected $billingPhone;

		protected $deliveryFirstnames;
		protected $deliverySurname;
		protected $deliveryAddress1;
		protected $deliveryAddress2;
		protected $deliveryCity;
		protected $deliveryPostCode;
		protected $deliveryCountry;
		protected $deliveryState;
		protected $deliveryPhone;

		public function prepareData() {
			$arrData = array();

			//card info
			$arrData['cardHolder'] = $this->cardHolder;
			$arrData['cardNumber'] = $this->cardNumber;
			$arrData['ExpiryDate'] = $this->expiryDate;
			$arrData['cardType'] = $this->cardType;
			$arrData['cv2'] = $this->cv2;

			//billing details
			$arrData['BillingFirstnames'] = $this->billingFirstnames;
			$arrData['BillingSurname'] = $this->billingSurname;
			$arrData['BillingAddress1'] = $this->billingAddress1;
			$arrData['BillingAddress2'] = $this->billingAddress2;
			$arrData['BillingCity'] = $this->billingCity;
			$arrData['BillingPostCode'] = $this->billingPostCode;
			$arrData['BillingCountry'] = $this->billingCountry;
			$arrData['BillingState'] = "";

			//delivery data
			$arrData['DeliveryFirstnames'] = $this->deliveryFirstnames;
			$arrData['DeliverySurname'] = $this->deliverySurname;
			$arrData['DeliveryAddress1'] = $this->deliveryAddress1;
			$arrData['DeliveryAddress2'] = $this->deliveryAddress2;
			$arrData['DeliveryCity'] = $this->deliveryCity;
			$arrData['DeliveryPostCode'] = $this->deliveryPostCode;
			$arrData['DeliveryCountry'] = $this->deliveryCountry;
			$arrData['DeliveryState'] = "";

			return $arrData;
		}

		public function obfuscateCardNumber() {
			$str = "";
			for ($i = (strlen($this->cardNumber) - 4); $i > 0; $i--)
				$str = $str . '*';
			$this->cardNumber = $str . substr($this->cardNumber, -4);

			$str = '';
			for ($i = strlen($this->cv2); $i > 0; $i--)
				$str = $str . '*';
			$this->cv2 = $str;
		}

		public function __set($name, $value) {
			switch ($name) {
				case 'cardHolder':
					if (Validate::cardHolder($value))
						$this->cardHolder = $value;
					break;

				case 'cardNumber':
					$value = str_replace(' ', '', $value); // remove spaces
					$value = str_replace('-', '', $value); // remove dashes
					if (Validate::cardNumber($value))
						$this->cardNumber = $value;
					break;

				case 'cardType':
					if (Validate::cardType($value))
						$this->cardType = $value;
					break;

				case 'cv2':
					if (Validate::cv2($value, $this->cardType))
						$this->cv2 = $value;
					break;

				case 'expiryDate':
					if (Validate::date($value))
						$this->expiryDate = $value;
					break;

				case 'billingFirstnames':
				case 'deliveryFirstnames':
				case 'billingSurname':
				case 'deliverySurname':
					if (Validate::names($value))
						$this->{$name} = $value;
					break;

				case 'billingAddress1':
				case 'deliveryAddress1':
					if (Validate::address($value))
						$this->{$name} = $value;
					break;
				case 'billingAddress2':
				case 'deliveryAddress2':
					if (Validate::address($value, true))
						$this->{$name} = $value;
					break;

				case 'billingCity':
				case 'deliveryCity':
					if (Validate::isLengthBeetween($value, 1, 40))
						$this->{$name} = $value;
					break;

				case 'billingPostCode':
				case 'deliveryPostCode':
					if (Validate::postcode($value))
						$this->{$name} = $value;
					break;

				case 'billingCountry':
				case 'deliveryCountry':
					if (Validate::country($value))
						$this->{$name} = $value;
					break;

				case 'billingPhone':
				case 'deliveryPhone':
					if (Validate::phone($value))
						$this->{$name} = $value;
					break;

				default:
					parent::__set($name, $value);
			}
		}

		public function __get($name) {

			if (isset($this->{$name}))
				return $this->{$name};

			return parent::__get($name);
		}
	}