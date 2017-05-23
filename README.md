# Liquet Database
Simple data MySQL query runner and imports database records from big data CSV. 

Made this library for loading layer of Data Warehousing

### Features:
- runs query in straight forward manner
- Import huge csv. Either update the existing record or add new records. 

Usage: 

~~~~
use Doyevaristo\LiquetDatabase\CsvReader;
use Doyevaristo\LiquetDatabase\LiquetCSVImporter;
use Doyevaristo\LiquetDatabase\LiquetDatabase;




$liquetDatabase = new LiquetDatabase('db_username','db_password','db_database','db_hostname');
$csvReader = new CsvReader();
$csvImporter = new LiquetCSVImporter($liquetDatabase,$csvReader);
$csvImporter
    ->table('your_table_name')
    ->import($file);
~~~~


Notes:
- CSV must have header same with table column name

To Do:
- Improve documentation
- Unit testing

