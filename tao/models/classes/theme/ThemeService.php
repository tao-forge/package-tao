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
 */
namespace oat\tao\model\theme;

use oat\oatbox\service\ConfigurableService;
use oat\taoAct\model\theme\ActTheme;
/**
 * 
 * @author Joel Bout
 */
class ThemeService extends ConfigurableService {
    
    const CONTEXT_BACKOFFICE = 'backoffice';
    
    const CONTEXT_FRONTOFFICE = 'frontoffice';
    
    const CONTEXT_QTI_ITEM = 'items';
    
    const SERVICE_ID = 'tao/theming';
    
    public function getTheme($context)
    {
        if ($this->hasOption($context)) {
            $theme = $this->getOption($context);
        } else {
            \common_Logger::w('Context '.$context.' unknown, falling back to back office');
            $theme = $this->getOption(self::CONTEXT_BACKOFFICE);
        }
        return $theme;
    }
    
    public function setTheme($context, Theme $default)
    {
        $this->setOption($context, $default);
    }
    
}