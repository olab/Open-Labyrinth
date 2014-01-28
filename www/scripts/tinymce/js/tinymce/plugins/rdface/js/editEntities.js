var parentWin = (!window.frameElement && window.dialogArguments) || opener || parent || top;
var plugin_url=parentWin.rdface_plugin_url;
var entity_type=parentWin.rdface_entity_type;
var pointer=parentWin.rdface_pointer;
var selected_txt=pointer.html();
var annotationF=parentWin.rdface_annotationF;
var editor=parentWin.rdface_editor;
var EditEntities = {
	init : function() {
		//load schemas
		$.getJSON(plugin_url+'/schema_creator/selection.json', function(data) {
			all_schemas=data;
			$.each(data.datatypes,function(i,v){
				all_datatypes.push(i);
			})	
			$('#button_area').append('<a class="btn" onclick="EditEntities.update();"> Update </a> <a class="btn btn-danger" onclick="EditEntities.delete();"> Delete </a> '+build_changetype_box(entity_type));
			$("#schema_search_menu").keyup(function(event){
				if(event.keyCode == 13){ //when enter is pressed
					handleSearchMenuInline($("#schema_search_menu").val().trim());
				}
				var k=$("#schema_search_menu").val().trim();
				setTimeout(function() {
					handleSearchMenuInline($("#schema_search_menu").val().trim());
				}, 300);
			});				
			$('#action_area').append('<ul class="nav nav-tabs" id="schema_tabs"><li class="active"><a href="#properties" data-toggle="tab">Main</a></li></ul>');
			$('#action_area').append('<div class="tab-content" id="schema_tabs_content"><div class="tab-pane active" id="properties"></div>');
			
			//check the relationship between entity and its parent entity
			var tmp=pointer.parent();
			var e_type;
			var current_rel='';
			var rel_properties=[];
			while(tmp.length){
				if(tmp.hasClass('r_entity')){
					//console.log(tmp);
					if(annotationF=='RDFa'){
						e_type=tmp.attr('typeof');
						e_type=e_type.split(':')[1];
						current_rel=pointer.attr('property');
						if(current_rel)
							current_rel=current_rel.split(':')[1];
					}else{
						e_type=tmp.attr('itemtype');
						e_type=e_type.split('http://schema.org/')[1];
						current_rel=pointer.attr('itemprop');
					}
					$.each(all_schemas['types'][e_type]['properties'],function(i,v){
						if(all_schemas['properties'][v]['ranges'][0]==entity_type){
							rel_properties.push(all_schemas['properties'][v]['id']);
							//console.log(all_schemas['properties'][v]['label']);
						}	
					})	
					//console.log(current_rel);
					if(rel_properties.length){
						//console.log(rel_properties);
						$('#schema_tabs_content').css('height','300px');
						$('#schema_tabs_content').css('max-height','300px');
						var lis='';
						if(current_rel)
							lis='<option value="'+current_rel+'" >'+all_schemas['properties'][current_rel]['label']+'</option>';
						$.each(rel_properties,function(index,value){
							if(value!=current_rel)
								lis +='<option value="'+value+'" >'+all_schemas['properties'][value]['label']+'</option>';
						})
						lis ='<select id="rel_property">'+lis+'</select>'
						$('#action_area').append('<br/><div style="margin: 2px 0 2px 0;padding: 2px 0 0 10px;" class="alert alert-info">Relation to the parent entity: '+lis+'</div>');
					}else{
						$('#schema_tabs_content').css('height','305px');
						$('#schema_tabs_content').css('max-height','305px');
						$('#action_area').append('<br/><div class="alert alert-error">Notice: "'+entity_type+'" is not a related property of "'+e_type+'".</div>');
					}
					break;
				}
				tmp=tmp.parent();
			}
			create_form(entity_type,'','properties');
			//fillout form based on the json values coming from the annotation
			var obj;
			if(annotationF=='RDFa')
				obj=create_json_from_rdfa_annotations(pointer);
			else
				obj=create_json_from_microdata_annotations(pointer);
			//console.log(obj);
			fillout_form_from_json(obj,'','properties');
			$('#schema_tabs a[href=#properties]').tab('show');
		});		
	},
	update : function() {

		//change annotation to manual
		pointer.removeClass("automatic");	
		var rel_property='';
		if($('#rel_property').length){
			rel_property=$('#rel_property').val();
		}
		//console.log(rel_property);
		var obj=create_json_from_forms('properties');
		//console.log(obj);
		if(annotationF=="RDFa"){
			create_rdfa_tags_from_json(obj,pointer)
			if(rel_property!=''){
				pointer.attr('property','schema:'+rel_property);
			}
			//entity_uri must be handled differently
			if(obj.properties.entity_uri){
				pointer.attr('resource',obj.properties.entity_uri.value)
			}
		}else{
			create_microdata_tags_from_json(obj,pointer)
			//add realtion
			if(rel_property!=''){
				pointer.attr('itemprop',rel_property);
				pointer.attr('itemscope','');
			}
			//entity_uri must be handled differently
			if(obj.properties.entity_uri){
				pointer.attr('itemid',obj.properties.entity_uri.value)
			}			
		}
		pointer.css("background-color","");
		pointer.find('.tooltip').remove();
		editor.nodeChanged();
		editor.windowManager.close(); 
	},
	delete : function() {
		var selected_txt=pointer.html();
		remove_annotation(pointer,annotationF);
		pointer.find('.tooltip').remove();
		editor.nodeChanged();
		$(editor.getBody()).html($(editor.getBody()).html());
		editor.windowManager.close(); 
	},
	changeType : function(type) {
		var answer = confirm("Changing the type will remove the value of the current properties. Are you sure you want to do it?");
		if (answer) {
			//yes
			//change annotation to manual
			pointer.removeClass("automatic");		
			//var selected_txt=tinyMCEPopup.getWindowArg('selected_txt');
			var selected_txt=pointer.html();
			//remove and insert
			remove_annotation(pointer,annotationF);
			pointer.attr('class','r_entity r_'+type.toLowerCase());
			
			if(annotationF=="RDFa"){
				pointer.attr('typeof','schema:'+type);
				if(pointer.children().length==1)
					pointer.children().attr("property",'schema:name').attr('class','r_prop r_name');
			}else{
				pointer.attr('itemtype','http://schema.org/'+type);
				if(pointer.children().length==1)
					pointer.children().attr("itemprop",'name').attr('class','r_prop r_name');			
			}
			//fix for Chrome
			pointer.css("background-color","");
			pointer.find('.tooltip').remove();
			editor.nodeChanged();
			$(editor.getBody()).html($(editor.getBody()).html());
			editor.windowManager.close(); 
		} else {
			return 0;// no
		}
	}
};

EditEntities.init();
