<?php

return [
    ['=B4+C4', '=R4C2+R4C3'],
    ['=B3+C3', '=R[2]C[1]+R[2]C[2]'],
    ['=SUM(B1:B4)', '=SUM(R1C2:R4C2)'],
    ['=CONCAT("Result of formula expression =R1C1+R1C2 is: ", A1+B1)', '=CONCAT("Result of formula expression =R1C1+R1C2 is: ", R1C1+R1C2)'],
];
