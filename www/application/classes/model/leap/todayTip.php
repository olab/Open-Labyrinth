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
 * Model for today tips table in database
 */
class Model_Leap_TodayTip extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => FALSE,
            )),

            'title' => new DB_ORM_Field_String($this, array(
                'max_length' => 300,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),

            'text' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
                'savable' => TRUE,
            )),

            'start_date' => new DB_ORM_Field_DateTime($this, array(
                'nullable' => FALSE,
                'savable' => TRUE,
            )),

            'end_date' => new DB_ORM_Field_DateTime($this, array(
                'nullable' => TRUE,
                'savable' => TRUE,
            )),

            'weight' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => FALSE,
                'default' => 0
            )),

            'is_active' => new DB_ORM_Field_Boolean($this, array(
                'default' => FALSE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),

            'is_archived' => new DB_ORM_Field_Boolean($this, array(
                'default' => FALSE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'today_tips';
    }

    public static function primary_key() {
        return array('id');
    }

    /**
     * Return current today tips
     *
     * @return array|null
     */
    public function getTodayTips() {
        $date = date('Y-m-d H:i:s');

        $records = DB_SQL::select('default')
                           ->from($this->table())
                           ->where('is_archived', '=', 0, 'AND')
                           ->where('is_active'  , '=', 1, 'AND')
                           ->where('start_date' , '<=', $date, 'AND')
                           ->where('end_date'   , '=' , null , 'OR')
                           ->where('end_date'   , '>=', $date)
                           ->order_by('weight', 'DESC')
                           ->column('id')
                           ->query();

        $result = null;
        if($records->is_loaded()) {
            $result  = array();
            $weights = array();
            foreach($records as $record) {
                $tip       = DB_ORM::model('TodayTip', array((int)$record['id']));
                $result[]  = $tip;
                $weights[] = $tip->weight;
            }

            if(count($result) > 0) {
                list($lockups, $totalWeight) = $this->calculateLockups($weights);
                $tip = $this->weightedRandom($result, $weights, $lockups, $totalWeight);

                if($tip != null) {
                    $result = array($tip);
                } else {
                    $result = array();
                }
            }
        }

        return $result;
    }

    /**
     * Return all active record order by date and weight
     *
     * @return array|null
     */
    public function getActiveTips() {
        $records = DB_SQL::select('default')
                           ->from($this->table())
                           ->where('is_archived', '=' , 0)
                           ->order_by('is_active' , 'DESC')
                           ->order_by('start_date', 'DESC')
                           ->order_by('weight'    , 'DESC')
                           ->column('id')
                           ->query();

        $result = null;
        if($records->is_loaded()) {
            $result = array();
            foreach($records as $record) {
                $result[] = DB_ORM::model('TodayTip', array((int)$record['id']));
            }
        }

        return $result;
    }

    /**
     * Return all archived tips
     *
     * @return array|null
     */
    public function getArchivedTips() {
        $records = DB_SQL::select('default')
                           ->from($this->table())
                           ->where('is_archived', '=' , 1)
                           ->order_by('is_active' , 'DESC')
                           ->order_by('start_date', 'DESC')
                           ->order_by('weight'    , 'DESC')
                           ->column('id')
                           ->query();

        $result = null;
        if($records->is_loaded()) {
            $result = array();
            foreach($records as $record) {
                $result[] = DB_ORM::model('TodayTip', array((int)$record['id']));
            }
        }

        return $result;
    }

    /**
     * Save tip or create new
     *
     * @param $id
     * @param $values
     */
    public function saveTip($id, $values) {
        $query = null;

        $dateString = Arr::get($values, 'date', '') . ' 00:00';
        $date = null;
        if(strlen($dateString) > 5) {
            $date = new DateTime($dateString);
            if($date != null) {
                $dateString = $date->format('Y-m-d H:i:s');
            }
        }

        $endDateString = Arr::get($values, 'dateEnd', '') . ' 23:59';
        $withoutEndDate = Arr::get($values, 'withoutDate', null);
        $endDate = null;
        if($withoutEndDate == null) {
            if(strlen($endDateString) > 5) {
                $endDate = new DateTime($endDateString);
                if($endDate != null) {
                    $endDateString = $endDate->format('Y-m-d H:i:s');
                } else if($date != null) {
                    $endDate = $date->add(new DateInterval('P1D'));
                    $endDateString = $endDate->format('Y-m-d H:i:s');
                }
            } else if($date != null) {
                $endDate = $date->add(new DateInterval('P1D'));
                $endDateString = $endDate->format('Y-m-d H:i:s');
            }
        } else {
            $endDateString = null;
        }

        if($id != null && $id > 0) {
            $tip = DB_ORM::model('TodayTip', array((int)$id));
            if($tip != null) {
                $updateEndDateString = $tip->end_date;
                if($endDateString != null) {
                    $updateEndDateString = $endDateString;
                } else if($endDate == null) {
                    $updateEndDateString = null;
                }

                $query = DB_ORM::update('TodayTip')
                                 ->set('title'     , Arr::get($values, 'title' , $tip->title))
                                 ->set('text'      , Arr::get($values, 'text'  , $tip->text))
                                 ->set('start_date', $date != null ? $dateString : $tip->start_date)
                                 ->set('end_date'  , $updateEndDateString)
                                 ->set('weight'    , (int) Arr::get($values, 'weight', $tip->weight))
                                 ->set('is_active' , Arr::get($values, 'active', $tip->is_active))
                                 ->where('id', '=', $id);
            }
        } else {
            $query = DB_ORM::insert('TodayTip')
                             ->column('title'       , Arr::get($values, 'title' , ''))
                             ->column('text'        , Arr::get($values, 'text'  , ''))
                             ->column('start_date'  , $dateString)
                             ->column('end_date'    , $endDateString)
                             ->column('weight'      , (int) Arr::get($values, 'weight', 0))
                             ->column('is_active'   , Arr::get($values, 'active', 0))
                             ->column('is_archived' , 0);
        }

        return $query->execute();
    }

    private function calculateLockups($weights) {
        $lockups     = array();
        $totalWeight = 0;

        for($i = 0; $i < count($weights); $i++) {
            $totalWeight += $weights[$i];
            $lockups[$i] = $totalWeight;
        }

        return array($lockups, $totalWeight);
    }

    private function weightedRandom($values, $weights, $lookup = null, $total_weight = null){
        if ($lookup == null) {
            list($lookup, $total_weight) = $this->calculateLockups($weights);
        }

        $r = mt_rand(0, $total_weight);
        return $values[$this->binarySearch($r, $lookup)];
    }

    private function binarySearch($needle, $haystack) {
        $high = count($haystack)-1;
        $low  = 0;

        while ( $low < $high ) {
            $probe = (int)(($high + $low) / 2);
            if ($haystack[$probe] < $needle){
                $low = $probe + 1;
            } else if ($haystack[$probe] > $needle) {
                $high = $probe - 1;
            } else {
                return $probe;
            }
        }

        if ( $low != $high ){
            return $probe;
        } else {
            if ($haystack[$low] >= $needle) {
                return $low;
            } else {
                return $low+1;
            }
        }
    }
}
?>