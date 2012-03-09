<?php defined('SYSPATH') or die('No direct script access.'); ?>

2012-03-07 07:19:10 --- ERROR: Kohana_Exception [ 0 ]: Cannot delete role model because it is not loaded. ~ MODPATH\orm\classes\kohana\orm.php [ 1326 ]
2012-03-07 07:19:10 --- STRACE: Kohana_Exception [ 0 ]: Cannot delete role model because it is not loaded. ~ MODPATH\orm\classes\kohana\orm.php [ 1326 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\usermanager.php(73): Kohana_ORM->delete()
#1 [internal function]: Controller_UserManager->action_editSave()
#2 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_UserManager))
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#5 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#6 {main}
2012-03-07 07:21:09 --- ERROR: ErrorException [ 1 ]: Call to undefined method Database_MySQL_Result::delete() ~ APPPATH\classes\controller\usermanager.php [ 74 ]
2012-03-07 07:21:09 --- STRACE: ErrorException [ 1 ]: Call to undefined method Database_MySQL_Result::delete() ~ APPPATH\classes\controller\usermanager.php [ 74 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-03-07 07:21:24 --- ERROR: Kohana_Exception [ 0 ]: Cannot delete role model because it is not loaded. ~ MODPATH\orm\classes\kohana\orm.php [ 1326 ]
2012-03-07 07:21:24 --- STRACE: Kohana_Exception [ 0 ]: Cannot delete role model because it is not loaded. ~ MODPATH\orm\classes\kohana\orm.php [ 1326 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\usermanager.php(75): Kohana_ORM->delete()
#1 [internal function]: Controller_UserManager->action_editSave()
#2 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_UserManager))
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#5 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#6 {main}
2012-03-07 07:23:22 --- ERROR: Database_Exception [ 1062 ]: Duplicate entry '1-1' for key 'PRIMARY' [ INSERT INTO `roles_users` (`user_id`, `role_id`) VALUES ('1', '1') ] ~ MODPATH\database\classes\kohana\database\mysql.php [ 194 ]
2012-03-07 07:23:22 --- STRACE: Database_Exception [ 1062 ]: Duplicate entry '1-1' for key 'PRIMARY' [ INSERT INTO `roles_users` (`user_id`, `role_id`) VALUES ('1', '1') ] ~ MODPATH\database\classes\kohana\database\mysql.php [ 194 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\modules\database\classes\kohana\database\query.php(245): Kohana_Database_MySQL->query(2, 'INSERT INTO `ro...', false, Array)
#1 Z:\home\localhost\www\OpenLabyrinth\modules\orm\classes\kohana\orm.php(1413): Kohana_Database_Query->execute(Object(Database_MySQL))
#2 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\usermanager.php(74): Kohana_ORM->add('roles', Object(Model_Role))
#3 [internal function]: Controller_UserManager->action_editSave()
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_UserManager))
#5 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#8 {main}
2012-03-07 07:24:20 --- ERROR: ORM_Validation_Exception [ 0 ]: Failed to validate array ~ MODPATH\orm\classes\kohana\orm.php [ 1174 ]
2012-03-07 07:24:20 --- STRACE: ORM_Validation_Exception [ 0 ]: Failed to validate array ~ MODPATH\orm\classes\kohana\orm.php [ 1174 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\modules\orm\classes\kohana\orm.php(1261): Kohana_ORM->check(NULL)
#1 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\usermanager.php(78): Kohana_ORM->update()
#2 [internal function]: Controller_UserManager->action_editSave()
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_UserManager))
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#6 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#7 {main}
2012-03-07 07:24:36 --- ERROR: Database_Exception [ 1062 ]: Duplicate entry '1-5' for key 'PRIMARY' [ INSERT INTO `roles_users` (`user_id`, `role_id`) VALUES ('1', '5') ] ~ MODPATH\database\classes\kohana\database\mysql.php [ 194 ]
2012-03-07 07:24:36 --- STRACE: Database_Exception [ 1062 ]: Duplicate entry '1-5' for key 'PRIMARY' [ INSERT INTO `roles_users` (`user_id`, `role_id`) VALUES ('1', '5') ] ~ MODPATH\database\classes\kohana\database\mysql.php [ 194 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\modules\database\classes\kohana\database\query.php(245): Kohana_Database_MySQL->query(2, 'INSERT INTO `ro...', false, Array)
#1 Z:\home\localhost\www\OpenLabyrinth\modules\orm\classes\kohana\orm.php(1413): Kohana_Database_Query->execute(Object(Database_MySQL))
#2 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\usermanager.php(75): Kohana_ORM->add('roles', Object(Model_Role))
#3 [internal function]: Controller_UserManager->action_editSave()
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_UserManager))
#5 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#8 {main}
2012-03-07 07:26:49 --- ERROR: ORM_Validation_Exception [ 0 ]: Failed to validate array ~ MODPATH\orm\classes\kohana\orm.php [ 1174 ]
2012-03-07 07:26:49 --- STRACE: ORM_Validation_Exception [ 0 ]: Failed to validate array ~ MODPATH\orm\classes\kohana\orm.php [ 1174 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\modules\orm\classes\kohana\orm.php(1261): Kohana_ORM->check(NULL)
#1 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\usermanager.php(81): Kohana_ORM->update()
#2 [internal function]: Controller_UserManager->action_editSave()
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_UserManager))
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#6 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#7 {main}
2012-03-07 07:27:19 --- ERROR: ORM_Validation_Exception [ 0 ]: Failed to validate array ~ MODPATH\orm\classes\kohana\orm.php [ 1174 ]
2012-03-07 07:27:19 --- STRACE: ORM_Validation_Exception [ 0 ]: Failed to validate array ~ MODPATH\orm\classes\kohana\orm.php [ 1174 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\modules\orm\classes\kohana\orm.php(1261): Kohana_ORM->check(NULL)
#1 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\usermanager.php(81): Kohana_ORM->update()
#2 [internal function]: Controller_UserManager->action_editSave()
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_UserManager))
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#6 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#7 {main}
2012-03-07 07:28:14 --- ERROR: ORM_Validation_Exception [ 0 ]: Failed to validate array ~ MODPATH\orm\classes\kohana\orm.php [ 1174 ]
2012-03-07 07:28:14 --- STRACE: ORM_Validation_Exception [ 0 ]: Failed to validate array ~ MODPATH\orm\classes\kohana\orm.php [ 1174 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\modules\orm\classes\kohana\orm.php(1261): Kohana_ORM->check(NULL)
#1 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\usermanager.php(81): Kohana_ORM->update()
#2 [internal function]: Controller_UserManager->action_editSave()
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_UserManager))
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#6 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#7 {main}
2012-03-07 07:29:59 --- ERROR: ORM_Validation_Exception [ 0 ]: Failed to validate array ~ MODPATH\orm\classes\kohana\orm.php [ 1174 ]
2012-03-07 07:29:59 --- STRACE: ORM_Validation_Exception [ 0 ]: Failed to validate array ~ MODPATH\orm\classes\kohana\orm.php [ 1174 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\modules\orm\classes\kohana\orm.php(1261): Kohana_ORM->check(NULL)
#1 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\usermanager.php(81): Kohana_ORM->update()
#2 [internal function]: Controller_UserManager->action_editSave()
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_UserManager))
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#6 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#7 {main}
2012-03-07 07:30:16 --- ERROR: ORM_Validation_Exception [ 0 ]: Failed to validate array ~ MODPATH\orm\classes\kohana\orm.php [ 1174 ]
2012-03-07 07:30:16 --- STRACE: ORM_Validation_Exception [ 0 ]: Failed to validate array ~ MODPATH\orm\classes\kohana\orm.php [ 1174 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\modules\orm\classes\kohana\orm.php(1261): Kohana_ORM->check(NULL)
#1 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\usermanager.php(81): Kohana_ORM->update()
#2 [internal function]: Controller_UserManager->action_editSave()
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_UserManager))
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#6 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#7 {main}
2012-03-07 07:30:41 --- ERROR: ORM_Validation_Exception [ 0 ]: Failed to validate array ~ MODPATH\orm\classes\kohana\orm.php [ 1174 ]
2012-03-07 07:30:41 --- STRACE: ORM_Validation_Exception [ 0 ]: Failed to validate array ~ MODPATH\orm\classes\kohana\orm.php [ 1174 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\modules\orm\classes\kohana\orm.php(1261): Kohana_ORM->check(NULL)
#1 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\usermanager.php(81): Kohana_ORM->update()
#2 [internal function]: Controller_UserManager->action_editSave()
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_UserManager))
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#6 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#7 {main}
2012-03-07 07:31:57 --- ERROR: ORM_Validation_Exception [ 0 ]: Failed to validate array ~ MODPATH\orm\classes\kohana\orm.php [ 1174 ]
2012-03-07 07:31:57 --- STRACE: ORM_Validation_Exception [ 0 ]: Failed to validate array ~ MODPATH\orm\classes\kohana\orm.php [ 1174 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\modules\orm\classes\kohana\orm.php(1261): Kohana_ORM->check(NULL)
#1 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\usermanager.php(81): Kohana_ORM->update()
#2 [internal function]: Controller_UserManager->action_editSave()
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_UserManager))
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#6 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#7 {main}
2012-03-07 07:32:24 --- ERROR: ORM_Validation_Exception [ 0 ]: Failed to validate array ~ MODPATH\orm\classes\kohana\orm.php [ 1174 ]
2012-03-07 07:32:24 --- STRACE: ORM_Validation_Exception [ 0 ]: Failed to validate array ~ MODPATH\orm\classes\kohana\orm.php [ 1174 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\modules\orm\classes\kohana\orm.php(1261): Kohana_ORM->check(NULL)
#1 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\usermanager.php(81): Kohana_ORM->update()
#2 [internal function]: Controller_UserManager->action_editSave()
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_UserManager))
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#6 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#7 {main}
2012-03-07 07:32:32 --- ERROR: ORM_Validation_Exception [ 0 ]: Failed to validate array ~ MODPATH\orm\classes\kohana\orm.php [ 1174 ]
2012-03-07 07:32:32 --- STRACE: ORM_Validation_Exception [ 0 ]: Failed to validate array ~ MODPATH\orm\classes\kohana\orm.php [ 1174 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\modules\orm\classes\kohana\orm.php(1261): Kohana_ORM->check(NULL)
#1 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\usermanager.php(81): Kohana_ORM->update()
#2 [internal function]: Controller_UserManager->action_editSave()
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_UserManager))
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#6 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#7 {main}
2012-03-07 07:32:57 --- ERROR: Database_Exception [ 1062 ]: Duplicate entry '1-1' for key 'PRIMARY' [ INSERT INTO `roles_users` (`user_id`, `role_id`) VALUES ('1', '1') ] ~ MODPATH\database\classes\kohana\database\mysql.php [ 194 ]
2012-03-07 07:32:57 --- STRACE: Database_Exception [ 1062 ]: Duplicate entry '1-1' for key 'PRIMARY' [ INSERT INTO `roles_users` (`user_id`, `role_id`) VALUES ('1', '1') ] ~ MODPATH\database\classes\kohana\database\mysql.php [ 194 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\modules\database\classes\kohana\database\query.php(245): Kohana_Database_MySQL->query(2, 'INSERT INTO `ro...', false, Array)
#1 Z:\home\localhost\www\OpenLabyrinth\modules\orm\classes\kohana\orm.php(1413): Kohana_Database_Query->execute(Object(Database_MySQL))
#2 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\usermanager.php(78): Kohana_ORM->add('roles', Object(Model_Role))
#3 [internal function]: Controller_UserManager->action_editSave()
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_UserManager))
#5 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#8 {main}
2012-03-07 07:35:42 --- ERROR: ErrorException [ 1 ]: Call to undefined method Model_Role::whele() ~ APPPATH\classes\controller\usermanager.php [ 43 ]
2012-03-07 07:35:42 --- STRACE: ErrorException [ 1 ]: Call to undefined method Model_Role::whele() ~ APPPATH\classes\controller\usermanager.php [ 43 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-03-07 07:37:05 --- ERROR: ErrorException [ 8 ]: Undefined variable: content ~ APPPATH\views\usermanager\base_tempate.php [ 6 ]
2012-03-07 07:37:05 --- STRACE: ErrorException [ 8 ]: Undefined variable: content ~ APPPATH\views\usermanager\base_tempate.php [ 6 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\application\views\usermanager\base_tempate.php(6): Kohana_Core::error_handler(8, 'Undefined varia...', 'Z:\home\localho...', 6, Array)
#1 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(61): include('Z:\home\localho...')
#2 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(343): Kohana_View::capture('Z:\home\localho...', Array)
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#4 [internal function]: Kohana_Controller_Template->after()
#5 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_UserManager))
#6 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#7 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#8 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#9 {main}
2012-03-07 07:39:52 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL usermanager/add was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 113 ]
2012-03-07 07:39:52 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL usermanager/add was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 113 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#3 {main}
2012-03-07 07:41:40 --- ERROR: ErrorException [ 8 ]: Undefined variable: roles ~ APPPATH\views\usermanager\add.php [ 13 ]
2012-03-07 07:41:40 --- STRACE: ErrorException [ 8 ]: Undefined variable: roles ~ APPPATH\views\usermanager\add.php [ 13 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\application\views\usermanager\add.php(13): Kohana_Core::error_handler(8, 'Undefined varia...', 'Z:\home\localho...', 13, Array)
#1 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(61): include('Z:\home\localho...')
#2 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(343): Kohana_View::capture('Z:\home\localho...', Array)
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(228): Kohana_View->render()
#4 Z:\home\localhost\www\OpenLabyrinth\application\views\usermanager\base_tempate.php(6): Kohana_View->__toString()
#5 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(61): include('Z:\home\localho...')
#6 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(343): Kohana_View::capture('Z:\home\localho...', Array)
#7 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after()
#9 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_UserManager))
#10 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#12 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#13 {main}
2012-03-07 07:46:17 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL addNewUser was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2012-03-07 07:46:17 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL addNewUser was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#3 {main}
2012-03-07 07:48:51 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: usermanager/index.php/usermanager/addNewUser ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-03-07 07:48:51 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: usermanager/index.php/usermanager/addNewUser ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#1 {main}
2012-03-07 07:49:52 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL addNewUser was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2012-03-07 07:49:52 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL addNewUser was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#3 {main}
2012-03-07 07:50:28 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL usermanager/usermanager/addNewUser was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 113 ]
2012-03-07 07:50:28 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL usermanager/usermanager/addNewUser was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 113 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#3 {main}
2012-03-07 07:52:03 --- ERROR: Database_Exception [ 1048 ]: Column 'user_id' cannot be null [ INSERT INTO `roles_users` (`user_id`, `role_id`) VALUES (NULL, '1') ] ~ MODPATH\database\classes\kohana\database\mysql.php [ 194 ]
2012-03-07 07:52:03 --- STRACE: Database_Exception [ 1048 ]: Column 'user_id' cannot be null [ INSERT INTO `roles_users` (`user_id`, `role_id`) VALUES (NULL, '1') ] ~ MODPATH\database\classes\kohana\database\mysql.php [ 194 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\modules\database\classes\kohana\database\query.php(245): Kohana_Database_MySQL->query(2, 'INSERT INTO `ro...', false, Array)
#1 Z:\home\localhost\www\OpenLabyrinth\modules\orm\classes\kohana\orm.php(1413): Kohana_Database_Query->execute(Object(Database_MySQL))
#2 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\usermanager.php(130): Kohana_ORM->add('roles', Object(Model_Role))
#3 [internal function]: Controller_UserManager->action_addNewUser()
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_UserManager))
#5 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#8 {main}
2012-03-07 07:53:34 --- ERROR: ErrorException [ 8 ]: Undefined variable: content ~ APPPATH\views\usermanager\base_tempate.php [ 6 ]
2012-03-07 07:53:34 --- STRACE: ErrorException [ 8 ]: Undefined variable: content ~ APPPATH\views\usermanager\base_tempate.php [ 6 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\application\views\usermanager\base_tempate.php(6): Kohana_Core::error_handler(8, 'Undefined varia...', 'Z:\home\localho...', 6, Array)
#1 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(61): include('Z:\home\localho...')
#2 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(343): Kohana_View::capture('Z:\home\localho...', Array)
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#4 [internal function]: Kohana_Controller_Template->after()
#5 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_UserManager))
#6 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#7 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#8 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#9 {main}
2012-03-07 07:54:02 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: usermanager/usermanager/delete/4 ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-03-07 07:54:02 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: usermanager/usermanager/delete/4 ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#1 {main}
2012-03-07 07:54:08 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: usermanager/usermanager/delete/4 ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-03-07 07:54:08 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: usermanager/usermanager/delete/4 ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#1 {main}
2012-03-07 07:58:25 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL openlabyrinth was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2012-03-07 07:58:25 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL openlabyrinth was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#3 {main}
2012-03-07 09:53:56 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: imagesimages/openlabyrinth-logo.jpg ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-03-07 09:53:56 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: imagesimages/openlabyrinth-logo.jpg ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#1 {main}
2012-03-07 09:54:12 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/openlabyrinth-logo.jpg ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-03-07 09:54:12 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/openlabyrinth-logo.jpg ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#1 {main}
2012-03-07 09:54:23 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: views/openlabyrinth-logo.jpg ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-03-07 09:54:23 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: views/openlabyrinth-logo.jpg ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#1 {main}
2012-03-07 09:54:28 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: viewsimages/openlabyrinth-logo.jpg ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-03-07 09:54:28 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: viewsimages/openlabyrinth-logo.jpg ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#1 {main}
2012-03-07 09:54:44 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: views/images/openlabyrinth-logo.jpg ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-03-07 09:54:44 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: views/images/openlabyrinth-logo.jpg ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#1 {main}
2012-03-07 09:56:41 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: cssapplication/views/images/openlabyrinth-logo.jpg ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-03-07 09:56:41 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: cssapplication/views/images/openlabyrinth-logo.jpg ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#1 {main}
2012-03-07 09:57:10 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: viewsapplication/views/images/openlabyrinth-logo.jpg ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-03-07 09:57:10 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: viewsapplication/views/images/openlabyrinth-logo.jpg ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#1 {main}
2012-03-07 09:57:20 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: views/images/openlabyrinth-logo.jpg ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-03-07 09:57:20 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: views/images/openlabyrinth-logo.jpg ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#1 {main}
2012-03-07 09:58:54 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: views/images/openlabyrinth-logo.jpg ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-03-07 09:58:54 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: views/images/openlabyrinth-logo.jpg ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#1 {main}
2012-03-07 09:58:58 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: views/images/openlabyrinth-logo.jpg ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-03-07 09:58:58 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: views/images/openlabyrinth-logo.jpg ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#1 {main}
2012-03-07 10:17:25 --- ERROR: ErrorException [ 2 ]: Attempt to assign property of non-object ~ APPPATH\classes\controller\mainDesign.php [ 13 ]
2012-03-07 10:17:25 --- STRACE: ErrorException [ 2 ]: Attempt to assign property of non-object ~ APPPATH\classes\controller\mainDesign.php [ 13 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\mainDesign.php(13): Kohana_Core::error_handler(2, 'Attempt to assi...', 'Z:\home\localho...', 13, Array)
#1 [internal function]: Controller_MainDesign->before()
#2 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(103): ReflectionMethod->invoke(Object(Controller_Main))
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#5 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#6 {main}
2012-03-07 10:17:52 --- ERROR: ErrorException [ 1 ]: Call to a member function set() on a non-object ~ APPPATH\classes\controller\mainDesign.php [ 13 ]
2012-03-07 10:17:52 --- STRACE: ErrorException [ 1 ]: Call to a member function set() on a non-object ~ APPPATH\classes\controller\mainDesign.php [ 13 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-03-07 10:18:59 --- ERROR: ErrorException [ 4 ]: syntax error, unexpected T_OBJECT_OPERATOR, expecting T_STRING or T_VARIABLE or '{' or '$' ~ APPPATH\classes\controller\mainDesign.php [ 13 ]
2012-03-07 10:18:59 --- STRACE: ErrorException [ 4 ]: syntax error, unexpected T_OBJECT_OPERATOR, expecting T_STRING or T_VARIABLE or '{' or '$' ~ APPPATH\classes\controller\mainDesign.php [ 13 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-03-07 10:19:11 --- ERROR: ErrorException [ 4 ]: syntax error, unexpected T_OBJECT_OPERATOR, expecting T_STRING or T_VARIABLE or '{' or '$' ~ APPPATH\classes\controller\mainDesign.php [ 13 ]
2012-03-07 10:19:11 --- STRACE: ErrorException [ 4 ]: syntax error, unexpected T_OBJECT_OPERATOR, expecting T_STRING or T_VARIABLE or '{' or '$' ~ APPPATH\classes\controller\mainDesign.php [ 13 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-03-07 10:19:19 --- ERROR: ErrorException [ 2 ]: Attempt to assign property of non-object ~ APPPATH\classes\controller\mainDesign.php [ 13 ]
2012-03-07 10:19:19 --- STRACE: ErrorException [ 2 ]: Attempt to assign property of non-object ~ APPPATH\classes\controller\mainDesign.php [ 13 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\mainDesign.php(13): Kohana_Core::error_handler(2, 'Attempt to assi...', 'Z:\home\localho...', 13, Array)
#1 [internal function]: Controller_MainDesign->before()
#2 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(103): ReflectionMethod->invoke(Object(Controller_Main))
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#5 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#6 {main}
2012-03-07 10:20:34 --- ERROR: ErrorException [ 2 ]: Attempt to assign property of non-object ~ APPPATH\classes\controller\mainDesign.php [ 13 ]
2012-03-07 10:20:34 --- STRACE: ErrorException [ 2 ]: Attempt to assign property of non-object ~ APPPATH\classes\controller\mainDesign.php [ 13 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\mainDesign.php(13): Kohana_Core::error_handler(2, 'Attempt to assi...', 'Z:\home\localho...', 13, Array)
#1 [internal function]: Controller_MainDesign->before()
#2 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(103): ReflectionMethod->invoke(Object(Controller_Main))
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#5 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#6 {main}
2012-03-07 10:20:47 --- ERROR: ErrorException [ 2 ]: Attempt to assign property of non-object ~ APPPATH\classes\controller\main.php [ 13 ]
2012-03-07 10:20:47 --- STRACE: ErrorException [ 2 ]: Attempt to assign property of non-object ~ APPPATH\classes\controller\main.php [ 13 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\main.php(13): Kohana_Core::error_handler(2, 'Attempt to assi...', 'Z:\home\localho...', 13, Array)
#1 [internal function]: Controller_Main->action_index()
#2 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_Main))
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#5 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#6 {main}
2012-03-07 10:20:58 --- ERROR: ErrorException [ 1 ]: Call to a member function render() on a non-object ~ SYSPATH\classes\kohana\controller\template.php [ 44 ]
2012-03-07 10:20:58 --- STRACE: ErrorException [ 1 ]: Call to a member function render() on a non-object ~ SYSPATH\classes\kohana\controller\template.php [ 44 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-03-07 10:28:01 --- ERROR: ErrorException [ 2 ]: Attempt to assign property of non-object ~ APPPATH\classes\controller\mainDesign.php [ 13 ]
2012-03-07 10:28:01 --- STRACE: ErrorException [ 2 ]: Attempt to assign property of non-object ~ APPPATH\classes\controller\mainDesign.php [ 13 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\mainDesign.php(13): Kohana_Core::error_handler(2, 'Attempt to assi...', 'Z:\home\localho...', 13, Array)
#1 [internal function]: Controller_MainDesign->before()
#2 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(103): ReflectionMethod->invoke(Object(Controller_Main))
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#5 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#6 {main}
2012-03-07 10:28:02 --- ERROR: ErrorException [ 2 ]: Attempt to assign property of non-object ~ APPPATH\classes\controller\mainDesign.php [ 13 ]
2012-03-07 10:28:02 --- STRACE: ErrorException [ 2 ]: Attempt to assign property of non-object ~ APPPATH\classes\controller\mainDesign.php [ 13 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\mainDesign.php(13): Kohana_Core::error_handler(2, 'Attempt to assi...', 'Z:\home\localho...', 13, Array)
#1 [internal function]: Controller_MainDesign->before()
#2 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(103): ReflectionMethod->invoke(Object(Controller_Main))
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#5 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#6 {main}
2012-03-07 10:28:03 --- ERROR: ErrorException [ 2 ]: Attempt to assign property of non-object ~ APPPATH\classes\controller\mainDesign.php [ 13 ]
2012-03-07 10:28:03 --- STRACE: ErrorException [ 2 ]: Attempt to assign property of non-object ~ APPPATH\classes\controller\mainDesign.php [ 13 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\mainDesign.php(13): Kohana_Core::error_handler(2, 'Attempt to assi...', 'Z:\home\localho...', 13, Array)
#1 [internal function]: Controller_MainDesign->before()
#2 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(103): ReflectionMethod->invoke(Object(Controller_Main))
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#5 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#6 {main}
2012-03-07 10:54:30 --- ERROR: ErrorException [ 1 ]: Call to a member function find() on a non-object ~ APPPATH\views\usermanager\view.php [ 21 ]
2012-03-07 10:54:30 --- STRACE: ErrorException [ 1 ]: Call to a member function find() on a non-object ~ APPPATH\views\usermanager\view.php [ 21 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-03-07 10:56:30 --- ERROR: ErrorException [ 1 ]: Call to a member function find() on a non-object ~ APPPATH\views\usermanager\view.php [ 21 ]
2012-03-07 10:56:30 --- STRACE: ErrorException [ 1 ]: Call to a member function find() on a non-object ~ APPPATH\views\usermanager\view.php [ 21 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-03-07 10:56:32 --- ERROR: ErrorException [ 1 ]: Call to a member function find() on a non-object ~ APPPATH\views\usermanager\view.php [ 21 ]
2012-03-07 10:56:32 --- STRACE: ErrorException [ 1 ]: Call to a member function find() on a non-object ~ APPPATH\views\usermanager\view.php [ 21 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-03-07 10:56:44 --- ERROR: ErrorException [ 1 ]: Class 'Model_Language' not found ~ MODPATH\orm\classes\kohana\orm.php [ 37 ]
2012-03-07 10:56:44 --- STRACE: ErrorException [ 1 ]: Class 'Model_Language' not found ~ MODPATH\orm\classes\kohana\orm.php [ 37 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-03-07 10:58:40 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'language.user_id' in 'where clause' [ SELECT `language`.* FROM `languages` AS `language` WHERE `language`.`user_id` = '2' LIMIT 1 ] ~ MODPATH\database\classes\kohana\database\mysql.php [ 194 ]
2012-03-07 10:58:40 --- STRACE: Database_Exception [ 1054 ]: Unknown column 'language.user_id' in 'where clause' [ SELECT `language`.* FROM `languages` AS `language` WHERE `language`.`user_id` = '2' LIMIT 1 ] ~ MODPATH\database\classes\kohana\database\mysql.php [ 194 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\modules\database\classes\kohana\database\query.php(245): Kohana_Database_MySQL->query(1, 'SELECT `languag...', false, Array)
#1 Z:\home\localhost\www\OpenLabyrinth\modules\orm\classes\kohana\orm.php(972): Kohana_Database_Query->execute(Object(Database_MySQL))
#2 Z:\home\localhost\www\OpenLabyrinth\modules\orm\classes\kohana\orm.php(898): Kohana_ORM->_load_result(false)
#3 Z:\home\localhost\www\OpenLabyrinth\application\views\usermanager\view.php(21): Kohana_ORM->find()
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(61): include('Z:\home\localho...')
#5 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(343): Kohana_View::capture('Z:\home\localho...', Array)
#6 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(228): Kohana_View->render()
#7 Z:\home\localhost\www\OpenLabyrinth\application\views\usermanager\base_tempate.php(6): Kohana_View->__toString()
#8 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(61): include('Z:\home\localho...')
#9 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(343): Kohana_View::capture('Z:\home\localho...', Array)
#10 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#11 [internal function]: Kohana_Controller_Template->after()
#12 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_UserManager))
#13 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#14 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#15 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#16 {main}
2012-03-07 11:00:35 --- ERROR: Kohana_Exception [ 0 ]: Method find() cannot be called on loaded objects ~ MODPATH\orm\classes\kohana\orm.php [ 885 ]
2012-03-07 11:00:35 --- STRACE: Kohana_Exception [ 0 ]: Method find() cannot be called on loaded objects ~ MODPATH\orm\classes\kohana\orm.php [ 885 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\application\views\usermanager\view.php(21): Kohana_ORM->find()
#1 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(61): include('Z:\home\localho...')
#2 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(343): Kohana_View::capture('Z:\home\localho...', Array)
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(228): Kohana_View->render()
#4 Z:\home\localhost\www\OpenLabyrinth\application\views\usermanager\base_tempate.php(6): Kohana_View->__toString()
#5 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(61): include('Z:\home\localho...')
#6 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(343): Kohana_View::capture('Z:\home\localho...', Array)
#7 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after()
#9 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_UserManager))
#10 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#12 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#13 {main}
2012-03-07 11:02:14 --- ERROR: ErrorException [ 8 ]: Trying to get property of non-object ~ APPPATH\views\usermanager\view.php [ 21 ]
2012-03-07 11:02:14 --- STRACE: ErrorException [ 8 ]: Trying to get property of non-object ~ APPPATH\views\usermanager\view.php [ 21 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\application\views\usermanager\view.php(21): Kohana_Core::error_handler(8, 'Trying to get p...', 'Z:\home\localho...', 21, Array)
#1 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(61): include('Z:\home\localho...')
#2 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(343): Kohana_View::capture('Z:\home\localho...', Array)
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(228): Kohana_View->render()
#4 Z:\home\localhost\www\OpenLabyrinth\application\views\usermanager\base_tempate.php(6): Kohana_View->__toString()
#5 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(61): include('Z:\home\localho...')
#6 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(343): Kohana_View::capture('Z:\home\localho...', Array)
#7 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after()
#9 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_UserManager))
#10 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#12 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#13 {main}
2012-03-07 11:12:27 --- ERROR: ErrorException [ 8 ]: Undefined index: lang ~ APPPATH\classes\controller\usermanager.php [ 133 ]
2012-03-07 11:12:27 --- STRACE: ErrorException [ 8 ]: Undefined index: lang ~ APPPATH\classes\controller\usermanager.php [ 133 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\usermanager.php(133): Kohana_Core::error_handler(8, 'Undefined index...', 'Z:\home\localho...', 133, Array)
#1 [internal function]: Controller_UserManager->action_addNewUser()
#2 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_UserManager))
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#5 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#6 {main}
2012-03-07 11:18:06 --- ERROR: ErrorException [ 8 ]: Undefined variable: left ~ APPPATH\views\mainTemplate.php [ 15 ]
2012-03-07 11:18:06 --- STRACE: ErrorException [ 8 ]: Undefined variable: left ~ APPPATH\views\mainTemplate.php [ 15 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\application\views\mainTemplate.php(15): Kohana_Core::error_handler(8, 'Undefined varia...', 'Z:\home\localho...', 15, Array)
#1 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(61): include('Z:\home\localho...')
#2 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\view.php(343): Kohana_View::capture('Z:\home\localho...', Array)
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#4 [internal function]: Kohana_Controller_Template->after()
#5 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_Main))
#6 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#7 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#8 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#9 {main}
2012-03-07 11:38:51 --- ERROR: ErrorException [ 1 ]: Call to undefined function iiset() ~ APPPATH\views\mainTemplate.php [ 21 ]
2012-03-07 11:38:51 --- STRACE: ErrorException [ 1 ]: Call to undefined function iiset() ~ APPPATH\views\mainTemplate.php [ 21 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-03-07 11:46:33 --- ERROR: ErrorException [ 1 ]: Call to undefined function GenerateLanguageArray() ~ APPPATH\classes\controller\mainDesign.php [ 17 ]
2012-03-07 11:46:33 --- STRACE: ErrorException [ 1 ]: Call to undefined function GenerateLanguageArray() ~ APPPATH\classes\controller\mainDesign.php [ 17 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-03-07 11:58:59 --- ERROR: ErrorException [ 1 ]: Call to undefined function GenerateLanguageArray() ~ APPPATH\classes\controller\mainDesign.php [ 21 ]
2012-03-07 11:58:59 --- STRACE: ErrorException [ 1 ]: Call to undefined function GenerateLanguageArray() ~ APPPATH\classes\controller\mainDesign.php [ 21 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-03-07 11:59:12 --- ERROR: Kohana_Exception [ 0 ]: The lang_id property does not exist in the Model_User class ~ MODPATH\orm\classes\kohana\orm.php [ 612 ]
2012-03-07 11:59:12 --- STRACE: Kohana_Exception [ 0 ]: The lang_id property does not exist in the Model_User class ~ MODPATH\orm\classes\kohana\orm.php [ 612 ]
--
#0 Z:\home\localhost\www\OpenLabyrinth\application\classes\controller\mainDesign.php(21): Kohana_ORM->__get('lang_id')
#1 [internal function]: Controller_MainDesign->before()
#2 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client\internal.php(103): ReflectionMethod->invoke(Object(Controller_Main))
#3 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#4 Z:\home\localhost\www\OpenLabyrinth\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#5 Z:\home\localhost\www\OpenLabyrinth\index.php(109): Kohana_Request->execute()
#6 {main}
2012-03-07 11:59:41 --- ERROR: ErrorException [ 1 ]: Class 'Model_Language_translate' not found ~ MODPATH\orm\classes\kohana\orm.php [ 37 ]
2012-03-07 11:59:41 --- STRACE: ErrorException [ 1 ]: Class 'Model_Language_translate' not found ~ MODPATH\orm\classes\kohana\orm.php [ 37 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-03-07 12:00:44 --- ERROR: ErrorException [ 1 ]: Class 'Model_Language_translate' not found ~ MODPATH\orm\classes\kohana\orm.php [ 37 ]
2012-03-07 12:00:44 --- STRACE: ErrorException [ 1 ]: Class 'Model_Language_translate' not found ~ MODPATH\orm\classes\kohana\orm.php [ 37 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-03-07 12:00:50 --- ERROR: ErrorException [ 1 ]: Class 'Model_Language_translate' not found ~ MODPATH\orm\classes\kohana\orm.php [ 37 ]
2012-03-07 12:00:50 --- STRACE: ErrorException [ 1 ]: Class 'Model_Language_translate' not found ~ MODPATH\orm\classes\kohana\orm.php [ 37 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-03-07 12:01:02 --- ERROR: ErrorException [ 1 ]: Class 'Model_Language_translate' not found ~ MODPATH\orm\classes\kohana\orm.php [ 37 ]
2012-03-07 12:01:02 --- STRACE: ErrorException [ 1 ]: Class 'Model_Language_translate' not found ~ MODPATH\orm\classes\kohana\orm.php [ 37 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-03-07 12:02:13 --- ERROR: ErrorException [ 1 ]: Class 'Model_Language_translate' not found ~ MODPATH\orm\classes\kohana\orm.php [ 37 ]
2012-03-07 12:02:13 --- STRACE: ErrorException [ 1 ]: Class 'Model_Language_translate' not found ~ MODPATH\orm\classes\kohana\orm.php [ 37 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-03-07 12:02:14 --- ERROR: ErrorException [ 1 ]: Class 'Model_Language_translate' not found ~ MODPATH\orm\classes\kohana\orm.php [ 37 ]
2012-03-07 12:02:14 --- STRACE: ErrorException [ 1 ]: Class 'Model_Language_translate' not found ~ MODPATH\orm\classes\kohana\orm.php [ 37 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-03-07 12:02:27 --- ERROR: ErrorException [ 1 ]: Class 'Model_Language_translate' not found ~ MODPATH\orm\classes\kohana\orm.php [ 37 ]
2012-03-07 12:02:27 --- STRACE: ErrorException [ 1 ]: Class 'Model_Language_translate' not found ~ MODPATH\orm\classes\kohana\orm.php [ 37 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-03-07 12:02:29 --- ERROR: ErrorException [ 1 ]: Class 'Model_Language_translate' not found ~ MODPATH\orm\classes\kohana\orm.php [ 37 ]
2012-03-07 12:02:29 --- STRACE: ErrorException [ 1 ]: Class 'Model_Language_translate' not found ~ MODPATH\orm\classes\kohana\orm.php [ 37 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-03-07 12:02:38 --- ERROR: ErrorException [ 1 ]: Class 'Model_Language_translate' not found ~ MODPATH\orm\classes\kohana\orm.php [ 37 ]
2012-03-07 12:02:38 --- STRACE: ErrorException [ 1 ]: Class 'Model_Language_translate' not found ~ MODPATH\orm\classes\kohana\orm.php [ 37 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-03-07 12:03:03 --- ERROR: ErrorException [ 1 ]: Class 'Model_LanguageTranslate' not found ~ MODPATH\orm\classes\kohana\orm.php [ 37 ]
2012-03-07 12:03:03 --- STRACE: ErrorException [ 1 ]: Class 'Model_LanguageTranslate' not found ~ MODPATH\orm\classes\kohana\orm.php [ 37 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}