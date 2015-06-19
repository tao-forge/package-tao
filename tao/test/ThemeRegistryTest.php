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
namespace oat\tao\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\tao\model\ThemeRegistry;

/**
 *
 * @author Lionel Lecaque, lionel@taotesting.com
 */
class ThemeRegistryTest extends TaoPhpUnitTestRunner
{

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function setUp()
    {
        TaoPhpUnitTestRunner::initTest();
    }

    public function testsetDefaultTheme()
    {
        ThemeRegistry::getRegistry()->createTarget('itemsTest', 'base');
        ThemeRegistry::getRegistry()->registerTheme('lightBlueOnDarkBlue', 'Light Blue on Dark Bluea', 'path', array('itemsTest'));
        ThemeRegistry::getRegistry()->setDefaultTheme('itemsTest', 'lightBlueOnDarkBlue');


        $map = ThemeRegistry::getRegistry()->getMap();
        $this->assertFalse(empty($map));
        $this->assertInternalType('array', $map);

        $this->assertArrayHasKey('itemsTest', $map);

        $this->assertInternalType('array', $map['itemsTest']);
        $this->assertArrayHasKey('available', $map['itemsTest']);

        $available = current($map['itemsTest']['available']);
        $this->assertInternalType('array', $available);
        $this->assertArrayHasKey('name', $available);
        $this->assertEquals('Light Blue on Dark Bluea', $available['name']);

        // target exist
        ThemeRegistry::getRegistry()->remove('itemsTest');
    }

    public function testRegister()
    {
        ThemeRegistry::getRegistry()->createTarget('itemsTest', 'base');
        ThemeRegistry::getRegistry()->registerTheme('blackOnLightMagenta', 'Black on Light Magenta', 'blackOnLightMagenta', array('itemsTest'));

        $map = ThemeRegistry::getRegistry()->getMap();
        $this->assertFalse(empty($map));
        $this->assertInternalType('array', $map);

        $this->assertArrayHasKey('itemsTest', $map);
        $this->assertInternalType('array', $map['itemsTest']);

        $this->assertInternalType('array', $map['itemsTest']);
        $this->assertArrayHasKey('available', $map['itemsTest']);

        $available = current($map['itemsTest']['available']);
        $this->assertInternalType('array', $available);
        $this->assertArrayHasKey('name', $available);

        $this->assertEquals('Black on Light Magenta', $available['name']);

        ThemeRegistry::getRegistry()->createTarget('testsTest', 'base');
        ThemeRegistry::getRegistry()->registerTheme('lightBlueOnDarkBlue', 'Light Blue on Dark Blue', 'lightBlueOnDarkBlue', array('itemsTest', 'testsTest'));

        $map = ThemeRegistry::getRegistry()->getMap();

        $this->assertArrayHasKey('testsTest', $map);
        $this->assertInternalType('array', $map['testsTest']);

        $this->assertArrayHasKey('available', $map['testsTest']);

        $available = current($map['testsTest']['available']);
        $this->assertInternalType('array', $available);
        $this->assertArrayHasKey('name', $available);

        $this->assertEquals('Light Blue on Dark Blue', $available['name']);

        foreach ($map['itemsTest']['available'] as $theme) {
            $this->assertInternalType('array', $theme);
            $this->assertArrayHasKey('id', $theme);
            $this->assertTrue(in_array($theme['id'], array(
                'blackOnLightMagenta',
                'lightBlueOnDarkBlue'
            )));
        }
        ThemeRegistry::getRegistry()->remove('itemsTest');
        ThemeRegistry::getRegistry()->remove('testsTest');
    }

    public function testGetAvailableTheme(){
        ThemeRegistry::getRegistry()->getAvailableThemes();
    }
}
