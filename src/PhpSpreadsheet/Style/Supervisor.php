<?php

namespace PhpOffice\PhpSpreadsheet\Style;

use PhpOffice\PhpSpreadsheet\IComparable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class Supervisor implements IComparable
{
    /**
     * Supervisor?
     */
    protected bool $isSupervisor;

    /**
     * Parent. Only used for supervisor.
     *
     * @var Spreadsheet|Supervisor
     */
    protected $parent;

    /**
     * Parent property name.
     */
    protected ?string $parentPropertyName = null;

    /**
     * Create a new Supervisor.
     *
     * @param bool $isSupervisor Flag indicating if this is a supervisor or not
     *                                    Leave this value at default unless you understand exactly what
     *                                        its ramifications are
     */
    public function __construct(bool $isSupervisor = false)
    {
        // Supervisor?
        $this->isSupervisor = $isSupervisor;
    }

    /**
     * Bind parent. Only used for supervisor.
     *
     * @return $this
     */
    public function bindParent(Spreadsheet|self $parent, ?string $parentPropertyName = null)
    {
        $this->parent = $parent;
        $this->parentPropertyName = $parentPropertyName;

        return $this;
    }

    /**
     * Is this a supervisor or a cell style component?
     */
    public function getIsSupervisor(): bool
    {
        return $this->isSupervisor;
    }

    /**
     * Get the currently active sheet. Only used for supervisor.
     */
    public function getActiveSheet(): Worksheet
    {
        return $this->parent->getActiveSheet();
    }

    /**
     * Get the currently active cell coordinate in currently active sheet.
     * Only used for supervisor.
     *
     * @return string E.g. 'A1'
     */
    public function getSelectedCells(): string
    {
        return $this->getActiveSheet()->getSelectedCells();
    }

    /**
     * Get the currently active cell coordinate in currently active sheet.
     * Only used for supervisor.
     *
     * @return string E.g. 'A1'
     */
    public function getActiveCell(): string
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

    /**
     * Export style as array.
     *
     * Available to anything which extends this class:
     * Alignment, Border, Borders, Color, Fill, Font,
     * NumberFormat, Protection, and Style.
     *
     * @return mixed[]
     */
    final public function exportArray(): array
    {
        return $this->exportArray1();
    }

    /**
     * Abstract method to be implemented in anything which
     * extends this class.
     *
     * This method invokes exportArray2 with the names and values
     * of all properties to be included in output array,
     * returning that array to exportArray, then to caller.
     *
     * @return mixed[]
     */
    abstract protected function exportArray1(): array;

    /**
     * Populate array from exportArray1.
     * This method is available to anything which extends this class.
     * The parameter index is the key to be added to the array.
     * The parameter objOrValue is either a primitive type,
     * which is the value added to the array,
     * or a Style object to be recursively added via exportArray.
     *
     * @param mixed[] $exportedArray
     */
    final protected function exportArray2(array &$exportedArray, string $index, mixed $objOrValue): void
    {
        if ($objOrValue instanceof self) {
            $exportedArray[$index] = $objOrValue->exportArray();
        } else {
            $exportedArray[$index] = $objOrValue;
        }
    }

    /**
     * Get the shared style component for the currently active cell in currently active sheet.
     * Only used for style supervisor.
     */
    abstract public function getSharedComponent(): mixed;

    /**
     * Build style array from subcomponents.
     *
     * @param mixed[] $array
     *
     * @return mixed[]
     */
    abstract public function getStyleArray(array $array): array;
}
