<?php
/**
 * DateHelper.php
 * Helper functions for Indonesian date/time formatting
 */

class DateHelper
{
    /**
     * Indonesian day names
     */
    private static $hari = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];

    /**
     * Indonesian month names
     */
    private static $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    /**
     * Format date to Indonesian format
     * @param string $date Date string (YYYY-MM-DD or any valid date format)
     * @param string $format Format type: 'full', 'short', 'long'
     * @return string Formatted date in Indonesian
     */
    public static function formatTanggal($date, $format = 'short')
    {
        if (empty($date) || $date == '0000-00-00') return '-';
        
        $timestamp = strtotime($date);
        if (!$timestamp) return $date;
        
        $day = date('d', $timestamp);
        $month = (int)date('m', $timestamp);
        $year = date('Y', $timestamp);
        $dayName = self::$hari[date('l', $timestamp)];
        
        switch ($format) {
            case 'full':
                // Senin, 06 Januari 2026
                return $dayName . ', ' . $day . ' ' . self::$bulan[$month] . ' ' . $year;
            case 'long':
                // 06 Januari 2026
                return $day . ' ' . self::$bulan[$month] . ' ' . $year;
            case 'short':
            default:
                // 06 Jan 2026
                return $day . ' ' . substr(self::$bulan[$month], 0, 3) . ' ' . $year;
        }
    }

    /**
     * Format time to 24-hour format
     * @param string $time Time string (HH:MM:SS or HH:MM)
     * @return string Formatted time (HH:MM)
     */
    public static function formatWaktu($time)
    {
        if (empty($time)) return '-';
        
        // If already in HH:MM format, return as is
        if (preg_match('/^\d{2}:\d{2}$/', $time)) {
            return $time;
        }
        
        // If in HH:MM:SS format, trim seconds
        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $time)) {
            return substr($time, 0, 5);
        }
        
        return $time;
    }

    /**
     * Get Indonesian day name from date
     * @param string $date Date string
     * @return string Indonesian day name
     */
    public static function getNamaHari($date)
    {
        if (empty($date)) return '-';
        
        $timestamp = strtotime($date);
        if (!$timestamp) return '-';
        
        return self::$hari[date('l', $timestamp)];
    }

    /**
     * Get Indonesian month name
     * @param int $month Month number (1-12)
     * @return string Indonesian month name
     */
    public static function getNamaBulan($month)
    {
        return self::$bulan[$month] ?? '-';
    }

    /**
     * Format datetime to Indonesian format with time
     * @param string $datetime Datetime string
     * @return string Formatted datetime
     */
    public static function formatTanggalWaktu($datetime)
    {
        if (empty($datetime) || $datetime == '0000-00-00 00:00:00') return '-';
        
        $timestamp = strtotime($datetime);
        if (!$timestamp) return $datetime;
        
        $date = date('Y-m-d', $timestamp);
        $time = date('H:i', $timestamp);
        
        return self::formatTanggal($date, 'long') . ' ' . $time;
    }

    /**
     * Convert English day name to Indonesian
     * @param string $dayEn English day name
     * @return string Indonesian day name
     */
    public static function hariKeIndo($dayEn)
    {
        return self::$hari[$dayEn] ?? $dayEn;
    }

    /**
     * Convert Indonesian day name to English
     * @param string $dayId Indonesian day name
     * @return string English day name
     */
    public static function hariKeInggris($dayId)
    {
        $flip = array_flip(self::$hari);
        return $flip[$dayId] ?? $dayId;
    }
}
