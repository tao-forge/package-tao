<?php

declare(strict_types=1);

namespace oat\taoResultServer\test\Unit\Models;

use PHPUnit\Framework\TestCase;

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
 * Copyright (c) 2020 (original work) Open Assessment Technologies S.A.
 */

class taoResultServer_models_classes_ResponseVariableTest extends TestCase
{
    public function testVariableCanBeJsonSerialized(): void
    {
        $subject = (new \taoResultServer_models_classes_ResponseVariable())
            ->setIdentifier('testIdentifier')
            ->setCardinality('single')
            ->setBaseType('testBaseType')
            ->setEpoch('testEpoch')
            ->setCorrectResponse('testCorrectResponse')
            ->setCandidateResponse('testCandidateResponse');

        $this->assertSame(json_encode([
            'identifier' => 'testIdentifier',
            'cardinality' => 'single',
            'baseType' => 'testBaseType',
            'epoch' => 'testEpoch',
            'correctResponse' => 'testCorrectResponse',
            'candidateResponse' => base64_encode('testCandidateResponse'),
        ]), json_encode($subject));
    }
}
