<?php

namespace Doyevaristo\LiquetDatabase;

interface CsvReaderInterface{
    /**
     * Get first line items of CSV file
     * @param $file
     * @return array array of first line
     */
    public static function getHeader($file);
}