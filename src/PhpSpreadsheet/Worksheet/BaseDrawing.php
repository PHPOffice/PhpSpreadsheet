<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\IComparable;

class BaseDrawing implements IComparable
{
    /**
     * Image counter.
     *
     * @var int
     */
    private static $imageCounter = 0;

    /**
     * Image index.
     *
     * @var int
     */
    private $imageIndex = 0;

    /**
     * Name.
     *
     * @var string
     */
    protected $name;

    /**
     * Description.
     *
     * @var string
     */
    protected $description;

    /**
     * Worksheet.
     *
     * @var Worksheet
     */
    protected $worksheet;

    /**
     * Coordinates.
     *
     * @var string
     */
    protected $coordinates;

    /**
     * Offset X.
     *
     * @var int
     */
    protected $offsetX;

    /**
     * Offset Y.
     *
     * @var int
     */
    protected $offsetY;

    /**
     * Width.
     *
     * @var int
     */
    protected $width;

    /**
     * Height.
     *
     * @var int
     */
    protected $height;

    /**
     * Proportional resize.
     *
     * @var bool
     */
    protected $resizeProportional;

    /**
     * Rotation.
     *
     * @var int
     */
    protected $rotation;

    /**
     * Shadow.
     *
     * @var Drawing\Shadow
     */
    protected $shadow;

    /**
     * Image hyperlink.
     *
     * @var null|Hyperlink
     */
    private $hyperlink;

    /**
     * Create a new BaseDrawing.
     */
    public function __construct()
    {
        // Initialise values
        $this->name = '';
        $this->description = '';
        $this->worksheet = null;
        $this->coordinates = 'A1';
        $this->offsetX = 0;
        $this->offsetY = 0;
        $this->width = 0;
        $this->height = 0;
        $this->resizeProportional = true;
        $this->rotation = 0;
        $this->shadow = new Drawing\Shadow();

        // Set image index
        ++self::$imageCounter;
        $this->imageIndex = self::$imageCounter;
    }

    /**
     * Get image index.
     *
     * @return int
     */
    public function getImageIndex()
    {
        return $this->imageIndex;
    }

    /**
     * Get Name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Name.
     *
     * @param string $pValue
     *
     * @return BaseDrawing
     */
    public function setName($pValue)
    {
        $this->name = $pValue;

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
     * @return BaseDrawing
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get Worksheet.
     *
     * @return Worksheet
     */
    public function getWorksheet()
    {
        return $this->worksheet;
    }

    /**
     * Set Worksheet.
     *
     * @param Worksheet $pValue
     * @param bool $pOverrideOld If a Worksheet has already been assigned, overwrite it and remove image from old Worksheet?
     *
     * @throws PhpSpreadsheetException
     *
     * @return BaseDrawing
     */
    public function setWorksheet(Worksheet $pValue = null, $pOverrideOld = false)
    {
        if ($this->worksheet === null) {
            // Add drawing to \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
            $this->worksheet = $pValue;
            $this->worksheet->getCell($this->coordinates);
            $this->worksheet->getDrawingCollection()->append($this);
        } else {
            if ($pOverrideOld) {
                // Remove drawing from old \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
                $iterator = $this->worksheet->getDrawingCollection()->getIterator();

                while ($iterator->valid()) {
                    if ($iterator->current()->getHashCode() == $this->getHashCode()) {
                        $this->worksheet->getDrawingCollection()->offsetUnset($iterator->key());
                        $this->worksheet = null;

                        break;
                    }
                }

                // Set new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
                $this->setWorksheet($pValue);
            } else {
                throw new PhpSpreadsheetException('A Worksheet has already been assigned. Drawings can only exist on one \\PhpOffice\\PhpSpreadsheet\\Worksheet.');
            }
        }

        return $this;
    }

    /**
     * Get Coordinates.
     *
     * @return string
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * Set Coordinates.
     *
     * @param string $pValue eg: 'A1'
     *
     * @return BaseDrawing
     */
    public function setCoordinates($pValue)
    {
        $this->coordinates = $pValue;

        return $this;
    }

    /**
     * Get OffsetX.
     *
     * @return int
     */
    public function getOffsetX()
    {
        return $this->offsetX;
    }

    /**
     * Set OffsetX.
     *
     * @param int $pValue
     *
     * @return BaseDrawing
     */
    public function setOffsetX($pValue)
    {
        $this->offsetX = $pValue;

        return $this;
    }

    /**
     * Get OffsetY.
     *
     * @return int
     */
    public function getOffsetY()
    {
        return $this->offsetY;
    }

    /**
     * Set OffsetY.
     *
     * @param int $pValue
     *
     * @return BaseDrawing
     */
    public function setOffsetY($pValue)
    {
        $this->offsetY = $pValue;

        return $this;
    }

    /**
     * Get Width.
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set Width.
     *
     * @param int $pValue
     *
     * @return BaseDrawing
     */
    public function setWidth($pValue)
    {
        // Resize proportional?
        if ($this->resizeProportional && $pValue != 0) {
            $ratio = $this->height / ($this->width != 0 ? $this->width : 1);
            $this->height = round($ratio * $pValue);
        }

        // Set width
        $this->width = $pValue;

        return $this;
    }

    /**
     * Get Height.
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set Height.
     *
     * @param int $pValue
     *
     * @return BaseDrawing
     */
    public function setHeight($pValue)
    {
        // Resize proportional?
        if ($this->resizeProportional && $pValue != 0) {
            $ratio = $this->width / ($this->height != 0 ? $this->height : 1);
            $this->width = round($ratio * $pValue);
        }

        // Set height
        $this->height = $pValue;

        return $this;
    }

    /**
     * Set width and height with proportional resize.
     *
     * Example:
     * <code>
     * $objDrawing->setResizeProportional(true);
     * $objDrawing->setWidthAndHeight(160,120);
     * </code>
     *
     * @author Vincent@luo MSN:kele_100@hotmail.com
     *
     * @param int $width
     * @param int $height
     *
     * @return BaseDrawing
     */
    public function setWidthAndHeight($width, $height)
    {
        $xratio = $width / ($this->width != 0 ? $this->width : 1);
        $yratio = $height / ($this->height != 0 ? $this->height : 1);
        if ($this->resizeProportional && !($width == 0 || $height == 0)) {
            if (($xratio * $this->height) < $height) {
                $this->height = ceil($xratio * $this->height);
                $this->width = $width;
            } else {
                $this->width = ceil($yratio * $this->width);
                $this->height = $height;
            }
        } else {
            $this->width = $width;
            $this->height = $height;
        }

        return $this;
    }

    /**
     * Get ResizeProportional.
     *
     * @return bool
     */
    public function getResizeProportional()
    {
        return $this->resizeProportional;
    }

    /**
     * Set ResizeProportional.
     *
     * @param bool $pValue
     *
     * @return BaseDrawing
     */
    public function setResizeProportional($pValue)
    {
        $this->resizeProportional = $pValue;

        return $this;
    }

    /**
     * Get Rotation.
     *
     * @return int
     */
    public function getRotation()
    {
        return $this->rotation;
    }

    /**
     * Set Rotation.
     *
     * @param int $pValue
     *
     * @return BaseDrawing
     */
    public function setRotation($pValue)
    {
        $this->rotation = $pValue;

        return $this;
    }

    /**
     * Get Shadow.
     *
     * @return Drawing\Shadow
     */
    public function getShadow()
    {
        return $this->shadow;
    }

    /**
     * Set Shadow.
     *
     * @param Drawing\Shadow $pValue
     *
     * @return BaseDrawing
     */
    public function setShadow(Drawing\Shadow $pValue = null)
    {
        $this->shadow = $pValue;

        return $this;
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode()
    {
        return md5(
            $this->name .
            $this->description .
            $this->worksheet->getHashCode() .
            $this->coordinates .
            $this->offsetX .
            $this->offsetY .
            $this->width .
            $this->height .
            $this->rotation .
            $this->shadow->getHashCode() .
            __CLASS__
        );
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if ($key == 'worksheet') {
                $this->worksheet = null;
            } elseif (is_object($value)) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }

    /**
     * @param null|Hyperlink $pHyperlink
     */
    public function setHyperlink(Hyperlink $pHyperlink = null)
    {
        $this->hyperlink = $pHyperlink;
    }

    /**
     * @return null|Hyperlink
     */
    public function getHyperlink()
    {
        return $this->hyperlink;
    }
}
