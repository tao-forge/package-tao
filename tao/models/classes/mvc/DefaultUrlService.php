<?php
/*
 * This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation; under version 2
 *  of the License (non-upgradable).
 *  
 * This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 * 
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 *  Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */
namespace oat\tao\model\mvc;

use oat\oatbox\service\ConfigurableService;

class DefaultUrlService extends ConfigurableService 
{
    
    const SERVICE_ID = 'tao/urlroute';

    /**
     * 
     * @param string $name
     * @return array
     */
    public function getUrlRoute($name) {
        return $this->getOption($name);
    }
    
    public function getUrl($name , $params) {
        $route = $this->getOption($name);
        return _url($route['action'], $route['controller'], $route['ext'], $params);
    }

    /**
     * 
     * @return array
     */
    public function getLoginUrl(array $params = array()) {
        return $this->getUrl('login' , $params);
    }
    
    /**
     * 
     * @return array
     */
    public function getLogoutUrl(array $params = array()) {
        return $this->getUrl('logout' , $params);
    }
    
    /**
     * 
     * @return array
     */
    public function getDefaultUrl(array $params = array()) {
        return $this->getUrl('default' , $params);
    }
}

