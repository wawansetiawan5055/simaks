<?php
/**
 * NumberHelper.php
 * Helper functions for number and currency formatting
 */

class NumberHelper
{
    /**
     * Convert number to Indonesian words (Terbilang)
     * @param float $n
     * @return string
     */
    public static function terbilang($n) {
        $n = abs($n);
        $huruf = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
        $temp = "";
        
        if ($n < 12) {
            $temp = " " . $huruf[$n];
        } else if ($n < 20) {
            $temp = self::terbilang($n - 10) . " belas";
        } else if ($n < 100) {
            $temp = self::terbilang($n / 10) . " puluh" . self::terbilang($n % 10);
        } else if ($n < 200) {
            $temp = " seratus" . self::terbilang($n - 100);
        } else if ($n < 1000) {
            $temp = self::terbilang($n / 100) . " ratus" . self::terbilang($n % 100);
        } else if ($n < 2000) {
            $temp = " seribu" . self::terbilang($n - 1000);
        } else if ($n < 1000000) {
            $temp = self::terbilang($n / 1000) . " ribu" . self::terbilang($n % 1000);
        } else if ($n < 1000000000) {
            $temp = self::terbilang($n / 1000000) . " juta" . self::terbilang($n % 1000000);
        } else if ($n < 1000000000000) {
            $temp = self::terbilang($n / 1000000000) . " milyar" . self::terbilang(fmod($n, 1000000000));
        } else if ($n < 1000000000000000) {
            $temp = self::terbilang($n / 1000000000000) . " trilyun" . self::terbilang(fmod($n, 1000000000000));
        }
        
        return trim($temp);
    }

    /**
     * Format to Indonesian Rupiah
     */
    public static function formatRupiah($n) {
        return "Rp " . number_format($n, 0, ',', '.');
    }
}
