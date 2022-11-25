<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Operands;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;

final class StructuredReference implements Operand
{
    public const NAME = 'Structured Reference';

    private const OPEN_BRACE = '[';
    private const CLOSE_BRACE = ']';

    private string $value;

    public function __construct(string $structuredReference)
    {
        $this->value = $structuredReference;
    }

    public static function fromParser(string $formula, int $index, array $matches): self
    {
        $val = $matches[0];

        $srCount = substr_count($val, self::OPEN_BRACE)
            - substr_count($val, self::CLOSE_BRACE);
        while ($srCount > 0) {
            $srIndex = strlen($val);
            $srStringRemainder = substr($formula, $index + $srIndex);
            $closingPos = strpos($srStringRemainder, self::CLOSE_BRACE);
            if ($closingPos === false) {
                throw new Exception("Formula Error: No closing ']' to match opening '['");
            }
            $srStringRemainder = substr($srStringRemainder, 0, $closingPos + 1);
            --$srCount;
            if (strpos($srStringRemainder, self::OPEN_BRACE) !== false) {
                ++$srCount;
            }
            $val .= $srStringRemainder;
        }

        return new self($val);
    }

    public function value(): string
    {
        return $this->value;
    }
}
