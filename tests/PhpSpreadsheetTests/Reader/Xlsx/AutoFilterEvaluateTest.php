<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class AutoFilterEvaluateTest extends AbstractFunctional
{
    private function getVisibleSheet(Worksheet $sheet): array
    {
        $actualVisible = [];
        for ($row = 2; $row <= $sheet->getHighestRow(); ++$row) {
            if ($sheet->getRowDimension($row)->getVisible()) {
                $actualVisible[] = $row;
            }
        }

        return $actualVisible;
    }

    private function initializeSpreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('Filtered');
        for ($row = 2; $row <= 15; ++$row) {
            $sheet->getCell("A$row")->setValue($row % 5);
        }
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange('A1:A15');
        $columnFilter = $autoFilter->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()->setRule(Rule::AUTOFILTER_COLUMN_RULE_EQUAL, 1);
        $columnFilter->createRule()->setRule(Rule::AUTOFILTER_COLUMN_RULE_EQUAL, 3);
        $sheet->getCell('A16')->setValue(2); // outside of range

        return $spreadsheet;
    }

    public function testAutoFilterUnevaluated(): void
    {
        $spreadsheet = $this->initializeSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        //$sheet->getAutoFilter->showHideRows();
        $sheet->getRowDimension(6)->setVisible(false);
        //$columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        // Because showHideRows isn't executed beforehand, it will be executed
        //   at write time, causing row 6 to be visible.

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getSheet(0);
        self::assertSame([3, 6, 8, 11, 13, 16], $this->getVisibleSheet($rsheet));
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testAutoFilterEvaluated(): void
    {
        $spreadsheet = $this->initializeSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getAutoFilter()->showHideRows();
        $sheet->getRowDimension(6)->setVisible(false);
        //$columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        // Because showHideRows is executed beforehand, it won't be executed
        //   at write time, so row 6 will remain hidden.

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getSheet(0);
        self::assertSame([3, 8, 11, 13, 16], $this->getVisibleSheet($rsheet));
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testAutoFilterReevaluated(): void
    {
        $spreadsheet = $this->initializeSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getAutoFilter()->showHideRows();
        $sheet->getRowDimension(6)->setVisible(false);
        $sheet->getAutoFilter()->getColumn('A')->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        // Because filter is changed after showHideRows, it will be reevaluated
        //   at write time, so row 6 will be visible.

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getSheet(0);
        self::assertSame([3, 6, 8, 11, 13, 16], $this->getVisibleSheet($rsheet));
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testAutoFilterSetRangeMax(): void
    {
        $spreadsheet = $this->initializeSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('Filtered');
        $sheet->getAutoFilter()->setRangeToMaxRow();
        $sheet->getAutoFilter()->showHideRows();
        $sheet->getRowDimension(6)->setVisible(false);
        $sheet->getAutoFilter()->getColumn('A')->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        // Because filter is changed after showHideRows, it will be reevaluated
        //   at write time, so row 6 will be visible.

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getSheet(0);
        self::assertSame([3, 6, 8, 11, 13], $this->getVisibleSheet($rsheet));
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testAutoFilterSpreadsheetReevaluate(): void
    {
        $spreadsheet = $this->initializeSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('Filtered');
        $sheet->getAutoFilter()->showHideRows();
        $sheet->getRowDimension(6)->setVisible(false);
        $spreadsheet->reevaluateAutoFilters(false);
        // Because filter is changed after showHideRows, it will be reevaluated
        //   at write time, so row 6 will be visible.

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getSheet(0);
        self::assertSame([3, 6, 8, 11, 13, 16], $this->getVisibleSheet($rsheet));
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testAutoFilterSpreadsheetReevaluateResetMax(): void
    {
        $spreadsheet = $this->initializeSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('Filtered');
        $sheet->getAutoFilter()->showHideRows();
        $sheet->getRowDimension(6)->setVisible(false);
        $spreadsheet->reevaluateAutoFilters(true);
        // Because filter is changed after showHideRows, it will be reevaluated
        //   at write time, so row 6 will be visible.

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getSheet(0);
        self::assertSame([3, 6, 8, 11, 13], $this->getVisibleSheet($rsheet));
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
