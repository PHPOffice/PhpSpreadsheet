<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

class HeaderFooterDrawing extends Drawing
{
    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode(): string
    {
        return md5(
            $this->getPath()
            . $this->name
            . $this->offsetX
            . $this->offsetY
            . $this->width
            . $this->height
            . __CLASS__
        );
    }
}
