<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class HyperGeometric
{
    use BaseValidations;

    /**
     * HYPGEOMDIST.
     *
     * Returns the hypergeometric distribution. HYPGEOMDIST returns the probability of a given number of
     * sample successes, given the sample size, population successes, and population size.
     *
     * @param mixed $sampleSuccesses Integer number of successes in the sample
     * @param mixed $sampleNumber Integer size of the sample
     * @param mixed $populationSuccesses Integer number of successes in the population
     * @param mixed $populationNumber Integer population size
     *
     * @return float|string
     */
    public static function distribution($sampleSuccesses, $sampleNumber, $populationSuccesses, $populationNumber)
    {
        $sampleSuccesses = Functions::flattenSingleValue($sampleSuccesses);
        $sampleNumber = Functions::flattenSingleValue($sampleNumber);
        $populationSuccesses = Functions::flattenSingleValue($populationSuccesses);
        $populationNumber = Functions::flattenSingleValue($populationNumber);

        try {
            $sampleSuccesses = self::validateInt($sampleSuccesses);
            $sampleNumber = self::validateInt($sampleNumber);
            $populationSuccesses = self::validateInt($populationSuccesses);
            $populationNumber = self::validateInt($populationNumber);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($sampleSuccesses < 0) || ($sampleSuccesses > $sampleNumber) || ($sampleSuccesses > $populationSuccesses)) {
            return Functions::NAN();
        }
        if (($sampleNumber <= 0) || ($sampleNumber > $populationNumber)) {
            return Functions::NAN();
        }
        if (($populationSuccesses <= 0) || ($populationSuccesses > $populationNumber)) {
            return Functions::NAN();
        }

        return MathTrig::COMBIN($populationSuccesses, $sampleSuccesses) *
            MathTrig::COMBIN($populationNumber - $populationSuccesses, $sampleNumber - $sampleSuccesses) /
            MathTrig::COMBIN($populationNumber, $sampleNumber);
    }
}
