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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\taoResultServer\models\classes;

interface ResultManagement extends \taoResultServer_models_classes_ReadableResultStorage {

    /**
     * Get only one property from a variable
     * @param string $variableId on which we want the property
     * @param string $property to retrieve
     * @return int|string the property retrieved
     */
    public function getVariableProperty($variableId, $property);

    /**
     * Get all the ids of the callItem for a specific delivery execution
     * @param string $deliveryResultIdentifier The identifier of the delivery execution
     * @return array the list of call item ids (across all results)
     */
    public function getRelatedItemCallIds($deliveryResultIdentifier);

    /**
     * Get the result information (test taker, delivery, delivery execution) from filters
     * @param array $columns list of columns on which to search : array('http://www.tao.lu/Ontologies/TAOResult.rdf#resultOfSubject','http://www.tao.lu/Ontologies/TAOResult.rdf#resultOfDelivery')
     * @param array $filter list of value to search : array('http://www.tao.lu/Ontologies/TAOResult.rdf#resultOfSubject' => array('test','myValue'))
     * @param array $options params to restrict results such as order, order direction, offset and limit
     * @return array test taker, delivery and delivery result that match the filter : array(array('deliveryResultIdentifier' => '123', 'testTakerIdentifier' => '456', 'deliveryIdentifier' => '789'))
     */
    public function getResultByColumn($columns, $filter, $options = array());

    /**
     * Count the number of result that match the filter
     * @param array $columns list of columns on which to search : array('http://www.tao.lu/Ontologies/TAOResult.rdf#resultOfSubject','http://www.tao.lu/Ontologies/TAOResult.rdf#resultOfDelivery')
     * @param array $filter list of value to search : array('http://www.tao.lu/Ontologies/TAOResult.rdf#resultOfSubject' => array('test','myValue'))
     * @return int the number of results that match filter
     */
    public function countResultByFilter($columns, $filter);


    /**
     * Remove the result and all the related variables
     * @param string $deliveryResultIdentifier The identifier of the delivery execution
     * @return boolean if the deletion was successful or not
     */
    public function deleteResult($deliveryResultIdentifier);

}
?>