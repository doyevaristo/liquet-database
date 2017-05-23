<?php namespace Doyevaristo\LiquetDatabase;


use Doyevaristo\LiquetDatabase\Exception\ConnectionErrorException;
use Doyevaristo\LiquetDatabase\Exception\Exception;
use Doyevaristo\LiquetDatabase\Exception\InvalidMySQLQueryException;

class LiquetDatabase implements LiquetDatabaseInterface{

    private $user;
    private $pass;
    private $host;
    private $database;
    private $query;
    private $_result;
    private $_connection;

    private $output;

    public function __construct($user, $pass, $database, $host = 'localhost')
    {
        $this->user = $user;
        $this->host = $host;
        $this->database = $database;

        $this->_connection = new \mysqli($host,$user,$pass,$database);

        if ($this->_connection->connect_error) {
            throw new ConnectionErrorException("Connection failed: " . $this->_connection->connect_error,Exception::ERROR_CONNECTION);
        }

    }

    public function getDatabaseName()
    {
        return $this->database;
    }

    public function getHost()
    {
        return $this->host;
    }


    public function getUser()
    {
        return $this->user;
    }

    public function query($query){

        $this->query = $query;

        $this->_result = $this->_connection->query($this->query);

        if(!$this->_result){
            throw new InvalidMySQLQueryException($this->query.' is not a valid MySQL Query',Exception::INVALID_QUERY);
        }

        return $this;
    }

    public function getNumberOfRecords(){
        return $this->_result->num_rows;
    }

    public function run(){
        if($this->_result){
            return true;
        }else{
            return false;
        }
    }

    public function fetch(){
        $output = [];
        if($this->_result->num_rows > 0){
            while ($row = $this->_result->fetch_assoc()){
                $output[] = $row;
            }
            $this->output = $output;
        }else{
            $this->output = $this->_result;
        }


        return $this->output;
    }

    public function disconnect(){
        $this->_connection->close();
    }

    public function __destruct()
    {
        $this->_connection->close();
    }

}