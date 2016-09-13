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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 * @license GPLv2
 * @package generis
 *
 */
namespace   oat\generis\model\kernel\persistence\smoothsql\search;

use core_kernel_persistence_smoothsql_SmoothModel;
use oat\oatbox\service\ConfigurableService;
use oat\search\base\QueryBuilderInterface;
use oat\search\base\SearchGateWayInterface;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;
/**
 * Complexe search service
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class ComplexSearchService extends ConfigurableService
{
    
    const SERVICE_ID = 'generis/complexSearch';
    
    const SERVICE_SEARCH_ID = 'search.tao.gateway';

    /**
     * internal service locator
     * @var \Zend\ServiceManager\ServiceLocatorInterface 
     */
    protected $services;
    /**
     * search gateway
     * @var SearchGateWayInterface
     */
    protected $gateway;
    /**
     * 
     * @param array $options
     */
    public function __construct($options = array()) {
        $config         = new Config($options);
        $this->services =  new ServiceManager($config);
        parent::__construct($options);
        
        $this->gateway = $this->services->get(self::SERVICE_SEARCH_ID)
                ->setServiceLocator($this->services)
                ->init();
    }
    /**
     * determine which operator may be used
     * @param boolean $like
     * @return string
     */
    protected function getOperator($like ) {
        $operator = 'equals';
        
        if($like) {
            $operator = 'contains';
        } 
        
        return $operator;
    }
    /**
     * return search gateway
     * @return SearchGateWayInterface
     */
    public function getGateway() {
        return $this->gateway;
    }
    /**
     * return a new query builder
     * @return \oat\search\QueryBuilder
     */
    public function query() {
        return $this->gateway->query();
    }

        /**
     * return a preset query builder with types
     * @param QueryBuilderInterface $query
     * @param string $class_uri
     * @param boolean $recursive
     * @return QueryBuilderInterface
     */
    public function searchType(QueryBuilderInterface $query , $class_uri , $recursive = false) {

        $Class    = new \core_kernel_classes_Class($class_uri);
        $rdftypes = [];
        
        foreach($Class->getSubClasses($recursive) as $subClass){
            $rdftypes[] = $subClass->getUri();
        }
         
        $rdftypes[] = $class_uri;
        
        $criteria = $query->newQuery()
                ->add('http://www.w3.org/1999/02/22-rdf-syntax-ns#type')
                ->in($rdftypes);
        
        return $criteria;
    }
    
    /**
     * set gateway language options
     * @param QueryBuilderInterface $query
     * @param string $userLanguage
     * @param string $defaultLanguage
     * @return $this
     */
    public function setLanguage(QueryBuilderInterface $query , $userLanguage = '' , $defaultLanguage = \DEFAULT_LANG) {
        $options = $this->gateway->getOptions();
        if(!empty($userLanguage)) {
            $options['language'] = $userLanguage;
        }
        $options['defaultLanguage'] = $defaultLanguage;
        
        $this->gateway->setOptions($options);
        
        return $query->newQuery();
    }
    
    protected function parseValue($value) {
        if($value instanceof \core_kernel_classes_Resource ){
            return $value->getUri();
        }
        return $value;
    }
    
    /**
     * verify if value is valid
     * @param string $value
     * @return boolean
     * @throws exception\InvalidValueException
     */
    protected function isValidValue($value) {
        if(is_array($value)) {
                
                if(empty($value)) {
                    throw new exception\InvalidValueException('query filter value cann\'t be empty ');
                }

            } 
            return true;
    }

    /**
     * serialyse a query for searchInstance
     * use for legacy search
     * @param core_kernel_persistence_smoothsql_SmoothModel $model
     * @param array $classUri
     * @param array $propertyFilters
     * @param boolean $and
     * @param boolean $like
     * @param string $lang
     * @param integer $offset
     * @param integer $limit
     * @param string $order
     * @param string $orderDir
     * @return string
     */
    public function getQuery(core_kernel_persistence_smoothsql_SmoothModel $model, $classUri, array $propertyFilters, $and = true, $like = true, $lang = '', $offset = 0, $limit = 0, $order = '', $orderDir = 'ASC') 
    {
        $query = $this->gateway->query()->setLimit( $limit )->setOffset($offset );
        
        if(!empty($order)) {
            $query->sort([$order => strtolower($orderDir)]);
        }
        
        $this->setLanguage($query , $lang);
        
        $operator = $this->getOperator($like);
        
        $criteria = $query->newQuery()
                ->add('http://www.w3.org/1999/02/22-rdf-syntax-ns#type')
                ->in($classUri);
        
        $query->setCriteria($criteria);
        
        foreach ($propertyFilters as $predicate => $value ) {
            
            if(is_array($value) && $this->isValidValue($value)) {
                
                $firstValue = array_shift($value);
            } 
            
            
            $criteria->addCriterion($predicate , $operator , $this->parseValue($firstValue));
            
            foreach ($value as $nextValue) {
                $criteria->addOr($this->parseValue($value));
            }
            if($and === false) {
                $criteria = $query->newQuery()
                ->add('http://www.w3.org/1999/02/22-rdf-syntax-ns#type')
                ->in($classUri);
                $query->setOr($criteria);
            }
        }
        $queryString = $this->gateway->serialyse($query)->getQuery();
        return $queryString;
    }
    
}
