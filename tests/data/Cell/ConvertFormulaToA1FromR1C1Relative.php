<?php

return [
    ['=N18+O18', '=R[2]C[1]+R[2]C[2]', 16, 13],
    ['=SUM(E1:E5)', '=SUM(R[-4]C:RC)', 5, 5],
    ['=CONCAT("Result of formula expression =R[-2]C[-2]+R[-1]C[-2] is: ", C3+C4)', '=CONCAT("Result of formula expression =R[-2]C[-2]+R[-1]C[-2] is: ", R[-2]C[-2]+R[-1]C[-2])', 5, 5],
];
