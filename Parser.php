<?php

namespace Parser;

include '/phpQuery.php';

abstract class Parser
{
    /**
     * Возращает массив с версткой и информацией о странице
     * 
     * @param string $url URL страницы
     * 
     * @return array В массиве два ключа, html и info. html - Вертска, info - Информация о странице
     */
    public static function getPage(string $url): array
    {
        if ($url) {
            $newUrl = urldecode($url);
            $newUrl = trim($newUrl);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $newUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            $content = curl_exec($ch);
            $info =  curl_getinfo($ch);

            curl_close($ch);
            return [
                'html' => $content,
                'info' => $info
            ];
        } else {
            return [];
        }
    }

    /**
     * Возращает массив из URL'ов из файла
     * 
     * @param string $fileName Путь к файлу
     * 
     * @return array Массив с URL'ами
     */
    public static function getUrlsFromFile(string $fileName): array
    {
        $urlsArr = [];
        if (file_exists($fileName) && is_readable($fileName)) {
            $text = fopen($fileName, 'r');
            while (($buffer = fgets($text)) !== false) {
                $urlsArr[] = $buffer;
            }
        }
        return $urlsArr;
    }

    /**
     * @param object $parseObject Объект phpQuery
     * @param string $elementSelector Селектор элемента из которого получаем данные
     * 
     * @return string Все что находится в указанном элементе.
     */
    public static function getTextFromElement(object $parseObject, string $elementSelector): string
    {
        $text = '';
        if ($parseObject && $elementSelector) {
            $entry = $parseObject->find($elementSelector);
            $text  = pq($entry)->text();
        }
        return $text;
    }

    /**
     * @param int $number Порядковый номер элемента
     * @param object $parseObject Объект phpQuery
     * @param string $elementSelector Селектор элемента из которого получаем данные
     * 
     * @return [type]
     */
    public static function getNubmeredTextFromElement(object $parseObject, string $elementSelector, array $number = [])
    {
        $text = '';
        if ($parseObject && $elementSelector) {
            $entry = $parseObject->find($elementSelector);
            foreach ($entry as $row) {
                $data[] = pq($row)->text();
            }
            if (!empty($number)) {
                return $data[$number[0]];
            } else {
                return $data;
            }
        }
    }

    /**
     * @param object $parseObject Объект phpQuery
     * @param string $elementSelector Селектор элемента из которого получаем данные
     * 
     * @return string Все что находится в указанном элементе в HTML виде.
     */
    public static function getHtmlFromElement(object $parseObject, string $elementSelector): string
    {
        $html = '';
        if ($parseObject && $elementSelector) {
            $entry = $parseObject->find($elementSelector);
            $html  = pq($entry)->html();
        }
        return $html;
    }

    /**
     * Перегон массива в csv
     * 
     * @param array $arr Массив с данными
     * 
     * @return void
     */
    public static function toCsv(array $arr, string $fileName): void
    {
        $fp = fopen($fileName, 'w');

        foreach ($arr as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);
    }


    /**
     * @param string $url  URL по которому лежит файл
     * @param string $filename Задаем имя файла
     * @param string $dir Папка для сохранения
     * 
     * @return void
     */
    public static function downloadFile(string $url, string $dir, string $filename)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $html = curl_exec($ch);
        curl_close($ch);

        mkdir($dir, 0777, true);

        file_put_contents($dir . "/" . $filename, $html);
    }

    /**
     * @param string $url  URL по которому лежит файл
     * @param string $filename Задаем имя файла
     * @param string $dir Папка для сохранения
     * 
     * @return void
     */
    public static function downloadFilePdf(string $url, string $fileName, string $dir)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $html = curl_exec($ch);
        curl_close($ch);

        file_put_contents($dir . $fileName, $html);
    }


    /**
     * Возращает расширение файла
     * @param mixed $filename
     * 
     * @return string
     */
    function getExtension($filename): string
    {
        return strtolower(end(explode(".", $filename)));
    }
}
