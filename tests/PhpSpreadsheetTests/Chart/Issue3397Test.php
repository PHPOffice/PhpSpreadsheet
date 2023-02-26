<?php

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Axis;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\Framework\TestCase;

class Issue3397Test extends TestCase
{
    public function testPreliminaries(): void
    {
        //  Issue 3397, writing srgbClr for label fill in wrong place
        $spreadsheet = new Spreadsheet();
        $dataSheet = $spreadsheet->getActiveSheet();
        $dataSheet->setTitle('Summary_report');
        $label1 = 'Before 10 a.m.';
        $label2 = 'Between 10 a.m. and 2 p.m.';
        $label3 = 'After 2 p.m.';
        $dataSheet->getCell('D8')->setValue($label1);
        $dataSheet->getCell('D9')->setValue($label2);
        $dataSheet->getCell('D10')->setValue($label3);

        $dataSheet->getCell('E7')->setValue(100);
        $dataSheet->getCell('E8')->setValue(101);
        $dataSheet->getCell('E9')->setValue(102);
        $dataSheet->getCell('E10')->setValue(103);
        $dataSheet->getCell('F7')->setValue(200);
        $dataSheet->getCell('F8')->setValue(201);
        $dataSheet->getCell('F9')->setValue(202);
        $dataSheet->getCell('F10')->setValue(203);
        $dataSheet->getCell('G7')->setValue(300);
        $dataSheet->getCell('G8')->setValue(301);
        $dataSheet->getCell('G9')->setValue(302);
        $dataSheet->getCell('G10')->setValue(303);
        $dataSheet->getCell('H7')->setValue(400);
        $dataSheet->getCell('H8')->setValue(401);
        $dataSheet->getCell('H9')->setValue(402);
        $dataSheet->getCell('H10')->setValue(403);
        $dataSheet->getCell('I7')->setValue(500);
        $dataSheet->getCell('I8')->setValue(501);
        $dataSheet->getCell('I9')->setValue(502);
        $dataSheet->getCell('I10')->setValue(503);
        $dataSheet->getCell('J7')->setValue(600);
        $dataSheet->getCell('J8')->setValue(601);
        $dataSheet->getCell('J9')->setValue(602);
        $dataSheet->getCell('J10')->setValue(603);

        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Chart');

        $col = 'J';
        $colNumber = 7;

        $dataSeriesLabels = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING, // label is string
                'Summary_report!$D$8', // data source
                null, // format code
                1, // point count
                null, // label values come from data source
                null, // marker
                'ff0000' // rgb fill color
            ),
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING, // label is string
                'Summary_report!$D$9', // data source
                null, // format code
                1, // point count
                null, // label values come from data source
                null, // marker
                '70ad47' // rgb fill color
            ),
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING, // label is string
                'Summary_report!$D$10', // data source
                null, // format code
                1, // point count
                null, // label values come from data source
                null, // marker
                'ffff00' // rgb fill color
            ),
        ];

        $xAxisTickValues = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                'Summary_report!$E$7:$' . $col . '$7',
                null,
                $colNumber
            ),
        ];
        $dataSeriesValues = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                'Summary_report!$E$8:$' . $col . '$8',
                null,
                $colNumber
            ),
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                'Summary_report!$E$9:$' . $col . '$9',
                null,
                $colNumber
            ),
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                'Summary_report!$E$10:$' . $col . '$10',
                null,
                $colNumber
            ),
        ];
        $series = new DataSeries(
            DataSeries::TYPE_BARCHART, // plotType
            DataSeries::GROUPING_STACKED, // plotGrouping
            range(0, count($dataSeriesValues) - 1), // plotOrder
            $dataSeriesLabels, // plotLabel
            $xAxisTickValues, // plotCategory
            $dataSeriesValues        // plotValues
        );
        $layout1 = new Layout();
        $layout1->setShowVal(true);
        $plotArea = new PlotArea($layout1, [$series]);

        $legend = new Legend(Legend::POSITION_BOTTOM, null, false);
        $title = new Title('Submission Report');
        $yAxisLabel = new Title('Count');
        $xAxisLabel = new Title('period');
        $yaxis = new Axis();
        $yaxis->setAxisOptionsProperties('low');

        // Create the chart
        $chart = new Chart(
            'chart1', // name
            $title, // title
            $legend, // legend
            $plotArea, // plotArea
            true, // plotVisibleOnly
            'gap', // displayBlanksAs
            $xAxisLabel, // xAxisLabel
            $yAxisLabel, // yAxisLabel
            null,
            $yaxis
        );

        // Set the position where the chart should appear in the worksheet
        $chart->setTopLeftPosition('A7');
        $chart->setBottomRightPosition('H20');

        // Add the chart to the worksheet
        $sheet->addChart($chart);

        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);
        $outputFilename = File::temporaryFilename();
        $writer->save($outputFilename);
        $spreadsheet->disconnectWorksheets();

        $file = 'zip://';
        $file .= $outputFilename;
        $file .= '#xl/charts/chart1.xml';
        $data = file_get_contents($file);
        unlink($outputFilename);
        // confirm that file contains expected namespaced xml tag
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<c:tx><c:strRef><c:f>Summary_report!$D$8</c:f><c:strCache><c:ptCount val="1"/><c:pt idx="0"><c:v>Before 10 a.m.</c:v></c:pt></c:strCache></c:strRef></c:tx><c:spPr><a:solidFill><a:srgbClr val="ff0000"/></a:solidFill><a:ln/></c:spPr>', $data);
            self::assertStringContainsString('<c:tx><c:strRef><c:f>Summary_report!$D$9</c:f><c:strCache><c:ptCount val="1"/><c:pt idx="0"><c:v>Between 10 a.m. and 2 p.m.</c:v></c:pt></c:strCache></c:strRef></c:tx><c:spPr><a:solidFill><a:srgbClr val="70ad47"/></a:solidFill><a:ln/></c:spPr>', $data);
            self::assertStringContainsString('<c:tx><c:strRef><c:f>Summary_report!$D$10</c:f><c:strCache><c:ptCount val="1"/><c:pt idx="0"><c:v>After 2 p.m.</c:v></c:pt></c:strCache></c:strRef></c:tx><c:spPr><a:solidFill><a:srgbClr val="ffff00"/></a:solidFill><a:ln/></c:spPr>', $data);
        }
    }
}
