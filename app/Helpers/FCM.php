<?php

namespace App\Helpers;
use App\Helpers\RestCurl;


class FCM
{
	public static function broadcast($params = array())
	{
		// die(env('FCM_KEY_EXPO'))
		// using expo
		// cek dulu table user token nembak service notif
		if(env('FCM_TYPE') == strtolower('expo')){

			$data = array(
				'id_user' => 1
			);

			$response = RestCurl::exec('POST',env('AUTH_URL').'user-token/token-get-list',$data);

			if ($response['data']->status == 1) {

				foreach ($response['data']->data as $ress) {

					$interest[] = array(
						'to' 		=> $ress->token, 
						'title' 	=> $params['title'],
						'body' 		=> $params['body']
					);
				}

				$res = RestCurl::exec('POST', env('FCM_KEY_EXPO'), $interest);

				return $res;

			} else {
				return false;
			}
 
		}
	}

	public static function individu($id_user = null , $params = array() )
	{
		if(env('FCM_TYPE') == strtolower('expo')){

			$data = array(
				'id_user' => $id_user
			);

			$response = RestCurl::exec('POST',env('AUTH_URL').'user-token/token-get-individu',$data);

			if ($response['data']->status == 1) {

				foreach ($response['data']->data as $ress) {

					$interest[] = array(
						'to' 		=> $ress->token, 
						'title' 	=> $params['title'],
						'body' 		=> $params['body']
					);
				}

				$res = RestCurl::exec('POST', env('FCM_KEY_EXPO'), $interest);

				return true;

			} else {
				return false;
			}
 
		}
	}
}
