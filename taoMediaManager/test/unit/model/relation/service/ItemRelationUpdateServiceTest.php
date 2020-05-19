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

declare(strict_types=1);

namespace oat\taoMediaManager\test\unit\model\relation\service;

use oat\generis\test\TestCase;
use oat\taoMediaManager\model\relation\repository\MediaRelationRepositoryInterface;
use oat\taoMediaManager\model\relation\service\ItemRelationUpdateService;
use PHPUnit\Framework\MockObject\MockObject;

class ItemRelationUpdateServiceTest extends TestCase
{
    /** @var ItemRelationUpdateService */
    private $subject;

    /** @var MediaRelationRepositoryInterface|MockObject */
    private $repository;

    public function setUp(): void
    {
        $this->repository = $this->createMock(MediaRelationRepositoryInterface::class);
        $this->subject = new ItemRelationUpdateService();
        $this->subject->setServiceLocator(
            $this->getServiceLocatorMock(
                [
                    MediaRelationRepositoryInterface::SERVICE_ID => $this->repository,
                ]
            )
        );
    }

    public function testUpdateByItem(): void
    {
        $this->markTestIncomplete('Wip');

        $this->subject->updateByItem();
    }

    public function testRemoveMedia(): void
    {
        $this->markTestIncomplete('Wip');

        $this->subject->removeMedia();
    }
}
