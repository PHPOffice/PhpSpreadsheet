<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class CellDataValidation
{
    /**
     * @var array<string, int>
     */
    protected static array $validationTypeMap = [
        DataValidation::TYPE_NONE => 0x00,
        DataValidation::TYPE_WHOLE => 0x01,
        DataValidation::TYPE_DECIMAL => 0x02,
        DataValidation::TYPE_LIST => 0x03,
        DataValidation::TYPE_DATE => 0x04,
        DataValidation::TYPE_TIME => 0x05,
        DataValidation::TYPE_TEXTLENGTH => 0x06,
        DataValidation::TYPE_CUSTOM => 0x07,
    ];

    /**
     * @var array<string, int>
     */
    protected static array $errorStyleMap = [
        DataValidation::STYLE_STOP => 0x00,
        DataValidation::STYLE_WARNING => 0x01,
        DataValidation::STYLE_INFORMATION => 0x02,
    ];

    /**
     * @var array<string, int>
     */
    protected static array $operatorMap = [
        DataValidation::OPERATOR_BETWEEN => 0x00,
        DataValidation::OPERATOR_NOTBETWEEN => 0x01,
        DataValidation::OPERATOR_EQUAL => 0x02,
        DataValidation::OPERATOR_NOTEQUAL => 0x03,
        DataValidation::OPERATOR_GREATERTHAN => 0x04,
        DataValidation::OPERATOR_LESSTHAN => 0x05,
        DataValidation::OPERATOR_GREATERTHANOREQUAL => 0x06,
        DataValidation::OPERATOR_LESSTHANOREQUAL => 0x07,
    ];

    public static function type(DataValidation $dataValidation): int
    {
        $validationType = $dataValidation->getType();

        return self::$validationTypeMap[$validationType] ?? self::$validationTypeMap[DataValidation::TYPE_NONE];
    }

    public static function errorStyle(DataValidation $dataValidation): int
    {
        $errorStyle = $dataValidation->getErrorStyle();

        return self::$errorStyleMap[$errorStyle] ?? self::$errorStyleMap[DataValidation::STYLE_STOP];
    }

    public static function operator(DataValidation $dataValidation): int
    {
        $operator = $dataValidation->getOperator();

        return self::$operatorMap[$operator] ?? self::$operatorMap[DataValidation::OPERATOR_BETWEEN];
    }
}
