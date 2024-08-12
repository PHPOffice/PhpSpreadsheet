<?php

namespace PhpOffice\PhpSpreadsheet\Shared\Escher;

class DggContainer
{
    /**
     * Maximum shape index of all shapes in all drawings increased by one.
     */
    private int $spIdMax;

    /**
     * Total number of drawings saved.
     */
    private int $cDgSaved;

    /**
     * Total number of shapes saved (including group shapes).
     */
    private int $cSpSaved;

    /**
     * BLIP Store Container.
     */
    private ?DggContainer\BstoreContainer $bstoreContainer = null;

    /**
     * Array of options for the drawing group.
     */
    private array $OPT = [];

    /**
     * Array of identifier clusters containg information about the maximum shape identifiers.
     */
    private array $IDCLs = [];

    /**
     * Get maximum shape index of all shapes in all drawings (plus one).
     */
    public function getSpIdMax(): int
    {
        return $this->spIdMax;
    }

    /**
     * Set maximum shape index of all shapes in all drawings (plus one).
     */
    public function setSpIdMax(int $value): void
    {
        $this->spIdMax = $value;
    }

    /**
     * Get total number of drawings saved.
     */
    public function getCDgSaved(): int
    {
        return $this->cDgSaved;
    }

    /**
     * Set total number of drawings saved.
     */
    public function setCDgSaved(int $value): void
    {
        $this->cDgSaved = $value;
    }

    /**
     * Get total number of shapes saved (including group shapes).
     */
    public function getCSpSaved(): int
    {
        return $this->cSpSaved;
    }

    /**
     * Set total number of shapes saved (including group shapes).
     */
    public function setCSpSaved(int $value): void
    {
        $this->cSpSaved = $value;
    }

    /**
     * Get BLIP Store Container.
     */
    public function getBstoreContainer(): ?DggContainer\BstoreContainer
    {
        return $this->bstoreContainer;
    }

    /**
     * Set BLIP Store Container.
     */
    public function setBstoreContainer(DggContainer\BstoreContainer $bstoreContainer): void
    {
        $this->bstoreContainer = $bstoreContainer;
    }

    /**
     * Set an option for the drawing group.
     *
     * @param int $property The number specifies the option
     */
    public function setOPT(int $property, mixed $value): void
    {
        $this->OPT[$property] = $value;
    }

    /**
     * Get an option for the drawing group.
     *
     * @param int $property The number specifies the option
     */
    public function getOPT(int $property): mixed
    {
        if (isset($this->OPT[$property])) {
            return $this->OPT[$property];
        }

        return null;
    }

    /**
     * Get identifier clusters.
     */
    public function getIDCLs(): array
    {
        return $this->IDCLs;
    }

    /**
     * Set identifier clusters. [<drawingId> => <max shape id>, ...].
     */
    public function setIDCLs(array $IDCLs): void
    {
        $this->IDCLs = $IDCLs;
    }
}
