<?php
$api=$_POST['api'];
if(!$api){die("You must specify an API name!");}
$request_body = stripslashes($_POST['query']);
$additionals=null;
if($api=="Ontos"){
	$token="";
	$url= "http://rdface.aksw.org/APIs/ontosAPI.php?token=".$token."&content=".rawurlencode($request_body);
}elseif($api=="Calais"){
	$token="";
	$url= "http://api.opencalais.com/tag/rs/enrich";
	$additionals=array(
	CURLOPT_HTTPHEADER=>array('Content-Type: '."text/raw; charset=UTF-8" , "Accept: ". "application/json" ,"x-calais-licenseID:".$token),
	CURLOPT_POSTFIELDS=>$request_body
	);
}elseif($api=="Extractiv"){
	$token="";
	$url= "http://rest.extractiv.com/extractiv/?output_format=JSON&api_key=".$token."&content=".urlencode($request_body);
}elseif($api=="Alchemy"){
	$token="";
	$url="http://access.alchemyapi.com/calls/html/HTMLGetRankedNamedEntities?outputMode=json&apikey=".$token."&html=".urlencode($request_body);
}elseif($api=="Evri"){
	$token="";
	$url="http://api.evri.com/media/entities.json?appId=".$token."&uri=http://www.aksw.org&text=".urlencode($request_body);
}elseif($api=="Lupedia"){
	//$token="";
	$url="http://lupedia.ontotext.com/lookup/text2json?lookupText=".urlencode($request_body);
	$additionals=array(CURLOPT_HTTPHEADER=>array("Accept: ". "application/json"));
}elseif($api=="Swoogle"){
	$token="demo";
	$url="http://sparql.cs.umbc.edu:80/swoogle31/q?queryType=search_swd_ontology&key=".$token."&searchString=".urlencode($request_body);
}elseif($api=="DBpedia"){
/*
	$token="";
	$url="http://spotlight.dbpedia.org/rest/annotate?text=".urlencode($request_body)."&confidence=0.2&support=20";
	$additionals=array(CURLOPT_HTTPHEADER=>array("Accept: ". "application/json"));
	*/
    $url= "http://spotlight.sztaki.hu:2222/rest/annotate";
 	$request="text=".urlencode($request_body)."&confidence=".$_POST['confidence']."&support=20";
    $additionals=array(CURLOPT_HTTPHEADER=>array("Content-Type:application/x-www-form-urlencoded" , "Accept: application/json"),
          CURLOPT_POSTFIELDS=>$request,
          CURLOPT_POST=>1
    );
}elseif($api=="Saplo"){
	$api_key="";
	$secret_key="";
	$url="http://api.saplo.com/rpc/json?";
	//If it is needed to get a new token per each request
	$additionals=array(
	CURLOPT_HTTPHEADER=>array('Content-Type: '."text/raw; charset=UTF-8" , "Accept: ". "application/json"),
	CURLOPT_POSTFIELDS=>'{"method":"auth.accessToken","params":{"api_key":"'.$api_key.'","secret_key":"'.$secret_key.'"}}'
	);
	$session = curl_init ( $url);
	if($additionals){
		foreach ($additionals as $key => $value){
			curl_setopt($session, $key,$value);
		}
	}
	curl_setopt ( $session, CURLOPT_RETURNTRANSFER, true );
	$result = curl_exec ( $session );
	curl_close ( $session );
	$result=json_decode($result);	
	$token=$result->result->access_token;
	//you need to get a collection id
	$collection_id=2713;
	//then get a text id
	$url="http://api.saplo.com/rpc/json?access_token=".$token;
	$input=array("method"=>"text.create","params"=>array("body"=>$request_body,"collection_id"=>$collection_id),"id"=>"0");
	$additionals=array(
	CURLOPT_HTTPHEADER=>array('Content-Type: '."text/raw; charset=UTF-8" , "Accept: ". "application/json"),
	CURLOPT_POSTFIELDS=>json_encode($input)
	);
	$session = curl_init ( $url);
	if($additionals){
		foreach ($additionals as $key => $value){
			curl_setopt($session, $key,$value);
		}
	}
	curl_setopt ( $session, CURLOPT_RETURNTRANSFER, true );
	$result = curl_exec ( $session );
	curl_close ( $session );
	$result=json_decode($result);
	$text_id=$result->result->text_id;
	//prepare query
	$url="http://api.saplo.com/rpc/json?access_token=".$token;
	$additionals=array(
	CURLOPT_HTTPHEADER=>array('Content-Type: '."text/raw; charset=UTF-8" , "Accept: ". "application/json"),
	CURLOPT_POSTFIELDS=>'{"method":"text.tags","params":{"collection_id":'.$collection_id.',"text_id":'.$text_id.',"wait":15 },"id":0}'
	);
}elseif($api=="Sindice"){
	$token="";
	$url="http://api.sindice.com/v2/search?q=".urlencode($request_body)."&&qt=term&format=json&page=1";
	$additionals=array(CURLOPT_HTTPHEADER=>array("Accept: ". "application/json"));
}
$session = curl_init ( $url);
if($additionals){
	foreach ($additionals as $key => $value){
		curl_setopt($session, $key,$value);
	}
}
curl_setopt ( $session, CURLOPT_RETURNTRANSFER, true );
$response = curl_exec ( $session );
curl_close ( $session );
echo $response;
//var_dump(json_decode($response, true));
?>

