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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA
 *
 */

use oat\generis\model\data\RdfInterface;

/**
 * Implementation of the RDF interface for the smooth sql driver
 * 
 * @author joel bout <joel@taotesting.com>
 * @package generis
 */
class core_kernel_persistence_smoothsql_SmoothRdf
    implements RdfInterface
{
    /**
     * @var common_persistence_SqlPersistence
     */
    private $persistence;
    
    public function __construct(common_persistence_SqlPersistence $persistence) {
        $this->persistence = $persistence;
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfInterface::get()
     */
    public function get($subject, $predicate) {
        throw new \common_Exception('Not implemented');
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfInterface::add()
     */
    public function add(\core_kernel_classes_Triple $triple) {
        $query = "INSERT INTO statements ( modelId, subject, predicate, object, l_language) VALUES ( ? , ? , ? , ? , ? );";
        return $this->persistence->exec($query, array($triple->modelid, $triple->subject, $triple->predicate, $triple->object, is_null($triple->lg) ? '' : $triple->lg));
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfInterface::remove()
     */
    public function remove(\core_kernel_classes_Triple $triple) {
        $query = "DELETE FROM statements WHERE subject = ? AND predicate = ? AND object = ? AND l_language = ?;";
        return $this->persistence->exec($query, array($triple->subject, $triple->predicate, $triple->object, is_null($triple->lg) ? '' : $triple->lg));
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfInterface::search()
     */
    public function search($predicate, $object) {
        throw new \common_Exception('Not implemented');
    }
    
    public function getIterator() {
        return new core_kernel_persistence_smoothsql_SmoothIterator($this->persistence);
    }
}