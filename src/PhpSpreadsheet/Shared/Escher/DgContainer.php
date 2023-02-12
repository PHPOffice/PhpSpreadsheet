<?php

namespace PhpOffice\PhpSpreadsheet\Shared\Escher;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;

class DgContainer
{
    /**
     * Drawing index, 1-based.
     *
     * @var ?int
     */
    private $dgId;

    /**
     * Last shape index in this drawing.
     *
     * @var ?int
     */
    private $lastSpId;

    /** @var ?DgContainer\SpgrContainer */
    private $spgrContainer;

    public function getDgId(): ?int
    {
        return $this->dgId;
    }

    public function setDgId(int $value): void
    {
        $this->dgId = $value;
    }

    public function getLastSpId(): ?int
    {
        return $this->lastSpId;
    }

    public function setLastSpId(int $value): void
    {
        $this->lastSpId = $value;
    }

    public function getSpgrContainer(): ?DgContainer\SpgrContainer
    {
        return $this->spgrContainer;
    }

    public function getSpgrContainerOrThrow(): DgContainer\SpgrContainer
    {
        if ($this->spgrContainer !== null) {
            return $this->spgrContainer;
        }

        throw new SpreadsheetException('spgrContainer is unexpectedly null');
    }

    /** @param DgContainer\SpgrContainer $spgrContainer */
    public function setSpgrContainer($spgrContainer): DgContainer\SpgrContainer
    {
        return $this->spgrContainer = $spgrContainer;
    }
}
