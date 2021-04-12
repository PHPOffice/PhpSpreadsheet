<?php

namespace PhpOffice\PhpSpreadsheetTests\Document;

use PhpOffice\PhpSpreadsheet\Document\Properties;
use PHPUnit\Framework\TestCase;

class PropertiesTest extends TestCase
{
    /**
     * @var Properties
     */
    private $properties;

    protected function setup(): void
    {
        $this->properties = new Properties();
    }

    public function testNewInstance(): void
    {
        $createdTime = $modifiedTime = time();
        self::assertSame('Unknown Creator', $this->properties->getCreator());
        self::assertSame('Unknown Creator', $this->properties->getLastModifiedBy());
        self::assertSame('Untitled Spreadsheet', $this->properties->getTitle());
        self::assertSame('Microsoft Corporation', $this->properties->getCompany());
        self::assertSame($createdTime, $this->properties->getCreated());
        self::assertSame($modifiedTime, $this->properties->getModified());
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
        $expectedCreationTime = $expectedCreationTime ?? time();

        $this->properties->setCreated($created);
        self::assertSame($expectedCreationTime, $this->properties->getCreated());
    }

    public function providerCreationTime(): array
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
        $expectedModifiedTime = $expectedModifiedTime ?? time();

        $this->properties->setModified($modified);
        self::assertSame($expectedModifiedTime, $this->properties->getModified());
    }

    public function providerModifiedTime(): array
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
     * @param mixed $propertyName
     */
    public function testSetCustomProperties($expectedType, $expectedValue, $propertyName, ...$args): void
    {
        $this->properties->setCustomProperty($propertyName, ...$args);
        self::assertTrue($this->properties->isCustomPropertySet($propertyName));
        self::assertSame($expectedValue, $this->properties->getCustomPropertyValue($propertyName));
        self::assertSame($expectedType, $this->properties->getCustomPropertyType($propertyName));
    }

    public function providerCustomProperties(): array
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
