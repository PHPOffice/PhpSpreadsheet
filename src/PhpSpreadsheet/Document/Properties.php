<?php

namespace PhpOffice\PhpSpreadsheet\Document;

use DateTime;
use PhpOffice\PhpSpreadsheet\Shared\IntOrFloat;

class Properties
{
    /** constants */
    public const PROPERTY_TYPE_BOOLEAN = 'b';
    public const PROPERTY_TYPE_INTEGER = 'i';
    public const PROPERTY_TYPE_FLOAT = 'f';
    public const PROPERTY_TYPE_DATE = 'd';
    public const PROPERTY_TYPE_STRING = 's';
    public const PROPERTY_TYPE_UNKNOWN = 'u';

    private const VALID_PROPERTY_TYPE_LIST = [
        self::PROPERTY_TYPE_BOOLEAN,
        self::PROPERTY_TYPE_INTEGER,
        self::PROPERTY_TYPE_FLOAT,
        self::PROPERTY_TYPE_DATE,
        self::PROPERTY_TYPE_STRING,
    ];

    /**
     * Creator.
     */
    private string $creator = 'Unknown Creator';

    /**
     * LastModifiedBy.
     */
    private string $lastModifiedBy;

    /**
     * Created.
     */
    private float|int $created;

    /**
     * Modified.
     */
    private float|int $modified;

    /**
     * Title.
     */
    private string $title = 'Untitled Spreadsheet';

    /**
     * Description.
     */
    private string $description = '';

    /**
     * Subject.
     */
    private string $subject = '';

    /**
     * Keywords.
     */
    private string $keywords = '';

    /**
     * Category.
     */
    private string $category = '';

    /**
     * Manager.
     */
    private string $manager = '';

    /**
     * Company.
     */
    private string $company = '';

    /**
     * Custom Properties.
     *
     * @var array{value: null|bool|float|int|string, type: string}[]
     */
    private array $customProperties = [];

    private string $hyperlinkBase = '';

    private string $viewport = '';

    /**
     * Create a new Document Properties instance.
     */
    public function __construct()
    {
        // Initialise values
        $this->lastModifiedBy = $this->creator;
        $this->created = self::intOrFloatTimestamp(null);
        $this->modified = $this->created;
    }

    /**
     * Get Creator.
     */
    public function getCreator(): string
    {
        return $this->creator;
    }

    /**
     * Set Creator.
     *
     * @return $this
     */
    public function setCreator(string $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get Last Modified By.
     */
    public function getLastModifiedBy(): string
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set Last Modified By.
     *
     * @return $this
     */
    public function setLastModifiedBy(string $modifiedBy): self
    {
        $this->lastModifiedBy = $modifiedBy;

        return $this;
    }

    private static function intOrFloatTimestamp(null|float|int|string $timestamp): float|int
    {
        if ($timestamp === null) {
            $timestamp = (float) (new DateTime())->format('U');
        } elseif (is_string($timestamp)) {
            if (is_numeric($timestamp)) {
                $timestamp = (float) $timestamp;
            } else {
                $timestamp = (string) preg_replace('/[.][0-9]*$/', '', $timestamp);
                $timestamp = (string) preg_replace('/^(\\d{4})- (\\d)/', '$1-0$2', $timestamp);
                $timestamp = (string) preg_replace('/^(\\d{4}-\\d{2})- (\\d)/', '$1-0$2', $timestamp);
                $timestamp = (float) (new DateTime($timestamp))->format('U');
            }
        }

        return IntOrFloat::evaluate($timestamp);
    }

    /**
     * Get Created.
     */
    public function getCreated(): float|int
    {
        return $this->created;
    }

    /**
     * Set Created.
     *
     * @return $this
     */
    public function setCreated(null|float|int|string $timestamp): self
    {
        $this->created = self::intOrFloatTimestamp($timestamp);

        return $this;
    }

    /**
     * Get Modified.
     */
    public function getModified(): float|int
    {
        return $this->modified;
    }

    /**
     * Set Modified.
     *
     * @return $this
     */
    public function setModified(null|float|int|string $timestamp): self
    {
        $this->modified = self::intOrFloatTimestamp($timestamp);

        return $this;
    }

    /**
     * Get Title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set Title.
     *
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get Description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set Description.
     *
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get Subject.
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * Set Subject.
     *
     * @return $this
     */
    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get Keywords.
     */
    public function getKeywords(): string
    {
        return $this->keywords;
    }

    /**
     * Set Keywords.
     *
     * @return $this
     */
    public function setKeywords(string $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get Category.
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Set Category.
     *
     * @return $this
     */
    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get Company.
     */
    public function getCompany(): string
    {
        return $this->company;
    }

    /**
     * Set Company.
     *
     * @return $this
     */
    public function setCompany(string $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get Manager.
     */
    public function getManager(): string
    {
        return $this->manager;
    }

    /**
     * Set Manager.
     *
     * @return $this
     */
    public function setManager(string $manager): self
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Get a List of Custom Property Names.
     *
     * @return string[]
     */
    public function getCustomProperties(): array
    {
        return array_keys($this->customProperties);
    }

    /**
     * Check if a Custom Property is defined.
     */
    public function isCustomPropertySet(string $propertyName): bool
    {
        return array_key_exists($propertyName, $this->customProperties);
    }

    /**
     * Get a Custom Property Value.
     */
    public function getCustomPropertyValue(string $propertyName): bool|int|float|string|null
    {
        if (isset($this->customProperties[$propertyName])) {
            return $this->customProperties[$propertyName]['value'];
        }

        return null;
    }

    /**
     * Get a Custom Property Type.
     */
    public function getCustomPropertyType(string $propertyName): ?string
    {
        return $this->customProperties[$propertyName]['type'] ?? null;
    }

    private function identifyPropertyType(bool|int|float|string|null $propertyValue): string
    {
        if (is_float($propertyValue)) {
            return self::PROPERTY_TYPE_FLOAT;
        }
        if (is_int($propertyValue)) {
            return self::PROPERTY_TYPE_INTEGER;
        }
        if (is_bool($propertyValue)) {
            return self::PROPERTY_TYPE_BOOLEAN;
        }

        return self::PROPERTY_TYPE_STRING;
    }

    /**
     * Set a Custom Property.
     *
     * @param ?string $propertyType see `self::VALID_PROPERTY_TYPE_LIST`
     *
     * @return $this
     */
    public function setCustomProperty(string $propertyName, bool|int|float|string|null $propertyValue = '', ?string $propertyType = null): self
    {
        if (($propertyType === null) || (!in_array($propertyType, self::VALID_PROPERTY_TYPE_LIST))) {
            $propertyType = $this->identifyPropertyType($propertyValue);
        }

        $this->customProperties[$propertyName] = [
            'value' => self::convertProperty($propertyValue, $propertyType),
            'type' => $propertyType,
        ];

        return $this;
    }

    private const PROPERTY_TYPE_ARRAY = [
        'i' => self::PROPERTY_TYPE_INTEGER,      //    Integer
        'i1' => self::PROPERTY_TYPE_INTEGER,     //    1-Byte Signed Integer
        'i2' => self::PROPERTY_TYPE_INTEGER,     //    2-Byte Signed Integer
        'i4' => self::PROPERTY_TYPE_INTEGER,     //    4-Byte Signed Integer
        'i8' => self::PROPERTY_TYPE_INTEGER,     //    8-Byte Signed Integer
        'int' => self::PROPERTY_TYPE_INTEGER,    //    Integer
        'ui1' => self::PROPERTY_TYPE_INTEGER,    //    1-Byte Unsigned Integer
        'ui2' => self::PROPERTY_TYPE_INTEGER,    //    2-Byte Unsigned Integer
        'ui4' => self::PROPERTY_TYPE_INTEGER,    //    4-Byte Unsigned Integer
        'ui8' => self::PROPERTY_TYPE_INTEGER,    //    8-Byte Unsigned Integer
        'uint' => self::PROPERTY_TYPE_INTEGER,   //    Unsigned Integer
        'f' => self::PROPERTY_TYPE_FLOAT,        //    Real Number
        'r4' => self::PROPERTY_TYPE_FLOAT,       //    4-Byte Real Number
        'r8' => self::PROPERTY_TYPE_FLOAT,       //    8-Byte Real Number
        'decimal' => self::PROPERTY_TYPE_FLOAT,  //    Decimal
        's' => self::PROPERTY_TYPE_STRING,       //    String
        'empty' => self::PROPERTY_TYPE_STRING,   //    Empty
        'null' => self::PROPERTY_TYPE_STRING,    //    Null
        'lpstr' => self::PROPERTY_TYPE_STRING,   //    LPSTR
        'lpwstr' => self::PROPERTY_TYPE_STRING,  //    LPWSTR
        'bstr' => self::PROPERTY_TYPE_STRING,    //    Basic String
        'd' => self::PROPERTY_TYPE_DATE,         //    Date and Time
        'date' => self::PROPERTY_TYPE_DATE,      //    Date and Time
        'filetime' => self::PROPERTY_TYPE_DATE,  //    File Time
        'b' => self::PROPERTY_TYPE_BOOLEAN,      //    Boolean
        'bool' => self::PROPERTY_TYPE_BOOLEAN,   //    Boolean
    ];

    private const SPECIAL_TYPES = [
        'empty' => '',
        'null' => null,
    ];

    /**
     * Convert property to form desired by Excel.
     */
    public static function convertProperty(bool|int|float|string|null $propertyValue, string $propertyType): bool|int|float|string|null
    {
        return self::SPECIAL_TYPES[$propertyType] ?? self::convertProperty2($propertyValue, $propertyType);
    }

    /**
     * Convert property to form desired by Excel.
     */
    private static function convertProperty2(bool|int|float|string|null $propertyValue, string $type): bool|int|float|string|null
    {
        $propertyType = self::convertPropertyType($type);
        switch ($propertyType) {
            case self::PROPERTY_TYPE_INTEGER:
                $intValue = (int) $propertyValue;

                return ($type[0] === 'u') ? abs($intValue) : $intValue;
            case self::PROPERTY_TYPE_FLOAT:
                return (float) $propertyValue;
            case self::PROPERTY_TYPE_DATE:
                return self::intOrFloatTimestamp($propertyValue); // @phpstan-ignore-line
            case self::PROPERTY_TYPE_BOOLEAN:
                return is_bool($propertyValue) ? $propertyValue : ($propertyValue === 'true');
            default: // includes string
                return $propertyValue;
        }
    }

    public static function convertPropertyType(string $propertyType): string
    {
        return self::PROPERTY_TYPE_ARRAY[$propertyType] ?? self::PROPERTY_TYPE_UNKNOWN;
    }

    public function getHyperlinkBase(): string
    {
        return $this->hyperlinkBase;
    }

    public function setHyperlinkBase(string $hyperlinkBase): self
    {
        $this->hyperlinkBase = $hyperlinkBase;

        return $this;
    }

    public function getViewport(): string
    {
        return $this->viewport;
    }

    public const SUGGESTED_VIEWPORT = 'width=device-width, initial-scale=1';

    public function setViewport(string $viewport): self
    {
        $this->viewport = $viewport;

        return $this;
    }
}
