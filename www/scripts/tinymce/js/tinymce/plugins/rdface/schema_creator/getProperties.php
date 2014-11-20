<?php 
$string = file_get_contents("all.json");
$schemas=json_decode($string,true);
$selected_schemas=explode(',',@$_POST['selected']);
if(!isset($selected_schemas))
	die('error!');
	
$out=array();	
foreach($schemas['types'] as $key=>$val){
	if (in_array($key, $selected_schemas)) {
		$out[$key]=$val['properties'];
	}
}
echo json_encode(array('out'=>$out));
?>