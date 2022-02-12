<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;

class ArrayArgumentHelper
{
    /**
     * @var array
     */
    protected $arguments;

    /**
     * @var int
     */
    protected $argumentCount;

    /**
     * @var array
     */
    protected $rows;

    /**
     * @var array
     */
    protected $columns;

    public function initialise(array $arguments): void
    {
        $this->rows = $this->rows($arguments);
        $this->columns = $this->columns($arguments);

        $this->argumentCount = count($arguments);
        $this->arguments = $this->flattenSingleCellArrays($arguments, $this->rows, $this->columns);

        $this->rows = $this->rows($arguments);
        $this->columns = $this->columns($arguments);

        if ($this->arrayArguments() > 2) {
            throw new Exception('Formulae with more than two array arguments are not supported');
        }
    }

    public function arguments(): array
    {
        return $this->arguments;
    }

    public function hasArrayArgument(): bool
    {
        return $this->arrayArguments() > 0;
    }

    public function getFirstArrayArgumentNumber(): int
    {
        $rowArrays = $this->filterArray($this->rows);
        $columnArrays = $this->filterArray($this->columns);

        for ($index = 0; $index < $this->argumentCount; ++$index) {
            if (isset($rowArrays[$index]) || isset($columnArrays[$index])) {
                return ++$index;
            }
        }

        return 0;
    }

    public function getSingleRowVector(): ?int
    {
        $rowVectors = $this->getRowVectors();

        return count($rowVectors) === 1 ? array_pop($rowVectors) : null;
    }

    private function getRowVectors(): array
    {
        $rowVectors = [];
        for ($index = 0; $index < $this->argumentCount; ++$index) {
            if ($this->rows[$index] === 1 && $this->columns[$index] > 1) {
                $rowVectors[] = $index;
            }
        }

        return $rowVectors;
    }

    public function getSingleColumnVector(): ?int
    {
        $columnVectors = $this->getColumnVectors();

        return count($columnVectors) === 1 ? array_pop($columnVectors) : null;
    }

    private function getColumnVectors(): array
    {
        $columnVectors = [];
        for ($index = 0; $index < $this->argumentCount; ++$index) {
            if ($this->rows[$index] > 1 && $this->columns[$index] === 1) {
                $columnVectors[] = $index;
            }
        }

        return $columnVectors;
    }

    public function getMatrixPair(): array
    {
        for ($i = 0; $i < ($this->argumentCount - 1); ++$i) {
            for ($j = $i + 1; $j < $this->argumentCount; ++$j) {
                if (isset($this->rows[$i], $this->rows[$j])) {
                    return [$i, $j];
                }
            }
        }

        return [];
    }

    public function isVector(int $argument): bool
    {
        return $this->rows[$argument] === 1 || $this->columns[$argument] === 1;
    }

    public function isRowVector(int $argument): bool
    {
        return $this->rows[$argument] === 1;
    }

    public function isColumnVector(int $argument): bool
    {
        return $this->columns[$argument] === 1;
    }

    public function rowCount(int $argument): int
    {
        return $this->rows[$argument];
    }

    public function columnCount(int $argument): int
    {
        return $this->columns[$argument];
    }

    private function rows(array $arguments): array
    {
        return array_map(
            function ($argument) {
                return is_countable($argument) ? count($argument) : 1;
            },
            $arguments
        );
    }

    private function columns(array $arguments): array
    {
        return array_map(
            function ($argument) {
                return is_array($argument) && is_array($argument[array_keys($argument)[0]])
                    ? count($argument[array_keys($argument)[0]])
                    : 1;
            },
            $arguments
        );
    }

    public function arrayArguments(): int
    {
        $count = 0;
        foreach (array_keys($this->arguments) as $argument) {
            if ($this->rows[$argument] > 1 || $this->columns[$argument] > 1) {
                ++$count;
            }
        }

        return $count;
    }

    private function flattenSingleCellArrays(array $arguments, array $rows, array $columns): array
    {
        foreach ($arguments as $index => $argument) {
            if ($rows[$index] === 1 && $columns[$index] === 1) {
                while (is_array($argument)) {
                    $argument = array_pop($argument);
                }
                $arguments[$index] = $argument;
            }
        }

        return $arguments;
    }

    private function filterArray(array $array): array
    {
        return array_filter(
            $array,
            function ($value) {
                return $value > 1;
            }
        );
    }
}
