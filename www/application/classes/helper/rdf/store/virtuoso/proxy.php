<?php
/**
 * Created by PhpStorm.
 * User: larjohns
 * Date: 16/12/2013
 * Time: 3:33 πμ
 */

class Helper_RDF_Store_Virtuoso_Proxy {

    public function reset(){
        $sparql_config = Kohana::$config->load('sparql');
        $graph_uri = Model_Leap_Vocabulary::getGraphUri();
        $url = $sparql_config["endpoint-auth"];
        $username = $sparql_config["username"];
        $pass = $sparql_config["password"];

        $query="DELETE {graph <$graph_uri>{?s ?p ?o}}{graph <$graph_uri>{?s ?p ?o}}";
        $this->authenticateExecute($url, $username,$pass, array("query"=>$query));

    }

    public function insert($triples=array(), $graph = null){
        $sparql_config = Kohana::$config->load('sparql');
        $url = $sparql_config["endpoint-auth"];
        $username = $sparql_config["username"];
        $pass = $sparql_config["password"];

        $preamble = "INSERT IN GRAPH <$graph> {";

        $query = $preamble;

        foreach ($triples as $triple) {
            $s = $triple["s"];
            $p = $triple["p"];
            $o = $triple["o"];
            if($triple["o_type"]="uri"){
                $query .= "<$s> <$p> <$o>. ";
            }
            else{
                $query .= "<$s> <$p> '$o'. ";
            }

        }


        $query .= " } ";

        $this->authenticateExecute($url, $username,$pass, array("query"=>$query));



    }

    protected function authenticateExecute($url, $username, $password, $values){
        $process = curl_init($url);

        curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        curl_setopt($process, CURLOPT_HEADER, 1);
        curl_setopt($process, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_POST, 1);
        curl_setopt($process, CURLOPT_POSTFIELDS, $values);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($process);
        curl_close($process);
        return $return;
    }

} 