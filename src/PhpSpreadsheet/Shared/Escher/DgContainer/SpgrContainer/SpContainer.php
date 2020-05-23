<?php

namespace PhpOffice\PhpSpreadsheet\Shared\Escher\DgContainer\SpgrContainer;

use PhpOffice\PhpSpreadsheet\Shared\Escher\DgContainer\SpgrContainer;

class SpContainer
{
    /**
     * Parent Shape Group Container.
     *
     * @var SpgrContainer
     */
    private $parent;

    /**
     * Is this a group shape?
     *
     * @var bool
     */
    private $spgr = false;

    /**
     * Shape type.
     *
     * @var int
     */
    private $spType;

    /**
     * Shape flag.
     *
     * @var int
     */
    private $spFlag;

    /**
     * Shape index (usually group shape has index 0, and the rest: 1,2,3...).
     *
     * @var int
     */
    private $spId;

    /**
     * Array of options.
     *
     * @var array
     */
    private $OPT;

    /**
     * Cell coordinates of upper-left corner of shape, e.g. 'A1'.
     *
     * @var string
     */
    private $startCoordinates;

    /**
     * Horizontal offset of upper-left corner of shape measured in 1/1024 of column width.
     *
     * @var int
     */
    private $startOffsetX;

    /**
     * Vertical offset of upper-left corner of shape measured in 1/256 of row height.
     *
     * @var int
     */
    private $startOffsetY;

    /**
     * Cell coordinates of bottom-right corner of shape, e.g. 'B2'.
     *
     * @var string
     */
    private $endCoordinates;

    /**
     * Horizontal offset of bottom-right corner of shape measured in 1/1024 of column width.
     *
     * @var int
     */
    private $endOffsetX;

    /**
     * Vertical offset of bottom-right corner of shape measured in 1/256 of row height.
     *
     * @var int
     */
    private $endOffsetY;

    /**
     * Set parent Shape Group Container.
     *
     * @param SpgrContainer $parent
     */
    public function setParent($parent): void
    {
        $this->parent = $parent;
    }

    /**
     * Get the parent Shape Group Container.
     *
     * @return SpgrContainer
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set whether this is a group shape.
     *
     * @param bool $value
     */
    public function setSpgr($value): void
    {
        $this->spgr = $value;
    }

    /**
     * Get whether this is a group shape.
     *
     * @return bool
     */
    public function getSpgr()
    {
        return $this->spgr;
    }

    /**
     * Set the shape type.
     *
     * @param int $value
     */
    public function setSpType($value): void
    {
        $this->spType = $value;
    }

    /**
     * Get the shape type.
     *
     * @return int
     */
    public function getSpType()
    {
        return $this->spType;
    }

    /**
     * Set the shape flag.
     *
     * @param int $value
     */
    public function setSpFlag($value): void
    {
        $this->spFlag = $value;
    }

    /**
     * Get the shape flag.
     *
     * @return int
     */
    public function getSpFlag()
    {
        return $this->spFlag;
    }

    /**
     * Set the shape index.
     *
     * @param int $value
     */
    public function setSpId($value): void
    {
        $this->spId = $value;
    }

    /**
     * Get the shape index.
     *
     * @return int
     */
    public function getSpId()
    {
        return $this->spId;
    }

    /**
     * Set an option for the Shape Group Container.
     *
     * @param int $property The number specifies the option
     * @param mixed $value
     */
    public function setOPT($property, $value): void
    {
        $this->OPT[$property] = $value;
    }

    /**
     * Get an option for the Shape Group Container.
     *
     * @param int $property The number specifies the option
     *
     * @return mixed
     */
    public function getOPT($property)
    {
        if (isset($this->OPT[$property])) {
            return $this->OPT[$property];
        }

        return null;
    }

    /**
     * Get the collection of options.
     *
     * @return array
     */
    public function getOPTCollection()
    {
        return $this->OPT;
    }

    /**
     * Set cell coordinates of upper-left corner of shape.
     *
     * @param string $value eg: 'A1'
     */
    public function setStartCoordinates($value): void
    {
        $this->startCoordinates = $value;
    }

    /**
     * Get cell coordinates of upper-left corner of shape.
     *
     * @return string
     */
    public function getStartCoordinates()
    {
        return $this->startCoordinates;
    }

    /**
     * Set offset in x-direction of upper-left corner of shape measured in 1/1024 of column width.
     *
     * @param int $startOffsetX
     */
    public function setStartOffsetX($startOffsetX): void
    {
        $this->startOffsetX = $startOffsetX;
    }

    /**
     * Get offset in x-direction of upper-left corner of shape measured in 1/1024 of column width.
     *
     * @return int
     */
    public function getStartOffsetX()
    {
        return $this->startOffsetX;
    }

    /**
     * Set offset in y-direction of upper-left corner of shape measured in 1/256 of row height.
     *
     * @param int $startOffsetY
     */
    public function setStartOffsetY($startOffsetY): void
    {
        $this->startOffsetY = $startOffsetY;
    }

    /**
     * Get offset in y-direction of upper-left corner of shape measured in 1/256 of row height.
     *
     * @return int
     */
    public function getStartOffsetY()
    {
        return $this->startOffsetY;
    }

    /**
     * Set cell coordinates of bottom-right corner of shape.
     *
     * @param string $value eg: 'A1'
     */
    public function setEndCoordinates($value): void
    {
        $this->endCoordinates = $value;
    }

    /**
     * Get cell coordinates of bottom-right corner of shape.
     *
     * @return string
     */
    public function getEndCoordinates()
    {
        return $this->endCoordinates;
    }

    /**
     * Set offset in x-direction of bottom-right corner of shape measured in 1/1024 of column width.
     *
     * @param int $endOffsetX
     */
    public function setEndOffsetX($endOffsetX): void
    {
        $this->endOffsetX = $endOffsetX;
    }

    /**
     * Get offset in x-direction of bottom-right corner of shape measured in 1/1024 of column width.
     *
     * @return int
     */
    public function getEndOffsetX()
    {
        return $this->endOffsetX;
    }

    /**
     * Set offset in y-direction of bottom-right corner of shape measured in 1/256 of row height.
     *
     * @param int $endOffsetY
     */
    public function setEndOffsetY($endOffsetY): void
    {
        $this->endOffsetY = $endOffsetY;
    }

    /**
     * Get offset in y-direction of bottom-right corner of shape measured in 1/256 of row height.
     *
     * @return int
     */
    public function getEndOffsetY()
    {
        return $this->endOffsetY;
    }

    /**
     * Get the nesting level of this spContainer. This is the number of spgrContainers between this spContainer and
     * the dgContainer. A value of 1 = immediately within first spgrContainer
     * Higher nesting level occurs if and only if spContainer is part of a shape group.
     *
     * @return int Nesting level
     */
    public function getNestingLevel()
    {
        $nestingLevel = 0;

        $parent = $this->getParent();
        while ($parent instanceof SpgrContainer) {
            ++$nestingLevel;
            $parent = $parent->getParent();
        }

        return $nestingLevel;
    }
}
