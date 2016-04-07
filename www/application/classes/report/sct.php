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
 * Class 4R Report
 */
class Report_SCT extends Report
{

    private $maps;
    private $name;
    private $mapElements;

    /**
     * Default constructor
     *
     * @param Report_Impl $impl - report class implementation
     * @param string $name - report name
     */
    public function __construct(Report_Impl $impl, $name)
    {
        parent::__construct($impl);

        $this->maps = array();
        $this->name = $name;
        $this->mapElements = array();
    }

    /**
     * Add map for report
     *
     * @param integer $mapId - map ID
     * @param integer $webinarId - webinar ID
     * @param integer $webinarStep - webinar step
     */
    public function add($mapId, $webinarId, $expertWebinarId, $sectionId)
    {
        if ($mapId == null || $mapId <= 0) {
            return;
        }

        $this->maps[] = array(
            'mapId' => $mapId,
            'webinarId' => $webinarId,
            'expertWebinarId' => !empty($expertWebinarId) ? $expertWebinarId : $webinarId,
            'sectionId' => $sectionId
        );
    }

    /**
     * Generate report
     *
     * @return mixed
     */
    public function generate($latest = true)
    {
        if ($this->implementation == null || $this->maps == null || count($this->maps) <= 0) {
            return;
        }

        $this->implementation->setCreator('OpenLabyrinth System');
        $this->implementation->setLastModifiedBy('OpenLabyrinth System');
        $this->implementation->setTitle('SCT Report');
        $this->implementation->setSubject('SCT Statistic');
        $this->implementation->setDescription('SCT Statistic');
        $this->implementation->setKeywords('SCT, SCT Report, Report');
        $this->implementation->setCategory('Report');
        $this->implementation->setActiveSheet(0);

        foreach ($this->maps as $mapData) {
            $this->mapElements[] = new Report_SCT_Map(
                $this->implementation,
                $mapData['mapId'],
                $mapData['webinarId'],
                $mapData['expertWebinarId'],
                $mapData['sectionId'],
                $latest
            );
        }
    }

    /**
     * Get report
     *
     * @return mixed
     */
    public function get()
    {
        if ($this->implementation == null) {
            return;
        }

        if (count($this->mapElements) > 0) {
            $currentOffset = 1;
            foreach ($this->mapElements as $mapElement) {
                $currentOffset += $mapElement->insert($currentOffset);
            }
        }
        $this->implementation->download($this->name);
    }
}