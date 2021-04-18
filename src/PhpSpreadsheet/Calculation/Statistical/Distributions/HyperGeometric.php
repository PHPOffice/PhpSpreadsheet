<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Combinations;

class HyperGeometric
{
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
            $sampleSuccesses = DistributionValidations::validateInt($sampleSuccesses);
            $sampleNumber = DistributionValidations::validateInt($sampleNumber);
            $populationSuccesses = DistributionValidations::validateInt($populationSuccesses);
            $populationNumber = DistributionValidations::validateInt($populationNumber);
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

        $successesPopulationAndSample = (float) Combinations::withoutRepetition($populationSuccesses, $sampleSuccesses);
        $numbersPopulationAndSample = (float) Combinations::withoutRepetition($populationNumber, $sampleNumber);
        $adjustedPopulationAndSample = (float) Combinations::withoutRepetition(
            $populationNumber - $populationSuccesses,
            $sampleNumber - $sampleSuccesses
        );

        return $successesPopulationAndSample * $adjustedPopulationAndSample / $numbersPopulationAndSample;
    }
}
