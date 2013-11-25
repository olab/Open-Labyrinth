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
 * Model for map_elements table in database 
 */
class Model_Leap_Map_Element extends DB_ORM_Model {
    private $mimes = array();
    
    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'map_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'mime' => new DB_ORM_Field_String($this, array(
                'max_length' => 500,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'path' => new DB_ORM_Field_String($this, array(
                'max_length' => 300,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'args' => new DB_ORM_Field_String($this, array(
                'max_length' => 100,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'width' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'height' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'h_align' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'v_align' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'width_type' => new DB_ORM_Field_String($this, array(
                'max_length' => 2,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'height_type' => new DB_ORM_Field_String($this, array(
                'max_length' => 2,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),

            'is_shared' => new DB_ORM_Field_Boolean($this, array(
                'savable' => TRUE,
                'nullable' => FALSE,
                'default' => TRUE
            ))
        );
        
        $this->relations = array(
            'map' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('map_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map',
            )),

            'metadata' => new DB_ORM_Relation_HasOne($this, array(
                'child_key' => array('element_id'),
                'parent_key' => array('id'),
                'child_model' => 'map_element_metadata',
            ))
        );
        
        $this->mimes[] = 'image/jpg';
        $this->mimes[] = 'image/jpeg';
        $this->mimes[] = 'image/gif';
        $this->mimes[] = 'image/png';
        $this->mimes[] = 'application/vnd.open';
        $this->mimes[] = 'application/x-shockw';  
        $this->mimes[] = 'application/x-shockwave-flash'; 
        $this->mimes[] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        $this->mimes[] = 'video/x-msvideo';
        $this->mimes[] = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
        $this->mimes[] = 'application/msword';
        $this->mimes[] = 'application/x-director';
        $this->mimes[] = 'text/html';
        $this->mimes[] = 'application/x-msaccess';
        $this->mimes[] = 'video/quicktime';
        $this->mimes[] = 'video/x-sgi-movie';
        $this->mimes[] = 'video/mpeg';
        $this->mimes[] = 'audio/mpeg';
        $this->mimes[] = 'application/pdf';
        $this->mimes[] = 'application/vnd.ms-powerpoint';
        $this->mimes[] = 'audio/x-pn-realaudio';
        $this->mimes[] = 'application/rtf';
        $this->mimes[] = 'text/plain';
        $this->mimes[] = 'audio/x-wav';
        $this->mimes[] = 'application/zip';
        $this->mimes[] = 'application/excel';
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_elements';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getImagesByMap($mapId) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('map_id', '=', $mapId)
                ->where('mime', 'IN', array('image/gif', 'image/jpg', 'image/jpeg', 'image/png'));
        
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $elements = array();
            foreach($result as $record) {
                $elements[] = DB_ORM::model('map_element', array((int)$record['id']));
            }
            
            return $elements;
        }
        
        return NULL;
    }

    public function getAllMediaFiles($mapId) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('map_id', '=', $mapId)
                ->where('mime', 'IN', array('image/gif', 'image/jpg', 'image/jpeg', 'image/png', 'application/x-shockwave-flash'));
        
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $elements = array();
            foreach($result as $record) {
                $elements[] = DB_ORM::model('map_element', array((int)$record['id']));
            }
            
            return $elements;
        }
        
        return NULL;
    }
    
    public function getAllMediaFilesNotInIds($ids) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('id', 'NOT IN', $ids, 'AND')
                ->where('mime', 'IN', array('image/gif', 'image/jpg', 'image/jpeg', 'image/png', 'application/x-shockwave-flash'));
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $elements = array();
            foreach($result as $record) {
                $elements[] = DB_ORM::model('map_element', array((int)$record['id']));
            }
            
            return $elements;
        }
        
        return NULL;
    }
    
    public function getAllFilesByMap($mapId) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('map_id', '=', $mapId);
        
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $elements = array();
            foreach($result as $record) {
                $elements[] = DB_ORM::model('map_element', array((int)$record['id']));
            }
            
            return $elements;
        }
        
        return NULL;
    }

    public function addFile($mapId, $values) {
        return DB_ORM::insert('map_element')
                       ->column('map_id', $mapId)
                       ->column('name', Arr::get($values, 'name', ''))
                       ->column('mime', Arr::get($values, 'mime', ''))
                       ->column('path', Arr::get($values, 'path', ''))
                       ->column('width', Arr::get($values, 'width', 0))
                       ->column('height', Arr::get($values, 'height', 0))
                       ->execute();
    }

    public function uploadFile($mapId, $values) {
        if($values['filename']['size'] < 1024 * 3 * 1024) {
            if(is_uploaded_file($values['filename']['tmp_name'])) {
                if (!file_exists(DOCROOT.'/files/'.$mapId)) {
                    mkdir(DOCROOT.'/files/'.$mapId, 0777, true);
                }
                if(file_exists(DOCROOT.'/files/'.$mapId.'/'.$values['filename']['name'])) {
                    $name = pathinfo($values['filename']['name'], PATHINFO_FILENAME);
                    $extension = pathinfo($values['filename']['name'], PATHINFO_EXTENSION);
                    $values['filename']['name'] = $name.'_'.time().'.'.$extension;
                }
                move_uploaded_file($values['filename']['tmp_name'], DOCROOT.'/files/'.$mapId.'/'.$values['filename']['name']);
                $fileName = 'files/'.$mapId.'/'.$values['filename']['name'];
                
                $mime = File::mime($fileName);
                
                if(in_array($mime, $this->mimes)) {
                    $this->map_id = $mapId;
                    $this->path = $fileName;
                    $this->mime = File::mime($fileName);
                    $this->name = $values['filename']['name'];

                    $this->save();
                } else {
                    unlink(DOCROOT.'/'.$fileName);
                }
            }
        }
    }

    public function saveElement($mapId, $values){
        $this->map_id = $mapId;
        $this->path = $values['path'];
        $this->mime = File::mime($values['path']);
        $this->name = $values['name'];

        $this->save();
        return $this->getLastAddedElement($mapId);
    }

    public function getLastAddedElement($mapId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId)->order_by('id', 'DESC')->limit(1);
        $result = $builder->query();

        if ($result->is_loaded()) {
            return DB_ORM::model('map_element', array($result[0]['id']));
        }

        return NULL;
    }

    public function deleteFile($fileId) {
        $this->id = $fileId;
        $this->load();
        $this->delete();
        $filePath = DOCROOT.'/'.$this->path;
        if (file_exists($filePath)){
            unlink($filePath);
        }
    }

    public function getFilesSize($filesArray) {
        $totalsize = 0;
        $total['size'] = 0;
        $total['count'] = 0;
        if (count($filesArray) > 0){
            foreach($filesArray as $file){
                $filePath = DOCROOT.$file->path;
                if(file_exists($filePath)){
                    $totalsize += filesize($filePath);
                }
            }

            $total['size'] = $totalsize;
            $total['count'] = count($filesArray);
        }
        return $total;
    } 

    public function sizeFormat($size) 
    { 
        if($size<1024) 
        { 
            return $size." bytes"; 
        } 
        else if($size<(1024*1024)) 
        { 
            $size=round($size/1024,1); 
            return $size." KB"; 
        } 
        else if($size<(1024*1024*1024)) 
        { 
            $size=round($size/(1024*1024),1); 
            return $size." MB"; 
        } 
        else 
        { 
            $size=round($size/(1024*1024*1024),1); 
            return $size." GB"; 
        } 
    } 
    
    public function updateFile($fileId, $values) {
        $this->id = $fileId;
        $this->load();
        
        $this->mime = Arr::get($values, 'mrelmime', $this->mime);
        $this->name = Arr::get($values, 'mrelname', $this->name);
        $this->width = Arr::get($values, 'w', $this->width);
        $this->height = Arr::get($values, 'h', $this->height);
        $this->h_align = Arr::get($values, 'a', $this->h_align);
        $this->v_align = Arr::get($values, 'v', $this->v_align);
        $this->width_type = Arr::get($values, 'wv', $this->width_type);
        $this->height_type = Arr::get($values, 'hv', $this->height_type);
        $this->is_shared = Arr::get($values, 'shared', false);

        DB_ORM::model('map_element_metadata')->saveMetadata($this->id, $values);
        
        $this->save();
    }
    
    public function duplicateElements($fromMapId, $toMapId) {
        $elements = $this->getAllFilesByMap($fromMapId);
        
        if($elements == null || $toMapId == null || $toMapId <= 0) return null;
        
        $elementMap = array();
        foreach($elements as $element) {
            $newFileName = $this->duplicateFile(DOCROOT.'/'.$element->path, $toMapId);
            if($newFileName == null) continue;
            
            $newPath = 'files/' . $newFileName;
            
            $builder = DB_ORM::insert('map_element')
                    ->column('map_id', $toMapId)
                    ->column('mime', $element->mime)
                    ->column('name', $newFileName)
                    ->column('path', $newPath)
                    ->column('args', $element->args)
                    ->column('width', $element->width)
                    ->column('height', $element->height)
                    ->column('h_align', $element->h_align)
                    ->column('v_align', $element->v_align)
                    ->column('width_type', $element->width_type)
                    ->column('height_type', $element->height_type);
            
            $elementMap[$element->id] = $builder->execute();
        }
        
        return $elementMap;
    }
    
    private function duplicateFile($srcPath, $addName) {
        if(strlen($srcPath) <= 0 || strlen($addName) <= 0) return null;
        
        if(!file_exists($srcPath)) return null;
        
        $pathinfo = pathinfo($srcPath);
        $newFileName = $pathinfo['filename'] . '_' . $addName;
        
        $dstPath = $pathinfo['dirname'] . '/' . $newFileName . '.' . $pathinfo['extension'];
        
        $result = null;
        if(copy($srcPath, $dstPath))
            $result = $newFileName . '.' . $pathinfo['extension'];
        
        return $result;
    }

    public function exportMVP($mapId) {
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('map_id', '=', $mapId);

        $result = $builder->query();

        if($result->is_loaded()) {
            $elements = array();
            foreach($result as $record) {
                $elements[] = $record;
            }

            return $elements;
        }

        return NULL;
    }
}

?>