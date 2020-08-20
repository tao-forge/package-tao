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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 *
 * @author Sergei Mikhailov <sergei.mikhailov@taotesting.com>
 */

declare(strict_types=1);

namespace oat\tao\test\unit\session\DataAccess\Factory;

use common_Config as Config;
use oat\generis\test\TestCase;
use oat\tao\model\session\DataAccess\Factory\SessionCookieAttributesFactory;
use tao_helpers_Uri as UriHelper;

/**
 * @covers \oat\tao\model\session\DataAccess\Factory\SessionCookieAttributesFactory
 * @runClassInSeparateProcess
 */
class SessionCookieAttributesFactoryTest extends TestCase
{
    /** @var SessionCookieAttributesFactory */
    private $sut;

    /**
     * @beforeClass
     */
    public static function initializeConfiguration(): void
    {
        Config::load();
    }

    /**
     * @before
     */
    public function init(): void
    {
        $this->sut = new SessionCookieAttributesFactory();
    }

    public function testCreate(): void
    {
        static::assertEquals(
            UriHelper::getPath(ROOT_URL),
            $this->sut->create()
        );
    }
}
