<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use DateTimeZone;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class Issue2331Test extends TestCase
{
    public function testIssue2331(): void
    {
        // Leading space instead of 0 in month and/or day of timestamp.
        $filename = 'tests/data/Reader/XLSX/issue.2331c.xlsx';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $properties = $spreadsheet->getProperties();
        $created = (string) $properties->getCreated();
        $modified = (string) $properties->getModified();

        self::assertEquals('2021-08-02', Date::formattedDateTimeFromTimestamp($created, 'Y-m-d', new DateTimeZone('UTC')));
        self::assertEquals('2021-09-03', Date::formattedDateTimeFromTimestamp($modified, 'Y-m-d', new DateTimeZone('UTC')));
        $spreadsheet->disconnectWorksheets();
    }
}
