<?php

namespace Doyevaristo\LiquetDatabase;

class LiquetHelper{
    public static function removeNewlinesOnString($string){
        $string = trim(preg_replace('/\s+/', ' ', $string));

        $string = str_replace('\\','\\\\',$string);

        return $string;
    }
}