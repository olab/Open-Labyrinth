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
 * Class phpSpreadsheet Report implementation
 * 04/2019 updated to successor PHPspreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 */

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Chart;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Report_Impl_PHPSpreadsheet extends Report_Impl {
    private $phpSpreadsheet;
    private $cursor;
    private $phpSpreadsheetProperties;

    /**
     * Default constructor
     */
    public function __construct() {
        $this->phpSpreadsheet = new Spreadsheet();
        $this->cursor = 'A1';
        $this->phpSpreadsheetProperties = $this->phpSpreadsheet->getProperties();
    }

    public function setCreator($creator) {
        if($this->phpSpreadsheetProperties == null) return;

        $this->phpSpreadsheetProperties->setCreator($creator);
    }

    public function setLastModifiedBy($modifiedBy) {
        if($this->phpSpreadsheetProperties == null) return;

        $this->phpSpreadsheetProperties->setLastModifiedBy($modifiedBy);
    }

    public function setTitle($title) {
        if($this->phpSpreadsheetProperties == null) return;

        $this->phpSpreadsheetProperties->setTitle($title);
    }

    public function setSubject($subject) {
        if($this->phpSpreadsheetProperties == null) return;

        $this->phpSpreadsheetProperties->setSubject($subject);
    }

    public function setDescription($description) {
        if($this->phpSpreadsheetProperties == null) return;

        $this->phpSpreadsheetProperties->setDescription($description);
    }

    public function setKeywords($keywords) {
        if($this->phpSpreadsheetProperties == null) return;

        $this->phpSpreadsheetProperties->setKeywords($keywords);
    }

    public function setCategory($category) {
        if($this->phpSpreadsheetProperties == null) return;

        $this->phpSpreadsheetProperties->setCategory($category);
    }

    public function setCursor($cursor) {
        if($this->cursor == null || empty($cursor)) return;

        $this->cursor = $cursor;
    }

    public function setValue($value) {
        if($this->phpSpreadsheet == null) return;

        $activeSheet = $this->phpSpreadsheet->getActiveSheet();
        if($activeSheet == null) return;

        $activeSheet->setCellValue($this->cursor, $value);
    }

    public function setAutoWidth($index) {
        if($this->phpSpreadsheet == null || $index == null) return;

        $this->phpSpreadsheet->getActiveSheet()->getColumnDimension($index)->setAutoSize(true);
    }

    public function setActiveSheet($index) {
        if($this->phpSpreadsheet == null || $index == null || $index <= 0) return;

        $this->phpSpreadsheet->setActiveSheetIndex($index);
    }

    public function download($name, $save_to_file = false) {
        $this->phpSpreadsheet->setActiveSheetIndex(0);

        $objWriter = IOFactory::createWriter($this->phpSpreadsheet, 'Xlsx');
        $objWriter->setIncludeCharts(TRUE);
        if (!$save_to_file) {
            $objWriter->save('php://output');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $name . '.xlsx"');
            header('Cache-Control: max-age=0');
            die();
        } else {
            $objWriter->save($_SERVER['DOCUMENT_ROOT'] . '/tmp/' . $name . '.xlsx');
        }
    }

    public function addStackedBarChart($startPosition, $endPosition, $dataSeriesLabels, $xAxisTickValue, $dataSeriesValues, $title, $yAxisLabel) {
        if($this->phpSpreadsheet   == null                                  ||
           $startPosition    == null                                  ||
           $endPosition      == null                                  ||
           $dataSeriesLabels == null || count($dataSeriesLabels) <= 0 ||
           $xAxisTickValue   == null || count($xAxisTickValue)   <= 0 ||
           $dataSeriesValues == null || count($dataSeriesValues) <= 0) {
            return;
        }

        $phpSpreadsheetDataSeriesLabels = array();
        foreach($dataSeriesLabels as $label) {
            $phpSpreadsheetDataSeriesLabels[] = new Chart\DataSeriesValues('String', $label, null, 1);
        }

        $phpSpreadsheetXAxisTickValue = array();
        foreach($xAxisTickValue as $value) {
            $phpSpreadsheetXAxisTickValue[] = new Chart\DataSeriesValues('String', $value['values'], null, $value['count']);
        }

        $phpSpreadsheetDataSeriesValues = array();
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

            $phpSpreadsheetDataSeriesValues[] = new Chart\DataSeriesValues('Number', $value, null, $count);
        }

        $series = new Chart\DataSeries(
            Chart\DataSeries::TYPE_BARCHART,
            Chart\DataSeries::GROUPING_STACKED,
            range(0, count($phpSpreadsheetDataSeriesValues) - 1),
            $phpSpreadsheetDataSeriesLabels,
            $phpSpreadsheetXAxisTickValue,
            $phpSpreadsheetDataSeriesValues
        );
        $series->setPlotDirection(Chart\DataSeries::DIRECTION_BAR);

        $plotArea   = new PlotArea(NULL, array($series));
        $legend     = new Legend(Legend::POSITION_RIGHT, NULL, false);
        $title      = new Title($title);
        $yAxisLabel = new Title($yAxisLabel);
        $chart      = new Chart(
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

        $activeSheet = $this->phpSpreadsheet->getActiveSheet();
        $activeSheet->addChart($chart);
    }

    public function addHorizontalBarChart($startPosition, $endPosition, $dataSeriesLabels, $xAxisTickValue, $dataSeriesValues, $title, $yAxisLabel) {
        if($this->phpSpreadsheet   == null                                  ||
            $startPosition    == null                                  ||
            $endPosition      == null                                  ||
            $dataSeriesLabels == null || count($dataSeriesLabels) <= 0 ||
            $xAxisTickValue   == null || count($xAxisTickValue)   <= 0 ||
            $dataSeriesValues == null || count($dataSeriesValues) <= 0) {
            return;
        }

        $phpSpreadsheetDataSeriesLabels = array();
        foreach($dataSeriesLabels as $label) {
            $phpSpreadsheetDataSeriesLabels[] = new Chart\DataSeriesValues('String', $label['values'], null, 1);
        }

        $phpSpreadsheetXAxisTickValue = array();
        foreach($xAxisTickValue as $value) {
            $phpSpreadsheetXAxisTickValue[] = new Chart\DataSeriesValues('String', $value['values'], null, $value['count']);
        }

        $phpSpreadsheetDataSeriesValues = array();
        foreach($dataSeriesValues as $value) {
            $phpSpreadsheetDataSeriesValues[] = new Chart\DataSeriesValues('Number', $value['values'], null, $value['count']);
        }

        $series = new Chart\DataSeries(
            Chart\DataSeries::TYPE_BARCHART,
            Chart\DataSeries::GROUPING_STANDARD,
            range(0, count($phpSpreadsheetDataSeriesValues) - 1),
            $phpSpreadsheetDataSeriesLabels,
            $phpSpreadsheetXAxisTickValue,
            $phpSpreadsheetDataSeriesValues
        );
        $series->setPlotDirection(Chart\DataSeries::DIRECTION_BAR);

        $plotArea   = new PlotArea(NULL, array($series));
        $legend     = new Legend(Legend::POSITION_RIGHT, NULL, false);
        $title      = new Title($title);
        $yAxisLabel = new Title($yAxisLabel);
        $chart      = new Chart(
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

        $activeSheet = $this->phpSpreadsheet->getActiveSheet();
        $activeSheet->addChart($chart);
    }

    public function addColumnBarChart($startPosition, $endPosition, $dataSeriesLabels, $xAxisValue, $dataSeriesValues, $title, $yAxisLabel) {
        if($this->phpSpreadsheet    == null                                  ||
            $startPosition    == null                                  ||
            $endPosition      == null                                  ||
            $xAxisValue       == null || count($xAxisValue)       <= 0 ||
            $dataSeriesValues == null || count($dataSeriesValues) <= 0) {
            return;
        }

        $phpSpreadsheetDataSeriesLabels = array();
        if($dataSeriesLabels != null && count($dataSeriesLabels) > 0) {
            foreach($dataSeriesLabels as $label) {
                $phpSpreadsheetDataSeriesLabels[] = new Chart\DataSeriesValues($label['type'], $label['source'], $label['format'], $label['count']);
            }
        }

        $phpSpreadsheetXAxisTickValue = array();
        foreach($xAxisValue as $value) {
            $phpSpreadsheetXAxisTickValue[] = new Chart\DataSeriesValues($value['type'], $value['source'], $value['format'], $value['count']);
        }

        $phpSpreadsheetDataSeriesValues = array();
        foreach($dataSeriesValues as $value) {
            $phpSpreadsheetDataSeriesValues[] = new Chart\DataSeriesValues($value['type'], $value['source'], $value['format'], $value['count']);
        }

        $series = new Chart\DataSeries(
            Chart\DataSeries::TYPE_BARCHART,
            Chart\DataSeries::GROUPING_STANDARD,
            range(0, count($phpSpreadsheetDataSeriesValues) - 1),
            $phpSpreadsheetDataSeriesLabels,
            $phpSpreadsheetXAxisTickValue,
            $phpSpreadsheetDataSeriesValues
        );
        $series->setPlotDirection(Chart\DataSeries::DIRECTION_COL);

        $plotArea   = new Chart\PlotArea(NULL, array($series));
        $legend     = new Chart\Legend(Chart\Legend::POSITION_RIGHT, NULL, false);
        $title      = new Chart\Title($title);
        $yAxisLabel = new Chart\Title($yAxisLabel);
        $chart      = new Chart\Chart(
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

        $activeSheet = $this->phpSpreadsheet->getActiveSheet();
        $activeSheet->addChart($chart);
    }

    public function addLineChart($startPosition, $endPosition, $dataSeriesLabels, $xAxisValue, $dataSeriesValues, $title, $yAxisLabel) {
        if($this->phpSpreadsheet    == null                                  ||
            $startPosition    == null                                  ||
            $endPosition      == null                                  ||
            $xAxisValue       == null || count($xAxisValue)       <= 0 ||
            $dataSeriesValues == null || count($dataSeriesValues) <= 0) {
            return;
        }

        $phpSpreadsheetDataSeriesLabels = array();
        if($dataSeriesLabels != null && count($dataSeriesLabels) > 0) {
            foreach($dataSeriesLabels as $label) {
                $phpSpreadsheetDataSeriesLabels[] = new Chart\DataSeriesValues($label['type'], $label['source'], $label['format'], $label['count']);
            }
        }

        $phpSpreadsheetXAxisTickValue = array();
        foreach($xAxisValue as $value) {
            $phpSpreadsheetXAxisTickValue[] = new Chart\DataSeriesValues($value['type'], $value['source'], $value['format'], $value['count']);
        }

        $phpSpreadsheetDataSeriesValues = array();
        foreach($dataSeriesValues as $value) {
            $phpSpreadsheetDataSeriesValues[] = new Chart\DataSeriesValues($value['type'], $value['source'], $value['format'], $value['count']);
        }

        $series = new Chart\DataSeries(
            Chart\DataSeries::TYPE_LINECHART,
            Chart\DataSeries::GROUPING_STACKED,
            range(0, count($phpSpreadsheetDataSeriesValues) - 1),
            $phpSpreadsheetDataSeriesLabels,
            $phpSpreadsheetXAxisTickValue,
            $phpSpreadsheetDataSeriesValues
        );
        $series->setPlotDirection(Chart\DataSeries::DIRECTION_COL);

        $plotArea   = new Chart\PlotArea(NULL, array($series));
        $legend     = new Chart\Legend(Chart\Legend::POSITION_RIGHT, NULL, false);
        $title      = new Chart\Title($title);
        $yAxisLabel = new Chart\Title($yAxisLabel);
        $chart      = new Chart(
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

        $activeSheet = $this->phpSpreadsheet->getActiveSheet();
        $activeSheet->addChart($chart);
    }

    public function setFontSize($cells, $size) {
        if($this->phpSpreadsheet == null || $cells == null || $size == null) return;

        $this->phpSpreadsheet->getActiveSheet()->getStyle($cells)->getFont()->setSize($size);
    }

    public function setCellsFormat($cells, $cellFormat) {
        if($this->phpSpreadsheet == null || $cells == null || $cellFormat == null) return;

        $this->phpSpreadsheet->getActiveSheet()->getStyle($cells)->getNumberFormat()->setFormatCode($cellFormat);
    }

    public function getStyle($styleName) {
        switch($styleName) {
            case 'PERCENTAGE_00':
                return PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00;
        }

        return null;
    }

    public function setURL($text, $url) {
        $this->setValue($text);

        $urlStyle = array('font'  => array('color' => array('rgb' => '0088cc')));

        $this->phpSpreadsheet->getActiveSheet()->getStyle($this->cursor)->applyFromArray($urlStyle);
        $this->phpSpreadsheet->getActiveSheet()->getCell($this->cursor)->getHyperlink()->setUrl($url);
    }
}