<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

/**
 * Copyright (c) 2006 - 2016 PhpSpreadsheet.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category   PhpSpreadsheet
 *
 * @copyright  Copyright (c) 2006 - 2016 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */
class BaseDrawing implements \PhpOffice\PhpSpreadsheet\IComparable
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
     * @var \PhpOffice\PhpSpreadsheet\Worksheet
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

        // Set image index++self::$imageCounter;
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
    public function setName($pValue = '')
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
     * @param string $pValue
     *
     * @return BaseDrawing
     */
    public function setDescription($pValue = '')
    {
        $this->description = $pValue;

        return $this;
    }

    /**
     * Get Worksheet.
     *
     * @return \PhpOffice\PhpSpreadsheet\Worksheet
     */
    public function getWorksheet()
    {
        return $this->worksheet;
    }

    /**
     * Set Worksheet.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet $pValue
     * @param bool $pOverrideOld If a Worksheet has already been assigned, overwrite it and remove image from old Worksheet?
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     *
     * @return BaseDrawing
     */
    public function setWorksheet(\PhpOffice\PhpSpreadsheet\Worksheet $pValue = null, $pOverrideOld = false)
    {
        if (is_null($this->worksheet)) {
            // Add drawing to \PhpOffice\PhpSpreadsheet\Worksheet
            $this->worksheet = $pValue;
            $this->worksheet->getCell($this->coordinates);
            $this->worksheet->getDrawingCollection()->append($this);
        } else {
            if ($pOverrideOld) {
                // Remove drawing from old \PhpOffice\PhpSpreadsheet\Worksheet
                $iterator = $this->worksheet->getDrawingCollection()->getIterator();

                while ($iterator->valid()) {
                    if ($iterator->current()->getHashCode() == $this->getHashCode()) {
                        $this->worksheet->getDrawingCollection()->offsetUnset($iterator->key());
                        $this->worksheet = null;
                        break;
                    }
                }

                // Set new \PhpOffice\PhpSpreadsheet\Worksheet
                $this->setWorksheet($pValue);
            } else {
                throw new \PhpOffice\PhpSpreadsheet\Exception("A \PhpOffice\PhpSpreadsheet\Worksheet has already been assigned. Drawings can only exist on one \PhpOffice\PhpSpreadsheet\Worksheet.");
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
     * @param string $pValue
     *
     * @return BaseDrawing
     */
    public function setCoordinates($pValue = 'A1')
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
    public function setOffsetX($pValue = 0)
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
    public function setOffsetY($pValue = 0)
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
    public function setWidth($pValue = 0)
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
    public function setHeight($pValue = 0)
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
     * Set width and height with proportional resize
     * Example:
     * <code>
     * $objDrawing->setResizeProportional(true);
     * $objDrawing->setWidthAndHeight(160,120);
     * </code>.
     *
     * @author Vincent@luo MSN:kele_100@hotmail.com
     *
     * @param int $width
     * @param int $height
     *
     * @return BaseDrawing
     */
    public function setWidthAndHeight($width = 0, $height = 0)
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
    public function setResizeProportional($pValue = true)
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
    public function setRotation($pValue = 0)
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
     * @throws \PhpOffice\PhpSpreadsheet\Exception
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
            if (is_object($value)) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }
}
