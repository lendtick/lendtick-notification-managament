<?php

namespace App\Helpers;

Class Api{

		public static function format($status, $data, $ErrorMessage){
			$arr['Status'] = !empty($status) ? $status : '';
	        $arr['Data']   = !empty($data) ? $data : '';
	        $arr['ErrorMessage'] = !empty($ErrorMessage) ? $ErrorMessage : '';
	        return $arr;	
		} 
}
