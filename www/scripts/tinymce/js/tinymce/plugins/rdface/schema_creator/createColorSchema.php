<?php 
//creates random light colors
function get_random_color() {
	return 'RGB('.rand(128,256).','.rand(128,256).','.rand(128,256).')';
}
echo"<h2>Colors of Schema.org Schemas (<a href='schema_colors.css'>CSS</a>)</h2>";
$string = file_get_contents("all.json");
$schemas=json_decode($string,true);
$all_colors=array();
$css='';
foreach($schemas['types'] as $key=>$val){
	$bg= get_random_color();
	while (in_array($bg, $all_colors)) {
		$bg= get_random_color();
	}
	$all_colors[]=$bg;
	$css .='.r_'.strtolower($val['id']).' {background-color:'.$bg.';} ';
	echo '<span style="background-color:'.$bg.'">'.$val['label'].'</span><br/>';
}
$fp = fopen('schema_colors.css', 'w');
fwrite($fp, $css);
fclose($fp);

function create_colors(){
	$css='';
	foreach($schemas['types'] as $key=>$val){
		$bg= get_random_color();
		while (in_array($bg, $all_colors)) {
			$bg= get_random_color();
		}
		$all_colors[]=$bg;
		$css .='.r_'.strtolower($val['id']).' {background-color:'.$bg.';} ';
		echo '<span style="background-color:'.$bg.'">'.$val['label'].'</span><br/>';
	}
	$fp = fopen('schema_colors.css', 'w');
	fwrite($fp, $css);
	fclose($fp);	
}
?>