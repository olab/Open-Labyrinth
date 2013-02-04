<?php defined('SYSPATH') or die('No direct script access.');

/**
 * PRINTR Data Response Renderer class for text/php-printr mime-type.
 *
 * @package		RESTful
 * @category	Renderers
 * @author		Michał Musiał
 * @copyright	(c) 2011 Michał Musiał
 */
class RESTful_Response_Renderer_PRINTR implements RESTful_Response_IRenderer
{
	/**
	 * @param	mixed $input
	 * @return	string
	 */
	static public function render($data)
	{
		return print_r($data, TRUE);
	}
}
