<script>
    var selection_json = "<?php echo URL::base()?>scripts/tinymce/js/tinymce/plugins/rdface/schema_creator/selection.json";

</script>

<script type="text/javascript" src="<?php echo URL::base()?>scripts/tinymce/js/tinymce/plugins/rdface/libs/jquery.linkify.min.js"></script>
<script type="text/javascript" src="<?php echo URL::base()?>scripts/tinymce/js/tinymce/plugins/rdface/schema_creator/static/js/schemas.js"></script>

<script>
    //a global variable holding the JSON of schemas
    var all_schemas;
    var all_datatypes=new Array();
</script>

<script type="text/javascript" src="<?php echo URL::base()?>scripts/tinymce/js/tinymce/plugins/rdface/js/editEntities.js"></script>

<script type="text/javascript" src="<?php echo URL::base()?>scripts/tinymce/js/tinymce/plugins/rdface//libs/bootstrap/plugins/datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo URL::base()?>scripts/tinymce/js/tinymce/plugins/rdface/js/config.js"></script>
<script type="text/javascript" src="<?php echo URL::base()?>scripts/tinymce/js/tinymce/plugins/rdface/js/functions.js"></script>

<div id="button_area">
</div>
<div id="action_area">
</div>
<style>
    body{
        padding-left:10px;
        padding-top:5px;
        padding-bottom:10px;
        padding-right:5px;
    }
    #action_area
    {
        display: block;
        margin-left: auto;
        margin-right: auto;
    }
    #button_area
    {
        text-align:center;
        padding-top:10px;
    }
    #selection_download{

    }
    .schema_hidden{
        display:none;
    }
    .subschema-icon{
        cursor:pointer;
        margin-left:2px;
    }
    .nav-tabs > li .close {
        margin: -2px 0 0 10px;
        font-size: 18px;
    }
    .hand-pointer{
        cursor:pointer;
    }
    #schema_tabs_content{
        max-height:380px;
    }
    .form-horizontal .control-label{
        /* text-align:right; */
        text-align:left;
    }
    .form-horizontal .control-group{
        /* text-align:right; */
        text-align:left;
    }
    .tab-content{
        padding-right:10px;
    }
</style>
