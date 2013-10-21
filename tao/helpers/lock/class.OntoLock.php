<?php
/*  
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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
/**
 * Implements Lock using a basic property in the ontology storing the lock data
 *
 * @note It would be preferably static but we may want to have the polymorphism on lock but it would be prevented by explicit class method static calls.
 * Also if you nevertheless call it statically you may want to avoid the late static binding for the getLockProperty
 */
class tao_helpers_lock_OntoLock
implements tao_helpers_lock_Lock
{

    
    private function getLockProperty(){
        return new core_kernel_classes_Property('#testLock');
    }

    /**
     * set a lock on @resource with owner @user, succeeds also if there is a lock already exists but with the same owner
     *
     * @throw common_Exception if the resource already has a lock with a different owner
     * @param core_kernel_classes_Resource $resource
     * @param core_kernel_classes_Resource $user
     */
    public function setLock(core_kernel_classes_Resource $resource, core_kernel_classes_Resource $owner){
        if (!($this->isLocked($resource))) {
        $lock = new tao_helpers_lock_LockData($resource, $owner, microtime(true));
        $resource->setPropertyValue($this->getLockProperty(), serialize($lock));
        } else {
            throw new common_Exception($resource->getUri()." is already locked");
        }

    }
    /**
     * return true is the resource is locked, else otherwise
     * @return boolean
     */
    public function isLocked(core_kernel_classes_Resource $resource){
        $values = $resource->getPropertyValues($this->getLockProperty());
        if ((is_array($values)) && (count($values)>0)) {
            return true;
        }
        return false;
    }
    /**
     *  release the lock if owned by @user
     * @param core_kernel_classes_Resource $resource
     * @param core_kernel_classes_Resource $user
     * @throw common_Exception no lock to release
     */
    public function releaseLock(core_kernel_classes_Resource $resource, core_kernel_classes_Resource $user){
        $lock = $resource->getPropertyValues($this->getLockProperty());
        if (count($lock)==0) {
            return false;
        } elseif (count($lock)>1) {
            throw new common_exception_InconsistentData();
        } else {
            $lockdata = unserialize(array_pop($lock));
            if (get_class($lockdata)!= "tao_helpers_lock_LockData") {
                throw new common_exception_InconsistentData();
            }
            if ($lockdata->getOwner()->getUri() == $user->getUri()) {
                $resource->removePropertyValues($this->getLockProperty());
                return true;
            } else {
                throw new common_exception_Unauthorized("The resource is owned by".$lockdata->getOwner());
            }
        }
        
    }
   /**
     *  release the lock
     * @param core_kernel_classes_Resource $resource
     */
    public function forceReleaseLock(core_kernel_classes_Resource $resource){
         $resource->removePropertyValues($this->getLockProperty());
    }
    /**
     * Return lock details
     * @param core_kernel_classes_Resource $resource
     * @return tao_helpers_lock_LockData
     */
    public function getLockData(core_kernel_classes_Resource $resource) {
        $values = $resource->getPropertyValues($this->getLockProperty());
        if ((is_array($values)) && (count($values)==1)) {
            $lockdata = unserialize(array_pop($values));
            if (get_class($lockdata)!= "tao_helpers_lock_LockData") {
                throw new common_exception_InconsistentData();
            } else {
                return $lockData;
            }
        } else {
            return false;
        }

    }
}

?>