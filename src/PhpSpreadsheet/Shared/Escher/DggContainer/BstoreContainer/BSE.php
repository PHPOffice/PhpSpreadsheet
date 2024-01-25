<?php

namespace PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer;

use PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer;

class BSE
{
    public const BLIPTYPE_ERROR = 0x00;
    public const BLIPTYPE_UNKNOWN = 0x01;
    public const BLIPTYPE_EMF = 0x02;
    public const BLIPTYPE_WMF = 0x03;
    public const BLIPTYPE_PICT = 0x04;
    public const BLIPTYPE_JPEG = 0x05;
    public const BLIPTYPE_PNG = 0x06;
    public const BLIPTYPE_DIB = 0x07;
    public const BLIPTYPE_TIFF = 0x11;
    public const BLIPTYPE_CMYKJPEG = 0x12;

    /**
     * The parent BLIP Store Entry Container.
     * Property is never currently read.
     */
    private BstoreContainer $parent; // @phpstan-ignore-line

    /**
     * The BLIP (Big Large Image or Picture).
     *
     * @var ?BSE\Blip
     */
    private ?BSE\Blip $blip = null;

    /**
     * The BLIP type.
     */
    private int $blipType;

    /**
     * Set parent BLIP Store Entry Container.
     */
    public function setParent(BstoreContainer $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * Get the BLIP.
     */
    public function getBlip(): ?BSE\Blip
    {
        return $this->blip;
    }

    /**
     * Set the BLIP.
     */
    public function setBlip(BSE\Blip $blip): void
    {
        $this->blip = $blip;
        $blip->setParent($this);
    }

    /**
     * Get the BLIP type.
     */
    public function getBlipType(): int
    {
        return $this->blipType;
    }

    /**
     * Set the BLIP type.
     */
    public function setBlipType(int $blipType): void
    {
        $this->blipType = $blipType;
    }
}
