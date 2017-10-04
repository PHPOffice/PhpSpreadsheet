<?php

namespace PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer;

class BSE
{
    const BLIPTYPE_ERROR = 0x00;
    const BLIPTYPE_UNKNOWN = 0x01;
    const BLIPTYPE_EMF = 0x02;
    const BLIPTYPE_WMF = 0x03;
    const BLIPTYPE_PICT = 0x04;
    const BLIPTYPE_JPEG = 0x05;
    const BLIPTYPE_PNG = 0x06;
    const BLIPTYPE_DIB = 0x07;
    const BLIPTYPE_TIFF = 0x11;
    const BLIPTYPE_CMYKJPEG = 0x12;

    /**
     * The parent BLIP Store Entry Container.
     *
     * @var \PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer
     */
    private $parent;

    /**
     * The BLIP (Big Large Image or Picture).
     *
     * @var BSE\Blip
     */
    private $blip;

    /**
     * The BLIP type.
     *
     * @var int
     */
    private $blipType;

    /**
     * Set parent BLIP Store Entry Container.
     *
     * @param \PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get the BLIP.
     *
     * @return BSE\Blip
     */
    public function getBlip()
    {
        return $this->blip;
    }

    /**
     * Set the BLIP.
     *
     * @param BSE\Blip $blip
     */
    public function setBlip($blip)
    {
        $this->blip = $blip;
        $blip->setParent($this);
    }

    /**
     * Get the BLIP type.
     *
     * @return int
     */
    public function getBlipType()
    {
        return $this->blipType;
    }

    /**
     * Set the BLIP type.
     *
     * @param int
     * @param mixed $blipType
     */
    public function setBlipType($blipType)
    {
        $this->blipType = $blipType;
    }
}
