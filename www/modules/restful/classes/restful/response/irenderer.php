<?php defined('SYSPATH') or die('No direct script access.');

/**
 * RESTful Response Renderer Interface
 *
 * @package		RESTful
 * @category	Interfaces
 * @author		Michał Musiał
 * @copyright	(c) 2011 Michał Musiał
 */
interface RESTful_Response_IRenderer
{
	/**
	 * @param mixed $data
	 */
	static public function render($data);
}
