<?php

namespace Doyevaristo\LiquetDatabase;


use Doyevaristo\LiquetDatabase\Exception\Exception;
use Doyevaristo\LiquetDatabase\Exception\FileNotFoundException;
use Keboola\Csv\CsvFile;

class CsvReader implements CsvReaderInterface{
    public static function getHeader($file){
        if(!file_exists($file)){
            throw new FileNotFoundException(print_r($file,true),Exception::FILE_NOT_FOUND);
        }
        $csvFile = new CsvFile($file);
        return $csvFile->getHeader();
    }
}