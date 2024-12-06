<?php


use NumberToWords\NumberToWords;

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

if (!function_exists('getAmountInWords'))
{
    function getAmountInWords($amount)
    {
        // Инициализируем библиотеку
        $numberToWords = new NumberToWords();
        // Генератор для русского языка
        $numberTransformer = $numberToWords->getNumberTransformer('ru');
        // Преобразуем сумму
        $amountInWords = $numberTransformer->toWords((int) $amount);
        // Разделение копеек
        $kopecks = round(($amount - floor($amount)) * 100);
        return ucfirst($amountInWords) . " рублей" . ($kopecks > 0 ? " $kopecks копеек" : '');
    }
}
