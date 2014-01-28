function connectEnricherAPI(url,request_data){
	var dataReceived;
	$.ajax({
		type : "POST",
		async: false,
		url : url,
		data : request_data,
		contentType: "application/x-www-form-urlencoded",
		dataType: "json",
		success : function(data) {
			dataReceived =  data;
		},
		error: function(xhr, txt, err){ 
			//alert("xhr: " + xhr + "\n textStatus: " +txt + "\n errorThrown: " + err+ "\n url: " + url);
			dataReceived=0;
		}
	});
	return dataReceived;
}
//top: returns only the top entity not all
function suggestURI(proxy_url,request_data,top){
	var dataReceived=connectEnricherAPI(proxy_url,request_data);
	if(dataReceived){
		//dataReceived = eval("(" + dataReceived + ")");
		if(dataReceived['totalResults']){
			if(top){
				return dataReceived.entries[0].link;
			}else{
				return dataReceived;
			}
		}	
	}
	return null;
}
function setCookie(c_name,value,exdays)
{
	$.cookie(c_name, value, { expires: exdays, path: '/' });
}
//duplicated code also in plug.min.js
//todo: remove the duplicates
function remove_annotation(pointer, format) {
	//console.log(pointer);
	pointer.find('.tooltip').remove();
	pointer.find('.tooltip-t').remove();
	if (format == "RDFa") {
		pointer.css("background-color", "");
		pointer.removeAttr("typeof").removeAttr("class").removeAttr("resource")
				.removeAttr("property");
		pointer.find('>[property]').each(function(i, v) {
			remove_annotation($(v), 'RDFa')
		});
	} else {
		pointer.css("background-color", "");
		pointer.removeAttr("itemtype").removeAttr("class").removeAttr("itemid")
				.removeAttr("itemscope").removeAttr("itemprop");
		pointer.find('>[itemprop]').each(function(i, v) {
			remove_annotation($(v), 'Microdata')
		});
	}
	// remove spans which have no attribute
	pointer.find('span').each(function(i, v) {
		if (!$(v)[0].attributes.length) {
			// $(v).unwrap();
			$(v).replaceWith($(v).html());
		}
	});
	// remove pointer tags as well if necessary
	var tagName = pointer.prop("tagName").toLowerCase();
	if (tagName == 'span' && !$(pointer)[0].attributes.length) {
		// pointer.unwrap();
		pointer.replaceWith(pointer.html());
	}
}
