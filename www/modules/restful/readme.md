# Kohana RESTful Module
## Installation

1. Copy and paste files and folders to MODPATH/restful
2. Copy MODPATH/restful/config/restful.php to your APPPATH/config folder
3. Add this entry under `Kohana::modules` array in APPPATH/bootstrap.php : `'restful' => MODPATH.'restful', // RESTful interface`
4. Create your a controller and extend `RESTful_Controller` (eg: `class Controller_YOURNAME extends RESTful_Controller`).
5. Enjoy!

## Usage

Four methods are already setup in `MODPATH/restful/classes/restful/controller.php`
> protected $_action_map = array(
>     HTTP_Request::GET    => 'get',
>     HTTP_Request::PUT    => 'update',
>     HTTP_Request::POST   => 'create',
>     HTTP_Request::DELETE => 'delete',
> );

In your controller you have to define those actions :
> public function action_get()
> {
>     // some actions..
> }
> public function action_update()
> {
>     // some actions..
> }
> public function action_create()
> {
>     // some actions..
> }
> public function action_delete()
> {
>     // some actions..
> }

