<?php namespace SagePay;

/**
* SecureAuth class
* Handles the integration with 3dSecure
**/

class SecureAuth {

	public
		$vendorTxCode = '',	// vendor transaction code. must be unqiue
		$status = '',		// status returned from the cURL request
		$error = '';		// stores any errors
	private
		$md = '',			// param received from SagePay to pass with the 3D Secure request
		$pareq = '',		// param received from SagePay to pass with the 3D Secure request
		$data = array(),	// the data to post to the 3D Secure server
		$response = '',		// the response from the server
		$url = '',			// the url to pos the cURL request to
		$env = '',			// the environment, set according to 'ENV' site constant
		$curl_str = '';		// the url encoded string derrived from the $this->data array


	/**
	 * Constructor method
	 * Sets the $this->env property, assigns the necessary urls,
	 * sets and formats the data to pass to 3D Secure
	 * @param array $data - the data provided by the user (billing, price and card)
	 **/
	public function __construct($data) {
		$this->data = $data;

		$this->env = $_ENV;
		$this->setUrls();
		$this->formatData();
		$this->execute();
	}

	/**
	 * setUrls method
	 * Selects which SagePay url to use (live or test)
	 * based on the $this->env property
	 * @return void
	 **/
	private function setUrls() {
		$this->url = ($this->env === 'DEVELOPMENT') ? 'https://test.sagepay.com/gateway/service/direct3dcallback.vsp' : 'https://live.sagepay.com/gateway/service/direct3dcallback.vsp';
	}


	/**
	 * formatData method
	 * Takes $this->data and converts it to
	 * a url encoded query string
	 * @return void
	 **/
	private function formatData() {
		// Initialise arr variable
		$str = array();

		// Step through the fields
		foreach($this->data as $key => $value){
			// Stick them together as key=value pairs (url encoded)
			$str[] = $key . '=' . urlencode($value);
		}

		// Implode the arry using & as the glue and store the data
		$this->curl_str = implode('&', $str);
	}


	/**
	 * execute method
	 * Executes the cURL request to SagePay and formats the result
	 *
	 * @return void
	 **/
	private function execute() {
		// Max exec time of 1 minute.
		set_time_limit(60);
		// Open cURL request
		$curl_session = curl_init();

		// Set the url to post request to
		curl_setopt ($curl_session, CURLOPT_URL, $this->url);
		// cURL params
		curl_setopt ($curl_session, CURLOPT_HEADER, 0);
		curl_setopt ($curl_session, CURLOPT_POST, 1);
		// Pass it the query string we created from $this->data earlier
		curl_setopt ($curl_session, CURLOPT_POSTFIELDS, $this->curl_str);
		// Return the result instead of print
		curl_setopt($curl_session, CURLOPT_RETURNTRANSFER,1);
		// Set a cURL timeout of 30 seconds
		curl_setopt($curl_session, CURLOPT_TIMEOUT,30);
		curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, FALSE);

		// Send the request and convert the return value to an array
		$response = preg_split('/$\R?^/m',curl_exec($curl_session));

		// Check that it actually reached the SagePay server
		// If it didn't, set the status as FAIL and the error as the cURL error
		if (curl_error($curl_session)){
			$this->status = 'FAIL';
			$this->error = curl_error($curl_session);
		}

		// Close the cURL session
		curl_close ($curl_session);

		// Turn the response into an associative array
		for ($i=0; $i < count($response); $i++) {
			// Find position of first "=" character
			$splitAt = strpos($response[$i], '=');
			// Create an associative array
			$this->response[trim(substr($response[$i], 0, $splitAt))] = trim(substr($response[$i], ($splitAt+1)));
		}

		// Return values. Assign stuff based on the return 'Status' value from SagePay
		switch($this->response['Status']) {
			case 'OK':
				// Transactino made succssfully
				$this->status = 'success';
				$_SESSION['transaction']['VPSTxId'] = $this->response['VPSTxId']; // assign the VPSTxId to a session variable for storing if need be
				$_SESSION['transaction']['TxAuthNo'] = $this->response['TxAuthNo']; // assign the TxAuthNo to a session variable for storing if need be
				break;
			case '3DAUTH':
				// Transaction required 3D Secure authentication
				// The request will return two parameters that need to be passed with the 3D Secure
				$this->acsurl = $this->response['ACSURL']; // the url to request for 3D Secure
				$this->pareq = $this->response['PAReq']; // param to pass to 3D Secure
				$this->md = $this->response['MD']; // param to pass to 3D Secure
				$this->status = '3dAuth'; // set $this->status to '3dAuth' so your controller knows how to handle it
				break;
			case 'REJECTED':
				// errors for if the card is declined
				$this->status = 'declined';
				$this->error = 'Your payment was not authorised by your bank or your card details where incorrect.';
				break;
			case 'NOTAUTHED':
				// errors for if their card doesn't authenticate
				$this->status = 'notauthed';
				$this->error = 'Your payment was not authorised by your bank or your card details where incorrect.';
				break;
			case 'INVALID':
				// errors for if the user provides incorrect card data
				$this->status = 'invalid';
				$this->error = 'One or more of your card details where invalid. Please try again.';
				break;
			case 'FAIL':
				// errors for if the transaction fails for any reason
				$this->status = 'fail';
				$this->error = 'An unexpected error has occurred. Please try again.';
				break;
			default:
				// default error if none of the above conditions are met
				$this->status = 'error';
				$this->error = 'An error has occurred. Please try again.';
				break;
		}

		// set error sessions if the request failed or was declined to be handled by controller
		if($this->status !== 'success') {
			$_SESSION['error']['status'] = $this->status;
			$_SESSION['error']['description'] = $this->error;
		}
	}
}
