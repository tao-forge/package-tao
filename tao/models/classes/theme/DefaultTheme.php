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

use oat\tao\helpers\Template;

/**
 * Class DefaultTheme
 *
 * @package oat\tao\model\theme
 */
class DefaultTheme implements Theme
{
    public function getId()
    {
        return 'default';
    }

    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\theme\Theme::getLabel()
     */
    public function getLabel()
    {
        return __('Tao Default Theme');
    }

    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\theme\Theme::getStylesheet()
     */
    public function getStylesheet($context = Theme::CONTEXT_BACKOFFICE)
    {
        return Template::css('tao-3.css', 'tao');
    }

    /**
     * Get a template associated to given $id
     *
     * @param string $id
     * @param string $context
     * @return null|string
     */
    public function getTemplate($id, $context = Theme::CONTEXT_BACKOFFICE)
    {
        switch ($id) {
            case 'head':
                $template = Template::getTemplate('blocks/head.tpl', 'tao');
                break;
            case 'header-logo':
                $template = Template::getTemplate('blocks/header-logo.tpl', 'tao');
                break;
            case 'footer':
                $template = Template::getTemplate('blocks/footer.tpl', 'tao');
                break;
            case 'login-message':
                $template = Template::getTemplate('blocks/login-message.tpl', 'tao');
                break;
            case 'login':
                $template = Template::getTemplate('blocks/login.tpl', 'tao');
                break;
            default:
                return null;
        }
        return $template;
    }

    public function getLogoUrl(): string
    {
        switch (TAO_RELEASE_STATUS) {
            case 'alpha':
            case 'demoA':
                return Template::img('tao-logo-alpha.png', 'tao');
                break;
            case 'beta':
            case 'demoB':
                return Template::img('tao-logo-beta.png', 'tao');
                break;
        }
        return Template::img('tao-logo.png', 'tao');
    }
}
