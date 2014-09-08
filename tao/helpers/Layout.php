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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\tao\helpers;

use oat\tao\helpers\Template;
use oat\tao\model\menu\Icon;


class Layout{


    /**
     * Compute the parameters for the release message
     *
     * @return array
     */
    public static function getReleaseMsgData(){
        $params = array(
            'version-type' => '',
            'is-unstable'  => true,
            'is-sandbox'   => false,
            'logo'         => 'tao-logo.png',
            'link'         => 'http://taotesting.com',
            'msg'          => __('Tao Home')
        );

        switch(TAO_RELEASE_STATUS){
            case 'alpha':
            case 'demoA':
                $params['version-type'] = __('Alpha version');
                $params['logo']         = 'tao-logo-alpha.png';
                $params['link']         = 'http://forge.taotesting.com/projects/tao';
                $params['msg']          = __('Please report bugs, ideas, comments or feedback on the TAO Forge');
                break;

            case 'beta':
            case 'demoB':
                $params['version-type'] = __('Beta version');
                $params['logo']         = 'tao-logo-beta.png';
                $params['link']         = 'http://forge.taotesting.com/projects/tao';
                $params['msg']          = __('Please report bugs, ideas, comments or feedback on the TAO Forge');
                break;

            case 'demoS':
                $params['version-type'] = __('Demo Sandbox');
                $params['is-unstable']   = false;
                $params['is-sandbox']    = true;
                break;

            default:
                $params['is-unstable'] = false;
        }

        return $params;
    }


    /**
     * Compute the expiration time for the sandbox version
     *
     * @return string
     */
    public static function getSandboxExpiration(){
        $d          = new \DateTime();
        $weekday    = $d->format('w');
        $weekNumber = $d->format('W');
        $diff       = $weekNumber % 2 ? 7 : 6 - $weekday;
        $d->modify(sprintf('+ %d day', $diff));
        $date      = $d->format('Y-m-d');
        $remainder = strtotime($date) - time();
        $days      = floor($remainder / 86400);
        $hours     = floor(($remainder % 86400) / 3600);
        $minutes   = floor(($remainder % 3600) / 60);

        return $days . ' ' . (($days > 1) ? __('days') : __('day')) . ' '
        . $hours . ' ' . (($hours > 1) ? __('hours') : __('hour')) . ' '
        . __('and') . ' '
        . $minutes . ' ' . (($minutes > 1) ? __('minutes') : __('minute')) . '.';
    }

    /**
     * $icon defined in oat\tao\model\menu\Perspective::fromSimpleXMLElement
     *
     * $icon has two methods, getSource() and getId().
     * There are three possible ways to include icons, either as font, img or svg (not yet supported).
     * - Font uses source to address the style sheet (TAO font as default) and id to build the class name
     * - Img uses source only
     * - Svg uses source to address an SVG sprite and id to point to the right icon in there
     *
     * @param Icon $icon
     * @param string $defaultIcon e.g. icon-extension | icon-action
     * @return string icon as html
     */
    public static function renderMenuIcon($icon, $defaultIcon) {

        \common_Logger::d($icon);
        debug_backtrace();

        // data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAAAAAA6fptVAAAAAnRSTlMA/1uRIrUAAAAKSURBVHjaY/gPAAEBAQAcsIyZAAAAAElFTkSuQmCC
        // 1 x 1 png
        $srcExt    = '';
        $isBase64  = false;
        $iconClass = $defaultIcon;

        if(!is_null($icon)){
            if($icon -> getSource()) {
                $imgXts   = 'png|jpg|jpe|jpeg|gif';
                $regExp   = sprintf('~((^data:image/(%s))|(\.(%s)$))~', $imgXts, $imgXts);
                $srcExt   = preg_match($regExp, $icon -> getSource(), $matches) ? array_pop($matches) : array();
                $isBase64 = 0 === strpos($icon -> getSource(), 'data:image');
            }

            $iconClass = $icon -> getId() ? $icon -> getId() : $defaultIcon;
        }

        switch($srcExt) {
            case 'png':
            case 'jpg':
            case 'jpe':
            case 'jpeg':
            case 'gif':
                return $isBase64
                    ? '<img src="' . $icon -> getSource() . '" alt="" class="glyph" />'
                    : '<img src="' . Template::img($icon -> getSource(), $icon -> getExtension()) . '" alt="" class="glyph" />';
                break;

            case 'svg':
                // not implemented yet
                return false;

            case ''; // no source means an icon font is used
                return sprintf('<span class="%s glyph"></span>', $iconClass);
        }
    }

    /**
     * Build script element for AMD loader
     *
     * @return string
     */
    public static function getAmdLoader(){
        if(\common_session_SessionManager::isAnonymous()) {
            $amdLoader = array(
                'src' => Template::js('lib/require.js', 'tao'),
                //'data-main' => TAOBASE_WWW . 'js/main'
                'data-main' => TAOBASE_WWW . 'js/login',
                'data-config' => get_data('client_config_url')
            );
        }
        else if(\tao_helpers_Mode::is('production')) {
            $amdLoader = array(
                'src' => Template::js('main.min.js', 'tao'),
                'data-config' => get_data('client_config_url')
            );
        }
        else {
            $amdLoader = array(
                'src' => Template::js('lib/require.js', 'tao'),
                'data-config' => get_data('client_config_url'),
                'data-main' => TAOBASE_WWW . 'js/main'
            );
        }

        $amdScript = '<script id="amd-loader" ';
        foreach($amdLoader as $attr => $value) {
            $amdScript .= $attr . '="' . $value . '" ';
        }
        return trim($amdScript) . '></script>';
    }

    /**
     * @return string
     */
    public static function getTitle() {
        $title = get_data('title');
        return $title ? $title : PRODUCT_NAME . ' ' .  TAO_VERSION;
    }


    /**
     * Retrieve the template with the actual content
     *
     * @return array
     */
    public static function getContentTemplate() {
        $templateData = (array)get_data('content-template');
        $contentTemplate['path'] = $templateData[0];
        $contentTemplate['ext']  = $templateData[1] ? $templateData[1] : 'tao';
        return $contentTemplate;
    }

    /**
     * Build script element for AMD loader
     *
     * @return string
     */
    public static function getAmdLoader(){
        if(\common_session_SessionManager::isAnonymous()) {
            $amdLoader = array(
                'src' => Template::js('lib/require.js', 'tao'),
                //'data-main' => TAOBASE_WWW . 'js/main'
                'data-main' => TAOBASE_WWW . 'js/login',
                'data-config' => get_data('client_config_url')
            );
        }
        else if(\tao_helpers_Mode::is('production')) {
            $amdLoader = array(
                'src' => Template::js('main.min.js', 'tao'),
                'data-config' => get_data('client_config_url')
            );
        }
        else {
            $amdLoader = array(
                'src' => Template::js('lib/require.js', 'tao'),
                'data-config' => get_data('client_config_url'),
                'data-main' => TAOBASE_WWW . 'js/main'
            );
        }

        $amdScript = '<script id="amd-loader" ';
        foreach($amdLoader as $attr => $value) {
            $amdScript .= $attr . '="' . $value . '" ';
        }
        return trim($amdScript) . '></script>';
    }

    /**
     * @return string
     */
    public static function getTitle() {
        $title = get_data('title');
        return $title ? $title : PRODUCT_NAME . ' ' .  TAO_VERSION;
    }


    /**
     * Retrieve the template with the actual content
     *
     * @return array
     */
    public static function getContentTemplate() {
        $templateData = (array)get_data('content-template');
        $contentTemplate['path'] = $templateData[0];
        $contentTemplate['ext']  = $templateData[1] ? $templateData[1] : 'tao';
        return $contentTemplate;
    }

    /**
     * Build script element for AMD loader
     *
     * @return string
     */
    public static function getAmdLoader(){
        if(\common_session_SessionManager::isAnonymous()) {
            $amdLoader = array(
                'src' => Template::js('lib/require.js', 'tao'),
                'data-main' => TAOBASE_WWW . 'js/main'
            );
        }
        else if(\tao_helpers_Mode::is('production')) {
            $amdLoader = array(
                'src' => Template::js('main.min.js', 'tao'),
                'data-config' => get_data('client_config_url')
            );
        }
        else {
            $amdLoader = array(
                'src' => Template::js('lib/require.js', 'tao'),
                'data-config' => get_data('client_config_url'),
                'data-main' => TAOBASE_WWW . 'js/main'
            );
        }

        $amdScript = '<script id="amd-loader" ';
        foreach($amdLoader as $attr => $value) {
            $amdScript .= $attr . '="' . $value . '" ';
        }
        return trim($amdScript) . '></script>';
    }

    /**
     * @return string
     */
    public static function getTitle() {
        $title = get_data('title');
        return $title ? $title : PRODUCT_NAME . ' ' .  TAO_VERSION;
    }


}
