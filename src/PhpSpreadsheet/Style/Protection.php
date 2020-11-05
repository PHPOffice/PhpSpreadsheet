<?php

namespace PhpOffice\PhpSpreadsheet\Style;

class Protection extends Supervisor
{
    /** Protection styles */
    const PROTECTION_INHERIT = 'inherit';
    const PROTECTION_PROTECTED = 'protected';
    const PROTECTION_UNPROTECTED = 'unprotected';

    /**
     * Locked.
     *
     * @var string
     */
    protected $locked;

    /**
     * Hidden.
     *
     * @var string
     */
    protected $hidden;

    /**
     * Create a new Protection.
     *
     * @param bool $isSupervisor Flag indicating if this is a supervisor or not
     *                                    Leave this value at default unless you understand exactly what
     *                                        its ramifications are
     * @param bool $isConditional Flag indicating if this is a conditional style or not
     *                                    Leave this value at default unless you understand exactly what
     *                                        its ramifications are
     */
    public function __construct($isSupervisor = false, $isConditional = false)
    {
        // Supervisor?
        parent::__construct($isSupervisor);

        // Initialise values
        if (!$isConditional) {
            $this->locked = self::PROTECTION_INHERIT;
            $this->hidden = self::PROTECTION_INHERIT;
        }
    }

    /**
     * Get the shared style component for the currently active cell in currently active sheet.
     * Only used for style supervisor.
     *
     * @return Protection
     */
    public function getSharedComponent()
    {
        return $this->parent->getSharedComponent()->getProtection();
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
        return ['protection' => $array];
    }

    /**
     * Apply styles from array.
     *
     * <code>
     * $spreadsheet->getActiveSheet()->getStyle('B2')->getLocked()->applyFromArray(
     *     [
     *         'locked' => TRUE,
     *         'hidden' => FALSE
     *     ]
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
            if (isset($styleArray['locked'])) {
                $this->setLocked($styleArray['locked']);
            }
            if (isset($styleArray['hidden'])) {
                $this->setHidden($styleArray['hidden']);
            }
        }

        return $this;
    }

    /**
     * Get locked.
     *
     * @return string
     */
    public function getLocked()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getLocked();
        }

        return $this->locked;
    }

    /**
     * Set locked.
     *
     * @param string $lockType see self::PROTECTION_*
     *
     * @return $this
     */
    public function setLocked($lockType)
    {
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['locked' => $lockType]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->locked = $lockType;
        }

        return $this;
    }

    /**
     * Get hidden.
     *
     * @return string
     */
    public function getHidden()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getHidden();
        }

        return $this->hidden;
    }

    /**
     * Set hidden.
     *
     * @param string $hiddenType see self::PROTECTION_*
     *
     * @return $this
     */
    public function setHidden($hiddenType)
    {
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['hidden' => $hiddenType]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->hidden = $hiddenType;
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
            return $this->getSharedComponent()->getHashCode();
        }

        return md5(
            $this->locked .
            $this->hidden .
            __CLASS__
        );
    }

    protected function exportArray1(): array
    {
        $exportedArray = [];
        $this->exportArray2($exportedArray, 'locked', $this->getLocked());
        $this->exportArray2($exportedArray, 'hidden', $this->getHidden());

        return $exportedArray;
    }
}
