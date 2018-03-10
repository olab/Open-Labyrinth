var parentWin = (!window.frameElement && window.dialogArguments) || opener || parent || top;
var editor=parentWin.rdface_editor;
var plugin_url=parentWin.rdface_plugin_url;
var c=editor.getBody();
$(function () {
	var seditor = ace.edit("seditor");
	seditor.setValue(vkbeautify.xml($(c).html()));
	seditor.setTheme("ace/theme/chrome");
	seditor.getSession().setMode("ace/mode/html");
	seditor.getSession().setUseWrapMode(true);
	 //$('#seditor_code').html(seditor.getValue());
	seditor.getSession().on('change', function(e) {
	    //$('#seditor_code').html(seditor.getValue());
		editor.setContent(seditor.getValue());
	});
})
