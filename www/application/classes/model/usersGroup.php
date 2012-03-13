<?php defined('SYSPATH') or die('No direct script access.');

class Model_UsersGroup extends ORM {

    public function AddUser($groupId, $userId) {
        if ($groupId != NULL and $userId != NULL) {
            $this->groupID = $groupId;
            $this->userID = $userId;

            $this->create();
        }
    }

}
