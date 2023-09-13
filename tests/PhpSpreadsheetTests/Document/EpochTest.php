<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Document;

use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class EpochTest extends AbstractFunctional
{
    public static function providerFormats(): array
    {
        return [
            ['Ods', '1921-03-17 11:30:00Z'],
            ['Ods', '2021-03-17 11:30:00Z'],
            ['Ods', '2041-03-17 11:30:00Z'],
            ['Xls', '1921-03-17 11:30:00Z'],
            ['Xls', '2021-03-17 11:30:00Z'],
            ['Xls', '2041-03-17 11:30:00Z'],
            ['Xlsx', '1921-03-17 11:30:00Z'],
            ['Xlsx', '2021-03-17 11:30:00Z'],
            ['Xlsx', '2041-03-17 11:30:00Z'],
        ];
    }

    /**
     * @dataProvider providerFormats
     */
    public function testSetCreated(string $format, string $timestamp): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(1);
        $spreadsheet->getProperties()->setCreated($timestamp);
        $timestamp2 = preg_replace('/1-/', '2-', $timestamp);
        self::AssertNotEquals($timestamp, $timestamp2);
        $spreadsheet->getProperties()->setModified($timestamp2);
        $timestamp3 = preg_replace('/1-/', '3-', $timestamp);
        self::AssertNotEquals($timestamp, $timestamp3);
        self::AssertNotEquals($timestamp2, $timestamp3);
        $spreadsheet->getProperties()->setCustomProperty('cprop', $timestamp3, 'd');
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);
        $created = $reloadedSpreadsheet->getProperties()->getCreated();
        $dt1 = DateTime::createFromFormat('U', "$created");
        $modified = $reloadedSpreadsheet->getProperties()->getModified();
        $dt2 = DateTime::createFromFormat('U', "$modified");
        if ($dt1 === false || $dt2 === false) {
            self::fail('Invalid timestamp for created or modified');
        } else {
            self::assertSame($timestamp, $dt1->format('Y-m-d H:i:s') . 'Z');
            self::assertSame($timestamp2, $dt2->format('Y-m-d H:i:s') . 'Z');
        }
        if ($format === 'Xlsx' || $format === 'Ods') {
            // No custom property support in Xls
            $cprop = $reloadedSpreadsheet->getProperties()->getCustomPropertyValue('cprop');
            if (!is_numeric($cprop)) {
                self::fail('Cannot find custom property');
            } else {
                $dt3 = DateTime::createFromFormat('U', "$cprop");
                if ($dt3 === false) {
                    self::fail('Invalid timestamp for custom property');
                } else {
                    self::assertSame($timestamp3, $dt3->format('Y-m-d H:i:s') . 'Z');
                }
            }
        }
    }

    public static function providerFormats2(): array
    {
        return [
            ['Ods'],
            ['Xls'],
            ['Xlsx'],
        ];
    }

    /**
     * @dataProvider providerFormats2
     */
    public function testConsistentTimeStamp(string $format): void
    {
        $pgmstart = (float) (new DateTime())->format('U');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(1);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);
        $pgmend = (float) (new DateTime())->format('U');
        self::assertLessThanOrEqual($pgmend, $pgmstart);
        $created = $reloadedSpreadsheet->getProperties()->getCreated();
        $modified = $reloadedSpreadsheet->getProperties()->getModified();
        self::assertLessThanOrEqual($pgmend, $created);
        self::assertLessThanOrEqual($pgmend, $modified);
        self::assertLessThanOrEqual($created, $pgmstart);
        self::assertLessThanOrEqual($modified, $pgmstart);
    }
}
