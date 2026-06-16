<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;

class Escher
{
    /**
     * Drawing Group Container.
     */
    private ?Escher\DggContainer $dggContainer = null;

    /**
     * Drawing Container.
     */
    private ?Escher\DgContainer $dgContainer = null;

    /**
     * Get Drawing Group Container.
     */
    public function getDggContainer(): ?Escher\DggContainer
    {
        return $this->dggContainer;
    }

    /**
     * Get Drawing Group Container.
     */
    public function getDggContainerOrThrow(): Escher\DggContainer
    {
        return $this->dggContainer ?? throw new SpreadsheetException('dggContainer is unexpectedly null');
    }

    /**
     * Set Drawing Group Container.
     */
    public function setDggContainer(Escher\DggContainer $dggContainer): Escher\DggContainer
    {
        return $this->dggContainer = $dggContainer;
    }

    /**
     * Get Drawing Container.
     */
    public function getDgContainer(): ?Escher\DgContainer
    {
        return $this->dgContainer;
    }

    /**
     * Get Drawing Container.
     */
    public function getDgContainerOrThrow(): Escher\DgContainer
    {
        return $this->dgContainer ?? throw new SpreadsheetException('dgContainer is unexpectedly null');
    }

    /**
     * Set Drawing Container.
     */
    public function setDgContainer(Escher\DgContainer $dgContainer): Escher\DgContainer
    {
        return $this->dgContainer = $dgContainer;
    }
}
