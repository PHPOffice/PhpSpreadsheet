<?php

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('UTC');

// Adjust the path as required to reference the PHPSpreadsheet Bootstrap file
require_once __DIR__ . '/../samples/Bootstrap.php';

class ExcelFunction
{
    public $category;

    public $functionName;

    public $excelVersion;

    public $implementation;

    public function __construct(string $category, string $functionName, string $excelVersion, string $implementation)
    {
        $this->category = $category;
        $this->functionName = $functionName;
        $this->excelVersion = $excelVersion;
        $this->implementation = $implementation;
    }
}

class ColumnSettings
{
    public $length;

    public $title;

    public $underline;

    public function __construct(string $title, int $length)
    {
        $this->length = $length;
        $this->title = str_pad($title, $length, ' ');
        $this->underline = str_repeat('-', $length);
    }
}

class ListBuilder
{
    private $inputFileName;

    public $list = [];

    public function __construct(string $inputFileName)
    {
        $this->inputFileName = $inputFileName;

        $this->buildList();
        uasort(
            $this->list,
            function ($a, $b) {
                $aSortName = str_replace('.', '', $a->functionName, $aCount) . str_repeat('.', $aCount);
                $bSortName = str_replace('.', '', $b->functionName, $bCount) . str_repeat('.', $bCount);

                return $aSortName <=> $bSortName;
            }
        );
    }

    private function buildList(): void
    {
        $inputFile = new \SplFileObject($this->inputFileName);
        $category = null;

        while (!$inputFile->eof()) {
            $line = $inputFile->fgets();
            if (strpos($line, '#') === 0) {
                if (strpos($line, '##') === 0) {
                    $category = trim(substr($line, 3));
                }

                continue;
            }

            $lineData = explode('|', $line);
            if (count($lineData) <= 1 || strpos($line, '--') === 0) {
                continue;
            }

            $functionData = array_map('trim', $lineData);

            if ($functionData[0] === 'Excel Function') {
                continue;
            }

            $function = new ExcelFunction($category, ...$functionData);
            if (array_key_exists($function->functionName, $this->list)) {
                echo "    ERROR: Duplicate entry for function {$function->functionName} in master file", PHP_EOL;

                continue;
            }

            $this->list[$function->functionName] = $function;
        }
    }
}

class AlphabeticFileWriter
{
    private $outputFileName;

    private $outputFile;

    public function __construct(string $outputFileName)
    {
        $this->outputFileName = $outputFileName;
    }

    public function generate(ExcelFunction ...$functionList): void
    {
        $this->outputFile = new \SplFileObject($this->outputFileName, 'w');

        $this->excelFunctionColumnSettings = new ColumnSettings('Excel Function', max(array_map('strlen', array_column($functionList, 'functionName'))));
        $this->categoryColumnSettings = new ColumnSettings('Category', max(array_map('strlen', array_column($functionList, 'category'))));
        $this->excelVersionColumnSettings = new ColumnSettings('Excel Version', 13);
        $this->phpSpreadsheetFunctionColumnSettings = new ColumnSettings('PhpSpreadsheet Function', 24);

        $this->header();
        $this->body(...$functionList);
    }

    private function header(): void
    {
        $this->outputFile->fwrite('# Function list by name' . PHP_EOL);
    }

    private function body(ExcelFunction ...$functionList): void
    {
        $initialCharacter = null;

        foreach ($functionList as $excelFunction) {
            if (substr($excelFunction->functionName, 0, 1) !== $initialCharacter) {
                $initialCharacter = $this->subHeader($excelFunction);
            }

            $functionName = str_pad($excelFunction->functionName, $this->excelFunctionColumnSettings->length, ' ');
            $category = str_pad($excelFunction->category, $this->categoryColumnSettings->length, ' ');
            $excelVersion = str_pad($excelFunction->excelVersion, $this->excelVersionColumnSettings->length, ' ');
            $this->outputFile->fwrite("{$functionName} | {$category} | {$excelVersion} | {$excelFunction->implementation}" . PHP_EOL);
        }
    }

    private function subHeader(ExcelFunction $excelFunction)
    {
        $initialCharacter = substr($excelFunction->functionName, 0, 1);

        $this->outputFile->fwrite(PHP_EOL . "## {$initialCharacter}" . PHP_EOL . PHP_EOL);
        $this->outputFile->fwrite("{$this->excelFunctionColumnSettings->title} | {$this->categoryColumnSettings->title} | {$this->excelVersionColumnSettings->title} | {$this->phpSpreadsheetFunctionColumnSettings->title}" . PHP_EOL);
        $this->outputFile->fwrite("{$this->excelFunctionColumnSettings->underline}-|-{$this->categoryColumnSettings->underline}-|-{$this->excelVersionColumnSettings->underline}-|-{$this->phpSpreadsheetFunctionColumnSettings->underline}" . PHP_EOL);

        return $initialCharacter;
    }
}

$folder = __DIR__ . '/../docs/references/';
$inputFileName = 'function-list-by-category.md';
$outputFileName = 'function-list-by-name.md';

echo "Building list of functions from master file {$inputFileName}", PHP_EOL;
$listBuilder = new ListBuilder($folder . $inputFileName);

echo "Building new documentation list of alphabetic functions in {$outputFileName}", PHP_EOL;
$alphabeticFileWriter = new AlphabeticFileWriter($folder . $outputFileName);
$alphabeticFileWriter->generate(...array_values($listBuilder->list));

echo 'Identifying discrepancies between the master file and the Calculation Engine codebase', PHP_EOL;
$definedFunctions = (new Calculation())->getFunctions();

foreach ($listBuilder->list as $excelFunction) {
    if (!array_key_exists($excelFunction->functionName, $definedFunctions)) {
        echo "    ERROR: Function {$excelFunction->functionName}() of category {$excelFunction->category} is not defined in the Calculation Engine", PHP_EOL;
    } elseif (array_key_exists($excelFunction->functionName, $definedFunctions) && $excelFunction->implementation === '**Not yet Implemented**') {
        if ($definedFunctions[$excelFunction->functionName]['functionCall'] !== [Functions::class, 'DUMMY']) {
            echo "    ERROR: Function {$excelFunction->functionName}() of category {$excelFunction->category} is flagged as not yet implemented in the documentation", PHP_EOL;
            echo '           but does have an implementation in the code', PHP_EOL;
        }
    }
}

foreach ($definedFunctions as $definedFunction => $definedFunctionDetail) {
    if (!array_key_exists($definedFunction, $listBuilder->list)) {
        echo "    ERROR: Function {$definedFunction}() of category {$definedFunctionDetail['category']} is defined in the Calculation Engine, but not in the master file", PHP_EOL;
    }
}
