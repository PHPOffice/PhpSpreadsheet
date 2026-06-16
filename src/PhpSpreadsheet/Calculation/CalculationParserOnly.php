<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

/**
 * A dedicated Calculation singleton for formula parsing only.
 *
 * This class provides an isolated instance specifically for parsing formulas
 * without branch pruning, avoiding state conflicts with the main Calculation
 * singleton used for cell value calculations.
 *
 * @internal
 */
final class CalculationParserOnly extends Calculation
{
    /**
     * Instance of this class.
     */
    private static ?CalculationParserOnly $parserInstance = null;

    /**
     * Branch pruning is disabled by default for parsing-only operations.
     */
    protected bool $branchPruningEnabled = false;

    /**
     * Get the singleton instance of this parser-only calculator.
     */
    public static function getParserInstance(): self
    {
        if (!self::$parserInstance) {
            self::$parserInstance = new self();
        }

        return self::$parserInstance;
    }

    /** @param mixed $enabled Unused, property will always be false in this class */
    public function setBranchPruningEnabled(mixed $enabled): self
    {
        return $this;
    }
}
