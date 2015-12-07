<?php namespace SagePay;

	abstract class StatusType {
		const Ok = 'OK';
		const NotAuthed = 'NOTAUTHED';

		const Rejected = 'REJECTED';
		const Invalid = 'INVALID';
		const Malformed = 'MALFORMED';
		const Error = 'ERROR';

		const ThreeDStart = '3DAUTH';
		const ThreeDAuthenticated = 'AUTHENTICATED'; //The 3D-Secure checks were performed successfully and the card details secured at Sage Pay.
		const ThreeDRegistered = 'REGISTERED'; //3D-Secure checks failed or were not performed, but the card details are still secured at Sage Pay.
	}