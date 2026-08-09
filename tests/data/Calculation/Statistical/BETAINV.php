<?php

declare(strict_types=1);

return [
    [
        1.862243320728,
        0.52, 3, 4, 1, 3,
    ],
    [
        2.164759759129,
        0.3, 7.5, 9, 1, 4,
    ],
    [
        2.164759759129,
        0.3, 7.5, 9, 4, 1,
    ],
    [
        7.761240188783,
        0.75, 8, 9, 5, 10,
    ],
    [
        2.0,
        0.685470581055, 8, 10, 1, 3,
    ],
    [
        0.303225844664,
        0.2, 4, 5, 0, 1,
    ],
    [
        0.303225844664,
        0.2, 4, 5, null, null,
    ],
    [
        '#VALUE!',
        'NAN', 4, 5, 0, 1,
    ],
    [
        '#VALUE!',
        0.2, 'NAN', 5, 0, 1,
    ],
    [
        '#VALUE!',
        0.2, 4, 'NAN', 0, 1,
    ],
    [
        '#VALUE!',
        0.2, 4, 5, 'NAN', 1,
    ],
    [
        '#VALUE!',
        0.2, 4, 5, 0, 'NAN',
    ],
    // Large shape parameters: the CDF underflows to 0 at the low probes of the
    // bisection long before the root is reached. Onset is alpha ~ 1080 for beta = 1.
    'symmetric, median' => [
        0.5,
        0.5, 5000, 5000, 0, 1,
    ],
    'symmetric, upper quartile' => [
        0.503372494704,
        0.75, 5000, 5000, 0, 1,
    ],
    'symmetric, lower decile' => [
        0.493592345088,
        0.1, 5000, 5000, 0, 1,
    ],
    'symmetric, median, alpha = beta = 20000' => [
        0.5,
        0.5, 20000, 20000, 0, 1,
    ],
    'symmetric, scaled to [1, 3]' => [
        2.006744989408,
        0.75, 5000, 5000, 1, 3,
    ],
    'beta = 1, closed form 0.5 ** (1 / alpha)' => [
        0.999861380173,
        0.5, 5000, 1, 0, 1,
    ],
    'beta = 1, just above the onset' => [
        0.999370064692,
        0.5, 1100, 1, 0, 1,
    ],
    'right-skewed, alpha >> beta' => [
        0.999866312606,
        0.5, 20000, 3, 0, 1,
    ],
    'left-skewed, beta >> alpha' => [
        0.000133687394,
        0.5, 3, 20000, 0, 1,
    ],
    'moderately large alpha' => [
        0.973403556565,
        0.25, 2000, 50, 0, 1,
    ],
    'alpha so large that the quantile rounds to 1' => [
        1.0,
        0.5, 1.0e20, 3, 0, 1,
    ],
    'huge symmetric shapes still give the median' => [
        0.5,
        0.5, 1.0e20, 1.0e20, 0, 1,
    ],
    'alpha + beta beyond the range incompleteBeta will evaluate' => [
        '#NUM!',
        0.5, 2.0e305, 1.0e305, 0, 1,
    ],
    'alpha < 0' => [
        '#NUM!',
        0.2, -4, 5, 0, 1,
    ],
    'alpha = 0' => [
        '#NUM!',
        0.2, 0, 5, 0, 1,
    ],
    'beta < 0' => [
        '#NUM!',
        0.2, 4, -5, 0, 1,
    ],
    'beta = 0' => [
        '#NUM!',
        0.2, 4, 0, 0, 1,
    ],
    'Probability < 0' => [
        '#NUM!',
        -0.5, 4, 5, 1, 3,
    ],
    'Probability = 0' => [
        '#NUM!',
        0.0, 4, 5, 1, 3,
    ],
    'Probability > 1' => [
        '#NUM!',
        1.5, 4, 5, 1, 3,
    ],
    'Min = Max' => [
        '#NUM!',
        1, 4, 5, 1, 1,
    ],
];
