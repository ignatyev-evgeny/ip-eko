<?php


if (!function_exists('getTextAfterFirstDashIfMatched')) {
    function getTextAfterFirstDashIfMatched(string $string) {
        $validStatuses = ['Активный', 'Закрыт', 'Согласование', 'Приостановлен'];
        if (strpos($string, '-') !== false) {
            $parts = explode('-', $string, 2);
            if (in_array(trim($parts[0]), $validStatuses)) {
                return trim($parts[1]);
            }
        }
        return trim($string);
    }
}
