<?php

namespace App\Helpers;

class Terbilang {
    public static function make($angka) {
        $angka = abs($angka);
        $satuan = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        
        if ($angka < 12) {
            return $satuan[$angka];
        } elseif ($angka < 20) {
            return self::make($angka - 10) . " Belas";
        } elseif ($angka < 100) {
            return self::make(intval($angka / 10)) . " Puluh " . self::make($angka % 10);
        } elseif ($angka < 200) {
            return "Seratus " . self::make($angka - 100);
        } elseif ($angka < 1000) {
            return self::make(intval($angka / 100)) . " Ratus " . self::make($angka % 100);
        } elseif ($angka < 2000) {
            return "Seribu " . self::make($angka - 1000);
        } elseif ($angka < 1000000) {
            return self::make(intval($angka / 1000)) . " Ribu " . self::make($angka % 1000);
        } elseif ($angka < 1000000000) {
            return self::make(intval($angka / 1000000)) . " Juta " . self::make($angka % 1000000);
        } else {
            return "Angka terlalu besar";
        }
    }
}
