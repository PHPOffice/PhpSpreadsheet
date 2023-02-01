<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Document\Properties;
use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class OdsPropertiesTest extends AbstractFunctional
{
    /**
     * @var string
     */
    private $timeZone;

    protected function setUp(): void
    {
        $this->timeZone = date_default_timezone_get();
        date_default_timezone_set('UTC');
    }

    protected function tearDown(): void
    {
        date_default_timezone_set($this->timeZone);
    }

    public function testLoadOdsWorkbookProperties(): void
    {
        $customPropertySet = [
            'Owner' => ['type' => Properties::PROPERTY_TYPE_STRING, 'value' => 'PHPOffice'],
            'Tested' => ['type' => Properties::PROPERTY_TYPE_BOOLEAN, 'value' => true],
            'Counter' => ['type' => Properties::PROPERTY_TYPE_FLOAT, 'value' => 10.0],
            'TestDate' => ['type' => Properties::PROPERTY_TYPE_DATE, 'value' => '2019-06-30'],
            'HereAndNow' => ['type' => Properties::PROPERTY_TYPE_DATE, 'value' => '2019-06-30'],
        ];

        $filename = 'tests/data/Reader/Ods/propertyTest.ods';
        $reader = new Ods();
        $spreadsheet = $reader->load($filename);

        $properties = $spreadsheet->getProperties();
        // Core Properties
//        self::assertSame('Mark Baker', $properties->getCreator());
        self::assertSame('Property Test File', $properties->getTitle());
        self::assertSame('Testing for Properties', $properties->getSubject());
        self::assertSame('TEST ODS PHPSpreadsheet', $properties->getKeywords());

        // Extended Properties
//        self::assertSame('PHPOffice', $properties->getCompany());
//        self::assertSame('The Big Boss', $properties->getManager());

        // Custom Properties
        $customProperties = $properties->getCustomProperties();
        self::assertIsArray($customProperties);
        $customProperties = array_flip($customProperties);
        self::assertArrayHasKey('TestDate', $customProperties);

        foreach ($customPropertySet as $propertyName => $testData) {
            self::assertTrue($properties->isCustomPropertySet($propertyName));
            self::assertSame($testData['type'], $properties->getCustomPropertyType($propertyName));
            $result = $properties->getCustomPropertyValue($propertyName);
            if ($properties->getCustomPropertyType($propertyName) == Properties::PROPERTY_TYPE_DATE) {
                $result = Date::formattedDateTimeFromTimestamp("$result", 'Y-m-d');
            }
            self::assertSame($testData['value'], $result);
        }
        $spreadsheet->disconnectWorksheets();
    }

    public function testReloadOdsWorkbookProperties(): void
    {
        $customPropertySet = [
            'Owner' => ['type' => Properties::PROPERTY_TYPE_STRING, 'value' => 'PHPOffice'],
            'Tested' => ['type' => Properties::PROPERTY_TYPE_BOOLEAN, 'value' => true],
            'Counter' => ['type' => Properties::PROPERTY_TYPE_FLOAT, 'value' => 10.0],
            'TestDate' => ['type' => Properties::PROPERTY_TYPE_DATE, 'value' => '2019-06-30'],
            'HereAndNow' => ['type' => Properties::PROPERTY_TYPE_DATE, 'value' => '2019-06-30'],
        ];

        $filename = 'tests/data/Reader/Ods/propertyTest.ods';
        $reader = new Ods();
        $spreadsheetOld = $reader->load($filename);
        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Ods');
        $spreadsheetOld->disconnectWorksheets();

        $properties = $spreadsheet->getProperties();
        // Core Properties
//        self::assertSame('Mark Baker', $properties->getCreator());
        self::assertSame('Property Test File', $properties->getTitle());
        self::assertSame('Testing for Properties', $properties->getSubject());
        self::assertSame('TEST ODS PHPSpreadsheet', $properties->getKeywords());

        // Extended Properties
//        self::assertSame('PHPOffice', $properties->getCompany());
//        self::assertSame('The Big Boss', $properties->getManager());

        // Custom Properties
        $customProperties = $properties->getCustomProperties();
        self::assertIsArray($customProperties);
        $customProperties = array_flip($customProperties);
        self::assertArrayHasKey('TestDate', $customProperties);

        foreach ($customPropertySet as $propertyName => $testData) {
            self::assertTrue($properties->isCustomPropertySet($propertyName));
            self::assertSame($testData['type'], $properties->getCustomPropertyType($propertyName));
            $result = $properties->getCustomPropertyValue($propertyName);
            if ($properties->getCustomPropertyType($propertyName) == Properties::PROPERTY_TYPE_DATE) {
                $result = Date::formattedDateTimeFromTimestamp("$result", 'Y-m-d');
            }
            self::assertSame($testData['value'], $result);
        }
        $spreadsheet->disconnectWorksheets();
    }
}
