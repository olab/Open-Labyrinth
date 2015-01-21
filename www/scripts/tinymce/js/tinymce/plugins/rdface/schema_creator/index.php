<?php
$string = file_get_contents("all.json");
$schemas=json_decode($string,true);
?>
<!DOCTYPE HTML>
<html>
<head>
<title>
RDFaCE - Schema Selector
</title>
	<link rel="stylesheet" href="../libs/bootstrap/css/bootstrap.min.css" media="all" />
	<link rel="stylesheet" href="static/css/index.css" media="all" />
	<script type="text/javascript" src="../libs/jstree/_lib/jquery.js"></script>
	<script type="text/javascript" src="../libs/jstree/_lib/jquery.cookie.js"></script>
	<script type="text/javascript" src="../libs/jstree/_lib/jquery.hotkeys.js"></script>
	<script type="text/javascript" src="../libs/jstree/jquery.jstree.js"></script>
	<script type="text/javascript" src="static/js/index.js"></script>
</head>
<body>
<div class="page-header" id="page_header">
	<h2>
		1.Select your desirable Schemas
	</h2>
</div>
<div id="action_area">
	<input id="schema_search" type="text" class="input span5 search-query" placeholder="Search">
	<div id="schema_types">
		<ul>
			<li id="schema_root">
				<a href="http://schema.org">Schemas</a>
				<ul>
				<?php 
					foreach($schemas['types'] as $key=>$val){
						echo '<li id="'.$val['id'].'"><a title="'.$val['comment_plain'].'" href="'.$val['url'].'">'.$key.'</a></li>';
					}
				?>
				</ul>
			</li>
		</ul>
	</div>
	<center><a id="next_btn" class="btn btn-large" href="#properties" onclick="select_properties();"> Next </a></center>
</div>
<script type="text/javascript">
$(function () {
	$('#action_area').css('width','400px');
	// TO CREATE AN INSTANCE
	// select the tree container using jQuery
	$("#schema_types")
		// call `.jstree` with the options object
		.jstree({
			// the `plugins` array allows you to configure the active plugins on this instance
			"themes" : {"icons" : false},
			"plugins" : ["themes","html_data","ui","crrm","hotkeys","checkbox"],
			// each plugin you have included can have its own config object
			"core" : { "initially_open" : [ "schema_root" ] }
			// it makes sense to configure a plugin only if overriding the defaults
		})
		// EVENTS
		// each instance triggers its own events - to process those listen on the container
		// all events are in the `.jstree` namespace
		// so listen for `function_name`.`jstree` - you can function names from the docs
		.bind("loaded.jstree", function (event, data) {
			// you get two params - event & data - check the core docs for a detailed description
		})
	 	.bind("select_node.jstree", function (e, data) {
		    var href = data.rslt.obj.children("a").attr("href");
		    window.open(href);
		  });
	$("#schema_search").keyup(function(event){
		
		if(event.keyCode == 13){ //when enter is pressed
			handleSearch($("#schema_search").val().trim());
		}
		
		var k=$("#schema_search").val().trim();
		setTimeout(function() {
			handleSearch($("#schema_search").val().trim());
		}, 300);
	});	
});
</script>
</body>
</html>
