<?php

namespace Doyevaristo\LiquetDatabase;
use Doyevaristo\LiquetDatabase\Exception\Exception;
use Doyevaristo\LiquetDatabase\Exception\FileNotFoundException;
use Doyevaristo\LiquetDatabase\Exception\ImportErrorException;
use Doyevaristo\LiquetDatabase\Exception\InvalidQueryException;


/**
 * Class LiquetCSVImporter
 * @package Doyevaristo\LiquetDatabase
 */
class LiquetCSVImporter{

    use DataMoverQueryGeneratorTrait;

    private $_liquetDatabase;
    private $_csvReader;
    private $_csvFile;

    private $_prefix;
    private $_tablename;
    private $_columns;

    private $_tablePrimaryKey;

    private $_dataDir;
    private $_dataDirTempFile;


    /**
     * Auto set column from imported CSV. Header will be use as column names
     * @var bool
     */
    public $isAutoSetColumn = true;

    public function __construct(LiquetDatabaseInterface $liquetDatabase, CsvReaderInterface $csvReader){
        $this->_liquetDatabase = $liquetDatabase;
        $this->_csvReader = $csvReader;

        $this->_prefix = 'TEMP_'.time();
    }

    public function getTemporaryTablename(){
        return $this->_prefix.'_'.$this->_tablename;
    }

    public function setCsvFilepath($file){

        if(!file_exists($file)){
            throw new FileNotFoundException($file,Exception::FILE_NOT_FOUND);
        }

        $this->_csvFile = $file;
    }

    public function import($file){

        if(!file_exists($file)){
            throw new FileNotFoundException($file,Exception::FILE_NOT_FOUND);
        }

        $this->setCsvFilepath($file);

        if($this->isAutoSetColumn){
            $this->autoSetColumns();
        }


        //move file to database directory
        $dbDirFilePath = $this->_copyCsvFileToDatabaseDirectory($file);
        $tempTable = $this->_createTemporaryTable();


        //Load data to temporary table
        $this->_loadDataInFile(basename($dbDirFilePath),$tempTable);

        //move temporary data to table
        $this->_moveDataToTable($tempTable,$this->_tablename);

        //remove csv from database directory
        $this->_clearTemporaryFiles();

        return $this;
    }

    public function run(){

    }

    public function table($table){
        $this->_tablename = $table;
        return $this;
    }

    public function setColumns($columns){
        $this->_columns = $columns;
        return $this;
    }

    public function autoSetColumns(){
        $header = CsvReader::getHeader($this->_csvFile);
        $this->setColumns($header);
        return $this;
    }


    private function _moveDataToTable($tempTable,$table){
        $query = $this->generateDataMoverQuery($tempTable,$table,$this->_columns);

        if(!$this->_liquetDatabase->query($query)->run()){
            throw new ImportErrorException('Error moving data from '.$tempTable.' to '.$table,Exception::IMPORT_ERROR);
        }

        return true;

    }

    private function _loadDataInFile($csvFilename,$tempTable,$columns=null){


        if(!$columns){
            $columns = $this->_columns;
        }

        foreach($columns as $i=>$column){
            $columns[$i] = "`{$column}`";
        }

        $strColumns = implode(',',$columns);

        $query = "  LOAD DATA INFILE '{$csvFilename}'
                    INTO TABLE `{$tempTable}`
                    FIELDS TERMINATED BY ','
                    ENCLOSED BY '\"'
                    LINES TERMINATED BY '\\n'
                    IGNORE 1 LINES
                    ({$strColumns})";

//        file_put_contents(APP_VAR_PATH.'query.sql',$query);

        if(!$this->_liquetDatabase->query($query)->run()){
            throw new ImportErrorException('Error importing '.$csvFilename.' to '.$tempTable,Exception::IMPORT_ERROR);
        }

        return true;
    }

    private function _copyCsvFileToDatabaseDirectory($csvFilePath){

        $databaseDir = $this->_getDatabaseDataDir();
        $csvFilePathInfo = pathinfo($csvFilePath);

        $dbDirFilePath = $databaseDir.$csvFilePathInfo['basename'];

        LiquetFileHelper::copy($csvFilePath,$dbDirFilePath);

        $this->_dataDirTempFile = $dbDirFilePath;

        return $dbDirFilePath;
    }

    /**
     * create temporary table for data holder
     */
    private function _createTemporaryTable(){

        $tempTable = $this->getTemporaryTablename();
        $this->_liquetDatabase->query("DROP TABLE IF EXISTS {$tempTable}")->run();
        $this->_liquetDatabase->query("CREATE TEMPORARY TABLE {$tempTable} LIKE {$this->_tablename}")->run();

        return $tempTable;
    }


    private function _clearTemporaryFiles(){

        LiquetFileHelper::remove($this->_dataDirTempFile);

    }


    /**
     * load data from csv to temporary table
     */
    private function _loadDataToTemporaryTable(){

    }


    private function _getTablePrimaryKey(){

        $result = $this->_liquetDatabase->query("Show index from {$this->_tablename} where key_name='PRIMARY'");
        $this->_tablePrimaryKey = $result['Column_name'];
        return $this->_tablePrimaryKey;

    }

    /**
     * Get the path of directory where it can import csv file.
     * @return string
     * @throws InvalidQueryException
     */
    private function _getDatabaseDataDir(){
        $query = "show variables where Variable_Name='datadir'";
        try{
            $record = $this->_liquetDatabase->query($query)->fetch();
            $dataDir = reset($record);
            $dataDir = $dataDir['Value'];
        }catch (Exception $e){
            throw new InvalidQueryException($query,Exception::INVALID_QUERY);
        }

        if(PHP_OS === "CYGWIN") {
            $dataDir = LiquetFileHelper::winToCygwinPath($dataDir);
        }

        $databaseName = $this->_liquetDatabase->getDatabaseName();

        $dataDir.=$databaseName.DIRECTORY_SEPARATOR;

        return $dataDir;
    }




}