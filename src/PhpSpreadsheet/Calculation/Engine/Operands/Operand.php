<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Operands;

interface Operand
{
    /** @param string[] $matches */
    public static function fromParser(string $formula, int $index, array $matches): self;

    public function value(): string;
}
