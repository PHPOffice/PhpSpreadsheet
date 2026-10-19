<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Chart\BoxWhisker;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ChartExDefinedNames
{
    /**
     * Get the ChartEx defined names required by the workbook.
     *
     * @return array<string, string>
     */
    public static function getDefinedNames(Spreadsheet $spreadsheet): array
    {
        $definedNames = [];
        $nextIndex = 0;

        foreach ($spreadsheet->getAllSheets() as $worksheet) {
            foreach ($worksheet->getChartCollection() as $chart) {
                $chartEx = $chart->getChartEx();

                if (!$chartEx instanceof BoxWhisker) {
                    continue;
                }

                foreach ($chartEx->getSeries() as $series) {
                    self::addDefinedName($definedNames, $series->getCategories(), $nextIndex);
                    self::addDefinedName($definedNames, $series->getLabel(), $nextIndex);
                    self::addDefinedName($definedNames, $series->getValues(), $nextIndex);
                }
            }
        }

        return $definedNames;
    }

    /**
     * Add a data source to the ChartEx defined names.
     *
     * @param array<string, string> $definedNames
     */
    private static function addDefinedName(array &$definedNames, ?DataSeriesValues $values, int &$nextIndex): void
    {
        if ($values === null || $values->getDataSource() === null) {
            return;
        }

        $dataSource = $values->getDataSource();

        if (in_array($dataSource, $definedNames, true)) {
            return;
        }

        $definedNames['_xlchart.v1.' . $nextIndex] = $dataSource;
        ++$nextIndex;
    }
}
