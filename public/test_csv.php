<?php
$line = '"3171012304850001, Ahmad Subagja, L, Jl. Melati No. 5, Jakarta Selatan, \'081234567890, ahmad.subagja@email.com, 2026-01-10, aktif"';
$expectedColumns = [1,2,3];
$delimiter = ';';

$row = str_getcsv($line, $delimiter);
if (count($row) === 1 && count($expectedColumns) > 1) {
    $fallbackDelimiter = ($delimiter === ';') ? ',' : ';';
    if (strpos($line, $fallbackDelimiter) !== false) {
        $fallbackRow = str_getcsv($line, $fallbackDelimiter);
        if (count($fallbackRow) > count($row)) {
            $row = $fallbackRow;
        }
    }
    
    if (count($row) === 1 && preg_match('/^"(.*)"$/', $line, $m)) {
        $unwrapped = $m[1];
        $rComma = str_getcsv($unwrapped, ',');
        $rSemi = str_getcsv($unwrapped, ';');
        if (count($rComma) > count($rSemi) && count($rComma) > 1) {
            $row = $rComma;
        } elseif (count($rSemi) > 1) {
            $row = $rSemi;
        }
    }
}
print_r($row);
