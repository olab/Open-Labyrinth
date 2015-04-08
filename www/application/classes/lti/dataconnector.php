<?php
/**
 * Open Labyrinth [ http://www.openlabyrinth.ca ]
 *
 * Open Labyrinth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Open Labyrinth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Open Labyrinth.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright Copyright 2012 Open Labyrinth. All Rights Reserved.
 *
 */
defined('SYSPATH') or die('No direct script access.');

class Lti_DataConnector {

###
###  Lti_Tool_Consumer methods
###

###
#    Load the tool consumer from the database
###
    public function Tool_Consumer_load(Lti_ToolConsumer $consumer) {

        $ok = FALSE;
        $row = DB_ORM::select('Lti_Consumer')->where('consumer_key', '=', $consumer->getKey())->query()->fetch(0);
        if ($row) {
            $consumer->name = $row->name;
            $consumer->secret = $row->secret;
            $consumer->lti_version = $row->lti_version;
            $consumer->consumer_name = $row->consumer_name;
            $consumer->consumer_version = $row->consumer_version;
            $consumer->consumer_guid = $row->consumer_guid;
            $consumer->css_path = $row->css_path;
            $consumer->protected = ($row->protected == 1);
            $consumer->enabled = ($row->enabled == 1);
            $consumer->enable_from = NULL;
            if (!is_null($row->enable_from)) {
                $consumer->enable_from = strtotime($row->enable_from);
            }
            $consumer->enable_until = NULL;
            if (!is_null($row->enable_until)) {
                $consumer->enable_until = strtotime($row->enable_until);
            }
            $consumer->without_end_date = $row->without_end_date;
            $consumer->last_access = NULL;
            if (!is_null($row->last_access)) {
                $consumer->last_access = strtotime($row->last_access);
            }
            $consumer->created = strtotime($row->created);
            $consumer->updated = strtotime($row->updated);
            $consumer->role = $row->role;
            $ok = TRUE;
        }
        return $ok;
    }

###
#    Save the tool consumer to the database
###
    public function Tool_Consumer_save($consumer) {
        $key = $consumer->getKey();

        if ($consumer->protected) {
            $protected = 1;
        } else {
            $protected = 0;
        }
        if ($consumer->enabled) {
            $enabled = 1;
        } else {
            $enabled = 0;
        }
        $time = time();
        $now = date('Y-m-d H:i:s', $time);
        $from = NULL;
        if (!is_null($consumer->enable_from)) {
            $from = date('Y-m-d H:i:s', $consumer->enable_from);
        }
        $until = NULL;
        if (!is_null($consumer->enable_until)) {
            $until = date('Y-m-d H:i:s', $consumer->enable_until);
        }
        $last = NULL;
        if (!is_null($consumer->last_access)) {
            $last = date('Y-m-d', $consumer->last_access);
        }

        $data = array(
            'consumer_key'     => $key,
            'name'             => $consumer->name,
            'secret'           => $consumer->secret,
            'lti_version'      => $consumer->lti_version,
            'consumer_name'    => $consumer->consumer_name,
            'consumer_version' => $consumer->consumer_version,
            'consumer_guid'    => $consumer->consumer_guid,
            'css_path'         => $consumer->css_path,
            'protected'        => $protected,
            'enabled'          => $enabled,
            'enable_from'      => $from,
            'enable_until'     => $until,
            'without_end_date' => $consumer->without_end_date,
            'last_access'      => $last,
            'created'          => $now,
            'updated'          => $now,
            'role'             => $consumer->role
        );
        if (is_null($consumer->created)) {
            $key = '';
            $ok = DB_ORM::model('Lti_Consumer')->saveConsumer($key, $data);
        } else {
            $ok = DB_ORM::model('Lti_Consumer')->saveConsumer($key, $data);
        }
        return $ok;
    }

###
#    Delete the tool consumer from the database
###
    public function Tool_Consumer_delete($consumer) {

// Delete any nonce values for this consumer
        DB_ORM::delete('Lti_Nonce')->where('consumer_key','=', $consumer->getKey())->execute();

// Delete any outstanding share keys for resource links for this consumer
        DB_ORM::delete('Lti_ShareKey')->where('primary_consumer_key','=', $consumer->getKey())->execute();

// Delete any users in resource links for this consumer
        DB_ORM::delete('Lti_User')->where('consumer_key','=', $consumer->getKey())->execute();

// Update any resource links for which this consumer is acting as a primary resource link
        $data = array(
            'primary_consumer_key' => NULL,
            'primary_context_id' => NULL
        );
        DB_ORM::model('Lti_Context')->updateContext($consumer->getKey(), $data);

// Delete any resource links for this consumer
        DB_ORM::delete('Lti_Context')->where('consumer_key','=', $consumer->getKey())->execute();

// Delete consumer
        DB_ORM::delete('Lti_Consumer')->where('consumer_key','=', $consumer->getKey())->execute();
        $consumer->initialise();
        return true;
    }

###
#    Load all tool consumers from the database
###
    public function Tool_Consumer_list() {

        $consumers = array();
        $rows = DB_ORM::model('Lti_Consumer')->getAllRecords();
        if (is_array($rows)) {
            foreach ($rows as $row){
                $consumer = new Lti_ToolConsumer($row->consumer_key, $this);
                $consumer->name = $row['name'];
                $consumer->secret = $row['secret'];
                $consumer->lti_version = $row['lti_version'];
                $consumer->consumer_name = $row['consumer_name'];
                $consumer->consumer_version = $row['consumer_version'];
                $consumer->consumer_guid = $row['consumer_guid'];
                $consumer->css_path = $row['css_path'];
                $consumer->protected = ($row['protected'] == 1);
                $consumer->enabled = ($row['enabled'] == 1);
                $consumer->enable_from = NULL;
                if (!is_null($row['enable_from'])) {
                    $consumer->enable_from = strtotime($row['enable_from']);
                }
                $consumer->enable_until = NULL;
                if (!is_null($row['enable_until'])) {
                    $consumer->enable_until = strtotime($row['enable_until']);
                }
                $consumer->without_end_date = $row['without_end_date'];
                $consumer->last_access = NULL;
                if (!is_null($row['last_access'])) {
                    $consumer->last_access = strtotime($row['last_access']);
                }
                $consumer->created = strtotime($row['created']);
                $consumer->updated = strtotime($row['updated']);
                $consumer->role = $row['role'];
                $consumers[] = $consumer;
            }
        }
        return $consumers;
    }

###
###  Lti_Resource_Link methods
###

###
#    Load the resource link from the database
###
    public function Resource_Link_load($resource_link) {
        $ok = FALSE;
        $key = $resource_link->getKey();
        $id = $resource_link->getId();
        $row = DB_ORM::model('Lti_Context')->getByKeyId($key, $id);
        if ($row) {
            $resource_link->lti_context_id = $row['lti_context_id'];
            $resource_link->lti_resource_id = $row['lti_resource_id'];
            $resource_link->title = $row['title'];
            $resource_link->settings = unserialize($row['settings']);
            if (!is_array($resource_link->settings)) {
                $resource_link->settings = array();
            }
            $resource_link->primary_consumer_key = $row['primary_consumer_key'];
            $resource_link->primary_resource_link_id = $row['primary_context_id'];
            $resource_link->share_approved = (is_null($row['share_approved'])) ? NULL : ($row['share_approved'] == 1);
            $resource_link->created = strtotime($row['created']);
            $resource_link->updated = strtotime($row['updated']);
            $ok = TRUE;
        }
        return $ok;
    }

###
#    Save the resource link to the database
###
    public function Resource_Link_save($resource_link) {

        if (is_null($resource_link->share_approved)) {
            $approved = 'NULL';
        } else if ($resource_link->share_approved) {
            $approved = 1;
        } else {
            $approved = 0;
        }
        $time = time();
        $now = date('Y-m-d H:i:s', $time);
        $settingsValue = serialize($resource_link->settings);
        $data = array(
            'context_id'            => $resource_link->getId(),
            'lti_context_id'        => $resource_link->lti_context_id,
            'lti_resource_id'       => $resource_link->lti_resource_id,
            'title'                 => $resource_link->title,
            'settings'              => $settingsValue,
            'primary_consumer_key'  => $resource_link->primary_consumer_key,
            'primary_context_id'    => $resource_link->primary_resource_link_id,
            'share_approved'        => $approved,
            'created'               => $now,
            'updated'               => $now
        );
        $key = $resource_link->getKey();

        $ltiContext = DB::select()->from('lti_contexts')->where('consumer_key', '=', $key)->execute();
        // need to redone if block. Only else must presents
        if ($ltiContext[0]) {
            $data = array(
                'context_id'            => $resource_link->getId(),
                'lti_context_id'        => $ltiContext[0]['lti_context_id'],
                'lti_resource_id'       => $ltiContext[0]['lti_resource_id'],
                'title'                 => $ltiContext[0]['title'],
                'settings'              => serialize($ltiContext[0]['settings']),
                'primary_consumer_key'  => $ltiContext[0]['primary_consumer_key'],
                'primary_context_id'    => '',
                'share_approved'        => 1,
                'created'               => $ltiContext[0]['created'],
                'updated'               => $ltiContext[0]['updated']
            );
            DB_ORM::model('lti_context')->updateContext($key, $data);
        } else {
            if (is_null($resource_link->created)) {
                DB_ORM::model('lti_context')->addContext($key, $data);
                $resource_link->created = $time;
                $resource_link->updated = $time;
            } else {
                DB_ORM::model('lti_context')->updateContext($key, $data);
            }
        }

        return true;
    }

###
#    Delete the resource link from the database
###
    public function Resource_Link_delete($resource_link) {
// Delete any outstanding share keys for resource links for this consumer
        DB_ORM::delete('lti_shareKey')->where('primary_consumer_key', '=', $resource_link->getKey(), 'AND')->where('primary_context_id', '=', $resource_link->getId())->execute();

// Delete users
        DB_ORM::delete('lti_User')->where('consumer_key', '=', $resource_link->getKey(), 'AND')->where('context_id', '=', $resource_link->getId())->execute();

// Update any resource links for which this is the primary resource link
        $data = array(
            'primary_consumer_key' => NULL,
            'primary_context_id' => NULL
        );
        DB_ORM::model('Lti_Context')->updateContext($resource_link->getKey(), $data);

// Delete resource link
        DB_ORM::delete('lti_Context')->where('consumer_key', '=', $resource_link->getKey(), 'AND')->where('context_id', '=', $resource_link->getId())->execute();

        $resource_link->initialise();
        return true;

    }

###
#    Obtain an array of Lti_User objects for users with a result sourcedId.  The array may include users from other
#    resource links which are sharing this resource link.  It may also be optionally indexed by the user ID of a specified scope.
###
    public function Resource_Link_getUserResultSourcedIDs($resource_link, $local_only, $id_scope) {

        $users = array();

        if ($local_only) {


            $sql = sprintf('SELECT u.consumer_key, u.context_id, u.user_id, u.lti_result_sourcedid ' .
                "FROM {$this->dbTableNamePrefix}" . Lti_DataConnector::USER_TABLE_NAME . ' AS u '  .
                "INNER JOIN {$this->dbTableNamePrefix}" . Lti_DataConnector::RESOURCE_LINK_TABLE_NAME . ' AS c '  .
                'ON u.consumer_key = c.consumer_key AND u.context_id = c.context_id ' .
                "WHERE (c.consumer_key = %s) AND (c.context_id = %s) AND (c.primary_consumer_key IS NULL) AND (c.primary_context_id IS NULL)",
                $resource_link->getKey(),
                $resource_link->getId());
        } else {
            $sql = sprintf('SELECT u.consumer_key, u.context_id, u.user_id, u.lti_result_sourcedid ' .
                "FROM {$this->dbTableNamePrefix}" . Lti_DataConnector::USER_TABLE_NAME . ' AS u '  .
                "INNER JOIN {$this->dbTableNamePrefix}" . Lti_DataConnector::RESOURCE_LINK_TABLE_NAME . ' AS c '  .
                'ON u.consumer_key = c.consumer_key AND u.context_id = c.context_id ' .
                'WHERE ((c.consumer_key = %s) AND (c.context_id = %s) AND (c.primary_consumer_key IS NULL) AND (c.primary_context_id IS NULL)) OR ' .
                '((c.primary_consumer_key = %s) AND (c.primary_context_id = %s) AND (share_approved = 1))',
                $resource_link->getKey(),
                $resource_link->getId(),
                $resource_link->getKey(),
                $resource_link->getId());
        }
        $rs_user = mysql_query($sql);
        if ($rs_user) {
            while ($row = mysql_fetch_object($rs_user)) {
                $user = new Lti_User($resource_link, $row->user_id);
                $user->consumer_key = $row->consumer_key;
                $user->context_id = $row->context_id;
                $user->lti_result_sourcedid = $row->lti_result_sourcedid;
                if (is_null($id_scope)) {
                    $users[] = $user;
                } else {
                    $users[$user->getId($id_scope)] = $user;
                }
            }
        }

        return $users;

    }

###
#    Get an array of Lti_Resource_Link_Share objects for each resource link which is sharing this resource link.
###
    public function Resource_Link_getShares($resource_link) {

        $shares = array();

        $rows = DB_ORM::model('Lti_context')->getByKeyId($resource_link->getKey(), $resource_link->getId());
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $share = new Lti_ResourceLinkShare();
                $share->consumer_key = $row->consumer_key;
                $share->resource_link_id = $row->context_id;
                $share->title = $row->title;
                $share->approved = ($row->share_approved == 1);
                $shares[] = $share;
            }
        }
        return $shares;
    }


###
###  Lti_Consumer_Nonce methods
###

###
#    Load the consumer nonce from the database
###
    public function Consumer_Nonce_load($nonce) {
        $ok = TRUE;
#
### Delete any expired nonce values
#
        $now = date('Y-m-d H:i:s', time());
        DB_ORM::delete('Lti_Nonce')->where('expires', '<=', $now)->execute();
#
### load the nonce
#
        $row = DB_ORM::model('Lti_Nonce')->getByKeyId($nonce->getKey(), $nonce->getValue());
        if ($row == NULL) {
            $ok = FALSE;
        }
        return $ok;
    }

###
#    Save the consumer nonce in the database
###
    public function Consumer_Nonce_save(Lti_ConsumerNonce $nonce) {
        $expires = date('Y-m-d H:i:s', $nonce->expires);
        $data = array(
            'consumer_key'  => $nonce->getKey(),
            'value'         => $nonce->getValue(),
            'expires'       => $expires
        );
        DB_ORM::model('Lti_Nonce')->saveNonce($data);
        return true;
    }


###
###  Lti_Resource_Link_Share_Key methods
###

###
#    Load the resource link share key from the database
###
    public function Resource_Link_Share_Key_load($share_key) {

        $ok = FALSE;

// Clear expired share keys
        $now = date('Y-m-d H:i:s', time());
        DB_ORM::delete('Lti_ShareKey')->where('expires', '<=', $now)->execute();

// Load share key

        $row = DB_ORM::model('Lti_ShareKey', array($share_key->getId()));
        if ($row) {
            $share_key->primary_consumer_key = $row->primary_consumer_key;
            $share_key->primary_resource_link_id = $row->primary_context_id;
            $share_key->auto_approve = ($row->auto_approve == 1);
            $share_key->expires = strtotime($row->expires);
            $ok = TRUE;
        }
        return $ok;
    }

###
#    Save the resource link share key to the database
###
    static public function Resource_Link_Share_Key_save($share_key) {
        if ($share_key->auto_approve) {
            $approve = 1;
        } else {
            $approve = 0;
        }
        $expires = date('Y-m-d H:i:s', $share_key->expires);
        $data = array(
            'share_key_id'           => $share_key->getId(),
            'primary_consumer_key'   => $share_key->primary_consumer_key,
            'primary_context_id'     => $share_key->primary_resource_link_id,
            'auto_approve'           => $approve,
            'expires'                => $expires
        );
        return DB_ORM::model('Lti_ShareKey')->addShareKey($data);
    }

###
#    Delete the resource link share key from the database
###
    static public function Resource_Link_Share_Key_delete($share_key) {
        DB_ORM::delete('Lti_ShareKey')->where('share_key_id', '=', $share_key->getId())->execute();
        $share_key->initialise();
        return true;
    }

###
###  Lti_User methods
###


###
#    Load the user from the database
###
    public function User_load($user) {
        $ok = FALSE;
        $row = DB_ORM::model('Lti_User')->getByKeyContextIdUserId($user->getResourceLink()->getKey(),$user->getResourceLink()->getId(), $user->getId(Lti_ToolProvider::ID_SCOPE_ID_ONLY));
        if ($row) {
            $user->lti_result_sourcedid = $row['lti_result_sourcedid'];
            $user->created = strtotime($row['created']);
            $user->updated = strtotime($row['updated']);
            $ok = TRUE;
        }
        return $ok;
    }

    ###
    #    Save the user to the database
    ###
    public function User_save($user)
    {
        $time = time();
        $now = date('Y-m-d H:i:s', $time);
        $data = array(
            'consumer_key'          => $user->getResourceLink()->getKey(),
            'context_id'            => $user->getResourceLink()->getId(),
            'user_id'               => $user->getId(Lti_ToolProvider::ID_SCOPE_ID_ONLY),
            'lti_result_sourcedid'  => $user->lti_result_sourcedid,
            'created'               => $now,
            'updated'               => $now
        );

        if (is_null($user->created)) {
            DB_ORM::model('Lti_User')->addUser($data);
        } else {
            DB_ORM::model('Lti_User')->updateUser($data);
        }

        if (is_null($user->created)) {
            $user->created = $time;
        }
        $user->updated = $time;
        return true;
    }

###
#    Delete the user from the database
###
    public function User_delete($user) {

        DB_ORM::delete('Lti_User')->where('consumer_key', '=', $user->getResourceLink()->getKey(), 'AND')
            ->where('context_id', '=', $user->getResourceLink()->getId(), 'AND')
            ->where('user_id', '=', $user->getId(Lti_ToolProvider::ID_SCOPE_ID_ONLY));

        $ok = true;
        if ($ok) {
            $user->initialise();
        }
        return $ok;
    }

    /**
     * Generate a random string.
     *
     * The generated string will only comprise letters (upper- and lower-case) and digits.
     *
     * @param int $length Length of string to be generated (optional, default is 8 characters)
     *
     * @return string Random string
     */
    static function getRandomString($length = 8) {

        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        $value = '';
        $charsLength = strlen($chars) - 1;

        for ($i = 1 ; $i <= $length; $i++) {
            $value .= $chars[rand(0, $charsLength)];
        }

        return $value;

    }

    static function getLtiPost(){
        if ( ! empty($_POST['lti_message_type'])){
            $dataConnector = new Lti_DataConnector();
            $tool = new Lti_ToolProvider('lti_do_connect', $dataConnector);
            $tool->setParameterConstraint('resource_link_id', TRUE, 40);
            $tool->setParameterConstraint('user_id', TRUE);
            // Get settings and check whether sharing is enabled.
            $tool->allowSharing = TRUE;
            $tool->execute();
            exit();
        }
    }

}