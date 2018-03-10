<?php 
$string = file_get_contents("all.json");
$schemas=json_decode($string,true);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Colors for Schema.org Schemas</title>
	<link rel="stylesheet" href="schema_colors.css" type="text/css"/>
	<link rel="stylesheet" href="../css/rdface.css" type="text/css"/>
	<script type="text/javascript" src="../libs/jstree/_lib/jquery.min.js"></script>
</head>
<body>
<table cellspacing="1" cellpadding="2" align="center">
<tr style="text-align:center"><td></td><td><b>Schema</b></td><td>Color(RGB)</td><td>Color(Hex)</td></tr>
<?php 
$i=1;
foreach($schemas['types'] as $key=>$val){
	echo '<tr style="text-align:center;"><td>'.$i.'</td><td id="'.$val['id'].'" class="r_entity r_'.strtolower($val['id']).'">'.$val['id'].'</td><td></td><td></td></tr>';
	$i++;
}
?>
</table>
<script>
function rgb2hex(rgb){
	 rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
	 return "#" +
	  ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
	  ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
	  ("0" + parseInt(rgb[3],10).toString(16)).slice(-2);
	}
$(function () {
	$('.r_entity').each(function(i,v){
		$(v).next().append(' <small>'+$(v).css('background-color')+'</small>');
		$(v).next().css('color',$(v).css('background-color'));
		$(v).next().next().append(' <b>'+rgb2hex($(v).css('background-color'))+'</b>');
		$(v).next().next().css('color',$(v).css('background-color'));
		$(v).mouseover(function(e) {
			$(v).css("background-color","orange");
			$(v).css("cursor","pointer");
		});
		$(v).mouseout(function(e) {
			$(v).css("background-color","");
		});
		$(v).click(function(e) {
			window.open('http://schema.org/'+$(v).attr('id'),'_blank');
		});
	})
});
</script>
</body>
</html>
