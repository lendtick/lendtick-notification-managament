<?php 
namespace App\Helpers;

class OTP
{
	public static function OTPNumber()
	{
		try {
			return rand (1000,9999);
		} catch (Exception $e) {
			return 1111;
		}
	}
}
