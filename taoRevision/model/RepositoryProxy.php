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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 * 
 */

namespace oat\taoRevision\model;

use oat\taoRevision\model\mock\Repository as MockRepository;

/**
 * A proxy for the repository implementation
 */
class RepositoryProxy
{
    /**
     * @var Repository
     */
    private static $implementation = null;
    
    /**
     * @return Repository
     */
    protected static function getImplementation() {
        if (is_null(self::$implementation)) {
            self::$implementation = new MockRepository();
        }
        return self::$implementation;
    }
    
    /**
     * 
     * @param Repository $repository
     */
    protected function setImplementation(Repository $repository)
    {
        //@todo
    }
    
    /**
     * 
     * @param string $resourceId
     * @return array return an array of Revision objects
     */
    public static function getRevisions($resourceId)
    {
        return self::getImplementation()->getRevisions($resourceId); 
    }
    
    /**
     * 
     * @param string $resourceId
     * @param string $versionId
     * @return Revision
     */
    public static function getRevision($resourceId, $versionId)
    {
        return self::getImplementation()->getRevision($resourceId, $versionId);
    }
    
    /**
     * 
     * @param string $resourceId
     * @param string $message
     * @param string $versionId
     * @return Revision
     */
    public static function commit($resourceId, $message, $versionId = null)
    {
        return self::getImplementation()->commit($resourceId, $message, $versionId);
    }
    
}
