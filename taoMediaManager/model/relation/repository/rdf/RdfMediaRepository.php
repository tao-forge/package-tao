<?php

declare(strict_types=1);

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

namespace oat\taoMediaManager\model\relation\repository\rdf;

use oat\generis\model\OntologyAwareTrait;
use oat\generis\model\OntologyRdf;
use oat\oatbox\service\ConfigurableService;
use oat\taoMediaManager\model\relation\MediaRelation;
use oat\taoMediaManager\model\relation\MediaRelationCollection;
use oat\taoMediaManager\model\relation\repository\MediaRelationRepositoryInterface;
use oat\taoMediaManager\model\relation\repository\query\FindAllQuery;
use core_kernel_classes_Resource as RdfResource;

class RdfMediaRepository extends ConfigurableService implements MediaRelationRepositoryInterface
{
    use OntologyAwareTrait;

    protected const ITEM_RELATION_PROPERTY = 'http://www.tao.lu/Ontologies/TAOMedia.rdf#RelatedItem';
    protected const ASSET_RELATION_PROPERTY = 'http://www.tao.lu/Ontologies/TAOMedia.rdf#RelatedAsset';

    public function findAll(FindAllQuery $findAllQuery): MediaRelationCollection
    {
        $mediaResource = $this->getResource($findAllQuery->getMediaId());
        $mediaRelationCollections = new MediaRelationCollection();

        $this->getItemRelations($mediaResource, $mediaRelationCollections);
        $this->getAssetRelations($mediaResource, $mediaRelationCollections);

        return $mediaRelationCollections;
    }

    public function save(MediaRelation $relation): bool
    {
        // TODO: Implement save() method.
    }

    public function remove(MediaRelation $relation): bool
    {
        // TODO: Implement remove() method.
    }

    protected function getItemRelations(RdfResource $mediaResource, MediaRelationCollection $mediaRelationCollection) : void
    {
        $relatedItems = $mediaResource->getPropertyValues($this->getProperty(self::ITEM_RELATION_PROPERTY));

//        var_dump($findAllQuery->getMediaId());
//        var_dump($relatedItems);

        foreach ($relatedItems as $relatedItem) {
            $itemResource = $this->getResource($relatedItem);
            $mediaRelationCollection->add(
                new MediaRelation(MediaRelation::ITEM_TYPE, $itemResource->getUri(), $itemResource->getLabel())
            );
        }
    }

    protected function getAssetRelations(RdfResource $mediaResource, MediaRelationCollection $mediaRelationCollection) : void
    {
        $relatedAssets = $mediaResource->getPropertyValues($this->getProperty(self::ASSET_RELATION_PROPERTY));

        foreach ($relatedAssets as $relatedAsset) {
            $assetResource = $this->getResource($relatedAsset);
            $mediaRelationCollection->add(
                new MediaRelation(MediaRelation::ASSET_TYPE, $assetResource->getUri(), $assetResource->getLabel())
            );
        }
    }
}