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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

/**
 * Authentication adapter interface to be implemented by authentication methodes
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package core
 * @subpackage kernel_auth_adapter
 */
class core_kernel_users_AuthAdapter
	implements common_user_auth_Adapter
{
	private $username;
	
	private $password;
	
	/**
	 * 
	 * @param string $username
	 * @param string $password
	 */
	public function __construct($username, $password) {
		$this->username = $username;
		$this->password = md5($password);
	}
	
	/**
     * (non-PHPdoc)
     * @see common_user_auth_Adapter::authenticate()
     */
    public function authenticate() {
    	
    	$userClass = new core_kernel_classes_Class(CLASS_GENERIS_USER);
    	$filters = array(PROPERTY_USER_LOGIN => $this->username, PROPERTY_USER_PASSWORD => $this->password);
    	$options = array('like' => false, 'recursive' => true);
    	$users = $userClass->searchInstances($filters, $options);
    	
    	if (empty($users)){
    		throw new core_kernel_users_InvalidLoginException();
    	}
    	if (count($users) > 1){
    		// Multiple users matching or not at all.
    		throw new common_exception_Error("Multiple Users found with the same password for login '${$this->username}'.");
    	}
    	
		$userResource = current($users);
    	return new core_kernel_users_GenerisUser($userResource);
    }
}