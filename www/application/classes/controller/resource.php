<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 11/11/2012
 * Time: 3:10 μμ
 * To change this template use File | Settings | File Templates.
 */

defined('SYSPATH') or die('No direct script access.');
class Controller_Resource extends Controller_Base
{

    public function action_index(){
       $id = $this->request->param('id', NULL);
        $type = $this->request->param('type', NULL);


        $request_type = $this->request->accept_type();

        $response_type = "text/html";

        $min_quality = 0.0;

        foreach ($request_type as $req_type => $quality) {
            if ($req_type != '*/*') {
                if ($quality >= $min_quality) {
                    $response_type = $req_type;
                    $min_quality = $quality;
                }
            }
        }



        switch ($response_type) {
            case "application/rdf+xml":
                $path = URL::base() . 'data/$type/' . $id;
                 break;
            default:
                
                $path = self::getPagePath($type,$id);
                break;
        }
        Request::initial()->redirect($path, 303);

    }

    private static function getPagePath($type, $id){
        switch($type){
            case "map":{
                if(Auth::instance()->logged_in())
                    return URL::base() . 'labyrinthManager/info/' . $id;
                else{
                    return URL::base() . 'renderlabyrinth/index/' . $id;
                }

            }


            case "user":
                return URL::base() . 'usermanager/viewUser/'. $id;
            default:
                return URL::base();
        }
    }

    public function action_map()
    {
        //var_dump($this->request);die;
//        Log::instance()->add(Log::NOTICE, print_r($this->request->headers(), true));
//        Log::instance()->add(Log::NOTICE, print_r($this->request->accept_type(), true));
//        Log::instance()->write();



    }

}
