<?php namespace SagePay;

	/**
	 * SagePay class
	 * Handles the formatting of requests to SagePay,
	 * the actual request and response of the request
	 *
	 * @property string $basket you can use this field to supply details of the customer’s order.
	 * @property string $description Free text description of goods or services being purchased.
	 * @property string $vendorTxCode This should be your own unique reference code to the transaction
	 * @property float $amount Amount for the transaction containing minor digits formatted to 2 decimal places
	 * @property CustomerDetails $customerDetails customer card data and addresses
	 *
	 * @property-read string $status
	 * @property-read string $error
	 **/

	class SagePay extends Base {
		const PROTOCOL_VERSION = '3.0';	// SagePay protocol version

		protected $serverUrls = array();	// the URLs to post the cURL request to (set further down)
		protected $response = array();	    // response from SagePay cURL request

		protected $status = 'NOTEXECUTED';	    // status returned from the cURL request
		protected $error = '';		// stores any errors

		protected $vendor;
		protected $vendorTxCode;
		protected $amount;
		protected $currency = 'GBP';
		protected $description = ' ';
		protected $basket;

		/** @var CustomerDetails */
		protected $customerDetails = null;

		// used to store data for 3D Secure
		protected $acsurl = '';
		protected $pareq = '';
		protected $md = '';

		/**
		 * Constructor method
		 * @param string $strVendor - this should contain the SagePay vendor name supplied by SagePay when your account was created.
		 * @param string $strMode - mode of operation (simulator, test, live)
		 **/
		public function __construct($strVendor, $strMode = ModeType::SIMULATOR ) {
			$sage_pay_urls = array(
				ModeType::LIVE => array(
					'default' => 'https://live.sagepay.com/gateway/service/vspdirect-register.vsp',
					'3dsecure' => 'https://live.sagepay.com/gateway/service/direct3dcallback.vsp'
				),
				ModeType::TEST => array(
					'default' => 'https://test.sagepay.com/gateway/service/vspdirect-register.vsp',
					'3dsecure' => 'https://test.sagepay.com/gateway/service/direct3dcallback.vsp'
				),
				ModeType::SIMULATOR => array(
					'default' => 'https://test.sagepay.com/SIMULATOR/VSPDirectGateway.asp',
					'3dsecure' => 'https://test.sagepay.com/SIMULATOR/VSPDirectCallback.asp'
				)
			);
			$this->serverUrls = in_array($strMode, ModeType::$arrModeTypes, false) ? $sage_pay_urls[$strMode] : $sage_pay_urls[ModeType::SIMULATOR];

			$this->vendor = $strVendor;
		}

		/**
		 * formatData method
		 * Takes $this->data and converts it to a url encoded query string
		 *
		 * @param array $arr array with data
		 * @return string
		 **/
		private function formatData($arr) {
			$arrToReturn = array();
			foreach($arr as $key => $value){
				// assign as an item of $arr (field=value)
				$arrToReturn[] = $key . '='. urlencode($value);
			}

			// Implode the array using & as the glue and store the data
			return implode('&', $arrToReturn);
		}

		private function prepareData() {
			$arrData = array();

			// Add Payment Type
			$arrData['VPSProtocol'] = self::PROTOCOL_VERSION;
			$arrData['TxType'] = 'PAYMENT';

			// Add currency details
			$arrData['Amount'] = $this->amount;
			$arrData['Currency'] = $this->currency;

			$arrData['Vendor'] = $this->vendor;
			$arrData['VendorTxCode'] = $this->vendorTxCode;

			$arrData['Description'] = $this->description;
			$arrData['Basket'] = $this->basket;

			if ( array_key_exists('REMOTE_ADDR', $_SERVER) && $_SERVER['REMOTE_ADDR'] !== '::1' ) {
				$arrData['ClientIPAddress'] = $_SERVER['REMOTE_ADDR'];
			}

			return $arrData;
		}

		/**
		 * execute method
		 * Executes the cURL request to SagePay and formats the result
		 *
		 * @return void
		 **/
		public function execute() {
			$arrPost = $this->prepareData();
			$arrPost = array_merge($arrPost, $this->customerDetails->prepareData() );

			set_time_limit(60); // Max exec time of 1 minute.
			$curl_session = curl_init(); // Open cURL request

			curl_setopt ($curl_session, CURLOPT_URL, $this->serverUrls['default']); 	// Set the url to post request to
			curl_setopt ($curl_session, CURLOPT_HEADER, 0); // cURL params

			// Pass it the query string we created from $this->data earlier
			curl_setopt ($curl_session, CURLOPT_POSTFIELDS, $this->formatData($arrPost));
			curl_setopt ($curl_session, CURLOPT_POST, 1);

			curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, 1); // Return the result instead of print
			curl_setopt($curl_session, CURLOPT_TIMEOUT,30); // Set a cURL timeout of 30 seconds
			curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, FALSE);

			// Send the request and convert the return value to an array
			$response = preg_split('/$\R?^/m', curl_exec($curl_session));
			$responseLength = count($response);


			// Check that it actually reached the SagePay server
			// If it didn't, set the status as FAIL and the error as the cURL error
			if (curl_error($curl_session)) {
				$this->status = StatusType::Error;
				$this->error = curl_error($curl_session);
			}

			// Close the cURL session
			curl_close($curl_session);

			// Turn the response into an associative array

			for ($i=0; $i < $responseLength; $i++) {
				// Find position of first "=" character
				$splitAt = strpos($response[$i], '=');
				// Create an associative array
				$this->response[trim(substr($response[$i], 0, $splitAt))] = trim(substr($response[$i], $splitAt + 1));
			}

			// obfuscate credit card number
			$this->customerDetails->obfuscateCardNumber();

			// Return values. Assign stuff based on the return 'Status' value from SagePay

			switch($this->response['Status']) {
				case StatusType::Ok:
					// Transaction made successfully
					$this->status = StatusType::Ok;
					$_SESSION['transaction']['VPSTxId'] = $this->response['VPSTxId']; // assign the VPSTxId to a session variable for storing if need be
					$_SESSION['transaction']['TxAuthNo'] = $this->response['TxAuthNo']; // assign the TxAuthNo to a session variable for storing if need be
					break;

				case StatusType::ThreeDStart:
					// Transaction required 3D Secure authentication
					// The request will return two parameters that need to be passed with the 3D Secure
					$this->acsurl = $this->response['ACSURL']; // the url to request for 3D Secure
					$this->pareq = $this->response['PAReq']; // param to pass to 3D Secure
					$this->md = $this->response['MD']; // param to pass to 3D Secure
					$this->status = StatusType::ThreeDStart; // set $this->status to '3dAuth' so your controller knows how to handle it
					break;

				case StatusType::Rejected:
					// errors for if the card is declined
					$this->status = StatusType::Rejected;
					$this->error = $this->response['StatusDetail'];
					break;

				case StatusType::NotAuthed:
					// errors for if their card doesn't authenticate
					$this->status = StatusType::NotAuthed;
					$this->error = $this->response['StatusDetail'];
					break;

				case StatusType::Invalid:
					// errors for if the user provides incorrect card data
					$this->status = StatusType::Invalid;
					$this->error = $this->response['StatusDetail'];
					break;

				default:
					// default error if none of the above conditions are met
					$this->status = StatusType::Error;
					$this->error = $this->response['StatusDetail'];
					break;
			}
		}

		public function __set($name, $value) {
			switch ($name) {
				case 'basket':
					if (Helper::isLengthBetween($value, 0, 7500)) {
						$this->basket = $value;
					}
					break;

				case 'description':
					if (Helper::isLengthBetween($value, 0, 100)) {
						$this->description = $value;
					}
					break;

				case 'vendorTxCode':
					if (Helper::isLengthBetween($value, 1, 20)) {
						$this->vendorTxCode = $this->vendor . '.' . $value . '.' . @date('Y-m-d_H-i-s');
					}
					break;

				case 'amount':
					if (is_numeric($value) === true && $value >= 0.01 && $value <= 10000 ) {
						$this->amount = number_format($value, 2, '.', '');
					}
					break;

				case 'customerDetails':
					if ( $value instanceof CustomerDetails ) {
						$this->customerDetails = $value;
					}
					break;

				default:
					parent::__set($name, $value);
			}
		}

		public function __get($name) {
			switch ($name) {
				case 'basket': return $this->basket;
				case 'description': return $this->description;
				case 'vendorTxCode': return $this->vendorTxCode;
				case 'amount': return $this->amount;
				case 'customerDetails': return $this->customerDetails;

				case 'status': return $this->status;
				case 'error': return $this->error;

				default:
					if ( isset($this->$name) ) {
						return $this->$name;
					}

					return parent::__get($name);
			}
		}
	}