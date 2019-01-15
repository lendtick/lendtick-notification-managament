<?php 
namespace App\Helpers;


class TemplateEmail
{
	public static function get($templateName, $variables) {
		$template = file_get_contents($templateName);

		foreach($variables as $key => $value)
		{
			$template = str_replace('{{ '.$key.' }}', $value, $template);
		}
		return $template;
	}
}
