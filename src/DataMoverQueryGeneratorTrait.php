<?php

namespace Doyevaristo\LiquetDatabase;

trait DataMoverQueryGeneratorTrait{

    /**
     * Generate a Insert into Duplicate Key query
     * @param $fromTable
     * @param $toTable
     * @param $tableColumns
     * @param string $tableAlias
     * @return string Generated MySQL Query
     */
    public function generateDataMoverQuery($fromTable,$toTable,$tableColumns,$tableAlias='tmp'){

        $strColumns = implode(",",$tableColumns);

        $arrColumns = [];
        foreach($tableColumns as $column){
            $arrColumns[] = $column.'='.$tableAlias.".".$column;
        }
        $strColumnsWithAlias = implode(",",$arrColumns);

        $query = "INSERT INTO {$toTable} ({$strColumns}) 
        (SELECT $strColumns FROM {$fromTable} {$tableAlias})
        ON DUPLICATE KEY UPDATE {$strColumnsWithAlias}";


        return $query;

    }

}