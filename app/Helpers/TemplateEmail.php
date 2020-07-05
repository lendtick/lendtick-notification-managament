<?php 
namespace App\Helpers;


class TemplateEmail
{
	public static function get($templateName, $variables) {
		$arrContextOptions=array(
			"ssl"=>array(
				"verify_peer"=>false,
				"verify_peer_name"=>false,
			),
		);  

		$template = @file_get_contents($templateName, false, stream_context_create($arrContextOptions));
		
		foreach($variables as $key => $value)
		{
			$template = str_replace('{{ '.$key.' }}', $value, $template);
		}
		return $template;
	}
}
