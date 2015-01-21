<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Breadcrumbs
 *
 * @author Kieran Graham
 */

class Breadcrumb
{
	/**
	 * Breadcrumb Title
	 */
	private $title = "";

	/**
	 * Breadcrumb Link
	 */
	private $url = NULL;

    /**
     * Max length of any particular crumb.
     */
    private $title_strlen = 25;

	/**
	 * Breadcrumb Factory
	 */
	public static function factory()
	{
		return new Breadcrumb;
	}

	/**
	 * Set Title
	 */
	public function set_title($title = "")
	{
		if ( ! is_string($title) AND ! is_numeric($title) AND ! (is_object($title) AND method_exists($title, "__toString")))
			throw new Breadcrumb_Exception("Breadcrumb title is not numeric or a string.");

		$this->title = (string) $title;

        if (strlen($this->title) > $this->title_strlen) {
            $this->title = '&hellip;' . trim(substr($this->title, -$this->title_strlen));
        }

		return $this;
	}

	/**
	 * Get Title
	 */
	public function get_title()
	{
		return $this->title;
	}

	/**
	 * Set Link
	 */
	public function set_url($url = "")
	{
		$this->url = $url;

		return $this;
	}

	/**
	 * Get Link
	 */
	public function get_url()
	{
		return $this->url;
	}
}