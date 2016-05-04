/*
Product Name: dhtmlxCombo 
Version: 4.6 
Edition: Standard 
License: content of this file is covered by GPL. Usage outside GPL terms is prohibited. To obtain Commercial or Enterprise license contact sales@dhtmlx.com
Copyright UAB Dinamenta http://www.dhtmlx.com
*/

window.dhtmlxAjax={get:function(a,c,b){if(b){return dhx4.ajax.getSync(a)}else{dhx4.ajax.get(a,c)}},post:function(a,b,d,c){if(c){return dhx4.ajax.postSync(a,b)}else{dhx4.ajax.post(a,b,d)}},getSync:function(a){return dhx4.ajax.getSync(a)},postSync:function(a,b){return dhx4.ajax.postSync(a,b)}};dhtmlXCombo.prototype.loadXML=function(a,b){this.load(a,b)};dhtmlXCombo.prototype.loadXMLString=function(a){this.load(a)};dhtmlXCombo.prototype.enableOptionAutoHeight=function(){};dhtmlXCombo.prototype.enableOptionAutoPositioning=function(){};dhtmlXCombo.prototype.enableOptionAutoWidth=function(){};dhtmlXCombo.prototype.destructor=function(){this.unload()};dhtmlXCombo.prototype.render=function(){};dhtmlXCombo.prototype.setOptionHeight=function(){};dhtmlXCombo.prototype.attachChildCombo=function(){};dhtmlXCombo.prototype.setAutoSubCombo=function(){};