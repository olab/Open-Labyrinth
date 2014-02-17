<?php
/**
 * Open Labyrinth [ http://www.openlabyrinth.ca ]
 *
 * Open Labyrinth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Open Labyrinth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Open Labyrinth.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright Copyright 2012 Open Labyrinth. All Rights Reserved.
 *
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Skin editor v1.0</title>

    <link rel="stylesheet" type="text/css" href="<?php echo URL::base(); ?>scripts/skineditor/jquery/ui-lightness/jquery-ui-1.9.1.custom.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo URL::base(); ?>scripts/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo URL::base(); ?>scripts/farbtastic/farbtastic.css">
    <link rel="stylesheet" type="text/css" href="<?php echo URL::base(); ?>scripts/skineditor/css/skineditor.css">
</head>

<body>
    <div id="skinEditor"></div>

    <!-- GLOBAL VARIABLES -->
    <script>
        function getPlayURL()   { return '<?php echo URL::base() . 'renderLabyrinth/index/' . $templateData['map']->id; ?>'; }
        function getCloseURL()  { return '<?php echo URL::base() . 'labyrinthManager/global/' . $templateData['map']->id; ?>'; }
        function getSkinId()    { return <?php echo $templateData['skinData']->id; ?>; }
        function getUpdateURL() { return '<?php echo URL::base() . 'skinmanager/updateSkinData'; ?>'; }
        function getSkinData()  { return '<?php echo str_replace('\'', '\\\'', $templateData['skinData']->data); ?>'; }
        function getUploadURL() { return '<?php echo URL::base() . 'skinmanager/uploadSkinImage'; ?>'; }
        function getSkinHTML()  { return '<?php if(isset($templateData['skinHTML'])) { echo $templateData['skinHTML']; } ?>'; }
    </script>

    <!-- SYSTEM SCRIPTS -->
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/jquery.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/jquery-ui-1.9.1.custom.min.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/farbtastic/farbtastic.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/bootstrap/js/bootstrap.min.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/jquery.cookie.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/jquery.hotkeys.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/jquery.jstree.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/base64v1_0.js'); ?>"></script>

    <!-- MAIN APPLICATION SCRIPTS-->
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/common.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/uniqueobject.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/enumberable.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/enumerator.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/callback/callback.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/callback/callbackchain.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/observableproperty.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/serialization/formatter.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/serialization/jsonformatter.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/serialization/serializable.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/serialization/serializationinfo.js'); ?>"></script>

    <!-- UI COMPONENTS -->
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/uicomponents/uicomponent.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/uicomponents/propertywindowuicomponent.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/uicomponents/componentstree.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/uicomponents/componentslistuicomponent.js'); ?>"></script>

    <!-- COMPONENTS MODELS -->
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/propertymodels/blockpropertymodel.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/propertymodels/imageproprtymodel.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/propertymodels/nodetitlepropertymodel.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/propertymodels/nodecontentpropertymodel.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/propertymodels/counterscontainerpropertymodel.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/propertymodels/linkspropertymodel.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/propertymodels/reviewpropertymodel.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/propertymodels/mapinfopropertymodel.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/propertymodels/mapinfopropertymodel.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/propertymodels/rootpropertymodel.js'); ?>"></script>

    <!-- COMPONENTS VIEWS -->
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/proprtyviews/propertyview.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/proprtyviews/blockpropertyview.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/proprtyviews/imagepropertyview.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/proprtyviews/nodetitlepropertyview.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/proprtyviews/nodecontentproprtyview.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/proprtyviews/counterscontainerpropertyview.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/proprtyviews/linkspropertyview.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/proprtyviews/reviewpropertyview.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/proprtyviews/mapinfopropertyview.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/proprtyviews/rootpropertyview.js'); ?>"></script>

    <!-- COMPONENTS -->
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/component.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/rootcomponent.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/blockcomponent.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/imagecomponent.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/nodetitlecomponent.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/nodecontentcomponent.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/counterscontainercomponent.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/linkscomponent.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/reviewcomponent.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/mapinfocomponent.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/bookmarkcomponent.js'); ?>"></script>
    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/components/resetcomponent.js'); ?>"></script>

    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/componentsmanager.js'); ?>"></script>

    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/skineditor.js'); ?>"></script>

    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/plugins/skingeditor.jquery.plugin.js'); ?>"></script>

    <script src="<?php echo ScriptVersions::get(URL::base().'scripts/skineditor/application.js'); ?>"></script>
</body>
</html>