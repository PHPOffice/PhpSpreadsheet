<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

class Escher
{
    /**
     * Drawing Group Container.
     *
     * @var ?Escher\DggContainer
     */
    private ?Escher\DggContainer $dggContainer = null;

    /**
     * Drawing Container.
     *
     * @var ?Escher\DgContainer
     */
    private ?Escher\DgContainer $dgContainer = null;

    /**
     * Get Drawing Group Container.
     *
     * @return ?Escher\DggContainer
     */
    public function getDggContainer(): ?Escher\DggContainer
    {
        return $this->dggContainer;
    }

    /**
     * Set Drawing Group Container.
     *
     * @param Escher\DggContainer $dggContainer
     */
    public function setDggContainer($dggContainer): Escher\DggContainer
    {
        return $this->dggContainer = $dggContainer;
    }

    /**
     * Get Drawing Container.
     *
     * @return ?Escher\DgContainer
     */
    public function getDgContainer(): ?Escher\DgContainer
    {
        return $this->dgContainer;
    }

    /**
     * Set Drawing Container.
     *
     * @param Escher\DgContainer $dgContainer
     */
    public function setDgContainer($dgContainer): Escher\DgContainer
    {
        return $this->dgContainer = $dgContainer;
    }
}
