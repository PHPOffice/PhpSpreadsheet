<?php

return [
    [
        '=DAGEN360(DATUM(2010;2;5);DATUM(2010;12;31);WAAR)',
        'nl',
        '=DAYS360(DATE(2010,2,5),DATE(2010,12,31),TRUE)',
    ],
    [
        '=DIAS360(DATA(2010;2;5);DATA(2010;12;31);VERDADEIRO)',
        'pt_br',
        '=DAYS360(DATE(2010,2,5),DATE(2010,12,31),TRUE)',
    ],
    [
        '=ДНЕЙ360(ДАТА(2010;2;5);ДАТА(2010;12;31);ИСТИНА)',
        'ru',
        '=DAYS360(DATE(2010,2,5),DATE(2010,12,31),TRUE)',
    ],
    [
        '=TEKST.SAMENVOEGEN(A1; " "; B1)',
        'nl',
        '=CONCATENATE(A1, " ", B1)',
    ],
    [
        '=TEKST.SAMENVOEGEN("""Hello "; B1; ""","; " I said.")',
        'nl',
        '=CONCATENATE("""Hello ", B1, """,", " I said.")',
    ],
    [
        '=TEKST.SAMENVOEGEN(JAAR(VANDAAG());
            " is ";
            ALS(
                DAGEN(DATUM(JAAR(VANDAAG())+1; 1; 1); DATUM(JAAR(VANDAAG()); 1; 1)) = 365;
                "NOT a Leap Year";
                "a Leap Year"
            )
        )',
        'nl',
        '=CONCATENATE(YEAR(TODAY()),
            " is ",
            IF(
                DAYS(DATE(YEAR(TODAY())+1, 1, 1), DATE(YEAR(TODAY()), 1, 1)) = 365,
                "NOT a Leap Year",
                "a Leap Year"
            )
        )',
    ],
];
