<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;

class ExcelException
{
    public const EXCEL_ERROR_NULL = '#NULL!';
    public const EXCEL_ERROR_DIVISION_BY_ZERO = '#DIV/0!';
    public const EXCEL_ERROR_VALUE = '#VALUE!';
    public const EXCEL_ERROR_REFERENCE = '#REF!';
    public const EXCEL_ERROR_NAME = '#NAME?';
    public const EXCEL_ERROR_NUM = '#NUM!';
    public const EXCEL_ERROR_NA = '#N/A';
    public const EXCEL_ERROR_GETTING_DATA = '#GETTING_DATA';

    public const ERROR_CODES = [
        self::EXCEL_ERROR_NULL => 1,
        self::EXCEL_ERROR_DIVISION_BY_ZERO => 2,
        self::EXCEL_ERROR_VALUE => 3,
        self::EXCEL_ERROR_REFERENCE => 4,
        self::EXCEL_ERROR_NAME => 5,
        self::EXCEL_ERROR_NUM => 6,
        self::EXCEL_ERROR_NA => 7,
        self::EXCEL_ERROR_GETTING_DATA => 8,
    ];

    protected const ERROR_TYPES = [
        self::EXCEL_ERROR_NULL => [self::class, 'NULL'],
        self::EXCEL_ERROR_DIVISION_BY_ZERO => [self::class, 'DIV0'],
        self::EXCEL_ERROR_VALUE => [self::class, 'VALUE'],
        self::EXCEL_ERROR_REFERENCE => [self::class, 'REF'],
        self::EXCEL_ERROR_NAME => [self::class, 'NAME'],
        self::EXCEL_ERROR_NUM => [self::class, 'NUM'],
        self::EXCEL_ERROR_NA => [self::class, 'NA'],
        self::EXCEL_ERROR_GETTING_DATA => [self::class, 'DATA'],
    ];

    private $errorName;

    private $code;

    private function __construct($errorName)
    {
        $this->errorName = $errorName;
        $this->code = self::ERROR_CODES[$errorName];
    }

    public static function fromErrorName(string $value): self
    {
        if (!in_array($value, array_keys(self::ERROR_TYPES), true)) {
            throw new SpreadsheetException(sprintf('Invalid Excel Error Code "%s"', $value));
        }

        $errorType = self::ERROR_TYPES[$value];

        return $errorType();
    }

    public static function null(): self
    {
        return new self(self::EXCEL_ERROR_NULL);
    }

    public static function DIV0(): self
    {
        return new self(self::EXCEL_ERROR_DIVISION_BY_ZERO);
    }

    public static function VALUE(): self
    {
        return new self(self::EXCEL_ERROR_VALUE);
    }

    public static function REF(): self
    {
        return new self(self::EXCEL_ERROR_REFERENCE);
    }

    public static function NAME(): self
    {
        return new self(self::EXCEL_ERROR_NAME);
    }

    public static function NUM(): self
    {
        return new self(self::EXCEL_ERROR_NUM);
    }

    public static function NA(): self
    {
        return new self(self::EXCEL_ERROR_NA);
    }

    public static function DATA(): self
    {
        return new self(self::EXCEL_ERROR_GETTING_DATA);
    }

    public function code(): int
    {
        return $this->code;
    }

    public function errorName(): string
    {
        return $this->errorName;
    }

    public function __toString()
    {
        return $this->errorName;
    }
}
