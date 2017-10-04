<?php

namespace PhpOffice\PhpSpreadsheet\Style;

abstract class Supervisor
{
    /**
     * Supervisor?
     *
     * @var bool
     */
    protected $isSupervisor;

    /**
     * Parent. Only used for supervisor.
     *
     * @var \PhpOffice\PhpSpreadsheet\Style
     */
    protected $parent;

    /**
     * Create a new Supervisor.
     *
     * @param bool $isSupervisor Flag indicating if this is a supervisor or not
     *                                    Leave this value at default unless you understand exactly what
     *                                        its ramifications are
     */
    public function __construct($isSupervisor = false)
    {
        // Supervisor?
        $this->isSupervisor = $isSupervisor;
    }

    /**
     * Bind parent. Only used for supervisor.
     *
     * @param Style $parent
     * @param null|mixed $parentPropertyName
     *
     * @return Supervisor
     */
    public function bindParent($parent, $parentPropertyName = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Is this a supervisor or a cell style component?
     *
     * @return bool
     */
    public function getIsSupervisor()
    {
        return $this->isSupervisor;
    }

    /**
     * Get the currently active sheet. Only used for supervisor.
     *
     * @return \PhpOffice\PhpSpreadsheet\Worksheet
     */
    public function getActiveSheet()
    {
        return $this->parent->getActiveSheet();
    }

    /**
     * Get the currently active cell coordinate in currently active sheet.
     * Only used for supervisor.
     *
     * @return string E.g. 'A1'
     */
    public function getSelectedCells()
    {
        return $this->getActiveSheet()->getSelectedCells();
    }

    /**
     * Get the currently active cell coordinate in currently active sheet.
     * Only used for supervisor.
     *
     * @return string E.g. 'A1'
     */
    public function getActiveCell()
    {
        return $this->getActiveSheet()->getActiveCell();
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if ((is_object($value)) && ($key != 'parent')) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }
}
