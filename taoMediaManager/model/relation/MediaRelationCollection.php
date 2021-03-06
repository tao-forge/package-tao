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

use ArrayIterator;
use IteratorAggregate;
use JsonSerializable;

class MediaRelationCollection implements IteratorAggregate, JsonSerializable
{
    /** @var MediaRelation[] */
    private $mediaRelations = [];

    public function __construct(MediaRelation ...$mediaRelations)
    {
        foreach ($mediaRelations as $mediaRelation) {
            $this->add($mediaRelation);
        }
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->mediaRelations);
    }

    public function add(MediaRelation $mediaRelation): self
    {
        $this->mediaRelations[] = $mediaRelation;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return $this->mediaRelations;
    }

    public function filterNewMediaIds(array $currentMediaIds): array
    {
        return array_diff($currentMediaIds, $this->getMediaIds());
    }

    public function filterRemovedMediaIds(array $currentMediaIds): array
    {
        return array_diff($this->getMediaIds(), $currentMediaIds);
    }

    private function getMediaIds(): array
    {
        $mediaIds = [];

        foreach ($this->mediaRelations as $relation) {
            $mediaIds[] = $relation->getSourceId();
        }

        return $mediaIds;
    }
}
