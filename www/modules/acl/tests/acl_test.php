<?php
/**
 * Tests the acl functionality
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 *
 * @group Vendo_ACL
 */
class ACL_Test extends Vendo_TestCase
{
	/**
	 * Tests that we can process and read and delete a photo
	 * 
	 * @return null
	 */
	public function test_user_can()
	{
		// They aren't logged in
		$this->assertFalse(self::$user->can('manage_preferences'));
	}
}