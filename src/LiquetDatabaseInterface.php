<?php

namespace Doyevaristo\LiquetDatabase;

interface LiquetDatabaseInterface{

    function __construct($user, $pass, $database, $host='localhost');
    public function query($query);
    public function getNumberOfRecords();

    /**
     * expects returned record
     * @return mixed
     */
    public function fetch();

    /**
     * queries not expecting records.
     * @return mixed
     */
    public function run();
    public function disconnect();

    /**
     * GETTERS
     */

    public function getDatabaseName();
    public function getHost();
    public function getUser();
}