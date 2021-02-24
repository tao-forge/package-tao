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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA ;
 */

declare(strict_types=1);

namespace oat\taoQtiTestPreviewer\models\test\service;

use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\service\ConfigurableService;
use oat\taoQtiItem\model\qti\Service;
use oat\taoQtiTestPreviewer\models\test\TestPreviewRequest;
use oat\taoQtiTest\helpers\ItemResolver;
use oat\taoQtiTest\models\QtiTestService;
use qtism\data\AssessmentTest;
use qtism\data\storage\xml\XmlCompactDocument;

class TestPreviewerAssessmentTestGenerator extends ConfigurableService implements TestPreviewerAssessmentTestGeneratorInterface
{
    use OntologyAwareTrait;

    /**
     * @inheritDoc
     */
    public function generate(TestPreviewRequest $testPreviewRequest): AssessmentTest
    {
        $test = $this->getResource($testPreviewRequest->getTestUri());
        $resolver = $this->getItemResolver();
        $originalDoc = $this->getQtiTestService()->getDoc($test);

        $compiledDoc = XmlCompactDocument::createFromXmlAssessmentTestDocument($originalDoc, $resolver, $resolver);

        return $compiledDoc->getDocumentComponent();
    }

    private function getQtiTestService(): QtiTestService
    {
        return $this->getServiceLocator()->get(QtiTestService::class);
    }

    private function getItemResolver(): ItemResolver
    {
        $service = $this->getServiceLocator()->get(Service::class);

        return new ItemResolver($service);
    }
}
