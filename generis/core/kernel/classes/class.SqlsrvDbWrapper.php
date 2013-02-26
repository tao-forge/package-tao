<?php

error_reporting(E_ALL);

/**
 * Microsoft SQL Server Database Wrapper
 *
 * @author Lionel Lecaque, <lionel@taotesting.com>
 * @package core
 * @subpackage kernel_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Simple utility class that allow you to wrap the database connector.
 * You can retrieve an instance evreywhere using the singleton method.
 *
 * This database wrapper uses PDO.
 *
 * @author Lionel Lecaque, <lionel@taotesting.com>
 */
require_once('core/kernel/classes/class.DbWrapper.php');

/* user defined includes */
// section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F7E-includes begin
// section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F7E-includes end

/* user defined constants */
// section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F7E-constants begin
// section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F7E-constants end

/**
 * Microsoft SQL Server Database Wrapper
 *
 * @access public
 * @author Lionel Lecaque, <lionel@taotesting.com>
 * @package core
 * @subpackage kernel_classes
 */
class core_kernel_classes_SqlsrvDbWrapper
    extends core_kernel_classes_DbWrapper
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getExtraConfiguration
     *
     * @access public
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @return array
     */
    public function getExtraConfiguration()
    {
        $returnValue = array();

        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F83 begin
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F83 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getTableNotFoundErrorCode
     *
     * @access public
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @return string
     */
    public function getTableNotFoundErrorCode()
    {
        $returnValue = (string) '';

        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F89 begin
        $returnValue = '42S02';
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F89 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getColumnNotFoundErrorCode
     *
     * @access public
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @return string
     */
    public function getColumnNotFoundErrorCode()
    {
        $returnValue = (string) '';

        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F8B begin
        $returnValue = '42S22';
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F8B end

        return (string) $returnValue;
    }

    /**
     * Short description of method afterConnect
     *
     * @access public
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @return void
     */
    public function afterConnect()
    {
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F8D begin
        $this->dbConnector->exec("SET NAMES 'UTF8'");
        $this->dbConnector->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F8D end
    }

    /**
     * Retrieve an array of Tables of the databases
     *
     * @access public
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @return array
     */
    public function getTables()
    {
        $returnValue = array();

        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F8F begin
        $result = $this->dbConnector->query('SELECT "TABLE_NAME" FROM "INFORMATION_SCHEMA"."tables"');
        while ($t = $result->fetchColumn(0)){
            $tables[] = $t;
        }
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F8F end

        return (array) $returnValue;
    }

    /**
     * Short description of method getIndexAlreadyExistsErrorCode
     *
     * @access public
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @return string
     */
    public function getIndexAlreadyExistsErrorCode()
    {
        $returnValue = (string) '';

        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F97 begin
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F97 end

        return (string) $returnValue;
    }

    /**
     * Short description of method limitStatement
     *
     * @access public
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @param  string statement
     * @param  int limit
     * @param  int offset
     * @return string
     */
    public function limitStatement($statement, $limit, $offset = 0)
    {
        $returnValue = (string) '';

        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F99 begin
         if ($limit > 0) {
            if ($offset == 0) {
                //add TOP after select if no offset defined
                $returnValue = preg_replace('/^(SELECT\s(DISTINCT\s)?)/i', '\1TOP ' . $limit . ' ', $statement);
            }
            else{
                //looking for order by
                $orderBy = stristr($statement, 'ORDER BY');

                if (!$orderBy) {
                    $over = 'ORDER BY (SELECT 0)';
                } else {
                    $over = preg_replace('/\"[^,]*\".\"([^,]*)\"/i', '"inner_tbl"."$1"', $orderBy);
                }
                // Remove ORDER BY clause from $statement
                $statement = preg_replace('/\s+ORDER BY(.*)/', '', $statement);
                $statement = preg_replace('/^SELECT\s/', '', $statement);

                $start = $offset + 1;
                $end = $offset + $limit;

                $returnValue = 'WITH results AS
                            ( SELECT ROW_NUMBER() OVER (' . $over . ') AS RowNum,'.  $statement .')
                            SELECT * FROM results WHERE RowNum BETWEEN '.  $start. 'AND' . $end;

            }
        }
        else {
            common_Logger::e('Could not limitStatement to negative value');
        }
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F99 end

        return (string) $returnValue;
    }

    /**
     * Provide driver specific Dsn to connect to the db
     *
     * @access protected
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @return string
     */
    protected function getDSN()
    {
        $returnValue = (string) '';

        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001FA1 begin
        $driver = str_replace('pdo_', '', SGBD_DRIVER);
        $dbName = DATABASE_NAME;
        $dbUrl = DATABASE_URL;
        $returnValue  = $driver . ':server=' . $dbUrl. ';database=' . $dbName;
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001FA1 end

        return (string) $returnValue;
    }

    /**
     * Short description of method createIndex
     *
     * @access public
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @param  string indexName
     * @param  string tableName
     * @param  array columns
     * @return void
     */
    public function createIndex($indexName, $tableName, $columns)
    {
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001FA3 begin
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001FA3 end
    }

    /**
     * Short description of method rebuildIndexes
     *
     * @access public
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @param  string tableName
     * @return void
     */
    public function rebuildIndexes($tableName)
    {
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001FA9 begin
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001FA9 end
    }

    /**
     * Short description of method flush
     *
     * @access public
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @param  string tableName
     * @return void
     */
    public function flush($tableName)
    {
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001FAB begin
        return;
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001FAB end
    }

    /**
     * Short description of method getColumnNames
     *
     * @access public
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @param  string table
     * @return array
     */
    public function getColumnNames($table)
    {
        $returnValue = array();

        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001FAE begin
        $table = $this->dbConnector->quote($table);
        $result = $this->dbConnector->query(
                            'SELECT "COLUMN_NAME" FROM "INFORMATION_SCHEMA"."COLUMNS"
                             WHERE "TABLE_NAME" = ' . $table);
        while ($c = $result->fetchColumn(0)){
            $returnValue[] = $c;
        }
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001FAE end

        return (array) $returnValue;
    }

} /* end of class core_kernel_classes_SqlsrvDbWrapper */

?>