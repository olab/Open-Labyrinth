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
 * Class Report implementation
 */
abstract class Report_Impl {
    /**
     * Set document creator
     *
     * @param string $creator - creator
     */
    public abstract function setCreator($creator);

    /**
     * Set document last modified by
     *
     * @param string $modifiedBy - modified by
     */
    public abstract function setLastModifiedBy($modifiedBy);

    /**
     * Set document title
     *
     * @param string $title - document title
     */
    public abstract function setTitle($title);

    /**
     * Set document subject
     *
     * @param string $subject - document subject
     */
    public abstract function setSubject($subject);

    /**
     * Set document description
     *
     * @param string $description - document description
     */
    public abstract function setDescription($description);

    /**
     * Set document keywords
     *
     * @param string $keywords - document keywords
     */
    public abstract function setKeywords($keywords);

    /**
     * Set document category
     *
     * @param string $category - document category
     */
    public abstract function setCategory($category);

    /**
     * Set current cursor position
     *
     * @param $cursor - cursor position
     */
    public abstract function setCursor($cursor);

    /**
     * Set value in current cursor position
     *
     * @param $value - value
     */
    public abstract function setValue($value);

    /**
     * Set active sheet
     *
     * @param integer $index - sheet index
     */
    public abstract function setActiveSheet($index);

    /**
     * Add stacked bar chart (horizontal)
     *
     * @param $startPosition - start chart position
     * @param $endPosition - end chart position
     * @param $dataSeriesLabels - data series labels
     * @param $xAxisTickValue - x axis values
     * @param $dataSeriesValues - data series values
     * @param $title - title of chart
     * @param $yAxisLabel - y axis label
     */
    public abstract function addStackedBarChart($startPosition, $endPosition, $dataSeriesLabels, $xAxisTickValue, $dataSeriesValues, $title, $yAxisLabel);

    /**
     * Add horizontal bar chart
     *
     * @param $startPosition - start chart position
     * @param $endPosition - end chart position
     * @param $dataSeriesLabels - data series labels
     * @param $xAxisTickValue - x axis values
     * @param $dataSeriesValues - data series values
     * @param $title - title of chart
     * @param $yAxisLabel - y axis label
     */
    public abstract function addHorizontalBarChart($startPosition, $endPosition, $dataSeriesLabels, $xAxisTickValue, $dataSeriesValues, $title, $yAxisLabel);

    /**
     * Set font size for need cells
     *
     * @param string $cells - cells
     * @param integer $size - font size
     */
    public abstract function setFontSize($cells, $size);

    /**
     * Set cells format
     *
     * @param string $cells - cells
     * @param string $cellFormat - cells format
     */
    public abstract function setCellsFormat($cells, $cellFormat);

    /**
     * Return xlsx file
     *
     * @param string $name - download file name
     * @return mixed
     */
    public abstract function download($name);

    /**
     * Get style by name
     *
     * @param string $styleName - style name
     * @return mixed - style
     */
    public abstract function getStyle($styleName);

    /**
     * Set URL for cell
     *
     * @param string $text - text for URL
     * @param string $url - URL address
     */
    public abstract function setURL($text, $url);

}