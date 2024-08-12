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
    private const CURRENCY_SYMBOL_ROW = 19;

    private const FUNCTION_NAME_LIST_FIRST_ROW = 4;
    private const ENGLISH_FUNCTION_CATEGORIES_COLUMN = 'A';
    private const ENGLISH_REFERENCE_COLUMN = 'B';
    private const EOL = "\n"; // not PHP_EOL

    protected string $translationSpreadsheetName;

    protected string $translationBaseFolder;

    protected array $phpSpreadsheetFunctions;

    protected Spreadsheet $translationSpreadsheet;

    protected bool $verbose;

    protected Worksheet $localeTranslations;

    protected array $localeLanguageMap = [];

    protected array $errorCodeMap = [];

    private Worksheet $functionNameTranslations;

    protected array $functionNameLanguageMap = [];

    protected array $functionNameMap = [];

    public function __construct(
        string $translationBaseFolder,
        string $translationSpreadsheetName,
        array $phpSpreadsheetFunctions,
        bool $verbose = false
    ) {
        $this->translationBaseFolder = $translationBaseFolder;
        $this->translationSpreadsheetName = $translationSpreadsheetName;
        $this->phpSpreadsheetFunctions = $phpSpreadsheetFunctions;
        $this->verbose = $verbose;
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

    protected function buildConfigFileForLocale(string $column, string $locale): void
    {
        $language = $this->localeTranslations->getCell($column . self::ENGLISH_LANGUAGE_NAME_ROW)->getValue();
        if (!is_string($language)) {
            throw new Exception('Non-string language value at ' . $column . self::ENGLISH_LANGUAGE_NAME_ROW);
        }
        $localeLanguage = $this->localeTranslations->getCell($column . self::LOCALE_LANGUAGE_NAME_ROW)->getValue();
        if (!is_string($localeLanguage)) {
            throw new Exception('Non-string locale language value at ' . $column . self::LOCALE_LANGUAGE_NAME_ROW);
        }
        $configFile = $this->openConfigFile($locale, $language, $localeLanguage);

        $this->writeConfigArgumentSeparator($configFile, $column);
        $this->writeConfigCurrencySymbol($configFile, $column);
        $this->writeFileSectionHeader($configFile, 'Error Codes');

        foreach ($this->errorCodeMap as $errorCode => $row) {
            $translationCell = $this->localeTranslations->getCell($column . $row);
            $translationValue = $translationCell->getValue();
            if ($translationValue !== null && !is_string($translationValue)) {
                throw new Exception('Non-string translation value at ' . $column . $row);
            }
            if (!empty($translationValue)) {
                $errorCodeTranslation = "{$errorCode} = {$translationValue}" . self::EOL;
                fwrite($configFile, $errorCodeTranslation);
            } else {
                $errorCodeTranslation = "{$errorCode}" . self::EOL;
                fwrite($configFile, $errorCodeTranslation);
                $this->log("No {$language} translation available for error code {$errorCode}");
            }
        }

        fclose($configFile);
    }

    /** @param resource $configFile resource to write to */
    protected function writeConfigArgumentSeparator($configFile, string $column): void
    {
        $translationCell = $this->localeTranslations->getCell($column . self::ARGUMENT_SEPARATOR_ROW);
        $localeValue = $translationCell->getValue();
        if ($localeValue !== null && !is_string($localeValue)) {
            throw new Exception('Non-string locale value at ' . $column . self::CURRENCY_SYMBOL_ROW);
        }
        if (!empty($localeValue)) {
            $functionTranslation = "ArgumentSeparator = {$localeValue}" . self::EOL;
            fwrite($configFile, $functionTranslation);
        } else {
            $this->log('No Argument Separator defined');
        }
    }

    /** @param resource $configFile resource to write to */
    protected function writeConfigCurrencySymbol($configFile, string $column): void
    {
        $translationCell = $this->localeTranslations->getCell($column . self::CURRENCY_SYMBOL_ROW);
        $localeValue = $translationCell->getValue();
        if ($localeValue !== null && !is_string($localeValue)) {
            throw new Exception('Non-string locale value at ' . $column . self::CURRENCY_SYMBOL_ROW);
        }
        if (!empty($localeValue)) {
            $functionTranslation = "currencySymbol = {$localeValue}" . self::EOL;
            fwrite($configFile, '##' . self::EOL);
            fwrite($configFile, '##  (For future use)' . self::EOL);
            fwrite($configFile, '##' . self::EOL);
            fwrite($configFile, $functionTranslation);
        } else {
            $this->log('No Currency Symbol defined');
        }
    }

    protected function buildFunctionsFileForLocale(string $column, string $locale): void
    {
        $language = $this->functionNameTranslations->getCell($column . self::ENGLISH_LANGUAGE_NAME_ROW)->getValue();
        if (!is_string($language)) {
            throw new Exception('Non-string language value at ' . $column . self::ENGLISH_LANGUAGE_NAME_ROW);
        }
        $localeLanguage = $this->functionNameTranslations->getCell($column . self::LOCALE_LANGUAGE_NAME_ROW)
            ->getValue();
        if (!is_string($localeLanguage)) {
            throw new Exception('Non-string locale language value at ' . $column . self::LOCALE_LANGUAGE_NAME_ROW);
        }
        $functionFile = $this->openFunctionNameFile($locale, $language, $localeLanguage);

        foreach ($this->functionNameMap as $functionName => $row) {
            $translationCell = $this->functionNameTranslations->getCell($column . $row);
            $translationValue = $translationCell->getValue();
            if ($translationValue !== null && !is_string($translationValue)) {
                throw new Exception('Non-string translation value at ' . $column . $row);
            }
            if ($this->isFunctionCategoryEntry($translationCell)) {
                $this->writeFileSectionHeader($functionFile, "{$translationValue} ({$functionName})");
            } elseif (!array_key_exists($functionName, $this->phpSpreadsheetFunctions) && substr($functionName, 0, 1) !== '*') {
                $this->log("Function {$functionName} is not defined in PhpSpreadsheet");
            } elseif (!empty($translationValue)) {
                $functionTranslation = "{$functionName} = {$translationValue}" . self::EOL;
                fwrite($functionFile, $functionTranslation);
            } else {
                $this->log("No {$language} translation available for function {$functionName}");
            }
        }

        fclose($functionFile);
    }

    /** @return resource used by other methods in this class */
    protected function openConfigFile(string $locale, string $language, string $localeLanguage)
    {
        $this->log("Building locale {$locale} ($language) configuration");
        $localeFolder = $this->getLocaleFolder($locale);

        $configFileName = realpath($localeFolder) . DIRECTORY_SEPARATOR . 'config';
        $this->log("Writing locale configuration to {$configFileName}");

        $configFile = fopen($configFileName, 'wb');
        if ($configFile === false) {
            throw new Exception('Unable to open $configFileName for write');
        }
        $this->writeFileHeader($configFile, $localeLanguage, $language, 'locale settings');

        return $configFile;
    }

    /** @return resource used by other methods in this class */
    protected function openFunctionNameFile(string $locale, string $language, string $localeLanguage)
    {
        $this->log("Building locale {$locale} ($language) function names");
        $localeFolder = $this->getLocaleFolder($locale);

        $functionFileName = realpath($localeFolder) . DIRECTORY_SEPARATOR . 'functions';
        $this->log("Writing local function names to {$functionFileName}");

        $functionFile = fopen($functionFileName, 'wb');
        if ($functionFile === false) {
            throw new Exception('Unable to open $functionFileName for write');
        }
        $this->writeFileHeader($functionFile, $localeLanguage, $language, 'function name translations');

        return $functionFile;
    }

    protected function getLocaleFolder(string $locale): string
    {
        $lastchar = substr($this->translationBaseFolder, -1);
        $dirsep = ($lastchar === '/' || $lastchar === '\\') ? '' : DIRECTORY_SEPARATOR;
        $localeFolder = $this->translationBaseFolder
            . $dirsep
            . str_replace('_', DIRECTORY_SEPARATOR, $locale);
        if (!file_exists($localeFolder) || !is_dir($localeFolder)) {
            mkdir($localeFolder, 7 * 64 + 7 * 8 + 7, true); // octal 777
        }

        return $localeFolder;
    }

    /** @param resource $localeFile file being written to */
    protected function writeFileHeader($localeFile, string $localeLanguage, string $language, string $title): void
    {
        fwrite($localeFile, str_repeat('#', 60) . self::EOL);
        fwrite($localeFile, '##' . self::EOL);
        fwrite($localeFile, "## PhpSpreadsheet - {$title}" . self::EOL);
        fwrite($localeFile, '##' . self::EOL);
        fwrite($localeFile, "## {$localeLanguage} ({$language})" . self::EOL);
        fwrite($localeFile, '##' . self::EOL);
        fwrite($localeFile, str_repeat('#', 60) . self::EOL . self::EOL);
    }

    /** @param resource $localeFile file being written to */
    protected function writeFileSectionHeader($localeFile, string $header): void
    {
        fwrite($localeFile, self::EOL . '##' . self::EOL);
        fwrite($localeFile, "## {$header}" . self::EOL);
        fwrite($localeFile, '##' . self::EOL);
    }

    protected function openTranslationWorkbook(): void
    {
        $filepathName = $this->translationBaseFolder . '/' . $this->translationSpreadsheetName;
        $this->translationSpreadsheet = IOFactory::load($filepathName);
    }

    protected function getTranslationSheet(string $sheetName): Worksheet
    {
        $worksheet = $this->translationSpreadsheet->setActiveSheetIndexByName($sheetName);

        return $worksheet;
    }

    protected function mapLanguageColumns(Worksheet $translationWorksheet): array
    {
        $sheetName = $translationWorksheet->getTitle();
        $this->log("Mapping Languages for {$sheetName}:");

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
                    $this->log($cell->getColumn() . ' -> ' . $cell->getValue());
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
        $this->log('Mapping Error Codes:');
        $errorList = $this->localeTranslations->getRowIterator(self::ERROR_CODES_FIRST_ROW);

        foreach ($errorList as $errorRow) {
            /** @var Row $errorList */
            $cells = $errorRow->getCellIterator(self::ENGLISH_REFERENCE_COLUMN, self::ENGLISH_REFERENCE_COLUMN);
            $cells->setIterateOnlyExistingCells(true);
            foreach ($cells as $cell) {
                /** @var Cell $cell */
                if ($cell->getValue() != '') {
                    $this->log($cell->getRow() . ' -> ' . $cell->getValue());
                    $this->errorCodeMap[$cell->getValue()] = $cell->getRow();
                }
            }
        }
    }

    protected function mapFunctionNameRows(): void
    {
        $this->log('Mapping Functions:');
        $functionList = $this->functionNameTranslations->getRowIterator(self::FUNCTION_NAME_LIST_FIRST_ROW);

        foreach ($functionList as $functionRow) {
            /** @var Row $functionRow */
            $cells = $functionRow->getCellIterator(self::ENGLISH_REFERENCE_COLUMN, self::ENGLISH_REFERENCE_COLUMN);
            $cells->setIterateOnlyExistingCells(true);
            foreach ($cells as $cell) {
                /** @var Cell $cell */
                if ($this->isFunctionCategoryEntry($cell)) {
                    if (!empty($cell->getValue())) {
                        $this->log('CATEGORY: ' . $cell->getValue());
                        $this->functionNameMap[$cell->getValue()] = $cell->getRow();
                    }

                    continue;
                }
                if ($cell->getValue() != '') {
                    if (is_bool($cell->getValue())) {
                        $this->log($cell->getRow() . ' -> ' . ($cell->getValue() ? 'TRUE' : 'FALSE'));
                        $this->functionNameMap[($cell->getValue() ? 'TRUE' : 'FALSE')] = $cell->getRow();
                    } else {
                        $this->log($cell->getRow() . ' -> ' . $cell->getValue());
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

    private function log(string $message): void
    {
        if ($this->verbose === false) {
            return;
        }

        echo $message, self::EOL;
    }
}
