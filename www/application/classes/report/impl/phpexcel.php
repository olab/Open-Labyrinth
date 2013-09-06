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
 * Class PHPExcel Report implementation
 */
class Report_Impl_PHPExcel extends Report_Impl {
    private $phpExcel;
    private $cursor;
    private $phpExcelProperties;

    /**
     * Default constructor
     */
    public function __construct() {
        $this->phpExcel = new PHPExcel();
        $this->cursor = 'A1';
        $this->phpExcelProperties = $this->phpExcel->getProperties();
    }

    public function setCreator($creator) {
        if($this->phpExcelProperties == null) return;

        $this->phpExcelProperties->setCreator($creator);
    }

    public function setLastModifiedBy($modifiedBy) {
        if($this->phpExcelProperties == null) return;

        $this->phpExcelProperties->setLastModifiedBy($modifiedBy);
    }

    public function setTitle($title) {
        if($this->phpExcelProperties == null) return;

        $this->phpExcelProperties->setTitle($title);
    }

    public function setSubject($subject) {
        if($this->phpExcelProperties == null) return;

        $this->phpExcelProperties->setSubject($subject);
    }

    public function setDescription($description) {
        if($this->phpExcelProperties == null) return;

        $this->phpExcelProperties->setDescription($description);
    }

    public function setKeywords($keywords) {
        if($this->phpExcelProperties == null) return;

        $this->phpExcelProperties->setKeywords($keywords);
    }

    public function setCategory($category) {
        if($this->phpExcelProperties == null) return;

        $this->phpExcelProperties->setCategory($category);
    }

    public function setCursor($cursor) {
        if($this->cursor == null || empty($cursor)) return;

        $this->cursor = $cursor;
    }

    public function setValue($value) {
        if($this->phpExcel == null) return;

        $activeSheet = $this->phpExcel->getActiveSheet();
        if($activeSheet == null) return;

        $activeSheet->setCellValue($this->cursor, $value);
    }

    public function setActiveSheet($index) {
        if($this->phpExcel == null || $index == null || $index <= 0) return;

        $this->phpExcel->setActiveSheetIndex($index);
    }

    public function download($name) {
        $this->phpExcel->setActiveSheetIndex(0);

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpExcel, 'Excel2007');
        $objWriter->setIncludeCharts(TRUE);
        $objWriter->save('php://output');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $name . '.xlsx"');
        header('Cache-Control: max-age=0');

        die();
    }

    public function addStackedBarChart($startPosition, $endPosition, $dataSeriesLabels, $xAxisTickValue, $dataSeriesValues, $title, $yAxisLabel) {
        if($this->phpExcel   == null                                  ||
           $startPosition    == null                                  ||
           $endPosition      == null                                  ||
           $dataSeriesLabels == null || count($dataSeriesLabels) <= 0 ||
           $xAxisTickValue   == null || count($xAxisTickValue)   <= 0 ||
           $dataSeriesValues == null || count($dataSeriesValues) <= 0) {
            return;
        }

        $phpExcelDataSeriesLabels = array();
        foreach($dataSeriesLabels as $label) {
            $phpExcelDataSeriesLabels[] = new PHPExcel_Chart_DataSeriesValues('String', $label, null, 1);
        }

        $phpExcelXAxisTickValue = array();
        foreach($xAxisTickValue as $value) {
            $phpExcelXAxisTickValue[] = new PHPExcel_Chart_DataSeriesValues('String', $value['values'], null, $value['count']);
        }

        $phpExcelDataSeriesValues = array();
        foreach($dataSeriesValues as $values) {
            $value = '';
            $count = 0;
            if(count($values) > 0) {
                foreach($values as $v) {
                    $value .= $v['values'] . ',';
                    $count += $v['count'];
                }

                $value = '(' . substr($value, 0, strlen($value) - 1) . ')';
            } else {
                $value = $values['values'];
                $count = $values['count'];
            }

            $phpExcelDataSeriesValues[] = new PHPExcel_Chart_DataSeriesValues('Number', $value, null, $count);
        }

        $series = new PHPExcel_Chart_DataSeries(
            PHPExcel_Chart_DataSeries::TYPE_BARCHART,
            PHPExcel_Chart_DataSeries::GROUPING_STACKED,
            range(0, count($phpExcelDataSeriesValues) - 1),
            $phpExcelDataSeriesLabels,
            $phpExcelXAxisTickValue,
            $phpExcelDataSeriesValues
        );
        $series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_BAR);

        $plotArea   = new PHPExcel_Chart_PlotArea(NULL, array($series));
        $legend     = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
        $title      = new PHPExcel_Chart_Title($title);
        $yAxisLabel = new PHPExcel_Chart_Title($yAxisLabel);
        $chart      = new PHPExcel_Chart(
            'chart' . time(),
            $title,
            $legend,
            $plotArea,
            true,
            0,
            NULL,
            $yAxisLabel
        );

        $chart->setTopLeftPosition($startPosition);
        $chart->setBottomRightPosition($endPosition);

        $activeSheet = $this->phpExcel->getActiveSheet();
        $activeSheet->addChart($chart);
    }

    public function addHorizontalBarChart($startPosition, $endPosition, $dataSeriesLabels, $xAxisTickValue, $dataSeriesValues, $title, $yAxisLabel) {
        if($this->phpExcel   == null                                  ||
            $startPosition    == null                                  ||
            $endPosition      == null                                  ||
            $dataSeriesLabels == null || count($dataSeriesLabels) <= 0 ||
            $xAxisTickValue   == null || count($xAxisTickValue)   <= 0 ||
            $dataSeriesValues == null || count($dataSeriesValues) <= 0) {
            return;
        }

        $phpExcelDataSeriesLabels = array();
        foreach($dataSeriesLabels as $label) {
            $phpExcelDataSeriesLabels[] = new PHPExcel_Chart_DataSeriesValues('String', $label['values'], null, 1);
        }

        $phpExcelXAxisTickValue = array();
        foreach($xAxisTickValue as $value) {
            $phpExcelXAxisTickValue[] = new PHPExcel_Chart_DataSeriesValues('String', $value['values'], null, $value['count']);
        }

        $phpExcelDataSeriesValues = array();
        foreach($dataSeriesValues as $value) {
            $phpExcelDataSeriesValues[] = new PHPExcel_Chart_DataSeriesValues('Number', $value['values'], null, $value['count']);
        }

        $series = new PHPExcel_Chart_DataSeries(
            PHPExcel_Chart_DataSeries::TYPE_BARCHART,
            PHPExcel_Chart_DataSeries::GROUPING_STANDARD,
            range(0, count($phpExcelDataSeriesValues) - 1),
            $phpExcelDataSeriesLabels,
            $phpExcelXAxisTickValue,
            $phpExcelDataSeriesValues
        );
        $series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_BAR);

        $plotArea   = new PHPExcel_Chart_PlotArea(NULL, array($series));
        $legend     = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
        $title      = new PHPExcel_Chart_Title($title);
        $yAxisLabel = new PHPExcel_Chart_Title($yAxisLabel);
        $chart      = new PHPExcel_Chart(
            'chart' . time(),
            $title,
            $legend,
            $plotArea,
            true,
            0,
            NULL,
            $yAxisLabel
        );

        $chart->setTopLeftPosition($startPosition);
        $chart->setBottomRightPosition($endPosition);

        $activeSheet = $this->phpExcel->getActiveSheet();
        $activeSheet->addChart($chart);
    }

    public function setFontSize($cells, $size) {
        if($this->phpExcel == null || $cells == null || $size == null) return;

        $this->phpExcel->getActiveSheet()->getStyle($cells)->getFont()->setSize($size);
    }

    public function setCellsFormat($cells, $cellFormat) {
        if($this->phpExcel == null || $cells == null || $cellFormat == null) return;

        $this->phpExcel->getActiveSheet()->getStyle($cells)->getNumberFormat()->setFormatCode($cellFormat);
    }

    public function getStyle($styleName) {
        switch($styleName) {
            case 'PERCENTAGE_00':
                return PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00;
        }

        return null;
    }

    public function setURL($text, $url) {
        $this->setValue($text);

        $urlStyle = array('font'  => array('color' => array('rgb' => '0088cc')));

        $this->phpExcel->getActiveSheet()->getStyle($this->cursor)->applyFromArray($urlStyle);
        $this->phpExcel->getActiveSheet()->getCell($this->cursor)->getHyperlink()->setUrl($url);
    }
}