<?php

namespace PhpOffice\PhpSpreadsheetTests\Document;

use DateTime;
use DateTimeZone;
use PhpOffice\PhpSpreadsheet\Document\Properties;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class PropertiesTest extends TestCase
{
    /**
     * @var Properties
     */
    private $properties;

    /** @var float */
    private $startTime;

    protected function setup(): void
    {
        do {
            // loop to avoid rare situation where timestamp changes
            $this->startTime = (float) (new DateTime())->format('U');
            $this->properties = new Properties();
            $endTime = (float) (new DateTime())->format('U');
        } while ($this->startTime !== $endTime);
    }

    public function testNewInstance(): void
    {
        self::assertSame('Unknown Creator', $this->properties->getCreator());
        self::assertSame('Unknown Creator', $this->properties->getLastModifiedBy());
        self::assertSame('Untitled Spreadsheet', $this->properties->getTitle());
        self::assertSame('', $this->properties->getCompany());
        self::assertEquals($this->startTime, $this->properties->getCreated());
        self::assertEquals($this->startTime, $this->properties->getModified());
    }

    public function testSetCreator(): void
    {
        $creator = 'Mark Baker';

        $this->properties->setCreator($creator);
        self::assertSame($creator, $this->properties->getCreator());
    }

    /**
     * @dataProvider providerCreationTime
     *
     * @param mixed $expectedCreationTime
     * @param mixed $created
     */
    public function testSetCreated($expectedCreationTime, $created): void
    {
        $expectedCreationTime = $expectedCreationTime ?? $this->startTime;

        $this->properties->setCreated($created);
        self::assertEquals($expectedCreationTime, $this->properties->getCreated());
    }

    public static function providerCreationTime(): array
    {
        return [
            [null, null],
            [1615980600, 1615980600],
            [1615980600, '1615980600'],
            [1615980600, '2021-03-17 11:30:00Z'],
        ];
    }

    public function testSetModifier(): void
    {
        $creator = 'Mark Baker';

        $this->properties->setLastModifiedBy($creator);
        self::assertSame($creator, $this->properties->getLastModifiedBy());
    }

    /**
     * @dataProvider providerModifiedTime
     *
     * @param mixed $expectedModifiedTime
     * @param mixed $modified
     */
    public function testSetModified($expectedModifiedTime, $modified): void
    {
        $expectedModifiedTime = $expectedModifiedTime ?? $this->startTime;

        $this->properties->setModified($modified);
        self::assertEquals($expectedModifiedTime, $this->properties->getModified());
    }

    public static function providerModifiedTime(): array
    {
        return [
            [null, null],
            [1615980600, 1615980600],
            [1615980600, '1615980600'],
            [1615980600, '2021-03-17 11:30:00Z'],
        ];
    }

    public function testSetTitle(): void
    {
        $title = 'My spreadsheet title test';

        $this->properties->setTitle($title);
        self::assertSame($title, $this->properties->getTitle());
    }

    public function testSetDescription(): void
    {
        $description = 'A test for spreadsheet description';

        $this->properties->setDescription($description);
        self::assertSame($description, $this->properties->getDescription());
    }

    public function testSetSubject(): void
    {
        $subject = 'Test spreadsheet';

        $this->properties->setSubject($subject);
        self::assertSame($subject, $this->properties->getSubject());
    }

    public function testSetKeywords(): void
    {
        $keywords = 'Test PHPSpreadsheet Spreadsheet Excel LibreOffice Gnumeric OpenSpreadsheetML OASIS';

        $this->properties->setKeywords($keywords);
        self::assertSame($keywords, $this->properties->getKeywords());
    }

    public function testSetCategory(): void
    {
        $category = 'Testing';

        $this->properties->setCategory($category);
        self::assertSame($category, $this->properties->getCategory());
    }

    public function testSetCompany(): void
    {
        $company = 'PHPOffice Suite';

        $this->properties->setCompany($company);
        self::assertSame($company, $this->properties->getCompany());
    }

    public function testSetManager(): void
    {
        $manager = 'Mark Baker';

        $this->properties->setManager($manager);
        self::assertSame($manager, $this->properties->getManager());
    }

    /**
     * @dataProvider providerCustomProperties
     *
     * @param mixed $expectedType
     * @param mixed $expectedValue
     * @param string $propertyName
     * @param mixed $propertyValue
     * @param ?string $propertyType
     */
    public function testSetCustomProperties($expectedType, $expectedValue, $propertyName, $propertyValue, $propertyType = null): void
    {
        if ($propertyType === null) {
            $this->properties->setCustomProperty($propertyName, $propertyValue);
        } else {
            $this->properties->setCustomProperty($propertyName, $propertyValue, $propertyType);
        }
        self::assertTrue($this->properties->isCustomPropertySet($propertyName));
        self::assertSame($expectedType, $this->properties->getCustomPropertyType($propertyName));
        $result = $this->properties->getCustomPropertyValue($propertyName);
        if ($expectedType === Properties::PROPERTY_TYPE_DATE) {
            $result = Date::formattedDateTimeFromTimestamp("$result", 'Y-m-d', new DateTimeZone('UTC'));
        }
        self::assertSame($expectedValue, $result);
    }

    public static function providerCustomProperties(): array
    {
        return [
            [Properties::PROPERTY_TYPE_STRING, null, 'Editor', null],
            [Properties::PROPERTY_TYPE_STRING, 'Mark Baker', 'Editor', 'Mark Baker'],
            [Properties::PROPERTY_TYPE_FLOAT, 1.17, 'Version', 1.17],
            [Properties::PROPERTY_TYPE_INTEGER, 2, 'Revision', 2],
            [Properties::PROPERTY_TYPE_BOOLEAN, true, 'Tested', true],
            [Properties::PROPERTY_TYPE_DATE, '2021-03-17', 'Test Date', '2021-03-17', Properties::PROPERTY_TYPE_DATE],
        ];
    }

    public function testGetUnknownCustomProperties(): void
    {
        $propertyName = 'I DONT EXIST';

        self::assertFalse($this->properties->isCustomPropertySet($propertyName));
        self::assertNull($this->properties->getCustomPropertyValue($propertyName));
        self::assertNull($this->properties->getCustomPropertyType($propertyName));
    }
}
