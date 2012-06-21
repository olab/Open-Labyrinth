<?php defined('SYSPATH') or die('No direct script access.'); ?>

2012-06-21 10:44:24 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/nodeManager/grid/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2012-06-21 10:44:24 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/nodeManager/grid/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#3 {main}
2012-06-21 10:48:04 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/nodeManager/grid/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2012-06-21 10:48:04 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/nodeManager/grid/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#3 {main}
2012-06-21 10:48:05 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/nodeManager/grid/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2012-06-21 10:48:05 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/nodeManager/grid/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#3 {main}
2012-06-21 10:48:09 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/nodeManager/grid/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2012-06-21 10:48:09 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/nodeManager/grid/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#3 {main}
2012-06-21 10:48:10 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/nodeManager/grid/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2012-06-21 10:48:10 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/nodeManager/grid/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#3 {main}
2012-06-21 10:48:47 --- ERROR: ErrorException [ 2 ]: opendir(I:/xampp/htdocs/files/,I:/xampp/htdocs/files/) [function.opendir]: The system cannot find the file specified. (code: 2) ~ APPPATH\classes\model\leap\map\element.php [ 270 ]
2012-06-21 10:48:47 --- STRACE: ErrorException [ 2 ]: opendir(I:/xampp/htdocs/files/,I:/xampp/htdocs/files/) [function.opendir]: The system cannot find the file specified. (code: 2) ~ APPPATH\classes\model\leap\map\element.php [ 270 ]
--
#0 [internal function]: Kohana_Core::error_handler(2, 'opendir(I:/xamp...', 'I:\xampp\htdocs...', 270, Array)
#1 I:\xampp\htdocs\openlab\www\application\classes\model\leap\map\element.php(270): opendir('I:/xampp/htdocs...')
#2 I:\xampp\htdocs\openlab\www\application\classes\controller\fileManager.php(30): Model_Leap_Map_Element->getFilesSize()
#3 [internal function]: Controller_FileManager->action_index()
#4 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_FileManager))
#5 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#8 {main}
2012-06-21 10:48:48 --- ERROR: ErrorException [ 2 ]: opendir(I:/xampp/htdocs/files/,I:/xampp/htdocs/files/) [function.opendir]: The system cannot find the file specified. (code: 2) ~ APPPATH\classes\model\leap\map\element.php [ 270 ]
2012-06-21 10:48:48 --- STRACE: ErrorException [ 2 ]: opendir(I:/xampp/htdocs/files/,I:/xampp/htdocs/files/) [function.opendir]: The system cannot find the file specified. (code: 2) ~ APPPATH\classes\model\leap\map\element.php [ 270 ]
--
#0 [internal function]: Kohana_Core::error_handler(2, 'opendir(I:/xamp...', 'I:\xampp\htdocs...', 270, Array)
#1 I:\xampp\htdocs\openlab\www\application\classes\model\leap\map\element.php(270): opendir('I:/xampp/htdocs...')
#2 I:\xampp\htdocs\openlab\www\application\classes\controller\fileManager.php(30): Model_Leap_Map_Element->getFilesSize()
#3 [internal function]: Controller_FileManager->action_index()
#4 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_FileManager))
#5 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#8 {main}
2012-06-21 10:49:01 --- ERROR: ErrorException [ 2 ]: opendir(I:/xampp/htdocs/files/,I:/xampp/htdocs/files/) [function.opendir]: The system cannot find the file specified. (code: 2) ~ APPPATH\classes\model\leap\map\element.php [ 270 ]
2012-06-21 10:49:01 --- STRACE: ErrorException [ 2 ]: opendir(I:/xampp/htdocs/files/,I:/xampp/htdocs/files/) [function.opendir]: The system cannot find the file specified. (code: 2) ~ APPPATH\classes\model\leap\map\element.php [ 270 ]
--
#0 [internal function]: Kohana_Core::error_handler(2, 'opendir(I:/xamp...', 'I:\xampp\htdocs...', 270, Array)
#1 I:\xampp\htdocs\openlab\www\application\classes\model\leap\map\element.php(270): opendir('I:/xampp/htdocs...')
#2 I:\xampp\htdocs\openlab\www\application\classes\controller\fileManager.php(30): Model_Leap_Map_Element->getFilesSize()
#3 [internal function]: Controller_FileManager->action_index()
#4 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_FileManager))
#5 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#8 {main}
2012-06-21 10:49:03 --- ERROR: ErrorException [ 2 ]: opendir(I:/xampp/htdocs/files/,I:/xampp/htdocs/files/) [function.opendir]: The system cannot find the file specified. (code: 2) ~ APPPATH\classes\model\leap\map\element.php [ 270 ]
2012-06-21 10:49:03 --- STRACE: ErrorException [ 2 ]: opendir(I:/xampp/htdocs/files/,I:/xampp/htdocs/files/) [function.opendir]: The system cannot find the file specified. (code: 2) ~ APPPATH\classes\model\leap\map\element.php [ 270 ]
--
#0 [internal function]: Kohana_Core::error_handler(2, 'opendir(I:/xamp...', 'I:\xampp\htdocs...', 270, Array)
#1 I:\xampp\htdocs\openlab\www\application\classes\model\leap\map\element.php(270): opendir('I:/xampp/htdocs...')
#2 I:\xampp\htdocs\openlab\www\application\classes\controller\fileManager.php(30): Model_Leap_Map_Element->getFilesSize()
#3 [internal function]: Controller_FileManager->action_index()
#4 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_FileManager))
#5 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#8 {main}
2012-06-21 10:51:06 --- ERROR: ErrorException [ 2 ]: opendir(I:/xampp/htdocs/files/,I:/xampp/htdocs/files/) [function.opendir]: The system cannot find the file specified. (code: 2) ~ APPPATH\classes\model\leap\map\element.php [ 270 ]
2012-06-21 10:51:06 --- STRACE: ErrorException [ 2 ]: opendir(I:/xampp/htdocs/files/,I:/xampp/htdocs/files/) [function.opendir]: The system cannot find the file specified. (code: 2) ~ APPPATH\classes\model\leap\map\element.php [ 270 ]
--
#0 [internal function]: Kohana_Core::error_handler(2, 'opendir(I:/xamp...', 'I:\xampp\htdocs...', 270, Array)
#1 I:\xampp\htdocs\openlab\www\application\classes\model\leap\map\element.php(270): opendir('I:/xampp/htdocs...')
#2 I:\xampp\htdocs\openlab\www\application\classes\controller\fileManager.php(30): Model_Leap_Map_Element->getFilesSize()
#3 [internal function]: Controller_FileManager->action_index()
#4 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_FileManager))
#5 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#8 {main}
2012-06-21 10:51:07 --- ERROR: ErrorException [ 2 ]: opendir(I:/xampp/htdocs/files/,I:/xampp/htdocs/files/) [function.opendir]: The system cannot find the file specified. (code: 2) ~ APPPATH\classes\model\leap\map\element.php [ 270 ]
2012-06-21 10:51:07 --- STRACE: ErrorException [ 2 ]: opendir(I:/xampp/htdocs/files/,I:/xampp/htdocs/files/) [function.opendir]: The system cannot find the file specified. (code: 2) ~ APPPATH\classes\model\leap\map\element.php [ 270 ]
--
#0 [internal function]: Kohana_Core::error_handler(2, 'opendir(I:/xamp...', 'I:\xampp\htdocs...', 270, Array)
#1 I:\xampp\htdocs\openlab\www\application\classes\model\leap\map\element.php(270): opendir('I:/xampp/htdocs...')
#2 I:\xampp\htdocs\openlab\www\application\classes\controller\fileManager.php(30): Model_Leap_Map_Element->getFilesSize()
#3 [internal function]: Controller_FileManager->action_index()
#4 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_FileManager))
#5 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#8 {main}
2012-06-21 10:56:38 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/questionManager/index/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2012-06-21 10:56:38 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/questionManager/index/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#3 {main}
2012-06-21 10:56:39 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/questionManager/index/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2012-06-21 10:56:39 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/questionManager/index/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#3 {main}
2012-06-21 11:01:51 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/linkManager/editLinks/1/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2012-06-21 11:01:51 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/linkManager/editLinks/1/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#3 {main}
2012-06-21 11:01:52 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/linkManager/editLinks/1/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2012-06-21 11:01:52 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/linkManager/editLinks/1/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#3 {main}
2012-06-21 11:04:20 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/linkManager/editLinks/1/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2012-06-21 11:04:20 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/linkManager/editLinks/1/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#3 {main}
2012-06-21 11:04:21 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/linkManager/editLinks/1/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2012-06-21 11:04:21 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/linkManager/editLinks/1/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#3 {main}
2012-06-21 11:04:24 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/linkManager/editLinks/1/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2012-06-21 11:04:24 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/linkManager/editLinks/1/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#3 {main}
2012-06-21 11:04:25 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/linkManager/editLinks/1/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2012-06-21 11:04:25 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/linkManager/editLinks/1/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#3 {main}
2012-06-21 11:04:33 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/linkManager/editLinks/1/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2012-06-21 11:04:33 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/linkManager/editLinks/1/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#3 {main}
2012-06-21 11:04:34 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/linkManager/editLinks/1/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2012-06-21 11:04:34 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/linkManager/editLinks/1/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#3 {main}
2012-06-21 11:04:50 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/linkManager/editLinks/1/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2012-06-21 11:04:50 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/linkManager/editLinks/1/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#3 {main}
2012-06-21 11:04:51 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/linkManager/editLinks/1/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2012-06-21 11:04:51 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/linkManager/editLinks/1/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#3 {main}
2012-06-21 11:04:55 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/linkManager/editLinks/1/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2012-06-21 11:04:55 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/linkManager/editLinks/1/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#3 {main}
2012-06-21 11:04:56 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/linkManager/editLinks/1/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2012-06-21 11:04:56 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/linkManager/editLinks/1/1 was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#3 {main}
2012-06-21 11:17:12 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/collectionManager was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2012-06-21 11:17:12 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/collectionManager was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#3 {main}
2012-06-21 11:17:13 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/collectionManager was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2012-06-21 11:17:13 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL openlab/www/collectionManager was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 I:\xampp\htdocs\openlab\www\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 I:\xampp\htdocs\openlab\www\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 I:\xampp\htdocs\openlab\www\index.php(109): Kohana_Request->execute()
#3 {main}