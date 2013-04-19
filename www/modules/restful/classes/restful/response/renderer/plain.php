<?php defined('SYSPATH') or die('No direct script access.');

/**
 * PLAIN Data Response Renderer class for text/plain mime-type.
 *
 * @package		RESTful
 * @category	Renderers
 * @author		Michał Musiał
 * @copyright	(c) 2011 Michał Musiał
 */
class RESTful_Response_Renderer_PLAIN implements RESTful_Response_IRenderer
{
	/**
	 * @param	mixed $input
	 * @return	string
	 */
	static public function render($data)
	{
		if (is_object($data) AND ! method_exists($data, '__toString'))
			return 'Object of ' . get_class($data) . ' class';
		else
			return (string) $data;
	}
}
