<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use DateTimeZone;
use PhpOffice\PhpSpreadsheet\Document\Properties;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class PropertiesTest extends AbstractFunctional
{
    public function testLoadXlsxWorkbookProperties(): void
    {
        $customPropertySet = [
            'Publisher' => ['type' => Properties::PROPERTY_TYPE_STRING, 'value' => 'PHPOffice Suite'],
            'Tested' => ['type' => Properties::PROPERTY_TYPE_BOOLEAN, 'value' => true],
            'Counter' => ['type' => Properties::PROPERTY_TYPE_INTEGER, 'value' => 15],
            'Rate' => ['type' => Properties::PROPERTY_TYPE_FLOAT, 'value' => 1.15],
            'Refactor Date' => ['type' => Properties::PROPERTY_TYPE_DATE, 'value' => '2019-06-10'],
        ];

        $filename = 'tests/data/Reader/XLSX/propertyTest.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $properties = $spreadsheet->getProperties();
        // Core Properties
        self::assertSame('Mark Baker', $properties->getCreator());
        self::assertSame('Unit Testing', $properties->getTitle());
        self::assertSame('Property Test', $properties->getSubject());

        // Extended Properties
        self::assertSame('PHPOffice', $properties->getCompany());
        self::assertSame('The Big Boss', $properties->getManager());

        // Custom Properties
        $customProperties = $properties->getCustomProperties();
        self::assertIsArray($customProperties);
        $customProperties = array_flip($customProperties);
        self::assertArrayHasKey('Publisher', $customProperties);

        foreach ($customPropertySet as $propertyName => $testData) {
            self::assertTrue($properties->isCustomPropertySet($propertyName));
            self::assertSame($testData['type'], $properties->getCustomPropertyType($propertyName));
            $result = $properties->getCustomPropertyValue($propertyName);
            if ($properties->getCustomPropertyType($propertyName) == Properties::PROPERTY_TYPE_DATE) {
                $result = Date::formattedDateTimeFromTimestamp("$result", 'Y-m-d', new DateTimeZone('UTC'));
            }
            self::assertSame($testData['value'], $result);
        }
    }

    public function testReloadXlsxWorkbookProperties(): void
    {
        $customPropertySet = [
            'Publisher' => ['type' => Properties::PROPERTY_TYPE_STRING, 'value' => 'PHPOffice Suite'],
            'Tested' => ['type' => Properties::PROPERTY_TYPE_BOOLEAN, 'value' => true],
            'Counter' => ['type' => Properties::PROPERTY_TYPE_INTEGER, 'value' => 15],
            'Rate' => ['type' => Properties::PROPERTY_TYPE_FLOAT, 'value' => 1.15],
            'Refactor Date' => ['type' => Properties::PROPERTY_TYPE_DATE, 'value' => '2019-06-10'],
        ];

        $filename = 'tests/data/Reader/XLSX/propertyTest.xlsx';
        $reader = new Xlsx();
        $spreadsheetOld = $reader->load($filename);
        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Xlsx');

        $properties = $spreadsheet->getProperties();
        // Core Properties
        self::assertSame('Mark Baker', $properties->getCreator());
        self::assertSame('Unit Testing', $properties->getTitle());
        self::assertSame('Property Test', $properties->getSubject());

        // Extended Properties
        self::assertSame('PHPOffice', $properties->getCompany());
        self::assertSame('The Big Boss', $properties->getManager());

        // Custom Properties
        $customProperties = $properties->getCustomProperties();
        self::assertIsArray($customProperties);
        $customProperties = array_flip($customProperties);
        self::assertArrayHasKey('Publisher', $customProperties);

        foreach ($customPropertySet as $propertyName => $testData) {
            self::assertTrue($properties->isCustomPropertySet($propertyName));
            self::assertSame($testData['type'], $properties->getCustomPropertyType($propertyName));
            $result = $properties->getCustomPropertyValue($propertyName);
            if ($properties->getCustomPropertyType($propertyName) == Properties::PROPERTY_TYPE_DATE) {
                $result = Date::formattedDateTimeFromTimestamp("$result", 'Y-m-d', new DateTimeZone('UTC'));
            }
            self::assertSame($testData['value'], $result);
        }
    }
}
