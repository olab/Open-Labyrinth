function handleSearch(term){
    $('#schema_types li').each(function(i, element){
    	var str=$(element).attr('id');
    	str=str.toLowerCase();
    	if(str!='schema_root'){
	    	var n=str.search(term.toLowerCase());
	    	if (n!=-1)
	    		$(element).addClass('found_node');
	    	else
	    		$(element).css('display','none');
    	}
    });
    if(term=='')
    	$('#schema_types li').css('display','');
}
function select_properties(){

	var selected_schemas=new Array();
    $('#schema_types').find(".jstree-checked").each(function(i, element){ 
    	selected_schemas.push($(element).attr("id"));
    });
    if(!selected_schemas.length){
    	alert('please select at least one schema!');
    	return 0;// no
    }
    if(selected_schemas.length>20){
		var answer = confirm("You have selected more than 20 Schemas. It might take a long time to load the related properties. Are you sure?");
		if (answer) {
			//yes
		} else {
			return 0;// no
		}
    }	
	//$('#action_area').html('');
    hide_firstpage();
    $("#page_header").html('<h2>2. Select your desirable properties</h2>');
	$('#action_area').append('<div id="schema_properties"></div>');
	$.ajax({
		type : "POST",
		url : "getProperties.php",
		data : 'selected='+selected_schemas,
		success : function(msg) {
			msg= eval("(" + msg + ")");
			
			$.each(msg.out, function(i,v){
				var props='';
				$.each(v, function(ii,vv){
					props=props+'<li><a href="">'+vv+'</a></li>';
				})
				$('#schema_properties').append('<ul><li class="schema-type" id="'+i+'"><a href="http://schema.org/'+i+'">[<b>'+i+'</b>]</a><ul>'+props+'</ul></li></ul>');
			})
			//tree
			$("#schema_properties")
			.jstree({
				"themes" : {"icons" : false},
				"plugins" : ["themes","html_data","ui","crrm","hotkeys","checkbox"]
			})
			.bind("loaded.jstree", function (event, data) {
				$("#schema_properties").jstree("check_all");
				$('#action_area').append('<div id="second_buttons">Depth level for finding related Schemas: <input class="input span1" id="depth_level" type="text" value="2"><br/><center><a href="#types" class="btn btn-large" onclick="show_firstpage();"> Back </a> <a class="btn btn-large btn-success" onclick="build_schemas();"> Build </a></center></div>');
			});	
		}
	});	
}
function build_schemas(){
	var output=new Array();
    $('#schema_properties .schema-type').each(function(i, element){
    	if($(element).hasClass('jstree-undetermined') || $(element).hasClass('jstree-checked')){
    		var obj = new Object();
    		obj.name=$(element).attr('id');
    		var tmp=new Array();
    		$(element).find('.jstree-leaf').each(function(ii, element2){
    			if($(element2).hasClass('jstree-checked')){
    				tmp.push($(element2).text().trim());
    			}
    		});
    		obj.properties=tmp;
    		output.push(obj);
    	}
    });
    var depth=parseInt($('#depth_level').val().trim());
    if(depth=='' || isNaN(depth)){
    	//default
    	depth=2;
    }
    var json_input_data=encodeURIComponent(JSON.stringify(output));
	$.ajax({
		type : "POST",
		url : "createSelection.php",
		data : 'selected='+json_input_data+'&depth='+depth,
		success : function(msg) {
			if(!$('#selection_download').length)
				$('#action_area').append('<br/><div id="selection_download"><center><a href="selection.json" target="_blank"> Download Selection as JSON </a></center></div>');
			window.open('schemas.htm');
		}
	});	
}
function hide_firstpage(){
	$('#schema_search').hide();
	$('#schema_types').hide();
	$('#next_btn').hide();
}
function show_firstpage(){
	$('#selection_download').remove();
	$('#second_buttons').remove();
	$('#schema_properties').remove();
	
	$('#schema_search').show();
	$('#schema_types').show();
	$('#next_btn').show();
	 $("#page_header").html('<h2>1.Select your desirable Schemas</h2>');
}