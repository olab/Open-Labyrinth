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

class Model_Leap_Map_VisualDisplay extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();
        
        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'map_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),

            'is_all_page_show' => new DB_ORM_Field_Boolean($this, array(
                'default' => FALSE,
                'nullable' => FALSE,
                'savable' => TRUE
            ))
        );
        
        $this->relations = array(
            'map' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('map_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map'
            )),
            
            'images' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('visual_id'),
                'child_model' => 'map_visualdisplay_image',
                'parent_key' => array('id')
            )),
            
            'counters' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('visual_id'),
                'child_model' => 'Map_VisualDisplay_Counter',
                'parent_key' => array('id')
            )),
            
            'panels' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('visual_id'),
                'child_model' => 'Map_VisualDisplay_Panel',
                'parent_key' => array('id')
            ))
        );
    }
    
    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_visual_displays';
    }

    public static function primary_key() {
        return array('id');
    }

    /**
     * Update flag for showing visual display on all pages
     *
     * @param integer $id - visual display ID
     * @param boolean $isShow - flag for showing
     */
    public function updateShowOnAllPages($id, $isShow) {
        if($id == null || $id <= 0) return;

        DB_SQL::update('default')
                ->set('is_all_page_show', $isShow)
                ->where('id', '=', $id)
                ->table($this->table())
                ->execute();
    }

    /**
     * Return all displayes in map
     * 
     * @param integer $mapId - Map ID
     * @return array() or null - array of 'VisualDisplay' models
     */
    public function getMapDisplays($mapId) {
        if($mapId == null || $mapId <= 0) return null;
        
        return DB_ORM::select('map_visualdisplay')->where('map_id', '=', $mapId)->query();
    }

    public function getMapDisplaysShowOnAllPages($mapId) {
        if($mapId == null || $mapId <= 0) return null;

        return DB_ORM::select('map_visualdisplay')
                       ->where('map_id', '=', $mapId, 'AND')
                       ->where('is_all_page_show', '=', 1)
                       ->query();
    }
    
    /**
     * Save visual displays from json string
     * 
     * @param integer $mapId - Map ID
     * @param string $jsonString - JSON String
     */
    public function updateFromJSON($mapId, $jsonString) {
        $object = json_decode($jsonString);
        $visualDisplayId = null;
        $visualDisplay = null;
        
        if($object != null) {
            if($object->id == 'null') {
                $visualDisplayId = $this->createVisualDisplay($mapId);
            } else {
                $visualDisplay = DB_ORM::model('map_visualdisplay', array((int)$object->id));
                if($visualDisplay != null) {
                    $visualDisplayId = (int)$object->id;
                }
            }
            
            $this->updateElementsPositions($object);
            
            if(property_exists($object, 'panels')) {
                $this->updatePanels($visualDisplayId, $object->panels);
            } else if($visualDisplay != null && count($visualDisplay->panels) > 0) {
                foreach($visualDisplay->panels as $panel) {
                    DB_ORM::delete('map_visualdisplay_panel')
                            ->where('id', '=', $panel->id)
                            ->execute();
                }
            }
            
            if(property_exists($object, 'images')) {
                $this->updateImages($visualDisplayId, $object->images);
            } else if($visualDisplay != null && count($visualDisplay->images) > 0) {
                foreach($visualDisplay->images as $image) {
                    DB_ORM::delete('map_visualdisplay_image')
                            ->where('id', '=', $image->id)
                            ->execute();
                }
            }
            
            if(property_exists($object, 'counters')) {
                $this->updateCounters($visualDisplayId, $object->counters);
            } else if($visualDisplay != null && count($visualDisplay->counters) > 0) {
                foreach($visualDisplay->counters as $counters) {
                    DB_ORM::delete('map_visualdisplay_counter')
                            ->where('id', '=', $counters->id)
                            ->execute();
                }
            }
        }
        
        return $visualDisplayId;
    }
    
    /**
     * Convert visual display with all data to JSON string
     * 
     * @param integer $visualDisplayId - Visual display ID
     * @return string - JSON string or null 
     */
    public function toJSON($visualDisplayId) {
        if($visualDisplayId == null || $visualDisplayId <= 0) return null;
        
        $visualDisplay = DB_ORM::model('map_visualdisplay', array((int)$visualDisplayId));
        if($visualDisplay == null) return null;
        
        $resultJSON = array();
        
        $panelsJSON = array();
        if($visualDisplay->panels != null && count($visualDisplay->panels) > 0) {
            foreach($visualDisplay->panels as $panel) {
                $panelsJSON[] = '{             id: '  . $panel->id . ', ' .
                                           'width: '  . $panel->width . ', ' .
                                          'height: '  . $panel->height . ', ' .
                                          'border: '  . $panel->border_size . ', ' .
                                     'borderColor: "' . $panel->border_color . '", ' . 
                                    'borderRadius: '  . $panel->border_radius . ', ' .
                                          'zIndex: '  . $panel->z_index . ', ' .
                                 'backgroundColor: "' . $panel->background_color . '", ' . 
                                           'angle: '  . $panel->angle . ', ' . 
                                               'x: '  . $panel->x . ', ' .
                                               'y: '  . $panel->y .
                                '}';
            }
            
            if(count($panelsJSON) > 0) {
                $resultJSON[] = 'panels: [' . implode(', ', $panelsJSON) . ']';
            }
        }
        
        $imagesJSON = array();
        if($visualDisplay->images != null && count($visualDisplay->images) > 0) {
            foreach($visualDisplay->images as $image) {
                if(!file_exists(DOCROOT . '/files/' . $visualDisplay->map_id . '/vdImages/' . $image->name)) continue;
                
                $imagesJSON[] = '{    id: '  . $image->id . ', ' .
                                  'image: "' . URL::base() . 'files/' . $visualDisplay->map_id . '/vdImages/' . $image->name . '", ' . 
                                  'width: '  . $image->width . ', ' . 
                                 'height: '  . $image->height . ', ' .
                                  'angle: '  . $image->angle . ', ' . 
                                      'x: '  . $image->x . ', ' .
                                      'y: '  . $image->y . ', ' . 
                                 'zIndex: '  . $image->z_index .
                                '}';
            }
            
            if(count($imagesJSON) > 0) {
                $resultJSON[] = 'images: [' . implode(', ', $imagesJSON) . ']';
            }
        }
        
        $countersJSON = array();
        if($visualDisplay->counters != null && count($visualDisplay->counters) > 0) {
            foreach($visualDisplay->counters as $counter) {
                $countersJSON[] = '{         id: '  . $counter->id . ', ' .
                                     'counterId: '  . $counter->counter_id . ', ' .
                                        'labelX: '  . $counter->label_x . ', ' . 
                                        'labelY: '  . $counter->label_y . ', ' . 
                                    'labelAngle: '  . $counter->label_angle . ', ' . 
                                     'labelFont: "' . $counter->label_font_style . '", ' . 
                                     'labelText: "' . base64_encode(str_replace('&#43;', '+', $counter->label_text)) . '", ' . 
                                   'labelZIndex: '  . $counter->label_z_index . ', ' . 
                                        'valueX: '  . $counter->value_x . ', ' . 
                                        'valueY: '  . $counter->value_y . ', ' . 
                                    'valueAngle: '  . $counter->value_angle . ', ' . 
                                     'valueFont: "' . $counter->value_font_style . '", ' . 
                                   'valueZIndex: '  . $counter->value_z_index . 
                                  '}';
            }
            
            if(count($countersJSON) > 0) {
                $resultJSON[] = 'counters: [' . implode(', ', $countersJSON) . ']';
            }
        }
        
        $result = null;
        if(count($resultJSON) > 0) {
            $result = '{ id: ' . $visualDisplay->id . ', ' . implode(', ', $resultJSON) . '}';
        }
        
        return $result;
    }
    
    /**
     * Remove image
     * 
     * @param integer $visualDisplayId - Visual Display ID
     * @param integer $mapId - Map ID
     * @param string $imageName - Image name
     * @return none 
     */
    public function deleteImage($visualDisplayId, $mapId, $imageName) {
        $bigImageSrc = DOCROOT . '/files/' . $mapId . '/vdImages/' . $imageName;
        $smallImageSrc = DOCROOT . '/files/' . $mapId . '/vdImages/thumbs/' . $imageName;
        
        if(file_exists($bigImageSrc)) {
            unlink($bigImageSrc);
        }
        
        if(file_exists($smallImageSrc)) {
            unlink($smallImageSrc);
        }
        
        if($visualDisplayId == null || $visualDisplayId <= 0 || $imageName == null) return;
        
        $visualDisplay = DB_ORM::model('map_visualdisplay', array((int)$visualDisplayId));
        if($visualDisplay == null) return;
        
        if(count($visualDisplay->images) > 0) {
            foreach($visualDisplay->images as $image) {
                if($image->name == $imageName) {
                    DB_ORM::delete('map_visualdisplay_image')
                            ->where('id', '=', $image->id)
                            ->execute();
                }
            }
        }
    }
    
    /**
     * Create new visual display
     * 
     * @param integer $mapId - Map ID
     * @return integer - New Visual Display element ID 
     */
    private function createVisualDisplay($mapId) {
        if($mapId == null || $mapId <= 0) return;
        
        return DB_ORM::insert('map_visualdisplay')
                        ->column('map_id', $mapId)
                        ->execute();
    }
    
    /**
     * Update all position of object margin to (0, 0)
     * 
     * @param object $object - object of elements 
     */
    private function updateElementsPositions($object) {
        $minimumPosition = $this->getMinimumPosition($object);
        
        if(property_exists($object, 'panels') && count($object->panels) > 0) {
            foreach($object->panels as $panel) {
                $panel->x -= $minimumPosition[0];
                $panel->y -= $minimumPosition[1];
            }
        }
        
        if(property_exists($object, 'images') && count($object->images) > 0) {
            foreach($object->images as $image) {
                $image->x -= $minimumPosition[0];
                $image->y -= $minimumPosition[1];
            }
        }
        
        if(property_exists($object, 'counters') && count($object->counters) > 0) {
            foreach($object->counters as $counter) {
                $counter->labelX -= $minimumPosition[0];
                $counter->labelY -= $minimumPosition[1];
                $counter->valueX -= $minimumPosition[0];
                $counter->valueY -= $minimumPosition[1];
            }
        }
    }
    
    /**
     * Return minimum position from colleciton
     * 
     * NOTES: Each item in the collection should be subject to the fields 'x' and 'y'
     * 
     * @param array $collection - collection of objects
     * @return array - array of minimum position (x, y) 
     */
    private function getMinimumPositionXY($collection) {
        if($collection == null || count($collection) <= 0) return null;
        
        $minX = $collection[0]->x;
        $minY = $collection[0]->y;
        foreach($collection as $item) {
            if($minX > $item->x) {
                $minX = $item->x;
            }
            
            if($minY > $item->y) {
                $minY = $item->y;
            }
        }
        
        return array($minX, $minY);
    }
    
    /**
     * Return minimum position from colleciton of counters
     * 
     * NOTES: Each item in the collection should be subject to the fields 'labelX', 'labelY', 'valueX' and 'valueY'
     * 
     * @param array $counters - collection of objects
     * @return array - array of minimum position (x, y) 
     */
    private function getMinimumPositionCounters($counters) {
        if($counters == null || count($counters) <= 0) return null;
        
        $minX = $counters[0]->labelX;
        $minY = $counters[0]->labelY;
        foreach($counters as $item) {
            if($minX > $item->labelX) {
                $minX = $item->labelX;
            }
            
            if($minX > $item->valueX) {
                $minX = $item->valueX;
            }
            
            if($minY > $item->labelY) {
                $minY = $item->labelY;
            }
            
            if($minY > $item->valueY) {
                $minY = $item->valueY;
            }
        }
        
        return array($minX, $minY);
    }
    
    /**
     * Return minimum position from all objects in class
     * 
     * @param object $object - object of elementes
     * @return array - minimum position of elements (x, y) 
     */
    private function getMinimumPosition($object) {
        $minX = null;
        $minY = null;
        
        if(property_exists($object, 'panels')) {
            $position = $this->getMinimumPositionXY($object->panels);
            if($position != null) {
                if($minX == null) {
                    $minX = $position[0];
                }
                
                if($minY == null) {
                    $minY = $position[1];
                }
            }
        }
        
        if(property_exists($object, 'images')) {
            $position = $this->getMinimumPositionXY($object->images);
            if($position != null) {
                if($minX == null) {
                    $minX = $position[0];
                } else if($minX > $position[0]) {
                    $minX = $position[0];
                }
                
                if($minY == null) {
                    $minY = $position[1];
                } else if($minY > $position[1]) {
                    $minY = $position[1];
                }
            }
        }
        
        if(property_exists($object, 'counters')) {
            $position = $this->getMinimumPositionCounters($object->counters);
            if($position != null) {
                if($minX == null) {
                    $minX = $position[0];
                } else if($minX > $position[0]) {
                    $minX = $position[0];
                }
                
                if($minY == null) {
                    $minY = $position[1];
                } else if($minY > $position[1]) {
                    $minY = $position[1];
                }
            }
        }
        
        if($minX == null) {
            $minX = 0;
        }
        
        if($minY == null) {
            $minY = 0;
        }
        
        return array($minX, $minY);
    }
    
    /**
     * Update all panels for visual display
     * 
     * @param integer $visualDisplayId - Visual display element ID
     * @param array $panels - array of panels objects
     * @return none 
     */
    private function updatePanels($visualDisplayId, $panels) {
        if($visualDisplayId <= 0 || $panels == null || count($panels) <= 0) return;
        
        $visualDisplay = DB_ORM::model('map_visualdisplay', array((int)$visualDisplayId));
        
        $panelsMap = array();
        $updatedPanelsMap = array();
        if($visualDisplay != null && $visualDisplay->panels != null && count($visualDisplay->panels) > 0) {
            foreach($visualDisplay->panels as $panel) {
                $panelsMap[$panel->id] = $panel;
            }
        }
        
        foreach($panels as $panel) {
            if($panel->id == 'null') {
                DB_ORM::insert('map_visualdisplay_panel')
                        ->column('visual_id', $visualDisplayId)
                        ->column('x', $panel->x)
                        ->column('y', $panel->y)
                        ->column('width', $panel->width)
                        ->column('height', $panel->height)
                        ->column('background_color', $panel->backgroundColor)
                        ->column('border_size', $panel->border)
                        ->column('border_color', $panel->borderColor)
                        ->column('border_radius', $panel->borderRadius)
                        ->column('angle', ($panel->angle != 'null' ? (int)$panel->angle : 0))
                        ->column('z_index', ($panel->zIndex != 'null' ? (int)$panel->zIndex : 0))
                        ->execute();
            } else if(isset($panelsMap[$panel->id])) {
                $updatedPanelsMap[$panel->id] = true;
                DB_ORM::update('map_visualdisplay_panel')
                        ->set('x', $panel->x)
                        ->set('y', $panel->y)
                        ->set('width', $panel->width)
                        ->set('height', $panel->height)
                        ->set('background_color', $panel->backgroundColor)
                        ->set('border_size', $panel->border)
                        ->set('border_color', $panel->borderColor)
                        ->set('border_radius', $panel->borderRadius)
                        ->set('angle', ($panel->angle != 'null' ? (int)$panel->angle : 0))
                        ->set('z_index', ($panel->zIndex != 'null' ? (int)$panel->zIndex : 0))
                        ->where('id', '=', $panel->id)
                        ->execute();
            }
        }
        
        if(count($panelsMap) > 0) {
            foreach($panelsMap as $panel) {
                if(!isset($updatedPanelsMap[$panel->id])) {
                    DB_ORM::delete('map_visualdisplay_panel')
                            ->where('id', '=', $panel->id)
                            ->execute();
                }
            }
        }
    }
    
    /**
     * Update all images for visual display
     * 
     * @param integer $visualDisplayId - Visual display element ID
     * @param array $images - array of images objects
     * @return none 
     */
    private function updateImages($visualDisplayId, $images) {
        if($visualDisplayId <= 0 || $images == null || count($images) <= 0) return;
        
        $visualDisplay = DB_ORM::model('map_visualdisplay', array((int)$visualDisplayId));

        $imagesMap = array();
        $updatedImagesMap = array();
        if($visualDisplay != null && $visualDisplay->images != null && count($visualDisplay->images) > 0) {
            foreach($visualDisplay->images as $image) {
                $imagesMap[$image->id] = $image;
            }
        }
        
        foreach($images as $image) {
            $pathInfo = pathinfo($image->image);
            if($image->id == 'null') {
                DB_ORM::insert('map_visualdisplay_image')
                        ->column('visual_id', $visualDisplayId)
                        ->column('name', $pathInfo['basename'])
                        ->column('width', $image->width)
                        ->column('height', $image->height)
                        ->column('angle', ($image->angle != 'null' && is_numeric($image->angle) ? (int)$image->angle : 0))
                        ->column('z_index', ($image->zIndex != 'null' && is_numeric($image->zIndex) ? (int)$image->zIndex : 0))
                        ->column('x', $image->x)
                        ->column('y', $image->y)
                        ->execute();
            } else if(isset($imagesMap[$image->id])) {
                $updatedImagesMap[$image->id] = true;
                DB_ORM::update('map_visualdisplay_image')
                        ->set('visual_id', $visualDisplayId)
                        ->set('name', $pathInfo['basename'])
                        ->set('width', $image->width)
                        ->set('height', $image->height)
                        ->set('angle', ($image->angle != 'null' && is_numeric($image->angle) ? (int)$image->angle : 0))
                        ->set('z_index', ($image->zIndex != 'null' && is_numeric($image->zIndex) ? (int)$image->zIndex : 0))
                        ->set('x', $image->x)
                        ->set('y', $image->y)
                        ->where('id', '=', $image->id)
                        ->execute();
            }
        }
        
        if(count($imagesMap) > 0) {
            foreach($imagesMap as $image) {
                if(!isset($updatedImagesMap[$image->id])) {
                    DB_ORM::delete('map_visualdisplay_image')
                            ->where('id', '=', $image->id)
                            ->execute();
                }
            }
        }
    }
    
    /**
     * Update all counters for visual display
     * 
     * @param integer $visualDisplayId - Visual display element ID
     * @param array $counters - array of counters objects
     * @return none 
     */
    private function updateCounters($visualDisplayId, $counters) {
        if($visualDisplayId <= 0 || $counters == null || count($counters) <= 0) return;
        
        $visualDisplay = DB_ORM::model('map_visualdisplay', array((int)$visualDisplayId));

        $countersMap = array();
        $updatedCountersMap = array();
        $currentMapCountersMap = array();
        if($visualDisplay != null && $visualDisplay->counters != null && count($visualDisplay->counters) > 0) {
            foreach ($visualDisplay->counters as $counter) {
                $countersMap[$counter->id] = $counter;
            }
        }

        $mapCounters = DB_ORM::model('map_counter')->getCountersByMap($visualDisplay->map_id);
        if($mapCounters != null && count($mapCounters) > 0) {
            foreach ($mapCounters as $counter) {
                $currentMapCountersMap[$counter->id] = $counter;
            }
        }
        
        foreach($counters as $counter) {
            if(!isset($currentMapCountersMap[$counter->counterId])) continue;

            if($counter->id == 'null') {
                DB_ORM::insert('map_visualdisplay_counter')
                        ->column('visual_id', $visualDisplayId)
                        ->column('counter_id', $counter->counterId)
                        ->column('label_x', $counter->labelX)
                        ->column('label_y', $counter->labelY)
                        ->column('label_angle', ($counter->labelAngle != 'null' && is_numeric($counter->labelAngle) ? (int)$counter->labelAngle : 0))
                        ->column('label_font_style', $counter->labelFont)
                        ->column('label_text', urldecode(str_replace('+', '&#43;', base64_decode($counter->labelText))))
                        ->column('label_z_index', ($counter->labelZIndex != 'null' && is_numeric($counter->labelZIndex) ? (int)$counter->labelZIndex : 0))
                        ->column('value_x', $counter->valueX)
                        ->column('value_y', $counter->valueY)
                        ->column('value_angle', $counter->valueAngle)
                        ->column('value_font_style', $counter->valueFont)
                        ->column('value_z_index', ($counter->valueZIndex != 'null' && is_numeric($counter->valueZIndex) ? (int)$counter->valueZIndex : 0))
                        ->execute();
            } else if(isset($countersMap[$counter->id])) {
                $updatedCountersMap[$counter->id] = true;
                DB_ORM::update('map_visualdisplay_counter')
                        ->set('visual_id', $visualDisplayId)
                        ->set('counter_id', $counter->counterId)
                        ->set('label_x', $counter->labelX)
                        ->set('label_y', $counter->labelY)
                        ->set('label_angle', ($counter->labelAngle != 'null' && is_numeric($counter->labelAngle) ? (int)$counter->labelAngle : 0))
                        ->set('label_font_style', $counter->labelFont)
                        ->set('label_text', urldecode(str_replace('+', '&#43;', base64_decode($counter->labelText))))
                        ->set('label_z_index', ($counter->labelZIndex != 'null' && is_numeric($counter->labelZIndex) ? (int)$counter->labelZIndex : 0))
                        ->set('value_x', $counter->valueX)
                        ->set('value_y', $counter->valueY)
                        ->set('value_angle', $counter->valueAngle)
                        ->set('value_font_style', $counter->valueFont)
                        ->set('value_z_index', ($counter->valueZIndex != 'null' && is_numeric($counter->valueZIndex) ? (int)$counter->valueZIndex : 0))
                        ->where('id', '=', $counter->id)
                        ->execute();
            }
        }
        
        if(count($countersMap) > 0) {
            foreach($countersMap as $counter) {
                if(!isset($updatedCountersMap[$counter->id])) {
                    DB_ORM::delete('map_visualdisplay_counter')
                            ->where('id', '=', $counter->id)
                            ->execute();
                }
            }
        }
    }
};
?>