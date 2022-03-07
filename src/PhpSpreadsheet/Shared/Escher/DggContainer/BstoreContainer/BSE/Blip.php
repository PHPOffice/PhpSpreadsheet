<?php

namespace PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer\BSE;

use PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer\BSE;

class Blip
{
    /**
     * The parent BSE.
     *
     * @var BSE
     */
    private $parent;

    /**
     * Raw image data.
     *
     * @var string
     */
    private $data;

    /**
     * Get the raw image data.
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the raw image data.
     *
     * @param string $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * Set parent BSE.
     */
    public function setParent(BSE $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * Get parent BSE.
     */
    public function getParent(): BSE
    {
        return $this->parent;
    }
}
