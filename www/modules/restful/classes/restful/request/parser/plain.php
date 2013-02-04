<?php defined('SYSPATH') or die('No direct script access.');

/**
 * PLAIN Request Data Parser class for text/plain mime-type.
 *
 * @package		RESTful
 * @category	Parsers
 * @author		Michał Musiał
 * @copyright	(c) 2011 Michał Musiał
 */
class RESTful_Request_Parser_PLAIN implements RESTful_Request_IParser
{
	/**
	 * @param	string $data
	 * @return	string
	 */
	static public function parse($request_body)
	{
		return $request_body;
	}
}
