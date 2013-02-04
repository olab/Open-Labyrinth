<?php defined('SYSPATH') or die('No direct script access.');

/**
 * RESTful Response class
 *
 * @package		RESTful
 * @author		Michał Musiał
 * @copyright	(c) 2011 Michał Musiał
 */
class RESTful_Response
{
	/**
	 * @var	array
	 */
	protected static $_renderers = array();

	/**
	 * @param	string $type
	 * @return	mixed Returns all renderers if $type not specified, a parser callback if found or boolean FALSE otherwise
	 */
	public static function get_renderer($type = NULL)
	{
		if ($type === NULL)
		{
			return self::$_renderers;
		}
		else
		{
			return Arr::get(self::$_renderers, $type, FALSE);
		}
	}
	
	/**
	 * @param	string $type Content MIME type
	 * @param	callback $callback
	 * @return	mixed Returns previous renderer if one existed or TRUE otherwise
	 */
	public static function register_renderer($type, $callback)
	{
		if (array_key_exists($type, self::$_renderers))
		{
			$return = self::$_renderers[$type];
		}
		else
		{
			$return = TRUE;
		}
		
		self::$_renderers[$type] = $callback;
		return $return;
	}
}
