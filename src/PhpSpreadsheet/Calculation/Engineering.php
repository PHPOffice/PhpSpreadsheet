<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use Complex\Complex;
use Complex\Exception as ComplexException;

class Engineering
{
    /**
     * EULER.
     */
    const EULER = 2.71828182845904523536;

    /**
     * Details of the Units of measure that can be used in CONVERTUOM().
     *
     * @var mixed[]
     */
    private static $conversionUnits = [
        'g' => ['Group' => 'Mass', 'Unit Name' => 'Gram', 'AllowPrefix' => true],
        'sg' => ['Group' => 'Mass', 'Unit Name' => 'Slug', 'AllowPrefix' => false],
        'lbm' => ['Group' => 'Mass', 'Unit Name' => 'Pound mass (avoirdupois)', 'AllowPrefix' => false],
        'u' => ['Group' => 'Mass', 'Unit Name' => 'U (atomic mass unit)', 'AllowPrefix' => true],
        'ozm' => ['Group' => 'Mass', 'Unit Name' => 'Ounce mass (avoirdupois)', 'AllowPrefix' => false],
        'm' => ['Group' => 'Distance', 'Unit Name' => 'Meter', 'AllowPrefix' => true],
        'mi' => ['Group' => 'Distance', 'Unit Name' => 'Statute mile', 'AllowPrefix' => false],
        'Nmi' => ['Group' => 'Distance', 'Unit Name' => 'Nautical mile', 'AllowPrefix' => false],
        'in' => ['Group' => 'Distance', 'Unit Name' => 'Inch', 'AllowPrefix' => false],
        'ft' => ['Group' => 'Distance', 'Unit Name' => 'Foot', 'AllowPrefix' => false],
        'yd' => ['Group' => 'Distance', 'Unit Name' => 'Yard', 'AllowPrefix' => false],
        'ang' => ['Group' => 'Distance', 'Unit Name' => 'Angstrom', 'AllowPrefix' => true],
        'Pica' => ['Group' => 'Distance', 'Unit Name' => 'Pica (1/72 in)', 'AllowPrefix' => false],
        'yr' => ['Group' => 'Time', 'Unit Name' => 'Year', 'AllowPrefix' => false],
        'day' => ['Group' => 'Time', 'Unit Name' => 'Day', 'AllowPrefix' => false],
        'hr' => ['Group' => 'Time', 'Unit Name' => 'Hour', 'AllowPrefix' => false],
        'mn' => ['Group' => 'Time', 'Unit Name' => 'Minute', 'AllowPrefix' => false],
        'sec' => ['Group' => 'Time', 'Unit Name' => 'Second', 'AllowPrefix' => true],
        'Pa' => ['Group' => 'Pressure', 'Unit Name' => 'Pascal', 'AllowPrefix' => true],
        'p' => ['Group' => 'Pressure', 'Unit Name' => 'Pascal', 'AllowPrefix' => true],
        'atm' => ['Group' => 'Pressure', 'Unit Name' => 'Atmosphere', 'AllowPrefix' => true],
        'at' => ['Group' => 'Pressure', 'Unit Name' => 'Atmosphere', 'AllowPrefix' => true],
        'mmHg' => ['Group' => 'Pressure', 'Unit Name' => 'mm of Mercury', 'AllowPrefix' => true],
        'N' => ['Group' => 'Force', 'Unit Name' => 'Newton', 'AllowPrefix' => true],
        'dyn' => ['Group' => 'Force', 'Unit Name' => 'Dyne', 'AllowPrefix' => true],
        'dy' => ['Group' => 'Force', 'Unit Name' => 'Dyne', 'AllowPrefix' => true],
        'lbf' => ['Group' => 'Force', 'Unit Name' => 'Pound force', 'AllowPrefix' => false],
        'J' => ['Group' => 'Energy', 'Unit Name' => 'Joule', 'AllowPrefix' => true],
        'e' => ['Group' => 'Energy', 'Unit Name' => 'Erg', 'AllowPrefix' => true],
        'c' => ['Group' => 'Energy', 'Unit Name' => 'Thermodynamic calorie', 'AllowPrefix' => true],
        'cal' => ['Group' => 'Energy', 'Unit Name' => 'IT calorie', 'AllowPrefix' => true],
        'eV' => ['Group' => 'Energy', 'Unit Name' => 'Electron volt', 'AllowPrefix' => true],
        'ev' => ['Group' => 'Energy', 'Unit Name' => 'Electron volt', 'AllowPrefix' => true],
        'HPh' => ['Group' => 'Energy', 'Unit Name' => 'Horsepower-hour', 'AllowPrefix' => false],
        'hh' => ['Group' => 'Energy', 'Unit Name' => 'Horsepower-hour', 'AllowPrefix' => false],
        'Wh' => ['Group' => 'Energy', 'Unit Name' => 'Watt-hour', 'AllowPrefix' => true],
        'wh' => ['Group' => 'Energy', 'Unit Name' => 'Watt-hour', 'AllowPrefix' => true],
        'flb' => ['Group' => 'Energy', 'Unit Name' => 'Foot-pound', 'AllowPrefix' => false],
        'BTU' => ['Group' => 'Energy', 'Unit Name' => 'BTU', 'AllowPrefix' => false],
        'btu' => ['Group' => 'Energy', 'Unit Name' => 'BTU', 'AllowPrefix' => false],
        'HP' => ['Group' => 'Power', 'Unit Name' => 'Horsepower', 'AllowPrefix' => false],
        'h' => ['Group' => 'Power', 'Unit Name' => 'Horsepower', 'AllowPrefix' => false],
        'W' => ['Group' => 'Power', 'Unit Name' => 'Watt', 'AllowPrefix' => true],
        'w' => ['Group' => 'Power', 'Unit Name' => 'Watt', 'AllowPrefix' => true],
        'T' => ['Group' => 'Magnetism', 'Unit Name' => 'Tesla', 'AllowPrefix' => true],
        'ga' => ['Group' => 'Magnetism', 'Unit Name' => 'Gauss', 'AllowPrefix' => true],
        'C' => ['Group' => 'Temperature', 'Unit Name' => 'Celsius', 'AllowPrefix' => false],
        'cel' => ['Group' => 'Temperature', 'Unit Name' => 'Celsius', 'AllowPrefix' => false],
        'F' => ['Group' => 'Temperature', 'Unit Name' => 'Fahrenheit', 'AllowPrefix' => false],
        'fah' => ['Group' => 'Temperature', 'Unit Name' => 'Fahrenheit', 'AllowPrefix' => false],
        'K' => ['Group' => 'Temperature', 'Unit Name' => 'Kelvin', 'AllowPrefix' => false],
        'kel' => ['Group' => 'Temperature', 'Unit Name' => 'Kelvin', 'AllowPrefix' => false],
        'tsp' => ['Group' => 'Liquid', 'Unit Name' => 'Teaspoon', 'AllowPrefix' => false],
        'tbs' => ['Group' => 'Liquid', 'Unit Name' => 'Tablespoon', 'AllowPrefix' => false],
        'oz' => ['Group' => 'Liquid', 'Unit Name' => 'Fluid Ounce', 'AllowPrefix' => false],
        'cup' => ['Group' => 'Liquid', 'Unit Name' => 'Cup', 'AllowPrefix' => false],
        'pt' => ['Group' => 'Liquid', 'Unit Name' => 'U.S. Pint', 'AllowPrefix' => false],
        'us_pt' => ['Group' => 'Liquid', 'Unit Name' => 'U.S. Pint', 'AllowPrefix' => false],
        'uk_pt' => ['Group' => 'Liquid', 'Unit Name' => 'U.K. Pint', 'AllowPrefix' => false],
        'qt' => ['Group' => 'Liquid', 'Unit Name' => 'Quart', 'AllowPrefix' => false],
        'gal' => ['Group' => 'Liquid', 'Unit Name' => 'Gallon', 'AllowPrefix' => false],
        'l' => ['Group' => 'Liquid', 'Unit Name' => 'Litre', 'AllowPrefix' => true],
        'lt' => ['Group' => 'Liquid', 'Unit Name' => 'Litre', 'AllowPrefix' => true],
    ];

    /**
     * Details of the Multiplier prefixes that can be used with Units of Measure in CONVERTUOM().
     *
     * @var mixed[]
     */
    private static $conversionMultipliers = [
        'Y' => ['multiplier' => 1E24, 'name' => 'yotta'],
        'Z' => ['multiplier' => 1E21, 'name' => 'zetta'],
        'E' => ['multiplier' => 1E18, 'name' => 'exa'],
        'P' => ['multiplier' => 1E15, 'name' => 'peta'],
        'T' => ['multiplier' => 1E12, 'name' => 'tera'],
        'G' => ['multiplier' => 1E9, 'name' => 'giga'],
        'M' => ['multiplier' => 1E6, 'name' => 'mega'],
        'k' => ['multiplier' => 1E3, 'name' => 'kilo'],
        'h' => ['multiplier' => 1E2, 'name' => 'hecto'],
        'e' => ['multiplier' => 1E1, 'name' => 'deka'],
        'd' => ['multiplier' => 1E-1, 'name' => 'deci'],
        'c' => ['multiplier' => 1E-2, 'name' => 'centi'],
        'm' => ['multiplier' => 1E-3, 'name' => 'milli'],
        'u' => ['multiplier' => 1E-6, 'name' => 'micro'],
        'n' => ['multiplier' => 1E-9, 'name' => 'nano'],
        'p' => ['multiplier' => 1E-12, 'name' => 'pico'],
        'f' => ['multiplier' => 1E-15, 'name' => 'femto'],
        'a' => ['multiplier' => 1E-18, 'name' => 'atto'],
        'z' => ['multiplier' => 1E-21, 'name' => 'zepto'],
        'y' => ['multiplier' => 1E-24, 'name' => 'yocto'],
    ];

    /**
     * Details of the Units of measure conversion factors, organised by group.
     *
     * @var mixed[]
     */
    private static $unitConversions = [
        'Mass' => [
            'g' => [
                'g' => 1.0,
                'sg' => 6.85220500053478E-05,
                'lbm' => 2.20462291469134E-03,
                'u' => 6.02217000000000E+23,
                'ozm' => 3.52739718003627E-02,
            ],
            'sg' => [
                'g' => 1.45938424189287E+04,
                'sg' => 1.0,
                'lbm' => 3.21739194101647E+01,
                'u' => 8.78866000000000E+27,
                'ozm' => 5.14782785944229E+02,
            ],
            'lbm' => [
                'g' => 4.5359230974881148E+02,
                'sg' => 3.10810749306493E-02,
                'lbm' => 1.0,
                'u' => 2.73161000000000E+26,
                'ozm' => 1.60000023429410E+01,
            ],
            'u' => [
                'g' => 1.66053100460465E-24,
                'sg' => 1.13782988532950E-28,
                'lbm' => 3.66084470330684E-27,
                'u' => 1.0,
                'ozm' => 5.85735238300524E-26,
            ],
            'ozm' => [
                'g' => 2.83495152079732E+01,
                'sg' => 1.94256689870811E-03,
                'lbm' => 6.24999908478882E-02,
                'u' => 1.70725600000000E+25,
                'ozm' => 1.0,
            ],
        ],
        'Distance' => [
            'm' => [
                'm' => 1.0,
                'mi' => 6.21371192237334E-04,
                'Nmi' => 5.39956803455724E-04,
                'in' => 3.93700787401575E+01,
                'ft' => 3.28083989501312E+00,
                'yd' => 1.09361329797891E+00,
                'ang' => 1.00000000000000E+10,
                'Pica' => 2.83464566929116E+03,
            ],
            'mi' => [
                'm' => 1.60934400000000E+03,
                'mi' => 1.0,
                'Nmi' => 8.68976241900648E-01,
                'in' => 6.33600000000000E+04,
                'ft' => 5.28000000000000E+03,
                'yd' => 1.76000000000000E+03,
                'ang' => 1.60934400000000E+13,
                'Pica' => 4.56191999999971E+06,
            ],
            'Nmi' => [
                'm' => 1.85200000000000E+03,
                'mi' => 1.15077944802354E+00,
                'Nmi' => 1.0,
                'in' => 7.29133858267717E+04,
                'ft' => 6.07611548556430E+03,
                'yd' => 2.02537182785694E+03,
                'ang' => 1.85200000000000E+13,
                'Pica' => 5.24976377952723E+06,
            ],
            'in' => [
                'm' => 2.54000000000000E-02,
                'mi' => 1.57828282828283E-05,
                'Nmi' => 1.37149028077754E-05,
                'in' => 1.0,
                'ft' => 8.33333333333333E-02,
                'yd' => 2.77777777686643E-02,
                'ang' => 2.54000000000000E+08,
                'Pica' => 7.19999999999955E+01,
            ],
            'ft' => [
                'm' => 3.04800000000000E-01,
                'mi' => 1.89393939393939E-04,
                'Nmi' => 1.64578833693305E-04,
                'in' => 1.20000000000000E+01,
                'ft' => 1.0,
                'yd' => 3.33333333223972E-01,
                'ang' => 3.04800000000000E+09,
                'Pica' => 8.63999999999946E+02,
            ],
            'yd' => [
                'm' => 9.14400000300000E-01,
                'mi' => 5.68181818368230E-04,
                'Nmi' => 4.93736501241901E-04,
                'in' => 3.60000000118110E+01,
                'ft' => 3.00000000000000E+00,
                'yd' => 1.0,
                'ang' => 9.14400000300000E+09,
                'Pica' => 2.59200000085023E+03,
            ],
            'ang' => [
                'm' => 1.00000000000000E-10,
                'mi' => 6.21371192237334E-14,
                'Nmi' => 5.39956803455724E-14,
                'in' => 3.93700787401575E-09,
                'ft' => 3.28083989501312E-10,
                'yd' => 1.09361329797891E-10,
                'ang' => 1.0,
                'Pica' => 2.83464566929116E-07,
            ],
            'Pica' => [
                'm' => 3.52777777777800E-04,
                'mi' => 2.19205948372629E-07,
                'Nmi' => 1.90484761219114E-07,
                'in' => 1.38888888888898E-02,
                'ft' => 1.15740740740748E-03,
                'yd' => 3.85802469009251E-04,
                'ang' => 3.52777777777800E+06,
                'Pica' => 1.0,
            ],
        ],
        'Time' => [
            'yr' => [
                'yr' => 1.0,
                'day' => 365.25,
                'hr' => 8766.0,
                'mn' => 525960.0,
                'sec' => 31557600.0,
            ],
            'day' => [
                'yr' => 2.73785078713210E-03,
                'day' => 1.0,
                'hr' => 24.0,
                'mn' => 1440.0,
                'sec' => 86400.0,
            ],
            'hr' => [
                'yr' => 1.14077116130504E-04,
                'day' => 4.16666666666667E-02,
                'hr' => 1.0,
                'mn' => 60.0,
                'sec' => 3600.0,
            ],
            'mn' => [
                'yr' => 1.90128526884174E-06,
                'day' => 6.94444444444444E-04,
                'hr' => 1.66666666666667E-02,
                'mn' => 1.0,
                'sec' => 60.0,
            ],
            'sec' => [
                'yr' => 3.16880878140289E-08,
                'day' => 1.15740740740741E-05,
                'hr' => 2.77777777777778E-04,
                'mn' => 1.66666666666667E-02,
                'sec' => 1.0,
            ],
        ],
        'Pressure' => [
            'Pa' => [
                'Pa' => 1.0,
                'p' => 1.0,
                'atm' => 9.86923299998193E-06,
                'at' => 9.86923299998193E-06,
                'mmHg' => 7.50061707998627E-03,
            ],
            'p' => [
                'Pa' => 1.0,
                'p' => 1.0,
                'atm' => 9.86923299998193E-06,
                'at' => 9.86923299998193E-06,
                'mmHg' => 7.50061707998627E-03,
            ],
            'atm' => [
                'Pa' => 1.01324996583000E+05,
                'p' => 1.01324996583000E+05,
                'atm' => 1.0,
                'at' => 1.0,
                'mmHg' => 760.0,
            ],
            'at' => [
                'Pa' => 1.01324996583000E+05,
                'p' => 1.01324996583000E+05,
                'atm' => 1.0,
                'at' => 1.0,
                'mmHg' => 760.0,
            ],
            'mmHg' => [
                'Pa' => 1.33322363925000E+02,
                'p' => 1.33322363925000E+02,
                'atm' => 1.31578947368421E-03,
                'at' => 1.31578947368421E-03,
                'mmHg' => 1.0,
            ],
        ],
        'Force' => [
            'N' => [
                'N' => 1.0,
                'dyn' => 1.0E+5,
                'dy' => 1.0E+5,
                'lbf' => 2.24808923655339E-01,
            ],
            'dyn' => [
                'N' => 1.0E-5,
                'dyn' => 1.0,
                'dy' => 1.0,
                'lbf' => 2.24808923655339E-06,
            ],
            'dy' => [
                'N' => 1.0E-5,
                'dyn' => 1.0,
                'dy' => 1.0,
                'lbf' => 2.24808923655339E-06,
            ],
            'lbf' => [
                'N' => 4.448222,
                'dyn' => 4.448222E+5,
                'dy' => 4.448222E+5,
                'lbf' => 1.0,
            ],
        ],
        'Energy' => [
            'J' => [
                'J' => 1.0,
                'e' => 9.99999519343231E+06,
                'c' => 2.39006249473467E-01,
                'cal' => 2.38846190642017E-01,
                'eV' => 6.24145700000000E+18,
                'ev' => 6.24145700000000E+18,
                'HPh' => 3.72506430801000E-07,
                'hh' => 3.72506430801000E-07,
                'Wh' => 2.77777916238711E-04,
                'wh' => 2.77777916238711E-04,
                'flb' => 2.37304222192651E+01,
                'BTU' => 9.47815067349015E-04,
                'btu' => 9.47815067349015E-04,
            ],
            'e' => [
                'J' => 1.00000048065700E-07,
                'e' => 1.0,
                'c' => 2.39006364353494E-08,
                'cal' => 2.38846305445111E-08,
                'eV' => 6.24146000000000E+11,
                'ev' => 6.24146000000000E+11,
                'HPh' => 3.72506609848824E-14,
                'hh' => 3.72506609848824E-14,
                'Wh' => 2.77778049754611E-11,
                'wh' => 2.77778049754611E-11,
                'flb' => 2.37304336254586E-06,
                'BTU' => 9.47815522922962E-11,
                'btu' => 9.47815522922962E-11,
            ],
            'c' => [
                'J' => 4.18399101363672E+00,
                'e' => 4.18398900257312E+07,
                'c' => 1.0,
                'cal' => 9.99330315287563E-01,
                'eV' => 2.61142000000000E+19,
                'ev' => 2.61142000000000E+19,
                'HPh' => 1.55856355899327E-06,
                'hh' => 1.55856355899327E-06,
                'Wh' => 1.16222030532950E-03,
                'wh' => 1.16222030532950E-03,
                'flb' => 9.92878733152102E+01,
                'BTU' => 3.96564972437776E-03,
                'btu' => 3.96564972437776E-03,
            ],
            'cal' => [
                'J' => 4.18679484613929E+00,
                'e' => 4.18679283372801E+07,
                'c' => 1.00067013349059E+00,
                'cal' => 1.0,
                'eV' => 2.61317000000000E+19,
                'ev' => 2.61317000000000E+19,
                'HPh' => 1.55960800463137E-06,
                'hh' => 1.55960800463137E-06,
                'Wh' => 1.16299914807955E-03,
                'wh' => 1.16299914807955E-03,
                'flb' => 9.93544094443283E+01,
                'BTU' => 3.96830723907002E-03,
                'btu' => 3.96830723907002E-03,
            ],
            'eV' => [
                'J' => 1.60219000146921E-19,
                'e' => 1.60218923136574E-12,
                'c' => 3.82933423195043E-20,
                'cal' => 3.82676978535648E-20,
                'eV' => 1.0,
                'ev' => 1.0,
                'HPh' => 5.96826078912344E-26,
                'hh' => 5.96826078912344E-26,
                'Wh' => 4.45053000026614E-23,
                'wh' => 4.45053000026614E-23,
                'flb' => 3.80206452103492E-18,
                'BTU' => 1.51857982414846E-22,
                'btu' => 1.51857982414846E-22,
            ],
            'ev' => [
                'J' => 1.60219000146921E-19,
                'e' => 1.60218923136574E-12,
                'c' => 3.82933423195043E-20,
                'cal' => 3.82676978535648E-20,
                'eV' => 1.0,
                'ev' => 1.0,
                'HPh' => 5.96826078912344E-26,
                'hh' => 5.96826078912344E-26,
                'Wh' => 4.45053000026614E-23,
                'wh' => 4.45053000026614E-23,
                'flb' => 3.80206452103492E-18,
                'BTU' => 1.51857982414846E-22,
                'btu' => 1.51857982414846E-22,
            ],
            'HPh' => [
                'J' => 2.68451741316170E+06,
                'e' => 2.68451612283024E+13,
                'c' => 6.41616438565991E+05,
                'cal' => 6.41186757845835E+05,
                'eV' => 1.67553000000000E+25,
                'ev' => 1.67553000000000E+25,
                'HPh' => 1.0,
                'hh' => 1.0,
                'Wh' => 7.45699653134593E+02,
                'wh' => 7.45699653134593E+02,
                'flb' => 6.37047316692964E+07,
                'BTU' => 2.54442605275546E+03,
                'btu' => 2.54442605275546E+03,
            ],
            'hh' => [
                'J' => 2.68451741316170E+06,
                'e' => 2.68451612283024E+13,
                'c' => 6.41616438565991E+05,
                'cal' => 6.41186757845835E+05,
                'eV' => 1.67553000000000E+25,
                'ev' => 1.67553000000000E+25,
                'HPh' => 1.0,
                'hh' => 1.0,
                'Wh' => 7.45699653134593E+02,
                'wh' => 7.45699653134593E+02,
                'flb' => 6.37047316692964E+07,
                'BTU' => 2.54442605275546E+03,
                'btu' => 2.54442605275546E+03,
            ],
            'Wh' => [
                'J' => 3.59999820554720E+03,
                'e' => 3.59999647518369E+10,
                'c' => 8.60422069219046E+02,
                'cal' => 8.59845857713046E+02,
                'eV' => 2.24692340000000E+22,
                'ev' => 2.24692340000000E+22,
                'HPh' => 1.34102248243839E-03,
                'hh' => 1.34102248243839E-03,
                'Wh' => 1.0,
                'wh' => 1.0,
                'flb' => 8.54294774062316E+04,
                'BTU' => 3.41213254164705E+00,
                'btu' => 3.41213254164705E+00,
            ],
            'wh' => [
                'J' => 3.59999820554720E+03,
                'e' => 3.59999647518369E+10,
                'c' => 8.60422069219046E+02,
                'cal' => 8.59845857713046E+02,
                'eV' => 2.24692340000000E+22,
                'ev' => 2.24692340000000E+22,
                'HPh' => 1.34102248243839E-03,
                'hh' => 1.34102248243839E-03,
                'Wh' => 1.0,
                'wh' => 1.0,
                'flb' => 8.54294774062316E+04,
                'BTU' => 3.41213254164705E+00,
                'btu' => 3.41213254164705E+00,
            ],
            'flb' => [
                'J' => 4.21400003236424E-02,
                'e' => 4.21399800687660E+05,
                'c' => 1.00717234301644E-02,
                'cal' => 1.00649785509554E-02,
                'eV' => 2.63015000000000E+17,
                'ev' => 2.63015000000000E+17,
                'HPh' => 1.56974211145130E-08,
                'hh' => 1.56974211145130E-08,
                'Wh' => 1.17055614802000E-05,
                'wh' => 1.17055614802000E-05,
                'flb' => 1.0,
                'BTU' => 3.99409272448406E-05,
                'btu' => 3.99409272448406E-05,
            ],
            'BTU' => [
                'J' => 1.05505813786749E+03,
                'e' => 1.05505763074665E+10,
                'c' => 2.52165488508168E+02,
                'cal' => 2.51996617135510E+02,
                'eV' => 6.58510000000000E+21,
                'ev' => 6.58510000000000E+21,
                'HPh' => 3.93015941224568E-04,
                'hh' => 3.93015941224568E-04,
                'Wh' => 2.93071851047526E-01,
                'wh' => 2.93071851047526E-01,
                'flb' => 2.50369750774671E+04,
                'BTU' => 1.0,
                'btu' => 1.0,
            ],
            'btu' => [
                'J' => 1.05505813786749E+03,
                'e' => 1.05505763074665E+10,
                'c' => 2.52165488508168E+02,
                'cal' => 2.51996617135510E+02,
                'eV' => 6.58510000000000E+21,
                'ev' => 6.58510000000000E+21,
                'HPh' => 3.93015941224568E-04,
                'hh' => 3.93015941224568E-04,
                'Wh' => 2.93071851047526E-01,
                'wh' => 2.93071851047526E-01,
                'flb' => 2.50369750774671E+04,
                'BTU' => 1.0,
                'btu' => 1.0,
            ],
        ],
        'Power' => [
            'HP' => [
                'HP' => 1.0,
                'h' => 1.0,
                'W' => 7.45701000000000E+02,
                'w' => 7.45701000000000E+02,
            ],
            'h' => [
                'HP' => 1.0,
                'h' => 1.0,
                'W' => 7.45701000000000E+02,
                'w' => 7.45701000000000E+02,
            ],
            'W' => [
                'HP' => 1.34102006031908E-03,
                'h' => 1.34102006031908E-03,
                'W' => 1.0,
                'w' => 1.0,
            ],
            'w' => [
                'HP' => 1.34102006031908E-03,
                'h' => 1.34102006031908E-03,
                'W' => 1.0,
                'w' => 1.0,
            ],
        ],
        'Magnetism' => [
            'T' => [
                'T' => 1.0,
                'ga' => 10000.0,
            ],
            'ga' => [
                'T' => 0.0001,
                'ga' => 1.0,
            ],
        ],
        'Liquid' => [
            'tsp' => [
                'tsp' => 1.0,
                'tbs' => 3.33333333333333E-01,
                'oz' => 1.66666666666667E-01,
                'cup' => 2.08333333333333E-02,
                'pt' => 1.04166666666667E-02,
                'us_pt' => 1.04166666666667E-02,
                'uk_pt' => 8.67558516821960E-03,
                'qt' => 5.20833333333333E-03,
                'gal' => 1.30208333333333E-03,
                'l' => 4.92999408400710E-03,
                'lt' => 4.92999408400710E-03,
            ],
            'tbs' => [
                'tsp' => 3.00000000000000E+00,
                'tbs' => 1.0,
                'oz' => 5.00000000000000E-01,
                'cup' => 6.25000000000000E-02,
                'pt' => 3.12500000000000E-02,
                'us_pt' => 3.12500000000000E-02,
                'uk_pt' => 2.60267555046588E-02,
                'qt' => 1.56250000000000E-02,
                'gal' => 3.90625000000000E-03,
                'l' => 1.47899822520213E-02,
                'lt' => 1.47899822520213E-02,
            ],
            'oz' => [
                'tsp' => 6.00000000000000E+00,
                'tbs' => 2.00000000000000E+00,
                'oz' => 1.0,
                'cup' => 1.25000000000000E-01,
                'pt' => 6.25000000000000E-02,
                'us_pt' => 6.25000000000000E-02,
                'uk_pt' => 5.20535110093176E-02,
                'qt' => 3.12500000000000E-02,
                'gal' => 7.81250000000000E-03,
                'l' => 2.95799645040426E-02,
                'lt' => 2.95799645040426E-02,
            ],
            'cup' => [
                'tsp' => 4.80000000000000E+01,
                'tbs' => 1.60000000000000E+01,
                'oz' => 8.00000000000000E+00,
                'cup' => 1.0,
                'pt' => 5.00000000000000E-01,
                'us_pt' => 5.00000000000000E-01,
                'uk_pt' => 4.16428088074541E-01,
                'qt' => 2.50000000000000E-01,
                'gal' => 6.25000000000000E-02,
                'l' => 2.36639716032341E-01,
                'lt' => 2.36639716032341E-01,
            ],
            'pt' => [
                'tsp' => 9.60000000000000E+01,
                'tbs' => 3.20000000000000E+01,
                'oz' => 1.60000000000000E+01,
                'cup' => 2.00000000000000E+00,
                'pt' => 1.0,
                'us_pt' => 1.0,
                'uk_pt' => 8.32856176149081E-01,
                'qt' => 5.00000000000000E-01,
                'gal' => 1.25000000000000E-01,
                'l' => 4.73279432064682E-01,
                'lt' => 4.73279432064682E-01,
            ],
            'us_pt' => [
                'tsp' => 9.60000000000000E+01,
                'tbs' => 3.20000000000000E+01,
                'oz' => 1.60000000000000E+01,
                'cup' => 2.00000000000000E+00,
                'pt' => 1.0,
                'us_pt' => 1.0,
                'uk_pt' => 8.32856176149081E-01,
                'qt' => 5.00000000000000E-01,
                'gal' => 1.25000000000000E-01,
                'l' => 4.73279432064682E-01,
                'lt' => 4.73279432064682E-01,
            ],
            'uk_pt' => [
                'tsp' => 1.15266000000000E+02,
                'tbs' => 3.84220000000000E+01,
                'oz' => 1.92110000000000E+01,
                'cup' => 2.40137500000000E+00,
                'pt' => 1.20068750000000E+00,
                'us_pt' => 1.20068750000000E+00,
                'uk_pt' => 1.0,
                'qt' => 6.00343750000000E-01,
                'gal' => 1.50085937500000E-01,
                'l' => 5.68260698087162E-01,
                'lt' => 5.68260698087162E-01,
            ],
            'qt' => [
                'tsp' => 1.92000000000000E+02,
                'tbs' => 6.40000000000000E+01,
                'oz' => 3.20000000000000E+01,
                'cup' => 4.00000000000000E+00,
                'pt' => 2.00000000000000E+00,
                'us_pt' => 2.00000000000000E+00,
                'uk_pt' => 1.66571235229816E+00,
                'qt' => 1.0,
                'gal' => 2.50000000000000E-01,
                'l' => 9.46558864129363E-01,
                'lt' => 9.46558864129363E-01,
            ],
            'gal' => [
                'tsp' => 7.68000000000000E+02,
                'tbs' => 2.56000000000000E+02,
                'oz' => 1.28000000000000E+02,
                'cup' => 1.60000000000000E+01,
                'pt' => 8.00000000000000E+00,
                'us_pt' => 8.00000000000000E+00,
                'uk_pt' => 6.66284940919265E+00,
                'qt' => 4.00000000000000E+00,
                'gal' => 1.0,
                'l' => 3.78623545651745E+00,
                'lt' => 3.78623545651745E+00,
            ],
            'l' => [
                'tsp' => 2.02840000000000E+02,
                'tbs' => 6.76133333333333E+01,
                'oz' => 3.38066666666667E+01,
                'cup' => 4.22583333333333E+00,
                'pt' => 2.11291666666667E+00,
                'us_pt' => 2.11291666666667E+00,
                'uk_pt' => 1.75975569552166E+00,
                'qt' => 1.05645833333333E+00,
                'gal' => 2.64114583333333E-01,
                'l' => 1.0,
                'lt' => 1.0,
            ],
            'lt' => [
                'tsp' => 2.02840000000000E+02,
                'tbs' => 6.76133333333333E+01,
                'oz' => 3.38066666666667E+01,
                'cup' => 4.22583333333333E+00,
                'pt' => 2.11291666666667E+00,
                'us_pt' => 2.11291666666667E+00,
                'uk_pt' => 1.75975569552166E+00,
                'qt' => 1.05645833333333E+00,
                'gal' => 2.64114583333333E-01,
                'l' => 1.0,
                'lt' => 1.0,
            ],
        ],
    ];

    /**
     * parseComplex.
     *
     * Parses a complex number into its real and imaginary parts, and an I or J suffix
     *
     * @deprecated 2.0.0 No longer used by internal code. Please use the Complex\Complex class instead
     *
     * @param string $complexNumber The complex number
     *
     * @return mixed[] Indexed on "real", "imaginary" and "suffix"
     */
    public static function parseComplex($complexNumber)
    {
        $complex = new Complex($complexNumber);

        return [
            'real' => $complex->getReal(),
            'imaginary' => $complex->getImaginary(),
            'suffix' => $complex->getSuffix(),
        ];
    }

    /**
     * Formats a number base string value with leading zeroes.
     *
     * @param string $xVal The "number" to pad
     * @param int $places The length that we want to pad this value
     *
     * @return string The padded "number"
     */
    private static function nbrConversionFormat($xVal, $places)
    {
        if ($places !== null) {
            if (is_numeric($places)) {
                $places = (int) $places;
            } else {
                return Functions::VALUE();
            }
            if ($places < 0) {
                return Functions::NAN();
            }
            if (strlen($xVal) <= $places) {
                return substr(str_pad($xVal, $places, '0', STR_PAD_LEFT), -10);
            }

            return Functions::NAN();
        }

        return substr($xVal, -10);
    }

    /**
     * BESSELI.
     *
     *    Returns the modified Bessel function In(x), which is equivalent to the Bessel function evaluated
     *        for purely imaginary arguments
     *
     *    Excel Function:
     *        BESSELI(x,ord)
     *
     * @param float $x The value at which to evaluate the function.
     *                                If x is nonnumeric, BESSELI returns the #VALUE! error value.
     * @param int $ord The order of the Bessel function.
     *                                If ord is not an integer, it is truncated.
     *                                If $ord is nonnumeric, BESSELI returns the #VALUE! error value.
     *                                If $ord < 0, BESSELI returns the #NUM! error value.
     *
     * @return float|string Result, or a string containing an error
     */
    public static function BESSELI($x, $ord)
    {
        $x = ($x === null) ? 0.0 : Functions::flattenSingleValue($x);
        $ord = ($ord === null) ? 0.0 : Functions::flattenSingleValue($ord);

        if ((is_numeric($x)) && (is_numeric($ord))) {
            $ord = floor($ord);
            if ($ord < 0) {
                return Functions::NAN();
            }

            if (abs($x) <= 30) {
                $fResult = $fTerm = ($x / 2) ** $ord / MathTrig::FACT($ord);
                $ordK = 1;
                $fSqrX = ($x * $x) / 4;
                do {
                    $fTerm *= $fSqrX;
                    $fTerm /= ($ordK * ($ordK + $ord));
                    $fResult += $fTerm;
                } while ((abs($fTerm) > 1e-12) && (++$ordK < 100));
            } else {
                $f_2_PI = 2 * M_PI;

                $fXAbs = abs($x);
                $fResult = exp($fXAbs) / sqrt($f_2_PI * $fXAbs);
                if (($ord & 1) && ($x < 0)) {
                    $fResult = -$fResult;
                }
            }

            return (is_nan($fResult)) ? Functions::NAN() : $fResult;
        }

        return Functions::VALUE();
    }

    /**
     * BESSELJ.
     *
     *    Returns the Bessel function
     *
     *    Excel Function:
     *        BESSELJ(x,ord)
     *
     * @param float $x The value at which to evaluate the function.
     *                                If x is nonnumeric, BESSELJ returns the #VALUE! error value.
     * @param int $ord The order of the Bessel function. If n is not an integer, it is truncated.
     *                                If $ord is nonnumeric, BESSELJ returns the #VALUE! error value.
     *                                If $ord < 0, BESSELJ returns the #NUM! error value.
     *
     * @return float|string Result, or a string containing an error
     */
    public static function BESSELJ($x, $ord)
    {
        $x = ($x === null) ? 0.0 : Functions::flattenSingleValue($x);
        $ord = ($ord === null) ? 0.0 : Functions::flattenSingleValue($ord);

        if ((is_numeric($x)) && (is_numeric($ord))) {
            $ord = floor($ord);
            if ($ord < 0) {
                return Functions::NAN();
            }

            $fResult = 0;
            if (abs($x) <= 30) {
                $fResult = $fTerm = ($x / 2) ** $ord / MathTrig::FACT($ord);
                $ordK = 1;
                $fSqrX = ($x * $x) / -4;
                do {
                    $fTerm *= $fSqrX;
                    $fTerm /= ($ordK * ($ordK + $ord));
                    $fResult += $fTerm;
                } while ((abs($fTerm) > 1e-12) && (++$ordK < 100));
            } else {
                $f_PI_DIV_2 = M_PI / 2;
                $f_PI_DIV_4 = M_PI / 4;

                $fXAbs = abs($x);
                $fResult = sqrt(Functions::M_2DIVPI / $fXAbs) * cos($fXAbs - $ord * $f_PI_DIV_2 - $f_PI_DIV_4);
                if (($ord & 1) && ($x < 0)) {
                    $fResult = -$fResult;
                }
            }

            return (is_nan($fResult)) ? Functions::NAN() : $fResult;
        }

        return Functions::VALUE();
    }

    private static function besselK0($fNum)
    {
        if ($fNum <= 2) {
            $fNum2 = $fNum * 0.5;
            $y = ($fNum2 * $fNum2);
            $fRet = -log($fNum2) * self::BESSELI($fNum, 0) +
                (-0.57721566 + $y * (0.42278420 + $y * (0.23069756 + $y * (0.3488590e-1 + $y * (0.262698e-2 + $y *
                                    (0.10750e-3 + $y * 0.74e-5))))));
        } else {
            $y = 2 / $fNum;
            $fRet = exp(-$fNum) / sqrt($fNum) *
                (1.25331414 + $y * (-0.7832358e-1 + $y * (0.2189568e-1 + $y * (-0.1062446e-1 + $y *
                                (0.587872e-2 + $y * (-0.251540e-2 + $y * 0.53208e-3))))));
        }

        return $fRet;
    }

    private static function besselK1($fNum)
    {
        if ($fNum <= 2) {
            $fNum2 = $fNum * 0.5;
            $y = ($fNum2 * $fNum2);
            $fRet = log($fNum2) * self::BESSELI($fNum, 1) +
                (1 + $y * (0.15443144 + $y * (-0.67278579 + $y * (-0.18156897 + $y * (-0.1919402e-1 + $y *
                                    (-0.110404e-2 + $y * (-0.4686e-4))))))) / $fNum;
        } else {
            $y = 2 / $fNum;
            $fRet = exp(-$fNum) / sqrt($fNum) *
                (1.25331414 + $y * (0.23498619 + $y * (-0.3655620e-1 + $y * (0.1504268e-1 + $y * (-0.780353e-2 + $y *
                                    (0.325614e-2 + $y * (-0.68245e-3)))))));
        }

        return $fRet;
    }

    /**
     * BESSELK.
     *
     *    Returns the modified Bessel function Kn(x), which is equivalent to the Bessel functions evaluated
     *        for purely imaginary arguments.
     *
     *    Excel Function:
     *        BESSELK(x,ord)
     *
     * @param float $x The value at which to evaluate the function.
     *                                If x is nonnumeric, BESSELK returns the #VALUE! error value.
     * @param int $ord The order of the Bessel function. If n is not an integer, it is truncated.
     *                                If $ord is nonnumeric, BESSELK returns the #VALUE! error value.
     *                                If $ord < 0, BESSELK returns the #NUM! error value.
     *
     * @return float|string Result, or a string containing an error
     */
    public static function BESSELK($x, $ord)
    {
        $x = ($x === null) ? 0.0 : Functions::flattenSingleValue($x);
        $ord = ($ord === null) ? 0.0 : Functions::flattenSingleValue($ord);

        if ((is_numeric($x)) && (is_numeric($ord))) {
            if (($ord < 0) || ($x == 0.0)) {
                return Functions::NAN();
            }

            switch (floor($ord)) {
                case 0:
                    $fBk = self::besselK0($x);

                    break;
                case 1:
                    $fBk = self::besselK1($x);

                    break;
                default:
                    $fTox = 2 / $x;
                    $fBkm = self::besselK0($x);
                    $fBk = self::besselK1($x);
                    for ($n = 1; $n < $ord; ++$n) {
                        $fBkp = $fBkm + $n * $fTox * $fBk;
                        $fBkm = $fBk;
                        $fBk = $fBkp;
                    }
            }

            return (is_nan($fBk)) ? Functions::NAN() : $fBk;
        }

        return Functions::VALUE();
    }

    private static function besselY0($fNum)
    {
        if ($fNum < 8.0) {
            $y = ($fNum * $fNum);
            $f1 = -2957821389.0 + $y * (7062834065.0 + $y * (-512359803.6 + $y * (10879881.29 + $y * (-86327.92757 + $y * 228.4622733))));
            $f2 = 40076544269.0 + $y * (745249964.8 + $y * (7189466.438 + $y * (47447.26470 + $y * (226.1030244 + $y))));
            $fRet = $f1 / $f2 + 0.636619772 * self::BESSELJ($fNum, 0) * log($fNum);
        } else {
            $z = 8.0 / $fNum;
            $y = ($z * $z);
            $xx = $fNum - 0.785398164;
            $f1 = 1 + $y * (-0.1098628627e-2 + $y * (0.2734510407e-4 + $y * (-0.2073370639e-5 + $y * 0.2093887211e-6)));
            $f2 = -0.1562499995e-1 + $y * (0.1430488765e-3 + $y * (-0.6911147651e-5 + $y * (0.7621095161e-6 + $y * (-0.934945152e-7))));
            $fRet = sqrt(0.636619772 / $fNum) * (sin($xx) * $f1 + $z * cos($xx) * $f2);
        }

        return $fRet;
    }

    private static function besselY1($fNum)
    {
        if ($fNum < 8.0) {
            $y = ($fNum * $fNum);
            $f1 = $fNum * (-0.4900604943e13 + $y * (0.1275274390e13 + $y * (-0.5153438139e11 + $y * (0.7349264551e9 + $y *
                                (-0.4237922726e7 + $y * 0.8511937935e4)))));
            $f2 = 0.2499580570e14 + $y * (0.4244419664e12 + $y * (0.3733650367e10 + $y * (0.2245904002e8 + $y *
                            (0.1020426050e6 + $y * (0.3549632885e3 + $y)))));
            $fRet = $f1 / $f2 + 0.636619772 * (self::BESSELJ($fNum, 1) * log($fNum) - 1 / $fNum);
        } else {
            $fRet = sqrt(0.636619772 / $fNum) * sin($fNum - 2.356194491);
        }

        return $fRet;
    }

    /**
     * BESSELY.
     *
     * Returns the Bessel function, which is also called the Weber function or the Neumann function.
     *
     *    Excel Function:
     *        BESSELY(x,ord)
     *
     * @param float $x The value at which to evaluate the function.
     *                                If x is nonnumeric, BESSELK returns the #VALUE! error value.
     * @param int $ord The order of the Bessel function. If n is not an integer, it is truncated.
     *                                If $ord is nonnumeric, BESSELK returns the #VALUE! error value.
     *                                If $ord < 0, BESSELK returns the #NUM! error value.
     *
     * @return float|string Result, or a string containing an error
     */
    public static function BESSELY($x, $ord)
    {
        $x = ($x === null) ? 0.0 : Functions::flattenSingleValue($x);
        $ord = ($ord === null) ? 0.0 : Functions::flattenSingleValue($ord);

        if ((is_numeric($x)) && (is_numeric($ord))) {
            if (($ord < 0) || ($x == 0.0)) {
                return Functions::NAN();
            }

            switch (floor($ord)) {
                case 0:
                    $fBy = self::besselY0($x);

                    break;
                case 1:
                    $fBy = self::besselY1($x);

                    break;
                default:
                    $fTox = 2 / $x;
                    $fBym = self::besselY0($x);
                    $fBy = self::besselY1($x);
                    for ($n = 1; $n < $ord; ++$n) {
                        $fByp = $n * $fTox * $fBy - $fBym;
                        $fBym = $fBy;
                        $fBy = $fByp;
                    }
            }

            return (is_nan($fBy)) ? Functions::NAN() : $fBy;
        }

        return Functions::VALUE();
    }

    /**
     * BINTODEC.
     *
     * Return a binary value as decimal.
     *
     * Excel Function:
     *        BIN2DEC(x)
     *
     * @param string $x The binary number (as a string) that you want to convert. The number
     *                                cannot contain more than 10 characters (10 bits). The most significant
     *                                bit of number is the sign bit. The remaining 9 bits are magnitude bits.
     *                                Negative numbers are represented using two's-complement notation.
     *                                If number is not a valid binary number, or if number contains more than
     *                                10 characters (10 bits), BIN2DEC returns the #NUM! error value.
     *
     * @return string
     */
    public static function BINTODEC($x)
    {
        $x = Functions::flattenSingleValue($x);

        if (is_bool($x)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
                $x = (int) $x;
            } else {
                return Functions::VALUE();
            }
        }
        if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
            $x = floor($x);
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[01]/', $x, $out)) {
            return Functions::NAN();
        }
        if (strlen($x) > 10) {
            return Functions::NAN();
        } elseif (strlen($x) == 10) {
            //    Two's Complement
            $x = substr($x, -9);

            return '-' . (512 - bindec($x));
        }

        return bindec($x);
    }

    /**
     * BINTOHEX.
     *
     * Return a binary value as hex.
     *
     * Excel Function:
     *        BIN2HEX(x[,places])
     *
     * @param string $x The binary number (as a string) that you want to convert. The number
     *                                cannot contain more than 10 characters (10 bits). The most significant
     *                                bit of number is the sign bit. The remaining 9 bits are magnitude bits.
     *                                Negative numbers are represented using two's-complement notation.
     *                                If number is not a valid binary number, or if number contains more than
     *                                10 characters (10 bits), BIN2HEX returns the #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted, BIN2HEX uses the
     *                                minimum number of characters necessary. Places is useful for padding the
     *                                return value with leading 0s (zeros).
     *                                If places is not an integer, it is truncated.
     *                                If places is nonnumeric, BIN2HEX returns the #VALUE! error value.
     *                                If places is negative, BIN2HEX returns the #NUM! error value.
     *
     * @return string
     */
    public static function BINTOHEX($x, $places = null)
    {
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        // Argument X
        if (is_bool($x)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
                $x = (int) $x;
            } else {
                return Functions::VALUE();
            }
        }
        if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
            $x = floor($x);
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[01]/', $x, $out)) {
            return Functions::NAN();
        }
        if (strlen($x) > 10) {
            return Functions::NAN();
        } elseif (strlen($x) == 10) {
            //    Two's Complement
            return str_repeat('F', 8) . substr(strtoupper(dechex(bindec(substr($x, -9)))), -2);
        }
        $hexVal = (string) strtoupper(dechex(bindec($x)));

        return self::nbrConversionFormat($hexVal, $places);
    }

    /**
     * BINTOOCT.
     *
     * Return a binary value as octal.
     *
     * Excel Function:
     *        BIN2OCT(x[,places])
     *
     * @param string $x The binary number (as a string) that you want to convert. The number
     *                                cannot contain more than 10 characters (10 bits). The most significant
     *                                bit of number is the sign bit. The remaining 9 bits are magnitude bits.
     *                                Negative numbers are represented using two's-complement notation.
     *                                If number is not a valid binary number, or if number contains more than
     *                                10 characters (10 bits), BIN2OCT returns the #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted, BIN2OCT uses the
     *                                minimum number of characters necessary. Places is useful for padding the
     *                                return value with leading 0s (zeros).
     *                                If places is not an integer, it is truncated.
     *                                If places is nonnumeric, BIN2OCT returns the #VALUE! error value.
     *                                If places is negative, BIN2OCT returns the #NUM! error value.
     *
     * @return string
     */
    public static function BINTOOCT($x, $places = null)
    {
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        if (is_bool($x)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
                $x = (int) $x;
            } else {
                return Functions::VALUE();
            }
        }
        if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
            $x = floor($x);
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[01]/', $x, $out)) {
            return Functions::NAN();
        }
        if (strlen($x) > 10) {
            return Functions::NAN();
        } elseif (strlen($x) == 10) {
            //    Two's Complement
            return str_repeat('7', 7) . substr(strtoupper(decoct(bindec(substr($x, -9)))), -3);
        }
        $octVal = (string) decoct(bindec($x));

        return self::nbrConversionFormat($octVal, $places);
    }

    /**
     * DECTOBIN.
     *
     * Return a decimal value as binary.
     *
     * Excel Function:
     *        DEC2BIN(x[,places])
     *
     * @param string $x The decimal integer you want to convert. If number is negative,
     *                                valid place values are ignored and DEC2BIN returns a 10-character
     *                                (10-bit) binary number in which the most significant bit is the sign
     *                                bit. The remaining 9 bits are magnitude bits. Negative numbers are
     *                                represented using two's-complement notation.
     *                                If number < -512 or if number > 511, DEC2BIN returns the #NUM! error
     *                                value.
     *                                If number is nonnumeric, DEC2BIN returns the #VALUE! error value.
     *                                If DEC2BIN requires more than places characters, it returns the #NUM!
     *                                error value.
     * @param int $places The number of characters to use. If places is omitted, DEC2BIN uses
     *                                the minimum number of characters necessary. Places is useful for
     *                                padding the return value with leading 0s (zeros).
     *                                If places is not an integer, it is truncated.
     *                                If places is nonnumeric, DEC2BIN returns the #VALUE! error value.
     *                                If places is zero or negative, DEC2BIN returns the #NUM! error value.
     *
     * @return string
     */
    public static function DECTOBIN($x, $places = null)
    {
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        if (is_bool($x)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
                $x = (int) $x;
            } else {
                return Functions::VALUE();
            }
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[-0123456789.]/', $x, $out)) {
            return Functions::VALUE();
        }

        $x = (string) floor($x);
        if ($x < -512 || $x > 511) {
            return Functions::NAN();
        }

        $r = decbin($x);
        // Two's Complement
        $r = substr($r, -10);
        if (strlen($r) >= 11) {
            return Functions::NAN();
        }

        return self::nbrConversionFormat($r, $places);
    }

    /**
     * DECTOHEX.
     *
     * Return a decimal value as hex.
     *
     * Excel Function:
     *        DEC2HEX(x[,places])
     *
     * @param string $x The decimal integer you want to convert. If number is negative,
     *                                places is ignored and DEC2HEX returns a 10-character (40-bit)
     *                                hexadecimal number in which the most significant bit is the sign
     *                                bit. The remaining 39 bits are magnitude bits. Negative numbers
     *                                are represented using two's-complement notation.
     *                                If number < -549,755,813,888 or if number > 549,755,813,887,
     *                                DEC2HEX returns the #NUM! error value.
     *                                If number is nonnumeric, DEC2HEX returns the #VALUE! error value.
     *                                If DEC2HEX requires more than places characters, it returns the
     *                                #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted, DEC2HEX uses
     *                                the minimum number of characters necessary. Places is useful for
     *                                padding the return value with leading 0s (zeros).
     *                                If places is not an integer, it is truncated.
     *                                If places is nonnumeric, DEC2HEX returns the #VALUE! error value.
     *                                If places is zero or negative, DEC2HEX returns the #NUM! error value.
     *
     * @return string
     */
    public static function DECTOHEX($x, $places = null)
    {
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        if (is_bool($x)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
                $x = (int) $x;
            } else {
                return Functions::VALUE();
            }
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[-0123456789.]/', $x, $out)) {
            return Functions::VALUE();
        }
        $x = (string) floor($x);
        $r = strtoupper(dechex($x));
        if (strlen($r) == 8) {
            //    Two's Complement
            $r = 'FF' . $r;
        }

        return self::nbrConversionFormat($r, $places);
    }

    /**
     * DECTOOCT.
     *
     * Return an decimal value as octal.
     *
     * Excel Function:
     *        DEC2OCT(x[,places])
     *
     * @param string $x The decimal integer you want to convert. If number is negative,
     *                                places is ignored and DEC2OCT returns a 10-character (30-bit)
     *                                octal number in which the most significant bit is the sign bit.
     *                                The remaining 29 bits are magnitude bits. Negative numbers are
     *                                represented using two's-complement notation.
     *                                If number < -536,870,912 or if number > 536,870,911, DEC2OCT
     *                                returns the #NUM! error value.
     *                                If number is nonnumeric, DEC2OCT returns the #VALUE! error value.
     *                                If DEC2OCT requires more than places characters, it returns the
     *                                #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted, DEC2OCT uses
     *                                the minimum number of characters necessary. Places is useful for
     *                                padding the return value with leading 0s (zeros).
     *                                If places is not an integer, it is truncated.
     *                                If places is nonnumeric, DEC2OCT returns the #VALUE! error value.
     *                                If places is zero or negative, DEC2OCT returns the #NUM! error value.
     *
     * @return string
     */
    public static function DECTOOCT($x, $places = null)
    {
        $xorig = $x;
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        if (is_bool($x)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
                $x = (int) $x;
            } else {
                return Functions::VALUE();
            }
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[-0123456789.]/', $x, $out)) {
            return Functions::VALUE();
        }
        $x = (string) floor($x);
        $r = decoct($x);
        if (strlen($r) == 11) {
            //    Two's Complement
            $r = substr($r, -10);
        }

        return self::nbrConversionFormat($r, $places);
    }

    /**
     * HEXTOBIN.
     *
     * Return a hex value as binary.
     *
     * Excel Function:
     *        HEX2BIN(x[,places])
     *
     * @param string $x the hexadecimal number you want to convert.
     *                  Number cannot contain more than 10 characters.
     *                  The most significant bit of number is the sign bit (40th bit from the right).
     *                  The remaining 9 bits are magnitude bits.
     *                  Negative numbers are represented using two's-complement notation.
     *                  If number is negative, HEX2BIN ignores places and returns a 10-character binary number.
     *                  If number is negative, it cannot be less than FFFFFFFE00,
     *                      and if number is positive, it cannot be greater than 1FF.
     *                  If number is not a valid hexadecimal number, HEX2BIN returns the #NUM! error value.
     *                  If HEX2BIN requires more than places characters, it returns the #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted,
     *                                    HEX2BIN uses the minimum number of characters necessary. Places
     *                                    is useful for padding the return value with leading 0s (zeros).
     *                                    If places is not an integer, it is truncated.
     *                                    If places is nonnumeric, HEX2BIN returns the #VALUE! error value.
     *                                    If places is negative, HEX2BIN returns the #NUM! error value.
     *
     * @return string
     */
    public static function HEXTOBIN($x, $places = null)
    {
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        if (is_bool($x)) {
            return Functions::VALUE();
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[0123456789ABCDEF]/', strtoupper($x), $out)) {
            return Functions::NAN();
        }

        return self::DECTOBIN(self::HEXTODEC($x), $places);
    }

    /**
     * HEXTODEC.
     *
     * Return a hex value as decimal.
     *
     * Excel Function:
     *        HEX2DEC(x)
     *
     * @param string $x The hexadecimal number you want to convert. This number cannot
     *                                contain more than 10 characters (40 bits). The most significant
     *                                bit of number is the sign bit. The remaining 39 bits are magnitude
     *                                bits. Negative numbers are represented using two's-complement
     *                                notation.
     *                                If number is not a valid hexadecimal number, HEX2DEC returns the
     *                                #NUM! error value.
     *
     * @return string
     */
    public static function HEXTODEC($x)
    {
        $x = Functions::flattenSingleValue($x);

        if (is_bool($x)) {
            return Functions::VALUE();
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[0123456789ABCDEF]/', strtoupper($x), $out)) {
            return Functions::NAN();
        }

        if (strlen($x) > 10) {
            return Functions::NAN();
        }

        $binX = '';
        foreach (str_split($x) as $char) {
            $binX .= str_pad(base_convert($char, 16, 2), 4, '0', STR_PAD_LEFT);
        }
        if (strlen($binX) == 40 && $binX[0] == '1') {
            for ($i = 0; $i < 40; ++$i) {
                $binX[$i] = ($binX[$i] == '1' ? '0' : '1');
            }

            return (bindec($binX) + 1) * -1;
        }

        return bindec($binX);
    }

    /**
     * HEXTOOCT.
     *
     * Return a hex value as octal.
     *
     * Excel Function:
     *        HEX2OCT(x[,places])
     *
     * @param string $x The hexadecimal number you want to convert. Number cannot
     *                                    contain more than 10 characters. The most significant bit of
     *                                    number is the sign bit. The remaining 39 bits are magnitude
     *                                    bits. Negative numbers are represented using two's-complement
     *                                    notation.
     *                                    If number is negative, HEX2OCT ignores places and returns a
     *                                    10-character octal number.
     *                                    If number is negative, it cannot be less than FFE0000000, and
     *                                    if number is positive, it cannot be greater than 1FFFFFFF.
     *                                    If number is not a valid hexadecimal number, HEX2OCT returns
     *                                    the #NUM! error value.
     *                                    If HEX2OCT requires more than places characters, it returns
     *                                    the #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted, HEX2OCT
     *                                    uses the minimum number of characters necessary. Places is
     *                                    useful for padding the return value with leading 0s (zeros).
     *                                    If places is not an integer, it is truncated.
     *                                    If places is nonnumeric, HEX2OCT returns the #VALUE! error
     *                                    value.
     *                                    If places is negative, HEX2OCT returns the #NUM! error value.
     *
     * @return string
     */
    public static function HEXTOOCT($x, $places = null)
    {
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        if (is_bool($x)) {
            return Functions::VALUE();
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[0123456789ABCDEF]/', strtoupper($x), $out)) {
            return Functions::NAN();
        }

        $decimal = self::HEXTODEC($x);
        if ($decimal < -536870912 || $decimal > 536870911) {
            return Functions::NAN();
        }

        return self::DECTOOCT($decimal, $places);
    }

    /**
     * OCTTOBIN.
     *
     * Return an octal value as binary.
     *
     * Excel Function:
     *        OCT2BIN(x[,places])
     *
     * @param string $x The octal number you want to convert. Number may not
     *                                    contain more than 10 characters. The most significant
     *                                    bit of number is the sign bit. The remaining 29 bits
     *                                    are magnitude bits. Negative numbers are represented
     *                                    using two's-complement notation.
     *                                    If number is negative, OCT2BIN ignores places and returns
     *                                    a 10-character binary number.
     *                                    If number is negative, it cannot be less than 7777777000,
     *                                    and if number is positive, it cannot be greater than 777.
     *                                    If number is not a valid octal number, OCT2BIN returns
     *                                    the #NUM! error value.
     *                                    If OCT2BIN requires more than places characters, it
     *                                    returns the #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted,
     *                                    OCT2BIN uses the minimum number of characters necessary.
     *                                    Places is useful for padding the return value with
     *                                    leading 0s (zeros).
     *                                    If places is not an integer, it is truncated.
     *                                    If places is nonnumeric, OCT2BIN returns the #VALUE!
     *                                    error value.
     *                                    If places is negative, OCT2BIN returns the #NUM! error
     *                                    value.
     *
     * @return string
     */
    public static function OCTTOBIN($x, $places = null)
    {
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        if (is_bool($x)) {
            return Functions::VALUE();
        }
        $x = (string) $x;
        if (preg_match_all('/[01234567]/', $x, $out) != strlen($x)) {
            return Functions::NAN();
        }

        return self::DECTOBIN(self::OCTTODEC($x), $places);
    }

    /**
     * OCTTODEC.
     *
     * Return an octal value as decimal.
     *
     * Excel Function:
     *        OCT2DEC(x)
     *
     * @param string $x The octal number you want to convert. Number may not contain
     *                                more than 10 octal characters (30 bits). The most significant
     *                                bit of number is the sign bit. The remaining 29 bits are
     *                                magnitude bits. Negative numbers are represented using
     *                                two's-complement notation.
     *                                If number is not a valid octal number, OCT2DEC returns the
     *                                #NUM! error value.
     *
     * @return string
     */
    public static function OCTTODEC($x)
    {
        $x = Functions::flattenSingleValue($x);

        if (is_bool($x)) {
            return Functions::VALUE();
        }
        $x = (string) $x;
        if (preg_match_all('/[01234567]/', $x, $out) != strlen($x)) {
            return Functions::NAN();
        }
        $binX = '';
        foreach (str_split($x) as $char) {
            $binX .= str_pad(decbin((int) $char), 3, '0', STR_PAD_LEFT);
        }
        if (strlen($binX) == 30 && $binX[0] == '1') {
            for ($i = 0; $i < 30; ++$i) {
                $binX[$i] = ($binX[$i] == '1' ? '0' : '1');
            }

            return (bindec($binX) + 1) * -1;
        }

        return bindec($binX);
    }

    /**
     * OCTTOHEX.
     *
     * Return an octal value as hex.
     *
     * Excel Function:
     *        OCT2HEX(x[,places])
     *
     * @param string $x The octal number you want to convert. Number may not contain
     *                                    more than 10 octal characters (30 bits). The most significant
     *                                    bit of number is the sign bit. The remaining 29 bits are
     *                                    magnitude bits. Negative numbers are represented using
     *                                    two's-complement notation.
     *                                    If number is negative, OCT2HEX ignores places and returns a
     *                                    10-character hexadecimal number.
     *                                    If number is not a valid octal number, OCT2HEX returns the
     *                                    #NUM! error value.
     *                                    If OCT2HEX requires more than places characters, it returns
     *                                    the #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted, OCT2HEX
     *                                    uses the minimum number of characters necessary. Places is useful
     *                                    for padding the return value with leading 0s (zeros).
     *                                    If places is not an integer, it is truncated.
     *                                    If places is nonnumeric, OCT2HEX returns the #VALUE! error value.
     *                                    If places is negative, OCT2HEX returns the #NUM! error value.
     *
     * @return string
     */
    public static function OCTTOHEX($x, $places = null)
    {
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        if (is_bool($x)) {
            return Functions::VALUE();
        }
        $x = (string) $x;
        if (preg_match_all('/[01234567]/', $x, $out) != strlen($x)) {
            return Functions::NAN();
        }
        $hexVal = strtoupper(dechex(self::OCTTODEC($x)));

        return self::nbrConversionFormat($hexVal, $places);
    }

    /**
     * COMPLEX.
     *
     * Converts real and imaginary coefficients into a complex number of the form x +/- yi or x +/- yj.
     *
     * Excel Function:
     *        COMPLEX(realNumber,imaginary[,suffix])
     *
     * @param float $realNumber the real coefficient of the complex number
     * @param float $imaginary the imaginary coefficient of the complex number
     * @param string $suffix The suffix for the imaginary component of the complex number.
     *                                        If omitted, the suffix is assumed to be "i".
     *
     * @return string
     */
    public static function COMPLEX($realNumber = 0.0, $imaginary = 0.0, $suffix = 'i')
    {
        $realNumber = ($realNumber === null) ? 0.0 : Functions::flattenSingleValue($realNumber);
        $imaginary = ($imaginary === null) ? 0.0 : Functions::flattenSingleValue($imaginary);
        $suffix = ($suffix === null) ? 'i' : Functions::flattenSingleValue($suffix);

        if (((is_numeric($realNumber)) && (is_numeric($imaginary))) &&
            (($suffix == 'i') || ($suffix == 'j') || ($suffix == ''))
        ) {
            $complex = new Complex($realNumber, $imaginary, $suffix);

            return (string) $complex;
        }

        return Functions::VALUE();
    }

    /**
     * IMAGINARY.
     *
     * Returns the imaginary coefficient of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMAGINARY(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the imaginary
     *                                         coefficient
     *
     * @return float
     */
    public static function IMAGINARY($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (new Complex($complexNumber))->getImaginary();
    }

    /**
     * IMREAL.
     *
     * Returns the real coefficient of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMREAL(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the real coefficient
     *
     * @return float
     */
    public static function IMREAL($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (new Complex($complexNumber))->getReal();
    }

    /**
     * IMABS.
     *
     * Returns the absolute value (modulus) of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMABS(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the absolute value
     *
     * @return float
     */
    public static function IMABS($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (new Complex($complexNumber))->abs();
    }

    /**
     * IMARGUMENT.
     *
     * Returns the argument theta of a complex number, i.e. the angle in radians from the real
     * axis to the representation of the number in polar coordinates.
     *
     * Excel Function:
     *        IMARGUMENT(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the argument theta
     *
     * @return float|string
     */
    public static function IMARGUMENT($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        $complex = new Complex($complexNumber);
        if ($complex->getReal() == 0.0 && $complex->getImaginary() == 0.0) {
            return Functions::DIV0();
        }

        return $complex->argument();
    }

    /**
     * IMCONJUGATE.
     *
     * Returns the complex conjugate of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMCONJUGATE(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the conjugate
     *
     * @return string
     */
    public static function IMCONJUGATE($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->conjugate();
    }

    /**
     * IMCOS.
     *
     * Returns the cosine of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMCOS(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the cosine
     *
     * @return float|string
     */
    public static function IMCOS($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->cos();
    }

    /**
     * IMCOSH.
     *
     * Returns the hyperbolic cosine of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMCOSH(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the hyperbolic cosine
     *
     * @return float|string
     */
    public static function IMCOSH($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->cosh();
    }

    /**
     * IMCOT.
     *
     * Returns the cotangent of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMCOT(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the cotangent
     *
     * @return float|string
     */
    public static function IMCOT($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->cot();
    }

    /**
     * IMCSC.
     *
     * Returns the cosecant of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMCSC(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the cosecant
     *
     * @return float|string
     */
    public static function IMCSC($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->csc();
    }

    /**
     * IMCSCH.
     *
     * Returns the hyperbolic cosecant of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMCSCH(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the hyperbolic cosecant
     *
     * @return float|string
     */
    public static function IMCSCH($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->csch();
    }

    /**
     * IMSIN.
     *
     * Returns the sine of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMSIN(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the sine
     *
     * @return float|string
     */
    public static function IMSIN($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->sin();
    }

    /**
     * IMSINH.
     *
     * Returns the hyperbolic sine of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMSINH(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the hyperbolic sine
     *
     * @return float|string
     */
    public static function IMSINH($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->sinh();
    }

    /**
     * IMSEC.
     *
     * Returns the secant of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMSEC(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the secant
     *
     * @return float|string
     */
    public static function IMSEC($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->sec();
    }

    /**
     * IMSECH.
     *
     * Returns the hyperbolic secant of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMSECH(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the hyperbolic secant
     *
     * @return float|string
     */
    public static function IMSECH($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->sech();
    }

    /**
     * IMTAN.
     *
     * Returns the tangent of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMTAN(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the tangent
     *
     * @return float|string
     */
    public static function IMTAN($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->tan();
    }

    /**
     * IMSQRT.
     *
     * Returns the square root of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMSQRT(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the square root
     *
     * @return string
     */
    public static function IMSQRT($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        $theta = self::IMARGUMENT($complexNumber);
        if ($theta === Functions::DIV0()) {
            return '0';
        }

        return (string) (new Complex($complexNumber))->sqrt();
    }

    /**
     * IMLN.
     *
     * Returns the natural logarithm of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMLN(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the natural logarithm
     *
     * @return string
     */
    public static function IMLN($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        $complex = new Complex($complexNumber);
        if ($complex->getReal() == 0.0 && $complex->getImaginary() == 0.0) {
            return Functions::NAN();
        }

        return (string) (new Complex($complexNumber))->ln();
    }

    /**
     * IMLOG10.
     *
     * Returns the common logarithm (base 10) of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMLOG10(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the common logarithm
     *
     * @return string
     */
    public static function IMLOG10($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        $complex = new Complex($complexNumber);
        if ($complex->getReal() == 0.0 && $complex->getImaginary() == 0.0) {
            return Functions::NAN();
        }

        return (string) (new Complex($complexNumber))->log10();
    }

    /**
     * IMLOG2.
     *
     * Returns the base-2 logarithm of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMLOG2(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the base-2 logarithm
     *
     * @return string
     */
    public static function IMLOG2($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        $complex = new Complex($complexNumber);
        if ($complex->getReal() == 0.0 && $complex->getImaginary() == 0.0) {
            return Functions::NAN();
        }

        return (string) (new Complex($complexNumber))->log2();
    }

    /**
     * IMEXP.
     *
     * Returns the exponential of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMEXP(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the exponential
     *
     * @return string
     */
    public static function IMEXP($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->exp();
    }

    /**
     * IMPOWER.
     *
     * Returns a complex number in x + yi or x + yj text format raised to a power.
     *
     * Excel Function:
     *        IMPOWER(complexNumber,realNumber)
     *
     * @param string $complexNumber the complex number you want to raise to a power
     * @param float $realNumber the power to which you want to raise the complex number
     *
     * @return string
     */
    public static function IMPOWER($complexNumber, $realNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);
        $realNumber = Functions::flattenSingleValue($realNumber);

        if (!is_numeric($realNumber)) {
            return Functions::VALUE();
        }

        return (string) (new Complex($complexNumber))->pow($realNumber);
    }

    /**
     * IMDIV.
     *
     * Returns the quotient of two complex numbers in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMDIV(complexDividend,complexDivisor)
     *
     * @param string $complexDividend the complex numerator or dividend
     * @param string $complexDivisor the complex denominator or divisor
     *
     * @return string
     */
    public static function IMDIV($complexDividend, $complexDivisor)
    {
        $complexDividend = Functions::flattenSingleValue($complexDividend);
        $complexDivisor = Functions::flattenSingleValue($complexDivisor);

        try {
            return (string) (new Complex($complexDividend))->divideby(new Complex($complexDivisor));
        } catch (ComplexException $e) {
            return Functions::NAN();
        }
    }

    /**
     * IMSUB.
     *
     * Returns the difference of two complex numbers in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMSUB(complexNumber1,complexNumber2)
     *
     * @param string $complexNumber1 the complex number from which to subtract complexNumber2
     * @param string $complexNumber2 the complex number to subtract from complexNumber1
     *
     * @return string
     */
    public static function IMSUB($complexNumber1, $complexNumber2)
    {
        $complexNumber1 = Functions::flattenSingleValue($complexNumber1);
        $complexNumber2 = Functions::flattenSingleValue($complexNumber2);

        try {
            return (string) (new Complex($complexNumber1))->subtract(new Complex($complexNumber2));
        } catch (ComplexException $e) {
            return Functions::NAN();
        }
    }

    /**
     * IMSUM.
     *
     * Returns the sum of two or more complex numbers in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMSUM(complexNumber[,complexNumber[,...]])
     *
     * @param string ...$complexNumbers Series of complex numbers to add
     *
     * @return string
     */
    public static function IMSUM(...$complexNumbers)
    {
        // Return value
        $returnValue = new Complex(0.0);
        $aArgs = Functions::flattenArray($complexNumbers);

        try {
            // Loop through the arguments
            foreach ($aArgs as $complex) {
                $returnValue = $returnValue->add(new Complex($complex));
            }
        } catch (ComplexException $e) {
            return Functions::NAN();
        }

        return (string) $returnValue;
    }

    /**
     * IMPRODUCT.
     *
     * Returns the product of two or more complex numbers in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMPRODUCT(complexNumber[,complexNumber[,...]])
     *
     * @param string ...$complexNumbers Series of complex numbers to multiply
     *
     * @return string
     */
    public static function IMPRODUCT(...$complexNumbers)
    {
        // Return value
        $returnValue = new Complex(1.0);
        $aArgs = Functions::flattenArray($complexNumbers);

        try {
            // Loop through the arguments
            foreach ($aArgs as $complex) {
                $returnValue = $returnValue->multiply(new Complex($complex));
            }
        } catch (ComplexException $e) {
            return Functions::NAN();
        }

        return (string) $returnValue;
    }

    /**
     * DELTA.
     *
     * Tests whether two values are equal. Returns 1 if number1 = number2; returns 0 otherwise.
     *    Use this function to filter a set of values. For example, by summing several DELTA
     *    functions you calculate the count of equal pairs. This function is also known as the
     * Kronecker Delta function.
     *
     *    Excel Function:
     *        DELTA(a[,b])
     *
     * @param float $a the first number
     * @param float $b The second number. If omitted, b is assumed to be zero.
     *
     * @return int
     */
    public static function DELTA($a, $b = 0)
    {
        $a = Functions::flattenSingleValue($a);
        $b = Functions::flattenSingleValue($b);

        return (int) ($a == $b);
    }

    /**
     * GESTEP.
     *
     *    Excel Function:
     *        GESTEP(number[,step])
     *
     *    Returns 1 if number >= step; returns 0 (zero) otherwise
     *    Use this function to filter a set of values. For example, by summing several GESTEP
     * functions you calculate the count of values that exceed a threshold.
     *
     * @param float $number the value to test against step
     * @param float $step The threshold value.
     *                                    If you omit a value for step, GESTEP uses zero.
     *
     * @return int
     */
    public static function GESTEP($number, $step = 0)
    {
        $number = Functions::flattenSingleValue($number);
        $step = Functions::flattenSingleValue($step);

        return (int) ($number >= $step);
    }

    //
    //    Private method to calculate the erf value
    //
    private static $twoSqrtPi = 1.128379167095512574;

    public static function erfVal($x)
    {
        if (abs($x) > 2.2) {
            return 1 - self::erfcVal($x);
        }
        $sum = $term = $x;
        $xsqr = ($x * $x);
        $j = 1;
        do {
            $term *= $xsqr / $j;
            $sum -= $term / (2 * $j + 1);
            ++$j;
            $term *= $xsqr / $j;
            $sum += $term / (2 * $j + 1);
            ++$j;
            if ($sum == 0.0) {
                break;
            }
        } while (abs($term / $sum) > Functions::PRECISION);

        return self::$twoSqrtPi * $sum;
    }

    /**
     * Validate arguments passed to the bitwise functions.
     *
     * @param mixed $value
     *
     * @return int
     */
    private static function validateBitwiseArgument($value)
    {
        $value = Functions::flattenSingleValue($value);

        if (is_int($value)) {
            return $value;
        } elseif (is_numeric($value)) {
            if ($value == (int) ($value)) {
                $value = (int) ($value);
                if (($value > 2 ** 48 - 1) || ($value < 0)) {
                    throw new Exception(Functions::NAN());
                }

                return $value;
            }

            throw new Exception(Functions::NAN());
        }

        throw new Exception(Functions::VALUE());
    }

    /**
     * BITAND.
     *
     * Returns the bitwise AND of two integer values.
     *
     * Excel Function:
     *        BITAND(number1, number2)
     *
     * @param int $number1
     * @param int $number2
     *
     * @return int|string
     */
    public static function BITAND($number1, $number2)
    {
        try {
            $number1 = self::validateBitwiseArgument($number1);
            $number2 = self::validateBitwiseArgument($number2);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $number1 & $number2;
    }

    /**
     * BITOR.
     *
     * Returns the bitwise OR of two integer values.
     *
     * Excel Function:
     *        BITOR(number1, number2)
     *
     * @param int $number1
     * @param int $number2
     *
     * @return int|string
     */
    public static function BITOR($number1, $number2)
    {
        try {
            $number1 = self::validateBitwiseArgument($number1);
            $number2 = self::validateBitwiseArgument($number2);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $number1 | $number2;
    }

    /**
     * BITXOR.
     *
     * Returns the bitwise XOR of two integer values.
     *
     * Excel Function:
     *        BITXOR(number1, number2)
     *
     * @param int $number1
     * @param int $number2
     *
     * @return int|string
     */
    public static function BITXOR($number1, $number2)
    {
        try {
            $number1 = self::validateBitwiseArgument($number1);
            $number2 = self::validateBitwiseArgument($number2);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $number1 ^ $number2;
    }

    /**
     * BITLSHIFT.
     *
     * Returns the number value shifted left by shift_amount bits.
     *
     * Excel Function:
     *        BITLSHIFT(number, shift_amount)
     *
     * @param int $number
     * @param int $shiftAmount
     *
     * @return int|string
     */
    public static function BITLSHIFT($number, $shiftAmount)
    {
        try {
            $number = self::validateBitwiseArgument($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $shiftAmount = Functions::flattenSingleValue($shiftAmount);

        $result = $number << $shiftAmount;
        if ($result > 2 ** 48 - 1) {
            return Functions::NAN();
        }

        return $result;
    }

    /**
     * BITRSHIFT.
     *
     * Returns the number value shifted right by shift_amount bits.
     *
     * Excel Function:
     *        BITRSHIFT(number, shift_amount)
     *
     * @param int $number
     * @param int $shiftAmount
     *
     * @return int|string
     */
    public static function BITRSHIFT($number, $shiftAmount)
    {
        try {
            $number = self::validateBitwiseArgument($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $shiftAmount = Functions::flattenSingleValue($shiftAmount);

        return $number >> $shiftAmount;
    }

    /**
     * ERF.
     *
     * Returns the error function integrated between the lower and upper bound arguments.
     *
     *    Note: In Excel 2007 or earlier, if you input a negative value for the upper or lower bound arguments,
     *            the function would return a #NUM! error. However, in Excel 2010, the function algorithm was
     *            improved, so that it can now calculate the function for both positive and negative ranges.
     *            PhpSpreadsheet follows Excel 2010 behaviour, and accepts negative arguments.
     *
     *    Excel Function:
     *        ERF(lower[,upper])
     *
     * @param float $lower lower bound for integrating ERF
     * @param float $upper upper bound for integrating ERF.
     *                                If omitted, ERF integrates between zero and lower_limit
     *
     * @return float|string
     */
    public static function ERF($lower, $upper = null)
    {
        $lower = Functions::flattenSingleValue($lower);
        $upper = Functions::flattenSingleValue($upper);

        if (is_numeric($lower)) {
            if ($upper === null) {
                return self::erfVal($lower);
            }
            if (is_numeric($upper)) {
                return self::erfVal($upper) - self::erfVal($lower);
            }
        }

        return Functions::VALUE();
    }

    /**
     * ERFPRECISE.
     *
     * Returns the error function integrated between the lower and upper bound arguments.
     *
     *    Excel Function:
     *        ERF.PRECISE(limit)
     *
     * @param float $limit bound for integrating ERF
     *
     * @return float|string
     */
    public static function ERFPRECISE($limit)
    {
        $limit = Functions::flattenSingleValue($limit);

        return self::ERF($limit);
    }

    //
    //    Private method to calculate the erfc value
    //
    private static $oneSqrtPi = 0.564189583547756287;

    private static function erfcVal($x)
    {
        if (abs($x) < 2.2) {
            return 1 - self::erfVal($x);
        }
        if ($x < 0) {
            return 2 - self::ERFC(-$x);
        }
        $a = $n = 1;
        $b = $c = $x;
        $d = ($x * $x) + 0.5;
        $q1 = $q2 = $b / $d;
        $t = 0;
        do {
            $t = $a * $n + $b * $x;
            $a = $b;
            $b = $t;
            $t = $c * $n + $d * $x;
            $c = $d;
            $d = $t;
            $n += 0.5;
            $q1 = $q2;
            $q2 = $b / $d;
        } while ((abs($q1 - $q2) / $q2) > Functions::PRECISION);

        return self::$oneSqrtPi * exp(-$x * $x) * $q2;
    }

    /**
     * ERFC.
     *
     *    Returns the complementary ERF function integrated between x and infinity
     *
     *    Note: In Excel 2007 or earlier, if you input a negative value for the lower bound argument,
     *        the function would return a #NUM! error. However, in Excel 2010, the function algorithm was
     *        improved, so that it can now calculate the function for both positive and negative x values.
     *            PhpSpreadsheet follows Excel 2010 behaviour, and accepts nagative arguments.
     *
     *    Excel Function:
     *        ERFC(x)
     *
     * @param float $x The lower bound for integrating ERFC
     *
     * @return float|string
     */
    public static function ERFC($x)
    {
        $x = Functions::flattenSingleValue($x);

        if (is_numeric($x)) {
            return self::erfcVal($x);
        }

        return Functions::VALUE();
    }

    /**
     *    getConversionGroups
     * Returns a list of the different conversion groups for UOM conversions.
     *
     * @return array
     */
    public static function getConversionGroups()
    {
        $conversionGroups = [];
        foreach (self::$conversionUnits as $conversionUnit) {
            $conversionGroups[] = $conversionUnit['Group'];
        }

        return array_merge(array_unique($conversionGroups));
    }

    /**
     *    getConversionGroupUnits
     * Returns an array of units of measure, for a specified conversion group, or for all groups.
     *
     * @param string $group The group whose units of measure you want to retrieve
     *
     * @return array
     */
    public static function getConversionGroupUnits($group = null)
    {
        $conversionGroups = [];
        foreach (self::$conversionUnits as $conversionUnit => $conversionGroup) {
            if (($group === null) || ($conversionGroup['Group'] == $group)) {
                $conversionGroups[$conversionGroup['Group']][] = $conversionUnit;
            }
        }

        return $conversionGroups;
    }

    /**
     * getConversionGroupUnitDetails.
     *
     * @param string $group The group whose units of measure you want to retrieve
     *
     * @return array
     */
    public static function getConversionGroupUnitDetails($group = null)
    {
        $conversionGroups = [];
        foreach (self::$conversionUnits as $conversionUnit => $conversionGroup) {
            if (($group === null) || ($conversionGroup['Group'] == $group)) {
                $conversionGroups[$conversionGroup['Group']][] = [
                    'unit' => $conversionUnit,
                    'description' => $conversionGroup['Unit Name'],
                ];
            }
        }

        return $conversionGroups;
    }

    /**
     *    getConversionMultipliers
     * Returns an array of the Multiplier prefixes that can be used with Units of Measure in CONVERTUOM().
     *
     * @return array of mixed
     */
    public static function getConversionMultipliers()
    {
        return self::$conversionMultipliers;
    }

    /**
     * CONVERTUOM.
     *
     * Converts a number from one measurement system to another.
     *    For example, CONVERT can translate a table of distances in miles to a table of distances
     * in kilometers.
     *
     *    Excel Function:
     *        CONVERT(value,fromUOM,toUOM)
     *
     * @param float $value the value in fromUOM to convert
     * @param string $fromUOM the units for value
     * @param string $toUOM the units for the result
     *
     * @return float|string
     */
    public static function CONVERTUOM($value, $fromUOM, $toUOM)
    {
        $value = Functions::flattenSingleValue($value);
        $fromUOM = Functions::flattenSingleValue($fromUOM);
        $toUOM = Functions::flattenSingleValue($toUOM);

        if (!is_numeric($value)) {
            return Functions::VALUE();
        }
        $fromMultiplier = 1.0;
        if (isset(self::$conversionUnits[$fromUOM])) {
            $unitGroup1 = self::$conversionUnits[$fromUOM]['Group'];
        } else {
            $fromMultiplier = substr($fromUOM, 0, 1);
            $fromUOM = substr($fromUOM, 1);
            if (isset(self::$conversionMultipliers[$fromMultiplier])) {
                $fromMultiplier = self::$conversionMultipliers[$fromMultiplier]['multiplier'];
            } else {
                return Functions::NA();
            }
            if ((isset(self::$conversionUnits[$fromUOM])) && (self::$conversionUnits[$fromUOM]['AllowPrefix'])) {
                $unitGroup1 = self::$conversionUnits[$fromUOM]['Group'];
            } else {
                return Functions::NA();
            }
        }
        $value *= $fromMultiplier;

        $toMultiplier = 1.0;
        if (isset(self::$conversionUnits[$toUOM])) {
            $unitGroup2 = self::$conversionUnits[$toUOM]['Group'];
        } else {
            $toMultiplier = substr($toUOM, 0, 1);
            $toUOM = substr($toUOM, 1);
            if (isset(self::$conversionMultipliers[$toMultiplier])) {
                $toMultiplier = self::$conversionMultipliers[$toMultiplier]['multiplier'];
            } else {
                return Functions::NA();
            }
            if ((isset(self::$conversionUnits[$toUOM])) && (self::$conversionUnits[$toUOM]['AllowPrefix'])) {
                $unitGroup2 = self::$conversionUnits[$toUOM]['Group'];
            } else {
                return Functions::NA();
            }
        }
        if ($unitGroup1 != $unitGroup2) {
            return Functions::NA();
        }

        if (($fromUOM == $toUOM) && ($fromMultiplier == $toMultiplier)) {
            //    We've already factored $fromMultiplier into the value, so we need
            //        to reverse it again
            return $value / $fromMultiplier;
        } elseif ($unitGroup1 == 'Temperature') {
            if (($fromUOM == 'F') || ($fromUOM == 'fah')) {
                if (($toUOM == 'F') || ($toUOM == 'fah')) {
                    return $value;
                }
                $value = (($value - 32) / 1.8);
                if (($toUOM == 'K') || ($toUOM == 'kel')) {
                    $value += 273.15;
                }

                return $value;
            } elseif ((($fromUOM == 'K') || ($fromUOM == 'kel')) &&
                (($toUOM == 'K') || ($toUOM == 'kel'))
            ) {
                return $value;
            } elseif ((($fromUOM == 'C') || ($fromUOM == 'cel')) &&
                (($toUOM == 'C') || ($toUOM == 'cel'))
            ) {
                return $value;
            }
            if (($toUOM == 'F') || ($toUOM == 'fah')) {
                if (($fromUOM == 'K') || ($fromUOM == 'kel')) {
                    $value -= 273.15;
                }

                return ($value * 1.8) + 32;
            }
            if (($toUOM == 'C') || ($toUOM == 'cel')) {
                return $value - 273.15;
            }

            return $value + 273.15;
        }

        return ($value * self::$unitConversions[$unitGroup1][$fromUOM][$toUOM]) / $toMultiplier;
    }
}
