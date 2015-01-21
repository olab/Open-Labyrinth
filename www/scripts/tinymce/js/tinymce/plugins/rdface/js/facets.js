var parentWin = (!window.frameElement && window.dialogArguments) || opener || parent || top;
var editor=parentWin.rdface_editor;
var plugin_url=parentWin.rdface_plugin_url;
var c=editor.getBody();
var a_format=$.cookie("annotationF");
$(function () {
	reset_all_facets();
})

function sort_entities(obj){
	var sortable = [];
	for(var i in obj)
	{
		sortable.push([i, obj[i]['count']]);
	}	
	sortable.sort(function(a, b) {return  b[1]-a[1]});
	return sortable;
}
function merge_facets(obj1,obj2){
	var merged = {};
	merged=$.extend({}, obj1);
	for (var i in obj2){
		if(merged[i]){
			merged[i]['count']=parseInt(merged[i]['count']+obj2[i]['count']);
		}else{
			merged[i]={count:obj2[i]['count']};
		}
	}
	return merged;
}
function reset_all_facets(){
	var facet_types=get_factes_types($(editor.getDoc()));
	var sorted_types=[];
	sorted_types=sort_entities(facet_types);
	update_facets_types(sorted_types);
	var facet_properties=get_factes_properties($(editor.getDoc()));
	var sorted_properties=[];
	sorted_properties=sort_entities(facet_properties);
	update_facets_properties(sorted_properties)
	var facet_values=get_factes_values($(editor.getDoc()));
	var sorted_values=[];
	sorted_values=sort_entities(facet_values);
	update_facets_values(sorted_values)
}
function update_facets_types(sorted_types){
	$('.f-t-item').remove();
	var css_cls='';
	for(var i in sorted_types)
	{
		css_cls='r_'+sorted_types[i][0].toLowerCase();
		$('#facet_types').append('<li class="f_item f-t-item"><input type="checkbox" id="f_type_'+i+'" class="f-chbox chbox-type" name="selectedTypes[]" value="'+sorted_types[i][0]+'"> <span class="'+css_cls+'">'+sorted_types[i][0]+'</span>  <span class="small-badge">'+sorted_types[i][1]+'</span></li>');
	}
	$('.chbox-type').click(function(e){
		var all_props={};
		var all_vals={};
		var this_prop={};
		var this_val={};
		$("input[name='selectedTypes[]']:checked").each(function (i,v){
			if(a_format=='RDFa'){
				$.each($(editor.getDoc()).find('[typeof="schema:'+$(v).val()+'"]'),function(ii,vv){
					this_prop=get_factes_properties($(vv))
					all_props=merge_facets(this_prop,all_props);
					this_val=get_factes_values($(vv));
					all_vals=merge_facets(this_val,all_vals);
				});
			}else{
				$.each($(editor.getDoc()).find('[itemtype="http://schema.org/'+$(v).val()+'"]'),function(ii,vv){
					this_prop=get_factes_properties($(vv))
					all_props=merge_facets(this_prop,all_props);
					this_val=get_factes_values($(vv));
					all_vals=merge_facets(this_val,all_vals);
				});
			}
		});
		update_facets_properties(sort_entities(all_props))
		update_facets_values(sort_entities(all_vals))
		reset_when_all_unselected()
	})
}
function reset_when_all_unselected(){
	if(!$(".f-chbox:checked").length){
		reset_all_facets()
	}
}
function update_facets_properties(sorted_properties){
	$('.f-p-item').remove();
	for(var i in sorted_properties)
	{
		$('#facet_properties').append('<li class="f_item f-p-item"><input type="checkbox" id="f_type_'+i+'" class="f-chbox chbox-prop" name="selectedProperties[]" value="'+sorted_properties[i][0]+'"> <span>'+sorted_properties[i][0]+'</span>  <span class="small-badge">'+sorted_properties[i][1]+'</span></li>');
	}
	$('.chbox-prop').click(function(e){
		var all_vals={};
		var this_val={};
		var all_types={};
		var this_type={};
		$("input[name='selectedProperties[]']:checked").each(function (i,v){
			if(a_format=='RDFa'){
				$.each($(editor.getDoc()).find('[property="schema:'+$(v).val()+'"]'),function(ii,vv){
					this_val=get_factes_values($(vv));
					all_vals=merge_facets(this_val,all_vals);
					all_type=get_factes_types(get_type_node_from_prop($(vv)));
					all_types=merge_facets(all_type,all_types);
				});
			}else{
				$.each($(editor.getDoc()).find('[itemprop="'+$(v).val()+'"]'),function(ii,vv){
					this_val=get_factes_values($(vv));
					all_vals=merge_facets(this_val,all_vals);
					all_type=get_factes_types(get_type_node_from_prop($(vv)));
					all_types=merge_facets(all_type,all_types);
				});
			}
		});
		update_facets_values(sort_entities(all_vals))
		update_facets_types(sort_entities(all_types))
		reset_when_all_unselected()
	})
}
function get_type_node_from_prop(selector){
	var tmp;
	tmp=selector;
	if(a_format=='RDFa'){
		while(tmp.length){
			if(tmp.attr('typeof')){
				return tmp;
			}else{
				tmp=tmp.parent();
			}
		}
	}else{
		while(tmp.length){
			if(tmp.attr('itemtype')){
				return tmp;
			}else{
				tmp=tmp.parent();
			}
		}
	}
	//not found
	return 0;
}
function update_facets_values(sorted_values){
	$('.f-v-item').remove();
	for(var i in sorted_values)
	{
		$('#facet_values').append('<li class="f_item f-v-item"><input type="checkbox" id="f_type_'+i+'" class="f-chbox chbox-value" name="selectedValues[]" value="'+sorted_values[i][0]+'"> <span>'+sorted_values[i][0]+'</span>  <span class="small-badge">'+sorted_values[i][1]+'</span></li>');
	}
	$('.chbox-value').click(function(e){
		var all_props={};
		var this_prop={};
		var all_types={};
		var this_type={};
		$("input[name='selectedValues[]']:checked").each(function (i,v){
			if(a_format=='RDFa'){
				$.each($(editor.getDoc()).find('[property]'),function(ii,vv){
					if($(vv).is('meta')){
						if(shortenValue($(vv).attr('content'))==$(v).val()){
							this_prop=get_factes_properties($(vv));
							all_props=merge_facets(this_prop,all_props);
							all_type=get_factes_types(get_type_node_from_prop($(vv)));
							all_types=merge_facets(all_type,all_types);
						}
					}else{
						if(shortenValue($(vv).html())==$(v).val()){
							this_prop=get_factes_properties($(vv));
							all_props=merge_facets(this_prop,all_props);
							all_type=get_factes_types(get_type_node_from_prop($(vv)));
							all_types=merge_facets(all_type,all_types);
						}
					}
					

				});
			}else{
				$.each($(editor.getDoc()).find('[itemprop]'),function(ii,vv){
					if($(vv).is('meta')){
						if(shortenValue($(vv).attr('content'))==$(v).val()){
							this_prop=get_factes_properties($(vv));
							all_props=merge_facets(this_prop,all_props);
							all_type=get_factes_types(get_type_node_from_prop($(vv)));
							all_types=merge_facets(all_type,all_types);
						}
					}else{
						if(shortenValue($(vv).html())==$(v).val()){
							this_prop=get_factes_properties($(vv));
							all_props=merge_facets(this_prop,all_props);
							all_type=get_factes_types(get_type_node_from_prop($(vv)));
							all_types=merge_facets(all_type,all_types);
						}
					}

				});
			}
		});
		update_facets_properties(sort_entities(all_props))
		update_facets_types(sort_entities(all_types))
		reset_when_all_unselected()
	})
}
function get_factes_types(pointer){
	var facet_types={};
	var entity_type;
	if(a_format=='RDFa'){
		if(pointer.attr('typeof')){
			entity_type=pointer.attr('typeof').split(':')[1];
			facet_types[entity_type]={count:1};
		}else{
			$.each(pointer.find('[typeof]'),function(i,v){
				 entity_type=$(v).attr('typeof').split(':')[1];
				   if(facet_types[entity_type]){
					   facet_types[entity_type]['count']=facet_types[entity_type]['count']+1;
				   }else{
					   facet_types[entity_type]={count:1};
				   }
			})
		}
	}else{
		if(pointer.attr('itemtype')){
			 entity_type=pointer.attr('itemtype').split('http://schema.org/')[1];
			facet_types[entity_type]={count:1};
		}else{
			$.each(pointer.find('[itemtype]'),function(i,v){
				 entity_type=$(v).attr('itemtype').split('http://schema.org/')[1];
				   if(facet_types[entity_type]){
					   facet_types[entity_type]['count']=facet_types[entity_type]['count']+1;
				   }else{
					   facet_types[entity_type]={count:1};
				   }
			})
		}
	}
	return facet_types;
}
function get_factes_properties(pointer){
	var facet_properties={};
	var entity_prop;
	if(a_format=='RDFa'){
		$.each(pointer.find('[property]'),function(i,v){
			entity_prop=$(v).attr('property').split(':')[1];
			   if(facet_properties[entity_prop]){
				   facet_properties[entity_prop]['count']=facet_properties[entity_prop]['count']+1;
			   }else{
				   facet_properties[entity_prop]={count:1};
			   }
		})
		if(isObjEmpty(facet_properties)){
			if(pointer.attr('property')){
				entity_prop=pointer.attr('property').split(':')[1];
				facet_properties[entity_prop]={count:1};
			}
		}
	}else{
		$.each(pointer.find('[itemprop]'),function(i,v){
			entity_prop=$(v).attr('itemprop');
			   if(facet_properties[entity_prop]){
				   facet_properties[entity_prop]['count']=facet_properties[entity_prop]['count']+1;
			   }else{
				   facet_properties[entity_prop]={count:1};
			   }
		})
		if(isObjEmpty(facet_properties)){
			if(pointer.attr('itemprop')){
				entity_prop=pointer.attr('itemprop');
				facet_properties[entity_prop]={count:1};
			}
		}
	}
	return facet_properties;
}
function get_factes_values(pointer){
	var facet_values={};
	var entity_value;
	var short_entity_value;
	if(a_format=='RDFa'){
		$.each(pointer.find('[property]'),function(i,v){
			if($(v).is('meta')){
				entity_value=$(v).attr('content');
			}else{
				entity_value=$(v).text();
			}
			short_entity_value=shortenValue(entity_value);	
		   if(facet_values[short_entity_value]){
				   facet_values[short_entity_value]['count']=facet_values[short_entity_value]['count']+1;
		   }else{
				   facet_values[short_entity_value]={count:1};
			}
		})
	}else{
		$.each(pointer.find('[itemprop]'),function(i,v){
			if($(v).is('meta')){
				entity_value=$(v).attr('content');
			}else{
				entity_value=$(v).text();
			}
			short_entity_value=shortenValue(entity_value);
		    if(facet_values[short_entity_value]){
				   facet_values[short_entity_value]['count']=facet_values[short_entity_value]['count']+1;
			 }else{
				   facet_values[short_entity_value]={count:1};
			 }
		})
	}
	//node itself has value not the children
	if(isObjEmpty(facet_values)){
		if(pointer.is('meta')){
			entity_value=pointer.attr('content');
		}else{
			entity_value=pointer.text();
		}
		short_entity_value=shortenValue(entity_value);
	    if(facet_values[short_entity_value]){
			   facet_values[short_entity_value]['count']=facet_values[short_entity_value]['count']+1;
		 }else{
			   facet_values[short_entity_value]={count:1};
		 }
	}
	return facet_values;
}
function isObjEmpty(map) {
	   for(var key in map) {
	      if (map.hasOwnProperty(key)) {
	         return false;
	      }
	   }
	   return true;
	}
function shortenValue(entity_value){
	var short_entity_value= entity_value.substring(0,13);
	if(short_entity_value!=entity_value){
		short_entity_value=short_entity_value+'...';
	}
	return short_entity_value;
}