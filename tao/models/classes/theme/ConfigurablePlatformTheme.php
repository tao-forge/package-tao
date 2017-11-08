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

use Jig\Utils\StringUtils;
use oat\oatbox\Configurable;
use oat\tao\model\theme\DefaultTheme;
use oat\tao\helpers\Template;
use qtism\common\datatypes\String;


/**
 * Class ConfigurablePlatformTheme
 *
 * Class to easily configure a platform theme, the configuration is written to
 * /config/tao/theming.conf
 *
 * @package oat\tao\model\theme
 */
class ConfigurablePlatformTheme extends Configurable implements Theme
{

    /** Theme container id key */
    const CONTAINER_ID = 'containerId';
    
    /** Theme container type key */
    const CONTAINER_TYPE = 'containerId';

    /** Theme label key */
    const LABEL = 'label';

    /** Theme id key */
    const ID = 'id';

    /** Theme stylesheet key */
    const STYLESHEET = 'stylesheet';

    /** Theme logo url key */
    const LOGO_URL = 'logoUrl';

    /** Theme logo link key */
    const LINK = 'link';

    /** Theme logo title key */
    const MESSAGE = 'message';

    /** Theme templates key */
    const TEMPLATES = 'templates';

    /** Use the default path for logo, stylesheet, templates etc. */
    const DEFAULT_PATH = 'useDefaultThemePath';

    /**
     * Default theme path
     *
     * @var string
     */
    private $defaultThemePath = '';

    /**
     * Set of custom texts that can be used in the templates
     *
     * @var array
     */
    private $customTexts = [];

    /**
     * These options are required to build a new instance of ConfigurablePlatformTheme
     *
     * @var array
     */
    private $mandatoryOptions = [
        self::CONTAINER_TYPE,
        self::CONTAINER_ID,
        self::LABEL
    ];


    /**
     * ConfigurablePlatformTheme constructor.
     *
     * @examples
     * Only label and containerId are configured, this will create a default configuration
     * These are the only mandatory elements
     *
     * $options = [
     *     'label' => 'Default Theme',
     *     // extension id | tenant id
     *     'containerId' => 'taoSomething',
     *     // 'extension' | 'tenant'
     *     'containerType => 'extension'
     * ];
     * $theme = new \oat\tao\model\theme\ConfigurablePlatformTheme($options);
     *
     * This will end up as:
     * $options = [
     *     'logoUrl' => 'http://domain/taoSomething/views/img/themes/platform/default-theme/logo.png',
     *     'label' => 'Default Theme',
     *     'containerId' => 'taoSomething',
     *     'containerType => 'extension'
     *     'id' => 'taoSomethingDefaultTheme'
     * ];
     *
     * If this contains anything you don't like, just add that key to your $config array to override the default.
     * The same applies if something is missing that you would like to have - for these cases generic getter is available.
     *
     * // Full blown custom configuration example
     * // Note that when 'containerType => 'tenant' the values of ConfigurablePlatformTheme::DEFAULT_PATH
     * // will be adapted to work with tenants instead.
     * $options = [
     *     'label' => 'Default Theme',
     *     'containerId' => 'taoSomething',
     *     'containerType' => 'extension'
     *     'logoUrl' => 'http://example.com/foo.png',
     *     'link' => 'http://example.com',
     *     'message' => 'Tao Platform',
     *
     *     // if stylesheet === ConfigurablePlatformTheme::DEFAULT_PATH
     *     'stylesheet' => 'http://domain/taoSomething/views/css/themes/platform/default-theme/theme.css',
     *     // when no stylesheet is given:
     *     'stylesheet' => 'http://example.com/tao/views/css/tao-3.css',
     *     // when stylesheet is any other url:
     *     'stylesheet' => 'http://example.com/any-other-url.css',
     *
     *     'templates' => [
     *          'header-logo' => Template::getTemplate('blocks/header-logo.tpl', 'some-extension'),
     *
     *          // if the value of the template === ConfigurablePlatformTheme::DEFAULT_PATH
     *          // the default theme path will be used something like:
     *          // templates/themes/platform/default-theme/login-message.tpl
     *          'login-message' => ConfigurablePlatformTheme::DEFAULT_PATH,
     *     ],
     *     // array of translatable strings
     *     'customTexts' => [
     *          'diagBrowserCheckResult' => 'Your browser %CURRENT_BROWSER% is not compatible.',
     *          'diagOsCheckResult'      => 'Your Operating System %CURRENT_OS% is not compatible.'
     *     ],
     *     'whateverCustomStuff' => 'anything as long as the key is in camelCase'
     * ];
     *
     * @param array $options
     *
     * @throws \common_exception_MissingParameter
     */
    public function __construct(array $options = [])
    {
        // make sure label and extension id are set
        foreach ($this->mandatoryOptions as $required) {
            if (empty($options[$required])) {
                throw new \common_exception_MissingParameter($required, get_class());
            }
        }

        $this->setDefaultThemePath($options[static::LABEL]);

        parent::__construct($this->setupOptions($options));

        if ($this->hasOption('customTexts')) {
            $this->customTexts = $this->getOption('customTexts');
        }
    }


    /**
     * Get a template associated from a given $id
     *
     * @param string $id
     * @param string $context
     * @return string
     */
    public function getTemplate($id, $context = Theme::CONTEXT_BACKOFFICE)
    {
        $templates = $this->getOption(static::TEMPLATES);

        if (is_null($templates) || empty($templates[$id])) {
            return Template::getTemplate('blocks/' . $id . '.tpl', 'tao');
        }

        if ($templates[$id] === static::DEFAULT_PATH) {
            return Template::getTemplate(
                $this->defaultThemePath . '/' . $id . '.tpl',
                $this->getOption(static::CONTAINER_ID)
            );
        }

        // otherwise it will be assumed the template is already configured
        return $templates[$id];
    }


    /**
     * This method is here to handle custom options
     *
     * @param $method
     * @param $arguments
     * @return mixed
     * @throws \common_exception_NotFound
     */
    public function __call($method, $arguments)
    {
        if(substr($method, 0, 3) !== 'get') {
            throw new \common_exception_NotFound('Unknown method "' . $method . '"');
        }
        $optionKey = strtolower($method[3]) . substr($method, 4);
        if ($this->hasOption($optionKey)) {
            return $this->getOption($optionKey);
        }
        throw new \common_exception_NotFound('Unknown option "' . $optionKey . '"');
    }


    /**
     * Get all options
     *
     * @return array
     */
    public function getThemeData()
    {
        return $this->getOptions();
    }


    /**
     * Get the url of stylesheet associated to current theme configuration
     *
     * @param string $context
     * @return string
     */
    public function getStylesheet($context = Theme::CONTEXT_BACKOFFICE)
    {
        return $this->getOption(static::STYLESHEET);
    }


    /**
     * Get the logo url of current theme
     * If not empty, this url is used on the header logo
     *
     * @return string
     */
    public function getLogoUrl()
    {
        return $this->getOption(static::LOGO_URL);
    }


    /**
     * Get the url link of current theme
     * URL is used in the header as a link for the logo
     * and in the footer for the message
     *
     * @return string
     */
    public function getLink()
    {
        if ($this->hasOption(static::LINK)) {
            return $this->getOption(static::LINK);
        }

        return '';
    }

    /**
     * Get the message of current theme
     * Message is used in the header as title of the logo
     * Message is used in the footer as footer message
     *
     * @return string
     */
    public function getMessage()
    {
        if ($this->hasOption(static::MESSAGE)) {
            return $this->getOption(static::MESSAGE);
        }

        return '';
    }

    /**
     * Gets the label of current theme
     * Labels are useful in situations where you can choose between multiple themes
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->getOption(static::LABEL);
    }

    /**
     * Gets the id of current theme
     * IDs are used to register the theme
     *
     * @return string
     */
    public function getId()
    {
        return $this->getOption(static::ID);
    }


    /**
     * Construct the common part of the default theme part
     *
     * @param string $label
     */
    protected function setDefaultThemePath($label)
    {

        $this->defaultThemePath = 'themes/platform/' . StringUtils::removeSpecChars($label);
    }


    /**
     * Allow to retrieve a custom translatable string for a given key
     *
     * @param String $key
     * @return string
     */
    public function getText($key)
    {
        return (array_key_exists($key, $this->customTexts)) ? $this->customTexts[$key] : '';
    }

    /**
     * Retrieve all custom translatable strings for the given keys
     *
     * @param array $keys
     * @return array
     */
    public function getTextFromArray(array $keys = [])
    {
        $values = [];
        forEach ($keys as $key) {
            $values[$key] = $this->getText($key);
        }
        return $values;
    }

    /**
     * Retrieve all existing custom translatable strings
     *
     * @return array
     */
    public function getCustomTexts()
    {
        return $this->customTexts;
    }
    
    /**
     * This is now just an alias to keep backward compatibility
     *
     * @return array
     */
    public function getAllTexts()
    {
        return $this->getCustomTexts();
    }


    /**
     * This setup is used when configuring a theme for a custom extension.
     * In multi tenancy though the tenant id might be use instead of the extension id.
     *
     * @param $options
     *
     * @return array
     */
    protected function setupOptions($options)
    {
        $options = array_merge([
            static::STYLESHEET   => Template::css('tao-3.css', 'tao'),
            static::LOGO_URL     => Template::img('tao-logo.png', 'tao'),
            static::LABEL        => $options[static::LABEL],
            static::CONTAINER_ID => $options[static::CONTAINER_ID],
            static::ID           => $options[static::CONTAINER_ID]
                                    . StringUtils::camelize(StringUtils::removeSpecChars($options[static::LABEL]), true)
        ],
            $options
        );

        if($options[static::LOGO_URL] === static::DEFAULT_PATH) {
            $options[static::LOGO_URL] = Template::img($this->defaultThemePath . '/logo.png', $options[static::CONTAINER_ID]);
        }

        if($options[static::STYLESHEET] === static::DEFAULT_PATH) {
            $options[static::STYLESHEET] = Template::css($this->defaultThemePath . '/theme.css', $options[static::CONTAINER_ID]);
        }

        return $options;
    }
}
