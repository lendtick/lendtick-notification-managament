<?php

namespace App\Helpers;

Class PutImage{

		public static function save($Url, $ImageName){
			try {
				$SaveImage = base_path().'/public/'.$ImageName;
				$response = file_put_contents($SaveImage, file_get_contents($Url));
				// $response = file_put_contents($img, file_get_contents($url));
				return TRUE;
			} catch (Exception $e) {
				return FALSE;
				// throw New \Exception('Params not found', 500);
			}
		} 
}
