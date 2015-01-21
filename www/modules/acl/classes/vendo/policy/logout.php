<?php
/**
 * Policy class to determine if the user can logout
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */
class Vendo_Policy_Logout extends Policy
{
	const NOT_LOGGED_IN   = 1;
	const NOT_ACTIVE_USER = 2;

	/**
	 * Method to execute the policy
	 * 
	 * @param Model_ACL_User $user  the user account to run the policy on
	 * @param array          $extra an array of extra parameters that this policy
	 *                              can use
	 *
	 * @return bool/int
	 */
	public function execute(Model_Leap_User $user, array $extra = NULL)
	{
		if ($user->has('roles', Model_Vendo_Role::LOGIN))
		{
			return TRUE;
		}
		elseif ($user->id != Auth::instance()->get_user()->id)
		{
			return self::NOT_ACTIVE_USER;
		}
		elseif ( ! Auth::instance()->logged_in())
		{
			return self::NOT_LOGGED_IN;
		}

		return FALSE;
	}
}