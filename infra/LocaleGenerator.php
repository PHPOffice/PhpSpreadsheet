<?php

namespace PhpOffice\PhpSpreadsheetInfra;

use Exception;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LocaleGenerator
{
    private const EXCEL_LOCALISATION_WORKSHEET = 'Excel Localisation';
    private const EXCEL_FUNCTIONS_WORKSHEET = 'Excel Functions';

    private const LOCALE_NAME_ROW = 1;
    private const LOCALE_LANGUAGE_NAME_ROW = 2;
    private const ENGLISH_LANGUAGE_NAME_ROW = 3;

    private const ARGUMENT_SEPARATOR_ROW = 5;
    private const ERROR_CODES_FIRST_ROW = 8;

    private const FUNCTION_NAME_LIST_FIRST_ROW = 4;
    private const ENGLISH_FUNCTION_CATEGORIES_COLUMN = 'A';
    private const ENGLISH_REFERENCE_COLUMN = 'B';

    /**
     * @var string
     */
    protected $translationSpreadsheetName;

    /**
     * @var string
     */
    protected $translationBaseFolder;

    protected $phpSpreadsheetFunctions;

    /**
     * @var Spreadsheet
     */
    protected $translationSpreadsheet;

    /**
     * @var Worksheet
     */
    protected $localeTranslations;

    protected $localeLanguageMap = [];

    protected $errorCodeMap = [];

    /**
     * @var Worksheet
     */
    private $functionNameTranslations;

    protected $functionNameLanguageMap = [];

    protected $functionNameMap = [];

    public function __construct(
        string $translationBaseFolder,
        string $translationSpreadsheetName,
        array $phpSpreadsheetFunctions
    ) {
        $this->translationBaseFolder = $translationBaseFolder;
        $this->translationSpreadsheetName = $translationSpreadsheetName;
        $this->phpSpreadsheetFunctions = $phpSpreadsheetFunctions;
    }

    public function generateLocales(): void
    {
        $this->openTranslationWorkbook();

        $this->localeTranslations = $this->getTranslationSheet(self::EXCEL_LOCALISATION_WORKSHEET);
        $this->localeLanguageMap = $this->mapLanguageColumns($this->localeTranslations);
        $this->mapErrorCodeRows();

        $this->functionNameTranslations = $this->getTranslationSheet(self::EXCEL_FUNCTIONS_WORKSHEET);
        $this->functionNameLanguageMap = $this->mapLanguageColumns($this->functionNameTranslations);
        $this->mapFunctionNameRows();

        foreach ($this->localeLanguageMap as $column => $locale) {
            $this->buildConfigFileForLocale($column, $locale);
        }

        foreach ($this->functionNameLanguageMap as $column => $locale) {
            $this->buildFunctionsFileForLocale($column, $locale);
        }
    }

    protected function buildConfigFileForLocale($column, $locale): void
    {
        $language = $this->localeTranslations->getCell($column . self::ENGLISH_LANGUAGE_NAME_ROW)->getValue();
        $localeLanguage = $this->localeTranslations->getCell($column . self::LOCALE_LANGUAGE_NAME_ROW)->getValue();
        $configFile = $this->openConfigFile($locale, $language, $localeLanguage);

        $this->writeConfigArgumentSeparator($configFile, $column);
        $this->writeFileSectionHeader($configFile, 'Error Codes');

        foreach ($this->errorCodeMap as $errorCode => $row) {
            $translationCell = $this->localeTranslations->getCell($column . $row);
            $translationValue = $translationCell->getValue();
            if (!empty($translationValue)) {
                $errorCodeTranslation = "{$errorCode} = {$translationValue}" . PHP_EOL;
                fwrite($configFile, $errorCodeTranslation);
            } else {
                $errorCodeTranslation = "{$errorCode}" . PHP_EOL;
                fwrite($configFile, $errorCodeTranslation);
                echo "No {$language} translation available for error code {$errorCode}", PHP_EOL;
            }
        }

        fclose($configFile);
    }

    protected function writeConfigArgumentSeparator($configFile, $column): void
    {
        $translationCell = $this->localeTranslations->getCell($column . self::ARGUMENT_SEPARATOR_ROW);
        $localeValue = $translationCell->getValue();
        if (!empty($localeValue)) {
            $functionTranslation = "ArgumentSeparator = {$localeValue}" . PHP_EOL;
            fwrite($configFile, $functionTranslation);
        } else {
            echo 'No Argument Separator defined', PHP_EOL;
        }
    }

    protected function buildFunctionsFileForLocale($column, $locale): void
    {
        $language = $this->functionNameTranslations->getCell($column . self::ENGLISH_LANGUAGE_NAME_ROW)->getValue();
        $localeLanguage = $this->functionNameTranslations->getCell($column . self::LOCALE_LANGUAGE_NAME_ROW)
            ->getValue();
        $functionFile = $this->openFunctionNameFile($locale, $language, $localeLanguage);

        foreach ($this->functionNameMap as $functionName => $row) {
            $translationCell = $this->functionNameTranslations->getCell($column . $row);
            $translationValue = $translationCell->getValue();
            if ($this->isFunctionCategoryEntry($translationCell)) {
                $this->writeFileSectionHeader($functionFile, "{$translationValue} ({$functionName})");
            } elseif (!array_key_exists($functionName, $this->phpSpreadsheetFunctions)) {
                echo "Function {$functionName} is not defined in PhpSpreadsheet", PHP_EOL;
            } elseif (!empty($translationValue)) {
                $functionTranslation = "{$functionName} = {$translationValue}" . PHP_EOL;
                fwrite($functionFile, $functionTranslation);
            } else {
                echo "No {$language} translation available for function {$functionName}", PHP_EOL;
            }
        }

        fclose($functionFile);
    }

    protected function openConfigFile(string $locale, string $language, string $localeLanguage)
    {
        echo "Building locale {$locale} ($language) configuration", PHP_EOL;
        $localeFolder = $this->getLocaleFolder($locale);

        $configFileName = realpath($localeFolder . DIRECTORY_SEPARATOR . 'config');
        echo "Writing locale configuration to {$configFileName}", PHP_EOL;

        $configFile = fopen($configFileName, 'wb');
        $this->writeFileHeader($configFile, $localeLanguage, $language, 'locale settings');

        return $configFile;
    }

    protected function openFunctionNameFile(string $locale, string $language, string $localeLanguage)
    {
        echo "Building locale {$locale} ($language) function names", PHP_EOL;
        $localeFolder = $this->getLocaleFolder($locale);

        $functionFileName = realpath($localeFolder . DIRECTORY_SEPARATOR . 'functions');
        echo "Writing local function names to {$functionFileName}", PHP_EOL;

        $functionFile = fopen($functionFileName, 'wb');
        $this->writeFileHeader($functionFile, $localeLanguage, $language, 'function name translations');

        return $functionFile;
    }

    protected function getLocaleFolder(string $locale): string
    {
        $localeFolder = $this->translationBaseFolder .
            DIRECTORY_SEPARATOR .
            str_replace('_', DIRECTORY_SEPARATOR, $locale);
        if (!file_exists($localeFolder) || !is_dir($localeFolder)) {
            mkdir($localeFolder, 0777, true);
        }

        return $localeFolder;
    }

    protected function writeFileHeader($localeFile, string $localeLanguage, string $language, string $title): void
    {
        fwrite($localeFile, str_repeat('#', 60) . PHP_EOL);
        fwrite($localeFile, '##' . PHP_EOL);
        fwrite($localeFile, "## PhpSpreadsheet - {$title}" . PHP_EOL);
        fwrite($localeFile, '##' . PHP_EOL);
        fwrite($localeFile, "## {$localeLanguage} ({$language})" . PHP_EOL);
        fwrite($localeFile, '##' . PHP_EOL);
        fwrite($localeFile, str_repeat('#', 60) . PHP_EOL . PHP_EOL);
    }

    protected function writeFileSectionHeader($localeFile, string $header): void
    {
        fwrite($localeFile, PHP_EOL . '##' . PHP_EOL);
        fwrite($localeFile, "## {$header}" . PHP_EOL);
        fwrite($localeFile, '##' . PHP_EOL);
    }

    protected function openTranslationWorkbook(): void
    {
        $filepathName = $this->translationBaseFolder . '/' . $this->translationSpreadsheetName;
        $this->translationSpreadsheet = IOFactory::load($filepathName);
    }

    protected function getTranslationSheet(string $sheetName): Worksheet
    {
        $worksheet = $this->translationSpreadsheet->setActiveSheetIndexByName($sheetName);
        if ($worksheet === null) {
            throw new Exception("{$sheetName} Worksheet not found");
        }

        return $worksheet;
    }

    protected function mapLanguageColumns(Worksheet $translationWorksheet): array
    {
        $sheetName = $translationWorksheet->getTitle();
        echo "Mapping Languages for {$sheetName}:", PHP_EOL;

        $baseColumn = self::ENGLISH_REFERENCE_COLUMN;
        $languagesList = $translationWorksheet->getColumnIterator(++$baseColumn);

        $languageNameMap = [];
        foreach ($languagesList as $languageColumn) {
            /** @var Column $languageColumn */
            $cells = $languageColumn->getCellIterator(self::LOCALE_NAME_ROW, self::LOCALE_NAME_ROW);
            $cells->setIterateOnlyExistingCells(true);
            foreach ($cells as $cell) {
                /** @var Cell $cell */
                if ($this->localeCanBeSupported($translationWorksheet, $cell)) {
                    $languageNameMap[$cell->getColumn()] = $cell->getValue();
                    echo $cell->getColumn(), ' -> ', $cell->getValue(), PHP_EOL;
                }
            }
        }

        return $languageNameMap;
    }

    protected function localeCanBeSupported(Worksheet $worksheet, Cell $cell): bool
    {
        if ($worksheet->getTitle() === self::EXCEL_LOCALISATION_WORKSHEET) {
            // Only provide support for languages that have a function argument separator defined
            //      in the localisation worksheet
            return !empty(
                $worksheet->getCell($cell->getColumn() . self::ARGUMENT_SEPARATOR_ROW)->getValue()
            );
        }

        // If we're processing other worksheets, then language support is determined by whether we included the
        //      language in the map when we were processing the localisation worksheet (which is always processed first)
        return in_array($cell->getValue(), $this->localeLanguageMap, true);
    }

    protected function mapErrorCodeRows(): void
    {
        echo 'Mapping Error Codes:', PHP_EOL;
        $errorList = $this->localeTranslations->getRowIterator(self::ERROR_CODES_FIRST_ROW);

        foreach ($errorList as $errorRow) {
            /** @var Row $errorList */
            $cells = $errorRow->getCellIterator(self::ENGLISH_REFERENCE_COLUMN, self::ENGLISH_REFERENCE_COLUMN);
            $cells->setIterateOnlyExistingCells(true);
            foreach ($cells as $cell) {
                /** @var Cell $cell */
                if ($cell->getValue() != '') {
                    echo $cell->getRow(), ' -> ', $cell->getValue(), PHP_EOL;
                    $this->errorCodeMap[$cell->getValue()] = $cell->getRow();
                }
            }
        }
    }

    protected function mapFunctionNameRows(): void
    {
        echo 'Mapping Functions:', PHP_EOL;
        $functionList = $this->functionNameTranslations->getRowIterator(self::FUNCTION_NAME_LIST_FIRST_ROW);

        foreach ($functionList as $functionRow) {
            /** @var Row $functionRow */
            $cells = $functionRow->getCellIterator(self::ENGLISH_REFERENCE_COLUMN, self::ENGLISH_REFERENCE_COLUMN);
            $cells->setIterateOnlyExistingCells(true);
            foreach ($cells as $cell) {
                /** @var Cell $cell */
                if ($this->isFunctionCategoryEntry($cell)) {
                    if (!empty($cell->getValue())) {
                        echo 'CATEGORY: ', $cell->getValue(), PHP_EOL;
                        $this->functionNameMap[$cell->getValue()] = $cell->getRow();
                    }

                    continue;
                }
                if ($cell->getValue() != '') {
                    if (is_bool($cell->getValue())) {
                        echo $cell->getRow(), ' -> ', ($cell->getValue() ? 'TRUE' : 'FALSE'), PHP_EOL;
                        $this->functionNameMap[($cell->getValue() ? 'TRUE' : 'FALSE')] = $cell->getRow();
                    } else {
                        echo $cell->getRow(), ' -> ', $cell->getValue(), PHP_EOL;
                        $this->functionNameMap[$cell->getValue()] = $cell->getRow();
                    }
                }
            }
        }
    }

    private function isFunctionCategoryEntry(Cell $cell): bool
    {
        $categoryCheckCell = self::ENGLISH_FUNCTION_CATEGORIES_COLUMN . $cell->getRow();
        if ($this->functionNameTranslations->getCell($categoryCheckCell)->getValue() != '') {
            return true;
        }

        return false;
    }
}
