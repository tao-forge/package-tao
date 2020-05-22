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

namespace oat\taoMediaManager\model\relation\repository\rdf\map;

use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\service\ConfigurableService;
use oat\taoMediaManager\model\relation\MediaRelationCollection;
use core_kernel_classes_Resource as RdfResource;

abstract class AbstractRdfMediaRelationMap extends ConfigurableService implements RdfMediaRelationMapInterface
{
    use OntologyAwareTrait;

    abstract protected function getMediaRelationPropertyUri(): string;

    public function mapMediaRelations(
        RdfResource $mediaResource,
        MediaRelationCollection $mediaRelationCollection
    ): void
    {
        $mediaRelations = $mediaResource->getPropertyValues($this->getProperty($this->getMediaRelationPropertyUri()));

        foreach ($mediaRelations as $mediaRelation) {
            $mediaRelationResource = $this->getResource($mediaRelation);
            $mediaRelationCollection->add(
                $this->createMediaRelation($mediaRelationResource, $mediaResource->getUri())
            );
        }
    }
}
