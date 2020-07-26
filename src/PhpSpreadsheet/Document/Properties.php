<?php

namespace PhpOffice\PhpSpreadsheet\Document;

class Properties
{
    /** constants */
    const PROPERTY_TYPE_BOOLEAN = 'b';
    const PROPERTY_TYPE_INTEGER = 'i';
    const PROPERTY_TYPE_FLOAT = 'f';
    const PROPERTY_TYPE_DATE = 'd';
    const PROPERTY_TYPE_STRING = 's';
    const PROPERTY_TYPE_UNKNOWN = 'u';

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
     * @var string
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
     *
     * @return string
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set Creator.
     *
     * @param string $creator
     *
     * @return $this
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get Last Modified By.
     *
     * @return string
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set Last Modified By.
     *
     * @param string $pValue
     *
     * @return $this
     */
    public function setLastModifiedBy($pValue)
    {
        $this->lastModifiedBy = $pValue;

        return $this;
    }

    /**
     * Get Created.
     *
     * @return int
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set Created.
     *
     * @param int|string $time
     *
     * @return $this
     */
    public function setCreated($time)
    {
        if ($time === null) {
            $time = time();
        } elseif (is_string($time)) {
            if (is_numeric($time)) {
                $time = (int) $time;
            } else {
                $time = strtotime($time);
            }
        }

        $this->created = $time;

        return $this;
    }

    /**
     * Get Modified.
     *
     * @return int
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Set Modified.
     *
     * @param int|string $time
     *
     * @return $this
     */
    public function setModified($time)
    {
        if ($time === null) {
            $time = time();
        } elseif (is_string($time)) {
            if (is_numeric($time)) {
                $time = (int) $time;
            } else {
                $time = strtotime($time);
            }
        }

        $this->modified = $time;

        return $this;
    }

    /**
     * Get Title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set Title.
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get Description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set Description.
     *
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get Subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set Subject.
     *
     * @param string $subject
     *
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get Keywords.
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set Keywords.
     *
     * @param string $keywords
     *
     * @return $this
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get Category.
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set Category.
     *
     * @param string $category
     *
     * @return $this
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get Company.
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set Company.
     *
     * @param string $company
     *
     * @return $this
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get Manager.
     *
     * @return string
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Set Manager.
     *
     * @param string $manager
     *
     * @return $this
     */
    public function setManager($manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Get a List of Custom Property Names.
     *
     * @return array of string
     */
    public function getCustomProperties()
    {
        return array_keys($this->customProperties);
    }

    /**
     * Check if a Custom Property is defined.
     *
     * @param string $propertyName
     *
     * @return bool
     */
    public function isCustomPropertySet($propertyName)
    {
        return isset($this->customProperties[$propertyName]);
    }

    /**
     * Get a Custom Property Value.
     *
     * @param string $propertyName
     *
     * @return mixed
     */
    public function getCustomPropertyValue($propertyName)
    {
        if (isset($this->customProperties[$propertyName])) {
            return $this->customProperties[$propertyName]['value'];
        }
    }

    /**
     * Get a Custom Property Type.
     *
     * @param string $propertyName
     *
     * @return string
     */
    public function getCustomPropertyType($propertyName)
    {
        if (isset($this->customProperties[$propertyName])) {
            return $this->customProperties[$propertyName]['type'];
        }
    }

    /**
     * Set a Custom Property.
     *
     * @param string $propertyName
     * @param mixed $propertyValue
     * @param string $propertyType
     *      'i'    : Integer
     *   'f' : Floating Point
     *   's' : String
     *   'd' : Date/Time
     *   'b' : Boolean
     *
     * @return $this
     */
    public function setCustomProperty($propertyName, $propertyValue = '', $propertyType = null)
    {
        if (
            ($propertyType === null) || (!in_array($propertyType, [self::PROPERTY_TYPE_INTEGER,
                self::PROPERTY_TYPE_FLOAT,
                self::PROPERTY_TYPE_STRING,
                self::PROPERTY_TYPE_DATE,
                self::PROPERTY_TYPE_BOOLEAN,
            ]))
        ) {
            if ($propertyValue === null) {
                $propertyType = self::PROPERTY_TYPE_STRING;
            } elseif (is_float($propertyValue)) {
                $propertyType = self::PROPERTY_TYPE_FLOAT;
            } elseif (is_int($propertyValue)) {
                $propertyType = self::PROPERTY_TYPE_INTEGER;
            } elseif (is_bool($propertyValue)) {
                $propertyType = self::PROPERTY_TYPE_BOOLEAN;
            } else {
                $propertyType = self::PROPERTY_TYPE_STRING;
            }
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

    public static function convertProperty($propertyValue, $propertyType)
    {
        switch ($propertyType) {
            case 'empty':     //    Empty
                return '';

                break;
            case 'null':      //    Null
                return null;

                break;
            case 'i1':        //    1-Byte Signed Integer
            case 'i2':        //    2-Byte Signed Integer
            case 'i4':        //    4-Byte Signed Integer
            case 'i8':        //    8-Byte Signed Integer
            case 'int':       //    Integer
                return (int) $propertyValue;

                break;
            case 'ui1':       //    1-Byte Unsigned Integer
            case 'ui2':       //    2-Byte Unsigned Integer
            case 'ui4':       //    4-Byte Unsigned Integer
            case 'ui8':       //    8-Byte Unsigned Integer
            case 'uint':      //    Unsigned Integer
                return abs((int) $propertyValue);

                break;
            case 'r4':        //    4-Byte Real Number
            case 'r8':        //    8-Byte Real Number
            case 'decimal':   //    Decimal
                return (float) $propertyValue;

                break;
            case 'lpstr':     //    LPSTR
            case 'lpwstr':    //    LPWSTR
            case 'bstr':      //    Basic String
                return $propertyValue;

                break;
            case 'date':      //    Date and Time
            case 'filetime':  //    File Time
                return strtotime($propertyValue);

                break;
            case 'bool':     //    Boolean
                return $propertyValue == 'true';

                break;
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

                break;
        }

        return $propertyValue;
    }

    public static function convertPropertyType($propertyType)
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

                break;
            case 'r4':       //    4-Byte Real Number
            case 'r8':       //    8-Byte Real Number
            case 'decimal':  //    Decimal
                return self::PROPERTY_TYPE_FLOAT;

                break;
            case 'empty':    //    Empty
            case 'null':     //    Null
            case 'lpstr':    //    LPSTR
            case 'lpwstr':   //    LPWSTR
            case 'bstr':     //    Basic String
                return self::PROPERTY_TYPE_STRING;

                break;
            case 'date':     //    Date and Time
            case 'filetime': //    File Time
                return self::PROPERTY_TYPE_DATE;

                break;
            case 'bool':     //    Boolean
                return self::PROPERTY_TYPE_BOOLEAN;

                break;
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

                break;
        }

        return self::PROPERTY_TYPE_UNKNOWN;
    }
}
