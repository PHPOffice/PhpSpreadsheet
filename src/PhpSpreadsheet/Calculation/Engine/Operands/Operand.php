<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Operands;

interface Operand
{
    public static function fromParser(string $formula, int $index, array $matches): self;

    public function value(): string;
}
