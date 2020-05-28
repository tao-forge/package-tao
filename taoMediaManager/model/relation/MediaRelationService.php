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
 */

declare(strict_types=1);

namespace oat\taoMediaManager\model\relation;

use oat\oatbox\service\ConfigurableService;
use oat\taoMediaManager\model\relation\repository\query\FindAllByMediaQuery;
use oat\taoMediaManager\model\relation\repository\MediaRelationRepositoryInterface;

class MediaRelationService extends ConfigurableService
{
    public function getMediaRelations(string $id): MediaRelationCollection
    {
        return $this->getMediaRelationRepository()->findAll(
            new FindAllByMediaQuery($id)
        );
    }

    private function getMediaRelationRepository(): MediaRelationRepositoryInterface
    {
        return $this->getServiceLocator()->get(MediaRelationRepositoryInterface::SERVICE_ID);
    }
}
