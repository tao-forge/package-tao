<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 * @license GPLv2
 * @package generis
 *
 */

namespace   oat\generis\model\kernel\persistence\smoothsql\search;

use common_persistence_Manager;
use common_persistence_SqlPersistence;
use oat\oatbox\service\ServiceManager;
use oat\search\base\exception\SearchGateWayExeption;
use oat\search\base\QueryBuilderInterface;
use oat\search\TaoSearchGateWay;

/**
 * Description of GateWay
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class GateWay extends TaoSearchGateWay {
    
    /**
     *
     * @var common_persistence_SqlPersistence 
     */
    protected $connector;
    /**
     * parser service or className
     * @var string 
     */
    protected $parserList = [
        'taoRdf' => 'search.tao.parser'
    ];
    /**
     * driver escaper list
     * @var array 
     */
    protected $driverList = [
        'taoRdf' => 'search.driver.tao'
    ];
    
    /**
     * resultSet service or className
     * @var string 
     */
    protected $resultSetClassName = '\\oat\\generis\\model\\kernel\\persistence\\smoothsql\\search\\TaoResultSet';
    
    public function __construct() {
        $this->connector = ServiceManager::getServiceManager()
                ->get(common_persistence_Manager::SERVICE_KEY)
                ->getPersistenceById('default');
    }

        /**
     * try to connect to database. throw an exception
     * if connection failed.
     *
     * @throws SearchGateWayExeption
     * @return $this
     */
    public function connect() {
        return !is_null($this->connector);
    }
    /**
     * execute Parsed Query
     * 
     * @return type
     */
    public function search(QueryBuilderInterface $Builder) {
        $this->serialyse($Builder);
        $statement = $this->connector->query($this->parsedQuery);
        $result    = $this->statementToArray($statement);
        $cpt       = $this->count($Builder);
        $resultSet = $this->resultSetClassName;
        return new $resultSet($result , $cpt);
    }

        /**
     * 
     * @param \PDOStatement $statement
     * @return array
     */
    protected function statementToArray(\PDOStatement $statement) {
        $result = [];
        while($row = $statement->fetch(\PDO::FETCH_OBJ)) {
            $result[] = $row;
        }
        return $result;
    }


    /**
     * return total count result
     * @param QueryBuilderInterface $Builder
     * @return type
     */
    public function count(QueryBuilderInterface $Builder) {
        $this->parsedQuery = $this->getSerialyser()->setCriteriaList($Builder)->count(true)->serialyse();
        $statement = $this->connector->query($this->parsedQuery);
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        return $result['cpt'];
    }
    
        
    public function getJoiner() {
        $joiner = new QueryJoiner();
        $options = $this->getOptions();
        $joiner->setDriverEscaper($this->getDriverEscaper())->setOptions($options);
        $joiner->setParent($this);
        return $joiner;
    }
    
    public function join(QueryBuilderInterface $main , QueryBuilderInterface $join , $on , array $sort = [] , $limit = 0 , $offset = null) {
        $joiner = $this->getJoiner();
        $query = $joiner->setQuery($main)->join($join)->on($on)->sort($sort)
                ->setLimit($limit)->setOffset($offset)->execute();
 
        $queryCount = $joiner->count();
        
        $statement = $this->connector->query($queryCount);
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        $cpt = $result['cpt'];
        
        $statement = $this->connector->query($query);
        $result    = $this->statementToArray($statement);
        $resultSet = $this->resultSetClassName;
        return new $resultSet($result , $cpt);
    }

        /**
     * return parsed query as string
     * @return $this
     */
    public function printQuery() {
        echo $this->parsedQuery;
        return $this;
    }
    
}
