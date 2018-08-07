<?php

namespace PhpOffice\PhpSpreadsheet\Shared\Escher;

class DggContainer
{
    /**
     * Maximum shape index of all shapes in all drawings increased by one.
     *
     * @var int
     */
    private $spIdMax;

    /**
     * Total number of drawings saved.
     *
     * @var int
     */
    private $cDgSaved;

    /**
     * Total number of shapes saved (including group shapes).
     *
     * @var int
     */
    private $cSpSaved;

    /**
     * BLIP Store Container.
     *
     * @var DggContainer\BstoreContainer
     */
    private $bstoreContainer;

    /**
     * Array of options for the drawing group.
     *
     * @var array
     */
    private $OPT = [];

    /**
     * Array of identifier clusters containg information about the maximum shape identifiers.
     *
     * @var array
     */
    private $IDCLs = [];

    /**
     * Get maximum shape index of all shapes in all drawings (plus one).
     *
     * @return int
     */
    public function getSpIdMax()
    {
        return $this->spIdMax;
    }

    /**
     * Set maximum shape index of all shapes in all drawings (plus one).
     *
     * @param int $value
     */
    public function setSpIdMax($value)
    {
        $this->spIdMax = $value;
    }

    /**
     * Get total number of drawings saved.
     *
     * @return int
     */
    public function getCDgSaved()
    {
        return $this->cDgSaved;
    }

    /**
     * Set total number of drawings saved.
     *
     * @param int $value
     */
    public function setCDgSaved($value)
    {
        $this->cDgSaved = $value;
    }

    /**
     * Get total number of shapes saved (including group shapes).
     *
     * @return int
     */
    public function getCSpSaved()
    {
        return $this->cSpSaved;
    }

    /**
     * Set total number of shapes saved (including group shapes).
     *
     * @param int $value
     */
    public function setCSpSaved($value)
    {
        $this->cSpSaved = $value;
    }

    /**
     * Get BLIP Store Container.
     *
     * @return DggContainer\BstoreContainer
     */
    public function getBstoreContainer()
    {
        return $this->bstoreContainer;
    }

    /**
     * Set BLIP Store Container.
     *
     * @param DggContainer\BstoreContainer $bstoreContainer
     */
    public function setBstoreContainer($bstoreContainer)
    {
        $this->bstoreContainer = $bstoreContainer;
    }

    /**
     * Set an option for the drawing group.
     *
     * @param int $property The number specifies the option
     * @param mixed $value
     */
    public function setOPT($property, $value)
    {
        $this->OPT[$property] = $value;
    }

    /**
     * Get an option for the drawing group.
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
     * Get identifier clusters.
     *
     * @return array
     */
    public function getIDCLs()
    {
        return $this->IDCLs;
    }

    /**
     * Set identifier clusters. [<drawingId> => <max shape id>, ...].
     *
     * @param array $pValue
     */
    public function setIDCLs($pValue)
    {
        $this->IDCLs = $pValue;
    }
}
