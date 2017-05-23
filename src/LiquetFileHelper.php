<?php

namespace Doyevaristo\LiquetDatabase;

use Doyevaristo\LiquetDatabase\Exception\Exception;

class LiquetFileHelper{


    public static function copy($from,$to){

        $toDir = dirname($to);

        if (!is_dir($toDir)){
            throw new DirectoryNotFoundException($toDir.' not found.',Exception::DIRECTORY_NOT_FOUND);
        }

        if(!copy($from,$to)){
            throw new Exception('Error copying file from '.$from.' to '.$to);
        }

    }

    public static function remove($file,$ext='csv'){
        return unlink($file);
    }


    public static function winToCygwinPath($path){

        $regex = '/[a-zA-Z]\:\\\\/';

        preg_match_all($regex, $path, $match, PREG_SET_ORDER, 0);

        if(!isset($match[0][0])){
            throw new Exception('Invalid path '.$path);
        }

        $driveLetter = strtolower(str_replace(':\\','',$match[0][0]));

        $path = str_replace($match[0][0],"/cygdrive/{$driveLetter}/",$path);
        $path = str_replace('\\', '/', $path);

        return $path;
    }
}