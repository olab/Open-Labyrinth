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

class Report_4R_Node extends Report_Element {
    private $nodeId;
    private $title;
    private $countOfChoices;

    private $choicesMap = array(
        1 => 'C',
        2 => 'D',
        3 => 'E',
        4 => 'F'
    );

    /**
     * Default constructor
     *
     * @param Report_Impl $impl - report implementation
     */
    public function __construct(Report_Impl $impl, $nodeId, $title, $countOfChoices) {
        parent::__construct($impl);

        $this->nodeId         = $nodeId;
        $this->title          = $title;
        $this->countOfChoices = $countOfChoices;
    }

    /**
     * Insert node row
     *
     * @return integer - offset
     */
    public function insert($offset) {
        if($this->implementation == null) return 0;

        $this->implementation->setCursor('A' . $offset);
        $this->implementation->setValue($this->nodeId);
        for($i = 1; $i <= $this->countOfChoices; $i++) {
            if(isset($this->choicesMap[$i])) {
                $this->implementation->setCursor($this->choicesMap[$i] . $offset);
                $this->implementation->setValue(0);
            }
        }
        $this->implementation->setCursor('G' . $offset);
        $this->implementation->setCellsFormat('G' . $offset, $this->implementation->getStyle('PERCENTAGE_00'));
        $this->implementation->setValue(0.0);
        $this->implementation->setCursor('H' . $offset);
        $this->implementation->setValue($this->title);

        return 1;
    }

    /**
     * Get key node ID
     *
     * @return integer - node ID
     */
    public function getKey() {
        return $this->nodeId;
    }
}