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
 */

namespace oat\taoMediaManager\test\unit\model\sharedStimulus\factory;

use oat\generis\test\TestCase;
use oat\taoMediaManager\model\sharedStimulus\CreateCommand;
use oat\taoMediaManager\model\sharedStimulus\factory\CommandFactory;
use oat\taoMediaManager\model\sharedStimulus\service\CreateService;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ServerRequestInterface;

class CommandFactoryTest extends TestCase
{
    private const CLASS_URI = 'uri';
    private const LANGUAGE_URI = 'uri';
    private const NAME = 'name';

    /** @var CommandFactory */
    private $factory;

    /** @var CreateService|MockObject */
    private $createService;

    public function setUp(): void
    {
        $this->createService = $this->createMock(CreateService::class);
        $this->factory = new CommandFactory();
    }

    public function testCreateSharedStimulusByRequest(): void
    {
        $expectedCommand = new CreateCommand(
            self::CLASS_URI,
            self::NAME,
            self::LANGUAGE_URI
        );

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')
            ->willReturn(
                json_encode(
                    [
                        'classUri' => self::CLASS_URI,
                        'languageUri' => self::LANGUAGE_URI,
                        'name' => self::NAME,
                    ]
                )
            );

        $this->assertEquals($expectedCommand, $this->factory->createByRequest($request));
    }
}
