<?php

namespace PhpOffice\PhpSpreadsheet\Document;

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
     *
     * @var string
     */
    private $creator = 'Unknown Creator';

    /**
     * LastModifiedBy.
     *
     * @var string
     */
    private $lastModifiedBy;

    /**
     * Created.
     *
     * @var int
     */
    private $created;

    /**
     * Modified.
     *
     * @var int
     */
    private $modified;

    /**
     * Title.
     *
     * @var string
     */
    private $title = 'Untitled Spreadsheet';

    /**
     * Description.
     *
     * @var string
     */
    private $description = '';

    /**
     * Subject.
     *
     * @var string
     */
    private $subject = '';

    /**
     * Keywords.
     *
     * @var string
     */
    private $keywords = '';

    /**
     * Category.
     *
     * @var string
     */
    private $category = '';

    /**
     * Manager.
     *
     * @var string
     */
    private $manager = '';

    /**
     * Company.
     *
     * @var string
     */
    private $company = 'Microsoft Corporation';

    /**
     * Custom Properties.
     *
     * @var array{value: mixed, type: string}[]
     */
    private $customProperties = [];

    /**
     * Create a new Document Properties instance.
     */
    public function __construct()
    {
        // Initialise values
        $this->lastModifiedBy = $this->creator;
        $this->created = time();
        $this->modified = time();
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
    public function setLastModifiedBy(string $modifier): self
    {
        $this->lastModifiedBy = $modifier;

        return $this;
    }

    /**
     * Get Created.
     */
    public function getCreated(): int
    {
        return $this->created;
    }

    /**
     * Set Created.
     *
     * @param null|int|string $timestamp
     *
     * @return $this
     */
    public function setCreated($timestamp): self
    {
        if ($timestamp === null) {
            $timestamp = time();
        } elseif (is_string($timestamp)) {
            if (is_numeric($timestamp)) {
                $timestamp = (int) $timestamp;
            } else {
                $timestamp = strtotime($timestamp);
            }
        }

        $this->created = $timestamp;

        return $this;
    }

    /**
     * Get Modified.
     */
    public function getModified(): int
    {
        return $this->modified;
    }

    /**
     * Set Modified.
     *
     * @param null|int|string $timestamp
     *
     * @return $this
     */
    public function setModified($timestamp): self
    {
        if ($timestamp === null) {
            $timestamp = time();
        } elseif (is_string($timestamp)) {
            if (is_numeric($timestamp)) {
                $timestamp = (int) $timestamp;
            } else {
                $timestamp = strtotime($timestamp);
            }
        }

        $this->modified = $timestamp;

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
     *
     * @return mixed
     */
    public function getCustomPropertyValue(string $propertyName)
    {
        if (isset($this->customProperties[$propertyName])) {
            return $this->customProperties[$propertyName]['value'];
        }
    }

    /**
     * Get a Custom Property Type.
     *
     * @return null|string
     */
    public function getCustomPropertyType(string $propertyName)
    {
        return $this->customProperties[$propertyName]['type'] ?? null;
    }

    private function identifyPropertyType($propertyValue)
    {
        if ($propertyValue === null) {
            return self::PROPERTY_TYPE_STRING;
        } elseif (is_float($propertyValue)) {
            return self::PROPERTY_TYPE_FLOAT;
        } elseif (is_int($propertyValue)) {
            return self::PROPERTY_TYPE_INTEGER;
        } elseif (is_bool($propertyValue)) {
            return self::PROPERTY_TYPE_BOOLEAN;
        }

        return self::PROPERTY_TYPE_STRING;
    }

    /**
     * Set a Custom Property.
     *
     * @param mixed $propertyValue
     * @param string $propertyType
     *   'i' : Integer
     *   'f' : Floating Point
     *   's' : String
     *   'd' : Date/Time
     *   'b' : Boolean
     *
     * @return $this
     */
    public function setCustomProperty(string $propertyName, $propertyValue = '', $propertyType = null): self
    {
        if (($propertyType === null) || (!in_array($propertyType, self::VALID_PROPERTY_TYPE_LIST))) {
            $propertyType = $this->identifyPropertyType($propertyValue);
        }

        $this->customProperties[$propertyName] = [
            'value' => $propertyValue,
            'type' => $propertyType,
        ];

        return $this;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }

    public static function convertProperty($propertyValue, string $propertyType)
    {
        switch ($propertyType) {
            case 'empty':     //    Empty
                return '';
            case 'null':      //    Null
                return null;
            case 'i1':        //    1-Byte Signed Integer
            case 'i2':        //    2-Byte Signed Integer
            case 'i4':        //    4-Byte Signed Integer
            case 'i8':        //    8-Byte Signed Integer
            case 'int':       //    Integer
                return (int) $propertyValue;
            case 'ui1':       //    1-Byte Unsigned Integer
            case 'ui2':       //    2-Byte Unsigned Integer
            case 'ui4':       //    4-Byte Unsigned Integer
            case 'ui8':       //    8-Byte Unsigned Integer
            case 'uint':      //    Unsigned Integer
                return abs((int) $propertyValue);
            case 'r4':        //    4-Byte Real Number
            case 'r8':        //    8-Byte Real Number
            case 'decimal':   //    Decimal
                return (float) $propertyValue;
            case 'lpstr':     //    LPSTR
            case 'lpwstr':    //    LPWSTR
            case 'bstr':      //    Basic String
                return $propertyValue;
            case 'date':      //    Date and Time
            case 'filetime':  //    File Time
                return strtotime($propertyValue);
            case 'bool':     //    Boolean
                return $propertyValue == 'true';
            case 'cy':       //    Currency
            case 'error':    //    Error Status Code
            case 'vector':   //    Vector
            case 'array':    //    Array
            case 'blob':     //    Binary Blob
            case 'oblob':    //    Binary Blob Object
            case 'stream':   //    Binary Stream
            case 'ostream':  //    Binary Stream Object
            case 'storage':  //    Binary Storage
            case 'ostorage': //    Binary Storage Object
            case 'vstream':  //    Binary Versioned Stream
            case 'clsid':    //    Class ID
            case 'cf':       //    Clipboard Data
                return $propertyValue;
        }

        return $propertyValue;
    }

    public static function convertPropertyType(string $propertyType): string
    {
        switch ($propertyType) {
            case 'i1':       //    1-Byte Signed Integer
            case 'i2':       //    2-Byte Signed Integer
            case 'i4':       //    4-Byte Signed Integer
            case 'i8':       //    8-Byte Signed Integer
            case 'int':      //    Integer
            case 'ui1':      //    1-Byte Unsigned Integer
            case 'ui2':      //    2-Byte Unsigned Integer
            case 'ui4':      //    4-Byte Unsigned Integer
            case 'ui8':      //    8-Byte Unsigned Integer
            case 'uint':     //    Unsigned Integer
                return self::PROPERTY_TYPE_INTEGER;
            case 'r4':       //    4-Byte Real Number
            case 'r8':       //    8-Byte Real Number
            case 'decimal':  //    Decimal
                return self::PROPERTY_TYPE_FLOAT;
            case 'empty':    //    Empty
            case 'null':     //    Null
            case 'lpstr':    //    LPSTR
            case 'lpwstr':   //    LPWSTR
            case 'bstr':     //    Basic String
                return self::PROPERTY_TYPE_STRING;
            case 'date':     //    Date and Time
            case 'filetime': //    File Time
                return self::PROPERTY_TYPE_DATE;
            case 'bool':     //    Boolean
                return self::PROPERTY_TYPE_BOOLEAN;
            case 'cy':       //    Currency
            case 'error':    //    Error Status Code
            case 'vector':   //    Vector
            case 'array':    //    Array
            case 'blob':     //    Binary Blob
            case 'oblob':    //    Binary Blob Object
            case 'stream':   //    Binary Stream
            case 'ostream':  //    Binary Stream Object
            case 'storage':  //    Binary Storage
            case 'ostorage': //    Binary Storage Object
            case 'vstream':  //    Binary Versioned Stream
            case 'clsid':    //    Class ID
            case 'cf':       //    Clipboard Data
                return self::PROPERTY_TYPE_UNKNOWN;
        }

        return self::PROPERTY_TYPE_UNKNOWN;
    }
}
