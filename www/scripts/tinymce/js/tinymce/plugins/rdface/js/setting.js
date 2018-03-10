var parentWin = (!window.frameElement && window.dialogArguments) || opener || parent || top;
var editor=parentWin.rdface_editor;
var plugin_url=parentWin.rdface_plugin_url;
var rdfaSetting = {
		init : function() {	
			//show all the selected schemas
			var schemas;
			var all_entities=[];
			var all_entities_levels=[];
			$.ajax({
				  url: plugin_url+'/schema_creator/selection.json',
				  dataType: 'json',
				  async: false,
				  success: function(data) {
					  schemas=data;
				  }
				});
			$.each(schemas['types'],function(i,v){
				all_entities.push(i);
				all_entities_levels.push(v['level']);
				if(v['level']==1){
					$('#r_main_entities').append('<li><input type="hidden" value="'+v['level']+'"><input type="checkbox" class="first_level" id="r_check_'+i+'" name="selectedEntities[]" value="'+i+'"> <span class="r_entity r_'+i.toLowerCase()+'">'+i+'</span></li>');
				}else{
					$('#r_sub_entities').append('<li><input type="hidden" value="'+v['level']+'"><input type="checkbox" class="sec_level" id="r_check_'+i+'" name="selectedEntities[]" value="'+i+'"> <span class="r_entity r_'+i.toLowerCase()+'">'+i+'</span></li>');
				}
			})
			var f2 = document.getElementById('settingGeneral');
			var f3 = document.getElementById('settingAuto');
			var f4 = document.getElementById('settingEntities');
			var selectedArray = new Array();
			//default values
			if(!$.cookie("annotationF")){
				f2.a_format[0].checked = true;
				setCookie("annotationF",f2.a_format[0].value,30);
			}		
			if(!$.cookie("recEntities")){
				setCookie("recEntities",all_entities.join(),30);
				setCookie("recEntitiesLevels",all_entities_levels.join(),30);
				$("input[name='selectedEntities[]']").each(function (i,v){
					$(v).attr('checked','true');
				});
			}else{
				var recEntities = new Array();
				recEntities=$.cookie("recEntities").split(",");
				$("input[name='selectedEntities[]']").each(function (i,v){
					if(recEntities.indexOf($(v).val())!=-1){
						$(v).attr('checked','true');
					}
				});
				//if(recEntities.indexOf('Organization')!=-1)
					//f2.org.checked = true;					
			}			
			if(!$.cookie("confidence")){
				$('#confidence_box').val('0.20');
				$('#confidence').slider('setValue','0.20');
				setCookie("confidence",$('#confidence_box').val(),30);
			}else{
				$('#confidence_box').val($.cookie("confidence"));
				$('#confidence').slider('setValue',$.cookie("confidence"));
			}
			$('#confidence').on('slide', function(e){
				var value=parseFloat($('#confidence').val());
				$('#confidence_box').val(value.toFixed(2));
			}).on('slideStop', function(e){
				var value=parseFloat($('#confidence').val());
				$('#confidence_box').val(value.toFixed(2));
			});
			//set values based on the cookie
			if($.cookie("annotationF")=='RDFa'){
				f2.a_format[0].checked = true;
			}else{
				f2.a_format[1].checked = true;
			}
		},
		insert : function() {
			var f2 = document.getElementById('settingGeneral');
			var prev_af=$.cookie("annotationF");
			//ask for user to confirm because of RDFa Microdata mixup
			if(prev_af=="RDFa" && f2.a_format[1].checked && editor.dom.get('namespaces')){
				var r=confirm("You already have some annotations in RDFa format. Changing the annotation format might result in some inconsistencies in your document! Is it OK?");
				if (r==true)
					setCookie("annotationF",f2.a_format[1].value,30);
			}else{
				setCookie("annotationF",f2.a_format[1].value,30);
			}
			if(f2.a_format[0].checked)
				setCookie("annotationF",f2.a_format[0].value,30);		
			var recEntities = new Array();
			var recEntitiesLevels = new Array();
			$("input[name='selectedEntities[]']:checked").each(function (i,v){
				recEntities.push($(v).val());
				recEntitiesLevels.push($(v).prev().val());
			});
			if(recEntities.length){
				setCookie("recEntities",recEntities.join(','),30);
				setCookie("recEntitiesLevels",recEntitiesLevels.join(','),30);
			}
			if($('#confidence_box').val()>1){
				setCookie("confidence",1,30);
			}else{
				setCookie("confidence",$('#confidence_box').val(),30);
			}
			editor.windowManager.close(); 
		}
}
function setCookie(c_name,value,exdays)
{
	$.cookie(c_name, value, { expires: exdays, path: '/' });
}