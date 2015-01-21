var parentWin = (!window.frameElement && window.dialogArguments) || opener
		|| parent || top;
var plugin_url = parentWin.rdface_plugin_url;
var annotationF = parentWin.rdface_annotationF;
var editor = parentWin.rdface_editor;
// gets a prefix and returns all the types related to that prefix
function getPrefixTypeFromList(prefix, types) {
	var tmp = types.split(",");
	var c = 0;
	var tmp2;
	var output = [];
	$.each(tmp, function(i, v) {
		tmp2 = v.split(':');
		if (tmp2[0].toLowerCase() == prefix.toLowerCase()) {
			c++;
			output.push(tmp2[1]);
		}
	})
	if (!c) {
		// not found
		output = 0;
	}
	return output;
}
function mapDBpediaOutputToStandard(txt, proxy_url, recEntities,
		recEntitiesLevels) {
	var dataReceived;
	var data = encodeURIComponent(txt);
	if (!$.cookie("confidence")) {
		data = "api=DBpedia&confidence=0.20&query=" + data;
	} else {
		data = "api=DBpedia&confidence=" + $.cookie("confidence") + "&query="
				+ data;
	}
	dataReceived = connectEnricherAPI(proxy_url, data);
	// terminate if an error occured
	if (!dataReceived)
		return 0;
	var entities = new Array();
	$.each(dataReceived['Resources'], function(key, val) {
		var entity = new Array();
		var properties = new Array();
		// separate desired entities
		var tmp = val['@types'];
		var detectedTypes = getPrefixTypeFromList('schema', tmp);
		if (detectedTypes.length) {
			var tmp = 0;
			// choose the most suitable type for the entity
			$.each(detectedTypes, function(i, tp) {
				if ((recEntities.indexOf(tp) != -1)
						&& (recEntitiesLevels[recEntities.indexOf(tp)] > tmp)) {
					entity["type"] = 'schema:' + tp;
				}
				tmp = recEntitiesLevels[recEntities.indexOf(tp)];
			})
			entity["label"] = val['@surfaceForm'];
			entity["uri"] = val['@URI'];
			// add a property
			entity["start"] = parseInt(val['@offset']);
			entity["end"] = parseInt(val['@offset'])
					+ val['@surfaceForm'].length;
			entity["exact"] = txt.substring(entity["start"], entity["end"]);
			if (entity["type"]) {
				entities.push(entity);
			}
		}
	});

	return entities;
}
function remove_annotations(editor, only_automatic) {
	var tmp;
	var aF = $.cookie("annotationF");
	$(editor.getDoc()).find('.tooltip').remove();
	$(editor.getDoc()).find('.tooltip-t').remove();
	if (only_automatic) {
		$(editor.getDoc()).find('.automatic').each(function(i, v) {
			remove_annotation($(v), aF);
		});
	} else {
		$(editor.getDoc()).find('.r_entity').each(function(i, v) {
			remove_annotation($(v), aF);
		});
	}
	// remove namespaces as well
	var ns = editor.dom.get('namespaces');
	if (ns) {
		editor.setContent(ns.innerHTML);
	}
}
function sortDESC(a, b) {
	return (b - a);
}
function enrichText(entities, editor) {
	// handle overwriting of triples
	// get the list of exisitng annotated entities and add them to block arr to
	// prevent overwriting them
	var notOverwrite = new Array();
	$.each($(editor.getDoc()).find('.r_entity'), function(index, value) {
		if (!$(this).hasClass("automatic")) {
			notOverwrite.push($(this).text().trim());
		}
	});
	// -----------------------------
	var output = new Array();
	var enriched_text = editor.getContent();
	var extra_triples = '';
	// prepare positioning functions
	var sortArr = new Array();
	var nosortArr = new Array();
	$.each(entities, function(key, val) {
		nosortArr.push(val['start']);
	});
	$.each(nosortArr, function(ii, vv) {
		sortArr.push(vv);
	});
	sortArr.sort(sortDESC);
	var entitiesFinal = new Array();
	$.each(sortArr, function(i, v) {
		$.each(nosortArr, function(ii, vv) {
			if (vv == v) {
				var entityExtend = new Array();
				entityExtend["start"] = entities[ii]["start"];
				entityExtend["end"] = entities[ii]["end"];
				entityExtend["exact"] = entities[ii]["exact"];
				entityExtend["properties"] = entities[ii]["properties"];
				entityExtend["label"] = entities[ii]["label"];
				entityExtend["type"] = entities[ii]['type'];
				entityExtend["uri"] = entities[ii]['uri'];
				entitiesFinal.push(entityExtend);
			}
		});
	});
	entities = entitiesFinal;
	// replace the entities
	$.each(entities, function(key, val) {
		var selectedContent = val['label'];
		var start = val['start'];
		var end = val['end'];
		var selectedContent = val['exact'];
		if (notOverwrite.indexOf(selectedContent) == -1) {
			var subjectURI = '';
			if (val['uri']) {
				subjectURI = val['uri'];
			}
			var tmp2 = '';
			var annotatedContent, extra_triples;
			extra_triples = '';
			// different replacement for RDFa and Microdata
			var entity_type = val['type'].split(':')[1];
			var entity_type_cl = 'r_' + entity_type.toLowerCase();
			if ($.cookie("annotationF") == "RDFa") {
				// replacement for RDFa
				if (subjectURI) {
					tmp2 = "resource=" + subjectURI;
				}
				var temp = tmp2 + " typeof='" + val['type'] + "'";
				annotatedContent = "<span class='r_entity " + entity_type_cl
						+ " automatic' " + temp + ">";
				extra_triples = extra_triples + "<span class='r_prop r_name' property='schema:name'>"
						+ selectedContent + "</span>";
			} else {
				// replacement for Micodata Schema.org format
				if (subjectURI) {
					tmp2 = "itemid=" + subjectURI;
				}
				var temp = tmp2 + " itemtype='http://schema.org/" + entity_type
						+ "'";
				annotatedContent = "<span itemscope class='r_entity "
						+ entity_type_cl + " automatic' " + temp + ">";
				extra_triples = extra_triples + "<span class='r_prop r_name' itemprop='name'>"
						+ selectedContent + "</span>";
			}
			annotatedContent = annotatedContent + extra_triples + "</span>";
			enriched_text = enriched_text.substring(0, start)
					+ annotatedContent
					+ enriched_text.substring(end, enriched_text.length + 1);
		}
	});
	return enriched_text;
}
var Annotate = {
	init : function() {
		var recEntities = new Array();
		var recEntitiesLevels = new Array();
		if (!$.cookie("recEntities")) {
			var schemas;
			$.ajax({
				url : plugin_url + '/schema_creator/selection.json',
				dataType : 'json',
				async : false,
				success : function(data) {
					schemas = data;
				}
			});
			$.each(schemas['types'], function(i, v) {
				recEntities.push(i);
				recEntitiesLevels.push(v['level']);
			})
			setCookie("recEntities", recEntities.join(), 30);
			setCookie("recEntitiesLevels", recEntitiesLevels.join(), 30);
		} else {
			recEntities = $.cookie("recEntities").split(",");
			recEntitiesLevels = $.cookie("recEntitiesLevels").split(",");
		}
		// first we need to remove automatically generated annotations
		remove_annotations(editor, 1);
		var ns =editor.dom.get('namespaces');
		var txt;
		if(ns){
			txt=ns.innerHTML;
		}else{
			txt=editor.getContent();
		}
		var nsStart = "<div id='namespaces' prefix='schema: http://schema.org/'>";
		var nsEnd="</div>";
		var entities = mapDBpediaOutputToStandard(txt,
				proxy_url, recEntities, recEntitiesLevels);
		// enrich the text
		var enriched_text = enrichText(entities, editor);
		// -------------------------------------------------
		if ($.cookie("annotationF") == "RDFa") {
			editor.setContent(nsStart+enriched_text+nsEnd);
		} else {
			editor.setContent(enriched_text);
		}
		editor.nodeChanged();
		editor.windowManager.close();
	}
};


