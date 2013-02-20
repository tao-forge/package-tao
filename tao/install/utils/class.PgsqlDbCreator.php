<?php

/**
 * Dedicated database wrapper used for database creation in
 * a PostgreSQL context.
 * 
 * @see PDO
 * @author Bertrand CHEVRIER <bertrand.chevrier@tudor.lu>
 * @author Jerome BOGAERTS <jerome.bogaerts@tudor.lu>
 *
 */
class tao_install_utils_PgsqlDbCreator extends tao_install_utils_DbCreator{
	
	public function chooseSQLParsers(){
		$this->setSQLParser(new tao_install_utils_SimpleSQLParser());
		$this->setProcSQLParser(new tao_install_utils_PostgresProceduresParser());
	}
	
	/**
	 * Check if the database exists already
	 * @param string $name
	 */
	public function dbExists($dbName)
	{
		$result = $this->pdo->query('SELECT "datname" FROM "pg_database"');
		$databases = array();
		while($db = $result->fetchColumn(0)){
			$databases[] = $db;
		}
		
		if (in_array($dbName, $databases)){
			return true;
		}
		return false;
	}
	
	/**
	 * Clean database by droping all tables
	 * @param string $name
	 */
	public function cleanDb()
	{
		$tables = array();
		$result = $this->pdo->query('SELECT "table_name" FROM "information_schema"."tables" WHERE "table_schema" = \'public\'');
		
		while ($t = $result->fetchColumn(0)){
			$tables[] = $t;
		}

		foreach ($tables as  $t){
			$this->pdo->exec("DROP TABLE \"${t}\"");
		}
	}
	
	public function createDatabase($name){
		$this->pdo->exec('CREATE DATABASE "' . $name . '"');
		$this->setDatabase($name);
	}
	
	protected function afterConnect(){
		$this->pdo->exec("SET NAMES 'UTF8'");
	}
	
	protected function getExtraConfiguration(){
		return array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);	
	}
	
	protected function getDiscoveryDSN(){
		$driver = str_replace('pdo_', '', $this->driver);
		$dsn  = $driver . ':host=' . $this->host;
		return $dsn;
	}
	
	protected function getDatabaseDSN(){
		$driver = str_replace('pdo_', '', $this->driver);
		$dbName = $this->dbName;
		$dsn  = $driver . ':dbname=' . $dbName . ';host=' . $this->host;
		return $dsn;
	}
}
?>