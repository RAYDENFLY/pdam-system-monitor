<?php

if (!function_exists('terbilang')) {
    function terbilang($angka) {
        $angka = abs($angka);
        $satuan = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan"];
        $angkaStr = ["", "ribu", "juta", "milyar", "triliun"];

        $terbilang = "";
        $i = 0;

        while ($angka > 0) {
            $num = $angka % 1000;
            if ($num != 0) {
                $terbilang = tigaDigit($num) . " " . $angkaStr[$i] . " " . $terbilang;
            }
            $angka = floor($angka / 1000);
            $i++;
        }

        return trim($terbilang);
    }

    function tigaDigit($num) {
        $satuan = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan"];
        $hasil = "";

        if ($num >= 100) {
            $hasil .= $satuan[floor($num / 100)] . " ratus ";
            $num %= 100;
        }

        if ($num >= 10 && $num <= 19) {
            $belasan = ["sepuluh", "sebelas", "dua belas", "tiga belas", "empat belas", "lima belas", "enam belas", "tujuh belas", "delapan belas", "sembilan belas"];
            $hasil .= $belasan[$num - 10] . " ";
        } else {
            if ($num >= 20) {
                $hasil .= $satuan[floor($num / 10)] . " puluh ";
                $num %= 10;
            }
            if ($num > 0) {
                $hasil .= $satuan[$num] . " ";
            }
        }

        return trim($hasil);
    }
}
