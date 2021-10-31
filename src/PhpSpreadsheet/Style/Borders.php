<?php

namespace PhpOffice\PhpSpreadsheet\Style;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

class Borders extends Supervisor
{
    // Diagonal directions
    const DIAGONAL_NONE = 0;
    const DIAGONAL_UP = 1;
    const DIAGONAL_DOWN = 2;
    const DIAGONAL_BOTH = 3;

    /**
     * Left.
     *
     * @var Border
     */
    protected $left;

    /**
     * Right.
     *
     * @var Border
     */
    protected $right;

    /**
     * Top.
     *
     * @var Border
     */
    protected $top;

    /**
     * Bottom.
     *
     * @var Border
     */
    protected $bottom;

    /**
     * Diagonal.
     *
     * @var Border
     */
    protected $diagonal;

    /**
     * DiagonalDirection.
     *
     * @var int
     */
    protected $diagonalDirection;

    /**
     * All borders pseudo-border. Only applies to supervisor.
     *
     * @var Border
     */
    protected $allBorders;

    /**
     * Outline pseudo-border. Only applies to supervisor.
     *
     * @var Border
     */
    protected $outline;

    /**
     * Inside pseudo-border. Only applies to supervisor.
     *
     * @var Border
     */
    protected $inside;

    /**
     * Vertical pseudo-border. Only applies to supervisor.
     *
     * @var Border
     */
    protected $vertical;

    /**
     * Horizontal pseudo-border. Only applies to supervisor.
     *
     * @var Border
     */
    protected $horizontal;

    /**
     * Create a new Borders.
     *
     * @param bool $isSupervisor Flag indicating if this is a supervisor or not
     *                                    Leave this value at default unless you understand exactly what
     *                                        its ramifications are
     */
    public function __construct($isSupervisor = false)
    {
        // Supervisor?
        parent::__construct($isSupervisor);

        // Initialise values
        $this->left = new Border($isSupervisor);
        $this->right = new Border($isSupervisor);
        $this->top = new Border($isSupervisor);
        $this->bottom = new Border($isSupervisor);
        $this->diagonal = new Border($isSupervisor);
        $this->diagonalDirection = self::DIAGONAL_NONE;

        // Specially for supervisor
        if ($isSupervisor) {
            // Initialize pseudo-borders
            $this->allBorders = new Border(true);
            $this->outline = new Border(true);
            $this->inside = new Border(true);
            $this->vertical = new Border(true);
            $this->horizontal = new Border(true);

            // bind parent if we are a supervisor
            $this->left->bindParent($this, 'left');
            $this->right->bindParent($this, 'right');
            $this->top->bindParent($this, 'top');
            $this->bottom->bindParent($this, 'bottom');
            $this->diagonal->bindParent($this, 'diagonal');
            $this->allBorders->bindParent($this, 'allBorders');
            $this->outline->bindParent($this, 'outline');
            $this->inside->bindParent($this, 'inside');
            $this->vertical->bindParent($this, 'vertical');
            $this->horizontal->bindParent($this, 'horizontal');
        }
    }

    /**
     * Get the shared style component for the currently active cell in currently active sheet.
     * Only used for style supervisor.
     *
     * @return Borders
     */
    public function getSharedComponent()
    {
        return $this->parent->getSharedComponent()->getBorders();
    }

    /**
     * Build style array from subcomponents.
     *
     * @param array $array
     *
     * @return array
     */
    public function getStyleArray($array)
    {
        return ['borders' => $array];
    }

    /**
     * Apply styles from array.
     *
     * <code>
     * $spreadsheet->getActiveSheet()->getStyle('B2')->getBorders()->applyFromArray(
     *         [
     *             'bottom' => [
     *                 'borderStyle' => Border::BORDER_DASHDOT,
     *                 'color' => [
     *                     'rgb' => '808080'
     *                 ]
     *             ],
     *             'top' => [
     *                 'borderStyle' => Border::BORDER_DASHDOT,
     *                 'color' => [
     *                     'rgb' => '808080'
     *                 ]
     *             ]
     *         ]
     * );
     * </code>
     *
     * <code>
     * $spreadsheet->getActiveSheet()->getStyle('B2')->getBorders()->applyFromArray(
     *         [
     *             'allBorders' => [
     *                 'borderStyle' => Border::BORDER_DASHDOT,
     *                 'color' => [
     *                     'rgb' => '808080'
     *                 ]
     *             ]
     *         ]
     * );
     * </code>
     *
     * @param array $styleArray Array containing style information
     *
     * @return $this
     */
    public function applyFromArray(array $styleArray)
    {
        if ($this->isSupervisor) {
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($styleArray));
        } else {
            if (isset($styleArray['left'])) {
                $this->getLeft()->applyFromArray($styleArray['left']);
            }
            if (isset($styleArray['right'])) {
                $this->getRight()->applyFromArray($styleArray['right']);
            }
            if (isset($styleArray['top'])) {
                $this->getTop()->applyFromArray($styleArray['top']);
            }
            if (isset($styleArray['bottom'])) {
                $this->getBottom()->applyFromArray($styleArray['bottom']);
            }
            if (isset($styleArray['diagonal'])) {
                $this->getDiagonal()->applyFromArray($styleArray['diagonal']);
            }
            if (isset($styleArray['diagonalDirection'])) {
                $this->setDiagonalDirection($styleArray['diagonalDirection']);
            }
            if (isset($styleArray['allBorders'])) {
                $this->getLeft()->applyFromArray($styleArray['allBorders']);
                $this->getRight()->applyFromArray($styleArray['allBorders']);
                $this->getTop()->applyFromArray($styleArray['allBorders']);
                $this->getBottom()->applyFromArray($styleArray['allBorders']);
            }
        }

        return $this;
    }

    /**
     * Get Left.
     *
     * @return Border
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * Get Right.
     *
     * @return Border
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * Get Top.
     *
     * @return Border
     */
    public function getTop()
    {
        return $this->top;
    }

    /**
     * Get Bottom.
     *
     * @return Border
     */
    public function getBottom()
    {
        return $this->bottom;
    }

    /**
     * Get Diagonal.
     *
     * @return Border
     */
    public function getDiagonal()
    {
        return $this->diagonal;
    }

    /**
     * Get AllBorders (pseudo-border). Only applies to supervisor.
     *
     * @return Border
     */
    public function getAllBorders()
    {
        if (!$this->isSupervisor) {
            throw new PhpSpreadsheetException('Can only get pseudo-border for supervisor.');
        }

        return $this->allBorders;
    }

    /**
     * Get Outline (pseudo-border). Only applies to supervisor.
     *
     * @return Border
     */
    public function getOutline()
    {
        if (!$this->isSupervisor) {
            throw new PhpSpreadsheetException('Can only get pseudo-border for supervisor.');
        }

        return $this->outline;
    }

    /**
     * Get Inside (pseudo-border). Only applies to supervisor.
     *
     * @return Border
     */
    public function getInside()
    {
        if (!$this->isSupervisor) {
            throw new PhpSpreadsheetException('Can only get pseudo-border for supervisor.');
        }

        return $this->inside;
    }

    /**
     * Get Vertical (pseudo-border). Only applies to supervisor.
     *
     * @return Border
     */
    public function getVertical()
    {
        if (!$this->isSupervisor) {
            throw new PhpSpreadsheetException('Can only get pseudo-border for supervisor.');
        }

        return $this->vertical;
    }

    /**
     * Get Horizontal (pseudo-border). Only applies to supervisor.
     *
     * @return Border
     */
    public function getHorizontal()
    {
        if (!$this->isSupervisor) {
            throw new PhpSpreadsheetException('Can only get pseudo-border for supervisor.');
        }

        return $this->horizontal;
    }

    /**
     * Get DiagonalDirection.
     *
     * @return int
     */
    public function getDiagonalDirection()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getDiagonalDirection();
        }

        return $this->diagonalDirection;
    }

    /**
     * Set DiagonalDirection.
     *
     * @param int $direction see self::DIAGONAL_*
     *
     * @return $this
     */
    public function setDiagonalDirection($direction)
    {
        if ($direction == '') {
            $direction = self::DIAGONAL_NONE;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['diagonalDirection' => $direction]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->diagonalDirection = $direction;
        }

        return $this;
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getHashcode();
        }

        return md5(
            $this->getLeft()->getHashCode() .
            $this->getRight()->getHashCode() .
            $this->getTop()->getHashCode() .
            $this->getBottom()->getHashCode() .
            $this->getDiagonal()->getHashCode() .
            $this->getDiagonalDirection() .
            __CLASS__
        );
    }

    protected function exportArray1(): array
    {
        $exportedArray = [];
        $this->exportArray2($exportedArray, 'bottom', $this->getBottom());
        $this->exportArray2($exportedArray, 'diagonal', $this->getDiagonal());
        $this->exportArray2($exportedArray, 'diagonalDirection', $this->getDiagonalDirection());
        $this->exportArray2($exportedArray, 'left', $this->getLeft());
        $this->exportArray2($exportedArray, 'right', $this->getRight());
        $this->exportArray2($exportedArray, 'top', $this->getTop());

        return $exportedArray;
    }
}
