<?php

namespace Doyevaristo\LiquetDatabase;

class LiquetToolbox extends LiquetDatabase{

    public function __construct($user, $pass, $database, $host = 'localhost')
    {
        parent::__construct($user, $pass, $database, $host);
    }

}