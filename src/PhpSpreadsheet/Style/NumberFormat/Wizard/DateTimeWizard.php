<?php

namespace PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard;

abstract class DateTimeWizard implements Wizard
{
    protected function padSeparatorArray(array $separators, int $count): array
    {
        $lastSeparator = array_pop($separators);

        return $separators + array_fill(0, $count, $lastSeparator);
    }

    protected function wrapLiteral(string $value): string
    {
        // TODO Single characters can always be escaped, rather than quoted; and there are
        //      some single characters that can be left as literals.
        // Wrap any other string literals in quotes, so that they're clearly defined as string literals
        return '"' . str_replace('"', '""', $value) . '"';
    }

    protected function intersperse(string $formatBlock, ?string $separator): string
    {
        return "{$formatBlock}{$separator}";
    }

    public function __toString(): string
    {
        return $this->format();
    }
}
