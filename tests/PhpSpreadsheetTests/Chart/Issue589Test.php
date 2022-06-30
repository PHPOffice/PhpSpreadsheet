<?php

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use DOMDocument;
use DOMNode;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as Writer;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class Issue589Test extends TestCase
{
    /**
     * Build a testable chart in a spreadsheet and set fill color for series.
     *
     * @param string|string[] $color HEX color or array with HEX colors
     */
    private function buildChartSpreadsheet($color): Spreadsheet
    {
        // Problem occurs when setting plot line color
        // The output chart xml file is missing the a:ln tag
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->fromArray(
            [
                [2010],
                [12],
                [56],
            ]
        );

        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$1', null, 1),
        ];
        $dataSeriesLabels[0]->setFillColor($color);
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$A$2:$A$3', null, 2),
        ];

        // Build the dataseries
        $series = new DataSeries(
            DataSeries::TYPE_LINECHART,
            DataSeries::GROUPING_STACKED,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            [],
            $dataSeriesValues
        );

        // Set the series in the plot area
        $plotArea = new PlotArea(null, [$series]);

        // Create the chart
        $chart = new Chart(
            'chart1',
            null,
            null,
            $plotArea
        );

        // Add the chart to the worksheet
        $worksheet->addChart($chart);

        return $spreadsheet;
    }

    public function testLineChartFill(): void
    {
        $outputFilename = File::temporaryFilename();
        $spreadsheet = $this->buildChartSpreadsheet('98B954');
        $writer = new Writer($spreadsheet);
        $writer->setIncludeCharts(true);
        $writer->save($outputFilename);

        $zip = new ZipArchive();
        $zip->open($outputFilename);
        $resultChart1Raw = $zip->getFromName('xl/charts/chart1.xml');
        $zip->close();
        unlink($outputFilename);

        $dom = new DOMDocument();
        if ($resultChart1Raw === false) {
            self::fail('Unable to open the chart file');
        } else {
            $loaded = $dom->loadXML($resultChart1Raw);
            if (!$loaded) {
                self::fail('Unable to load the chart xml');
            } else {
                $series = $dom->getElementsByTagName('ser');
                $firstSeries = $series->item(0);
                if ($firstSeries === null) {
                    self::fail('The chart XML does not contain a \'ser\' tag!');
                } else {
                    $spPrList = $firstSeries->getElementsByTagName('spPr');

                    // expect to see only one element with name 'c:spPr'
                    self::assertCount(1, $spPrList);

                    /** @var DOMNode $node */
                    $node = $spPrList->item(0); // Get the spPr element
                    $actualXml = $dom->saveXML($node);
                    if ($actualXml === false) {
                        self::fail('Failure saving the spPr element as xml string!');
                    } else {
                        self::assertXmlStringEqualsXmlString('<c:spPr><a:ln><a:solidFill><a:srgbClr val="98B954"/></a:solidFill></a:ln></c:spPr>', $actualXml);
                    }
                }
            }
        }
    }

    public function testLineChartFillIgnoresColorArray(): void
    {
        $outputFilename = File::temporaryFilename();
        $spreadsheet = $this->buildChartSpreadsheet(['98B954']);
        $writer = new Writer($spreadsheet);
        $writer->setIncludeCharts(true);
        $writer->save($outputFilename);

        $zip = new ZipArchive();
        $zip->open($outputFilename);
        $resultChart1Raw = $zip->getFromName('xl/charts/chart1.xml');
        $zip->close();
        unlink($outputFilename);

        $dom = new DOMDocument();
        if ($resultChart1Raw === false) {
            self::fail('Unable to open the chart file');
        } else {
            $loaded = $dom->loadXML($resultChart1Raw);
            if (!$loaded) {
                self::fail('Unable to load the chart xml');
            } else {
                $series = $dom->getElementsByTagName('ser');
                $firstSeries = $series->item(0);
                if ($firstSeries === null) {
                    self::fail('The chart XML does not contain a \'ser\' tag!');
                } else {
                    $spPrList = $firstSeries->getElementsByTagName('spPr');

                    // expect to see only one element with name 'c:spPr'
                    self::assertCount(1, $spPrList);

                    /** @var DOMNode $node */
                    $node = $spPrList->item(0); // Get the spPr element
                    $actualXml = $dom->saveXML($node);
                    if ($actualXml === false) {
                        self::fail('Failure saving the spPr element as xml string!');
                    } else {
                        self::assertXmlStringEqualsXmlString('<c:spPr><a:ln/></c:spPr>', $actualXml);
                    }
                }
            }
        }
    }
}
