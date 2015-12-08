<?php namespace SagePay;

	abstract class StatusType {
		const OK = 'OK';
		const NOTAUTHED = 'NOTAUTHED';

		const REJECTED = 'REJECTED';
		const INVALID = 'INVALID';
		const MALFORMED = 'MALFORMED';
		const ERROR = 'ERROR';

		const THREEDSTART = '3DAUTH';
		const THREEDAUTHENTICATED = 'AUTHENTICATED'; //The 3D-Secure checks were performed successfully and the card details secured at Sage Pay.
		const THREEDREGISTERED = 'REGISTERED'; //3D-Secure checks failed or were not performed, but the card details are still secured at Sage Pay.
	}