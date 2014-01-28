<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Breadcrumbs
 *
 * @author Kieran Graham
 */
class BreadcrumbTest extends PHPUnit_Framework_TestCase 
{
	/**
	 * Breadcrumb to perform tests on.
	 */
	private $crumb;
	
	/**
	 * @test
	 * @group breadcrumbs
	 */
	public function setUp()
	{
		$this->crumb = Breadcrumb::factory();
	}
	
	/**
	 * @test
	 * @group breadcrumbs
	 */
	public function test_should_confirm_factory_returns_instance()
	{
		$this->assertEquals(new Breadcrumb(), Breadcrumb::factory());
	}
	
	/**
	 * @test
	 * @depends test_should_confirm_factory_returns_instance
	 * @group breadcrumbs
	 */
	public function test_should_set_and_get_a_string_as_title()
	{
		$this->crumb->set_title("Hello");
		
		$this->assertSame("Hello", $this->crumb->get_title());
	}
	
	/**
	 * @test
	 * @depends test_should_confirm_factory_returns_instance
	 * @group breadcrumbs
	 */
	public function test_should_set_and_get_a_number_as_title()
	{
		$this->crumb->set_title(1);
		$this->assertSame("1", $this->crumb->get_title());
		
		$this->crumb->set_title(1.1);
		$this->assertSame("1.1", $this->crumb->get_title());
		
	}
	
	/**
	 * @test
	 * @group breadcrumbs
	 * @depends test_should_confirm_factory_returns_instance
	 */
	public function test_should_set_invalid_url_as_url_to_null()
	{
		$this->crumb->set_url("not a url");
		$this->assertSame(NULL, $this->crumb->get_url());
	}
	
	/**
	 * @test
	 * @depends test_should_confirm_factory_returns_instance
	 * @group breadcrumbs
	 */
	public function test_should_set_a_valid_url_as_url()
	{
		$this->crumb->set_url("http://example.com/");
		$this->assertSame("http://example.com/", $this->crumb->get_url());
	}
}