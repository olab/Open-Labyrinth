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

class Searcher {
    private $elements = array();

    /**
     * Add element for searching
     *
     * @param Search_Element $element - searching elements
     */
    public function addElement($element) {
        $this->elements[] = $element;
    }

    /**
     * Search
     *
     * @param string $searchText - search string
     * @return array - array of Search_Result
     */
    public function search($searchText) {
        $data = array();

        if($this->elements != null && count($this->elements) > 0) {
            foreach($this->elements as $element) {
                $elementData = $element->search($searchText);
                if($elementData != null && count($elementData) > 0) {
                    foreach($elementData as $d) {
                        $data[] = $d;
                    }
                }
            }
        }

        return $data;
    }
};