<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Chart\BoxWhisker;
use PhpOffice\PhpSpreadsheet\Chart\BoxWhiskerSeries;
use PhpOffice\PhpSpreadsheet\Chart\Chart as SpreadsheetChart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Namespaces;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;

class ChartEx extends WriterPart
{
    /** @var array<string, string> */
    private array $definedNames = [];

    /**
     * Write a ChartEx chart to XML format.
     */
    public function writeChart(SpreadsheetChart $chart, bool $calculateCellValues = true): string
    {
        $chartEx = $chart->getChartEx();

        if (!$chartEx instanceof BoxWhisker) {
            throw new WriterException('Unsupported ChartEx type.');
        }

        if ($calculateCellValues) {
            $chart->refresh();
        }

        $this->definedNames = ChartExDefinedNames::getDefinedNames(
            $this->getParentWriter()->getSpreadsheet()
        );

        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(
                XMLWriter::STORAGE_DISK,
                $this->getParentWriter()->getDiskCachingDirectory()
            );
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        $objWriter->startElement('cx:chartSpace');
        $objWriter->writeAttribute('xmlns:a', Namespaces::DRAWINGML);
        $objWriter->writeAttribute('xmlns:r', Namespaces::SCHEMA_OFFICE_DOCUMENT);
        $objWriter->writeAttribute('xmlns:cx', Namespaces::CHART_EX);

        $this->writeChartData($objWriter, $chartEx);
        $this->writeChartContents($objWriter, $chart, $chartEx);

        $objWriter->endElement();

        return $objWriter->getData();
    }

    private function writeChartData(XMLWriter $objWriter, BoxWhisker $chartEx): void
    {
        $objWriter->startElement('cx:chartData');

        foreach ($chartEx->getSeries() as $index => $series) {
            $objWriter->startElement('cx:data');
            $objWriter->writeAttribute('id', (string) $index);

            $this->writeNumericDimension($objWriter, $series->getValues());

            $objWriter->endElement();
        }

        $objWriter->endElement();
    }

    private function writeNumericDimension(XMLWriter $objWriter, DataSeriesValues $values): void
    {
        $objWriter->startElement('cx:numDim');
        $objWriter->writeAttribute('type', 'val');

        $definedName = $this->getDefinedName($values);

        if ($definedName !== null) {
            $objWriter->writeElement('cx:f', $definedName);
        }

        $objWriter->endElement();
    }

    private function getDefinedName(DataSeriesValues $values): ?string
    {
        $dataSource = $values->getDataSource();

        if ($dataSource !== null) {
            $definedName = array_search($dataSource, $this->definedNames, true);

            if ($definedName !== false) {
                return (string) $definedName;
            }
        }

        return null;
    }

    private function writeChartContents(XMLWriter $objWriter, SpreadsheetChart $chart, BoxWhisker $chartEx): void
    {
        $objWriter->startElement('cx:chart');

        $this->writeTitle($objWriter, $chart);

        $objWriter->startElement('cx:plotArea');
        $objWriter->startElement('cx:plotAreaRegion');

        foreach ($chartEx->getSeries() as $index => $series) {
            $this->writeSeries($objWriter, $series, $chartEx, $index);
        }

        $objWriter->endElement();
        $this->writeCategoryAxis($objWriter);
        $this->writeValueAxis($objWriter);

        $objWriter->endElement();
        $objWriter->endElement();
    }

    private function writeSeries(XMLWriter $objWriter, BoxWhiskerSeries $series, BoxWhisker $chartEx, int $index): void
    {
        $objWriter->startElement('cx:series');
        $objWriter->writeAttribute('layoutId', BoxWhisker::TYPE);
        $objWriter->writeAttribute(
            'uniqueId',
            sprintf('{0000000%d-3A4D-46F4-9B7E-0F209ED1F2E2}', $index)
        );

        $label = $series->getLabel() ?? $series->getCategories();

        $objWriter->startElement('cx:tx');
        $objWriter->startElement('cx:txData');

        $definedName = $this->getDefinedName($label);

        if ($definedName !== null) {
            $objWriter->writeElement('cx:f', $definedName);
        }

        $labelValue = $label->getDataValue();

        if (is_scalar($labelValue)) {
            $objWriter->writeElement('cx:v', (string) $labelValue);
        }

        $objWriter->endElement();
        $objWriter->endElement();

        $objWriter->startElement('cx:dataId');
        $objWriter->writeAttribute('val', (string) $index);
        $objWriter->endElement();

        $objWriter->startElement('cx:layoutPr');

        $objWriter->startElement('cx:visibility');
        $objWriter->writeAttribute(
            'meanLine',
            (string) (int) $chartEx->getShowMeanLine()
        );
        $objWriter->writeAttribute(
            'meanMarker',
            (string) (int) $chartEx->getShowMeanMarker()
        );
        $objWriter->writeAttribute(
            'nonoutliers',
            (string) (int) $chartEx->getShowInnerPoints()
        );
        $objWriter->writeAttribute(
            'outliers',
            (string) (int) $chartEx->getShowOutliers()
        );
        $objWriter->endElement();

        $objWriter->startElement('cx:statistics');
        $objWriter->writeAttribute(
            'quartileMethod',
            $chartEx->getQuartileMethod()
        );
        $objWriter->endElement();

        $objWriter->endElement();
        $objWriter->endElement();
    }

    private function writeTitle(XMLWriter $objWriter, SpreadsheetChart $chart): void
    {
        $title = $chart->getTitle();

        if ($title !== null) {
            $objWriter->startElement('cx:title');
            $objWriter->startElement('cx:tx');
            $objWriter->startElement('cx:txData');
            $objWriter->writeElement('cx:v', $title->getCaptionText());
            $objWriter->endElement();
            $objWriter->endElement();
            $objWriter->endElement();
        }
    }

    private function writeCategoryAxis(XMLWriter $objWriter): void
    {
        $objWriter->startElement('cx:axis');
        $objWriter->writeAttribute('id', '0');

        $objWriter->startElement('cx:catScaling');
        $objWriter->writeAttribute('gapWidth', '1');
        $objWriter->endElement();

        $objWriter->startElement('cx:tickLabels');
        $objWriter->endElement();

        $objWriter->endElement();
    }

    private function writeValueAxis(XMLWriter $objWriter): void
    {
        $objWriter->startElement('cx:axis');
        $objWriter->writeAttribute('id', '1');

        $objWriter->startElement('cx:valScaling');
        $objWriter->endElement();

        $objWriter->startElement('cx:majorGridlines');
        $objWriter->endElement();

        $objWriter->startElement('cx:tickLabels');
        $objWriter->endElement();

        $objWriter->endElement();
    }
}
