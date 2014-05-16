<?php 
error_reporting(E_ALL);
$string = file_get_contents("all.json");
$schemas=json_decode($string,true);
if(!isset($_POST['selected']))
	die('error!');

//var_dump(stripcslashes($_POST['selected']));
$objects=json_decode(stripcslashes($_POST['selected']));
//var_dump($objects);
$depth=$_POST['depth'];
$selected_properties=array();
$selected_types=array();
$datatypes=array();
$objects_array=array();
foreach ($objects as $k=>$v){
	$selected_types[]=$v->name;
	foreach ($v->properties as $prop){
		$selected_properties[]=$prop;
	}
	$objects_array[$v->name]=$v->properties;
}
$selected_properties = array_unique($selected_properties);
//var_dump($selected_properties);
$selection=array();
$selection['datatypes']=$schemas['datatypes'];
foreach($schemas['properties'] as $key=>$val){
	if (in_array($key, $selected_properties)) {
		$selection['properties'][$key]=$val;
	}
}
foreach($schemas['datatypes'] as $key=>$val){
	$datatypes[]=$key;
}
$loopStop=0;
$pre_stat=array();
$selected_properties_extra=array();
$i=0;
while(!$loopStop){
$i++;	
	//depth of browsing
	if(count($pre_stat)==count($selected_properties) || $i>$depth){
		$loopStop=1;
		break;
	}
	$pre_stat=$selected_properties;
//add extra types and properties
	foreach($selection['properties'] as $key=>$val){
		$ranges=$val['ranges'];
		foreach ($ranges as $r){
			if (!in_array($r, $datatypes)) {
				if(!in_array($r, $selected_types)){
					$selected_types[]=$r;
				}
			}	
		}
	}	
	foreach($schemas['types'] as $key=>$val){
		if (in_array($key, $selected_types)) {
			if(!count(@$objects_array[$key])){
				$selection['types'][$key]=$val;
				$selection['types'][$key]['level']=$i+1;
				$tmp_arr=array();
				foreach ($val['properties'] as $v){
					if(!in_array($v, $selected_properties_extra))
						$selected_properties_extra[]=$v;	
					//remove some properties form sub schemas
					if($v!='additionalType'){
						$tmp_arr[]=$v;
					}
				}
				$selection['types'][$key]['properties']=$tmp_arr;
			}else{
				if(!@$selection['types'][$key]){
					$selection['types'][$key]['ancestors']=$val['ancestors'];
					$selection['types'][$key]['comment']=$val['comment'];
					$selection['types'][$key]['comment_plain']=$val['comment_plain'];
					$selection['types'][$key]['id']=$val['id'];
					$selection['types'][$key]['label']=$val['label'];
	
					foreach ($val['properties'] as $v){
						if (in_array($v, $objects_array[$key])) {
							$selection['types'][$key]['properties'][]=$v;
						}	
					}
					$selection['types'][$key]['subtypes']=$val['subtypes'];
					$selection['types'][$key]['supertypes']=$val['supertypes'];
					$selection['types'][$key]['url']=$val['url'];
					//level added by me to distinguish user intended schemas and sub schemas which are required
					$selection['types'][$key]['level']=1;
				}	
			}	
		}
	}
	foreach($schemas['properties'] as $key=>$val){
		if (in_array($key, $selected_properties_extra)) {
			$selection['properties'][$key]=$val;
			if(!in_array($key, $selected_properties)){
				$selected_properties[]=$key;
			}
		}
	}	
}

$fp = fopen('selection.json', 'w');
fwrite($fp, json_encode($selection));
fclose($fp);
echo "Schema is created as selection.json";
?>