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
     *
     * @var ?DggContainer\BstoreContainer
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
     *
     * @param int $value
     */
    public function setSpIdMax($value): void
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
     *
     * @param int $value
     */
    public function setCDgSaved($value): void
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
     *
     * @param int $value
     */
    public function setCSpSaved($value): void
    {
        $this->cSpSaved = $value;
    }

    /**
     * Get BLIP Store Container.
     *
     * @return ?DggContainer\BstoreContainer
     */
    public function getBstoreContainer(): ?DggContainer\BstoreContainer
    {
        return $this->bstoreContainer;
    }

    /**
     * Set BLIP Store Container.
     *
     * @param DggContainer\BstoreContainer $bstoreContainer
     */
    public function setBstoreContainer($bstoreContainer): void
    {
        $this->bstoreContainer = $bstoreContainer;
    }

    /**
     * Set an option for the drawing group.
     *
     * @param int $property The number specifies the option
     */
    public function setOPT($property, mixed $value): void
    {
        $this->OPT[$property] = $value;
    }

    /**
     * Get an option for the drawing group.
     *
     * @param int $property The number specifies the option
     */
    public function getOPT($property): mixed
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
     *
     * @param array $IDCLs
     */
    public function setIDCLs($IDCLs): void
    {
        $this->IDCLs = $IDCLs;
    }
}
