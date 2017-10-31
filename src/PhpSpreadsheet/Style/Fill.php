<?php

namespace PhpOffice\PhpSpreadsheet\Style;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

class Fill extends Supervisor
{
    // Fill types
    const FILL_NONE = 'none';
    const FILL_SOLID = 'solid';
    const FILL_GRADIENT_LINEAR = 'linear';
    const FILL_GRADIENT_PATH = 'path';
    const FILL_PATTERN_DARKDOWN = 'darkDown';
    const FILL_PATTERN_DARKGRAY = 'darkGray';
    const FILL_PATTERN_DARKGRID = 'darkGrid';
    const FILL_PATTERN_DARKHORIZONTAL = 'darkHorizontal';
    const FILL_PATTERN_DARKTRELLIS = 'darkTrellis';
    const FILL_PATTERN_DARKUP = 'darkUp';
    const FILL_PATTERN_DARKVERTICAL = 'darkVertical';
    const FILL_PATTERN_GRAY0625 = 'gray0625';
    const FILL_PATTERN_GRAY125 = 'gray125';
    const FILL_PATTERN_LIGHTDOWN = 'lightDown';
    const FILL_PATTERN_LIGHTGRAY = 'lightGray';
    const FILL_PATTERN_LIGHTGRID = 'lightGrid';
    const FILL_PATTERN_LIGHTHORIZONTAL = 'lightHorizontal';
    const FILL_PATTERN_LIGHTTRELLIS = 'lightTrellis';
    const FILL_PATTERN_LIGHTUP = 'lightUp';
    const FILL_PATTERN_LIGHTVERTICAL = 'lightVertical';
    const FILL_PATTERN_MEDIUMGRAY = 'mediumGray';

    /**
     * @var int
     */
    public $startcolorIndex;

    /**
     * @var int
     */
    public $endcolorIndex;

    /**
     * Fill type.
     *
     * @var string
     */
    protected $fillType = self::FILL_NONE;

    /**
     * Rotation.
     *
     * @var float
     */
    protected $rotation = 0;

    /**
     * Start color.
     *
     * @var Color
     */
    protected $startColor;

    /**
     * End color.
     *
     * @var Color
     */
    protected $endColor;

    /**
     * Create a new Fill.
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
        if ($isConditional) {
            $this->fillType = null;
        }
        $this->startColor = new Color(Color::COLOR_WHITE, $isSupervisor, $isConditional);
        $this->endColor = new Color(Color::COLOR_BLACK, $isSupervisor, $isConditional);

        // bind parent if we are a supervisor
        if ($isSupervisor) {
            $this->startColor->bindParent($this, 'startColor');
            $this->endColor->bindParent($this, 'endColor');
        }
    }

    /**
     * Get the shared style component for the currently active cell in currently active sheet.
     * Only used for style supervisor.
     *
     * @return Fill
     */
    public function getSharedComponent()
    {
        return $this->parent->getSharedComponent()->getFill();
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
        return ['fill' => $array];
    }

    /**
     * Apply styles from array.
     * <code>
     * $spreadsheet->getActiveSheet()->getStyle('B2')->getFill()->applyFromArray(
     *        array(
     *            'fillType'       => Fill::FILL_GRADIENT_LINEAR,
     *            'rotation'   => 0,
     *            'startColor' => array(
     *                'rgb' => '000000'
     *            ),
     *            'endColor'   => array(
     *                'argb' => 'FFFFFFFF'
     *            )
     *        )
     * );
     * </code>.
     *
     * @param array $pStyles Array containing style information
     *
     * @throws PhpSpreadsheetException
     *
     * @return Fill
     */
    public function applyFromArray(array $pStyles)
    {
        if ($this->isSupervisor) {
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($pStyles));
        } else {
            if (isset($pStyles['fillType'])) {
                $this->setFillType($pStyles['fillType']);
            }
            if (isset($pStyles['rotation'])) {
                $this->setRotation($pStyles['rotation']);
            }
            if (isset($pStyles['startColor'])) {
                $this->getStartColor()->applyFromArray($pStyles['startColor']);
            }
            if (isset($pStyles['endColor'])) {
                $this->getEndColor()->applyFromArray($pStyles['endColor']);
            }
            if (isset($pStyles['color'])) {
                $this->getStartColor()->applyFromArray($pStyles['color']);
                $this->getEndColor()->applyFromArray($pStyles['color']);
            }
        }

        return $this;
    }

    /**
     * Get Fill Type.
     *
     * @return string
     */
    public function getFillType()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getFillType();
        }

        return $this->fillType;
    }

    /**
     * Set Fill Type.
     *
     * @param string $pValue Fill type, see self::FILL_*
     *
     * @return Fill
     */
    public function setFillType($pValue)
    {
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['fillType' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->fillType = $pValue;
        }

        return $this;
    }

    /**
     * Get Rotation.
     *
     * @return float
     */
    public function getRotation()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getRotation();
        }

        return $this->rotation;
    }

    /**
     * Set Rotation.
     *
     * @param float $pValue
     *
     * @return Fill
     */
    public function setRotation($pValue)
    {
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['rotation' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->rotation = $pValue;
        }

        return $this;
    }

    /**
     * Get Start Color.
     *
     * @return Color
     */
    public function getStartColor()
    {
        return $this->startColor;
    }

    /**
     * Set Start Color.
     *
     * @param Color $pValue
     *
     * @throws PhpSpreadsheetException
     *
     * @return Fill
     */
    public function setStartColor(Color $pValue)
    {
        // make sure parameter is a real color and not a supervisor
        $color = $pValue->getIsSupervisor() ? $pValue->getSharedComponent() : $pValue;

        if ($this->isSupervisor) {
            $styleArray = $this->getStartColor()->getStyleArray(['argb' => $color->getARGB()]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->startColor = $color;
        }

        return $this;
    }

    /**
     * Get End Color.
     *
     * @return Color
     */
    public function getEndColor()
    {
        return $this->endColor;
    }

    /**
     * Set End Color.
     *
     * @param Color $pValue
     *
     * @throws PhpSpreadsheetException
     *
     * @return Fill
     */
    public function setEndColor(Color $pValue)
    {
        // make sure parameter is a real color and not a supervisor
        $color = $pValue->getIsSupervisor() ? $pValue->getSharedComponent() : $pValue;

        if ($this->isSupervisor) {
            $styleArray = $this->getEndColor()->getStyleArray(['argb' => $color->getARGB()]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->endColor = $color;
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
            $this->getFillType() .
            $this->getRotation() .
            $this->getStartColor()->getHashCode() .
            $this->getEndColor()->getHashCode() .
            __CLASS__
        );
    }
}
