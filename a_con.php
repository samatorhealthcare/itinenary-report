<?php

function generate_sppd_code($year, $month) {
    // Validate the input year and month
    if (!is_numeric($year) || !is_numeric($month) || $year < 2024 || $month < 1 || $month > 12) {
        return false; // Invalid input
    }

    // Generate a random 4-digit number
    $randomNumber = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

    // Construct the SPPD code
    $sppdCode = "SPPD-$year-$month-000$randomNumber";

    return $sppdCode;
}

// Example usage:
$year = 2024;
$month = 10;
$sppdCode = generate_sppd_code($year, $month);

if ($sppdCode) {
    echo "Generated SPPD code: $sppdCode";
} else {
    echo "Invalid input year or month.";
}
?>