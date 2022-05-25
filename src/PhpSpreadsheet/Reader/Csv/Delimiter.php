<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Csv;

class Delimiter
{
    protected const POTENTIAL_DELIMETERS = [',', ';', "\t", '|', ':', ' ', '~'];

    /** @var resource */
    protected $fileHandle;

    /** @var string */
    protected $escapeCharacter;

    /** @var string */
    protected $enclosure;

    /** @var array */
    protected $counts = [];

    /** @var int */
    protected $numberLines = 0;

    /** @var ?string */
    protected $delimiter;

    /**
     * @param resource $fileHandle
     */
    public function __construct($fileHandle, string $escapeCharacter, string $enclosure)
    {
        $this->fileHandle = $fileHandle;
        $this->escapeCharacter = $escapeCharacter;
        $this->enclosure = $enclosure;

        $this->countPotentialDelimiters();
    }

    public function getDefaultDelimiter(): string
    {
        return self::POTENTIAL_DELIMETERS[0];
    }

    public function linesCounted(): int
    {
        return $this->numberLines;
    }

    protected function countPotentialDelimiters(): void
    {
        $this->counts = array_fill_keys(self::POTENTIAL_DELIMETERS, []);
        $delimiterKeys = array_flip(self::POTENTIAL_DELIMETERS);

        // Count how many times each of the potential delimiters appears in each line
        $this->numberLines = 0;
        while (($line = $this->getNextLine()) !== false && (++$this->numberLines < 1000)) {
            $this->countDelimiterValues($line, $delimiterKeys);
        }
    }

    protected function countDelimiterValues(string $line, array $delimiterKeys): void
    {
        $splitString = str_split($line, 1);
        if (is_array($splitString)) {
            $distribution = array_count_values($splitString);
            $countLine = array_intersect_key($distribution, $delimiterKeys);

            foreach (self::POTENTIAL_DELIMETERS as $delimiter) {
                $this->counts[$delimiter][] = $countLine[$delimiter] ?? 0;
            }
        }
    }

    public function infer(): ?string
    {
        // Calculate the mean square deviations for each delimiter
        //     (ignoring delimiters that haven't been found consistently)
        $meanSquareDeviations = [];
        $middleIdx = floor(($this->numberLines - 1) / 2);

        foreach (self::POTENTIAL_DELIMETERS as $delimiter) {
            $series = $this->counts[$delimiter];
            sort($series);

            $median = ($this->numberLines % 2)
                ? $series[$middleIdx]
                : ($series[$middleIdx] + $series[$middleIdx + 1]) / 2;

            if ($median === 0) {
                continue;
            }

            $meanSquareDeviations[$delimiter] = array_reduce(
                $series,
                function ($sum, $value) use ($median) {
                    return $sum + ($value - $median) ** 2;
                }
            ) / count($series);
        }

        // ... and pick the delimiter with the smallest mean square deviation
        //         (in case of ties, the order in potentialDelimiters is respected)
        $min = INF;
        foreach (self::POTENTIAL_DELIMETERS as $delimiter) {
            if (!isset($meanSquareDeviations[$delimiter])) {
                continue;
            }

            if ($meanSquareDeviations[$delimiter] < $min) {
                $min = $meanSquareDeviations[$delimiter];
                $this->delimiter = $delimiter;
            }
        }

        return $this->delimiter;
    }

    /**
     * Get the next full line from the file.
     *
     * @return false|string
     */
    public function getNextLine()
    {
        $line = '';
        $enclosure = ($this->escapeCharacter === '' ? ''
                : ('(?<!' . preg_quote($this->escapeCharacter, '/') . ')'))
            . preg_quote($this->enclosure, '/');

        do {
            // Get the next line in the file
            $newLine = fgets($this->fileHandle);

            // Return false if there is no next line
            if ($newLine === false) {
                return false;
            }

            // Add the new line to the line passed in
            $line = $line . $newLine;

            // Drop everything that is enclosed to avoid counting false positives in enclosures
            $line = (string) preg_replace('/(' . $enclosure . '.*' . $enclosure . ')/Us', '', $line);

            // See if we have any enclosures left in the line
            // if we still have an enclosure then we need to read the next line as well
        } while (preg_match('/(' . $enclosure . ')/', $line) > 0);

        return ($line !== '') ? $line : false;
    }
}
