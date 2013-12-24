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
defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_skins table in database 
 */
class Model_Leap_Map_Skin extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 100,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'path' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),

            'user_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => TRUE,
            )),

            'enabled' => new DB_ORM_Field_Boolean($this, array(
                'default' => TRUE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),

            'data' => new DB_ORM_Field_Text($this, array(
                'nullable' => TRUE,
                'savable' => TRUE
            ))
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_skins';
    }

    public static function primary_key() {
        return array('id');
    }

    public function getSkinById($id){
        $builder = DB_SQL::select('default')->from($this->table())->where('id', '=', $id)->where('enabled', '=', '1');
        $result = $builder->query();

        if ($result->is_loaded()) {
            return DB_ORM::model('map_skin', array($result[0]['id']));
        }

        return NULL;
    }

    public function getAllSkinsId() {
        $builder = DB_SQL::select('default')->from($this->table())->column('id')->where('enabled', '=', '1');
        $result = $builder->query();

        $ids = array();
        if ($result->is_loaded()) {
            foreach ($result as $record) {
                $ids[] = (int)$record['id'];
            }
        }

        return $ids;
    }

    public function getAllSkins() {
        $result = array();
        $ids = $this->getAllSkinsId();
        
        foreach($ids as $id) {
            $result[] = DB_ORM::model('map_skin', array($id));
        }
        
        return $result;
    }

    public function getSkinsByUserId($user_id){
        $builder = DB_SQL::select('default')->from($this->table())->where('user_id', '=', $user_id)->where('enabled', '=', '1');
        $result = $builder->query();

        $skins = array();
        if ($result->is_loaded()) {
            foreach ($result as $record) {
                $skins[] = DB_ORM::model('map_skin', array((int)$record['id']));
            }
        }

        return $skins;
    }

    public function updateSkinName($id, $name, $mapId){
        $error = false;
        $nameChanged = true;
        $skin = $this->getMapBySkin($name);
        if ($skin != NULL){
            if ($skin->id != $id){
                $error = true;
            }else{
                $nameChanged = false;
            }
        }

        if (!$error){
            if ($nameChanged){
                $this->id = $id;
                $this->load();

                if ($this->is_loaded()){
                    $newPath = $mapId.'_'.$name;
                    rename(DOCROOT.'css/skin/'.$this->path, DOCROOT.'css/skin/'.$newPath);

                    $this->name = $name;
                    $this->path = $newPath;
                    $this->save();

                    return true;
                }
            }
        }else{
            Session::instance()->set('skinError', __('That name is already taken.'));
            return false;
        }
    }

    public function deleteSkin($id){
        $this->id = $id;
        $this->load();

        if ($this->is_loaded()){
            if ($this->user_id == Auth::instance()->get_user()->id){
                $this->enabled = 0;
                $this->save();
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    public function addSkin($skinName, $skinPath) {
        $this->name = $skinName;
        $this->path = $skinPath;
        $this->user_id = Auth::instance()->get_user()->id;
        $this->enabled = 1;
        $this->save();
        $skin = $this->getMapBySkin($this->name);
        return $skin;
    }

    public function getMapBySkin($name){
        $builder = DB_SQL::select('default')->from($this->table())->where('name', '=', $name)->where('enabled', '=', '1');
        $result = $builder->query();

        if ($result->is_loaded()) {
            return DB_ORM::model('map_skin', array($result[0]['id']));
        }

        return NULL;
    }

    public function updateSkinData($skinId, $data, $html, $body = null) {
        DB_ORM::update('map_skin')
                ->set('data', $data)
                ->where('id', '=', $skinId)
                ->execute();

        $templateDir = $_SERVER['DOCUMENT_ROOT'] . '/application/views/labyrinth/skin/basic/basic_template.php';
        $skinDir     = $_SERVER['DOCUMENT_ROOT'] . '/application/views/labyrinth/skin/' . $skinId . '/';
        if(!is_dir($skinDir)) { mkdir($skinDir); }

        $template = file_get_contents($templateDir);

        $html = str_replace(array('ui-resizable-handle', 'ui-resizable-se ui-icon', 'ui-icon-gripsmall-diagonal-se',
                                  'ui-resizable', 'component-selected', 'ui-resizable-e', 'ui-resizable-s'), '', $html);
        $html = str_replace('{NODE_CONTENT}', '<?php echo Arr::get($templateData, "node_text");' .
                                              'if (isset($templateData["node_annotation"]) && $templateData["node_annotation"] != null) echo "<div class=\"annotation\">" . $templateData["node_annotation"] . "</div>"; ?>', $html);
        $html = str_replace('{NODE_TITLE}', '<?php echo Arr::get($templateData, \'node_title\'); ?>', $html);
        $html = str_replace('{COUNTERS}', '<?php if (isset($templateData[\'counters\'])) echo $templateData[\'counters\']; ?>', $html);
        $html = str_replace('{LINKS}', '<?php if(isset($templateData[\'links\'])){echo $templateData[\'links\'];} if(isset($templateData[\'undoLinks\'])){echo $templateData[\'undoLinks\'];} ?>', $html);
        $html = str_replace('{REVIEW}', '<div><a href="#" onclick="toggle_visibility(\'track\');"><p class=\'style2\'><strong>Review your pathway</strong></p></a><div id=\'track\' style=\'display:none\'><?php if(isset($templateData[\'trace_links\'])){echo $templateData[\'trace_links\'];}?></div>', $html);
        $html = str_replace('{MAP_INFO}', '<?php if ($templateData[\'map\']->timing) { ?>
                                              <div>Timer: <div id="timer"></div>
                                              <br /><br />
                                           <?php }?>
                                           <?php if (isset($templateData[\'navigation\'])) echo $templateData[\'navigation\']; ?>
                                           <div>
                                              Map: <?php if(isset($templateData[\'map\'])) echo $templateData[\'map\']->name; ?> (<?php if (isset($templateData[\'map\'])) echo $templateData[\'map\']->id; ?>) <br/>
                                              Node: <?php if (isset($templateData[\'node\'])) echo $templateData[\'node\']->id; ?><br/>
                                              <strong>Score:</strong>
                                           </div>', $html);
        $html = str_replace('{BOOKMARK}', '<input type="button" onclick=\'ajaxBookmark();\' name="bookmark" value="bookmark"/>
                                           <p>
                                               <a href=\'<?php echo URL::base(); ?>renderLabyrinth/reset/<?php echo $templateData[\'map\']->id; ?>
                                               <?php if(isset($templateData[\'webinarId\']) && isset($templateData[\'webinarStep\'])) echo \'/\' . $templateData[\'webinarId\'] . \'/\' . $templateData[\'webinarStep\']; ?>\'>reset</a></p>', $html);
        $html = '<div class="popup-outside-container">' . $html . '</div>';
        $template = str_replace('{HTML}', $html, $template);
        if($body != null) {
            $template = str_replace('<body>', '<body style="' . $body . '">', $template);
        }

        file_put_contents($skinDir . 'skin.php', $template);
    }
}

?>