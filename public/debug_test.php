<?php
$line = "ANG-2026-0002,'3171012304850001,Ahmad Subagja,L,Jl. Melati No. 5, Jakarta Selatan,'081234567890,ahmad.subagja@email.com,10/01/2026,Aktif";
$row = str_getcsv($line, ',');
print_r($row);
