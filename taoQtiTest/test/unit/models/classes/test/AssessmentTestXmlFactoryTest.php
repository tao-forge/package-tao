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

namespace oat\taoQtiTest\models\test;


use common_exception_Error;
use common_ext_ExtensionException;
use oat\generis\test\TestCase;
use oat\oatbox\service\ConfigurableService;
use oat\tao\model\service\ApplicationService;
use qtism\data\AssessmentTest;
use qtism\data\NavigationMode;
use qtism\data\storage\xml\XmlStorageException;
use qtism\data\SubmissionMode;
use RuntimeException;
use SimpleXMLElement;
use Zend\ServiceManager\ServiceLocatorInterface;

class AssessmentTestXmlFactoryTest extends TestCase
{
    /** @var ServiceLocatorInterface */
    private $serviceLocator;

    protected function setUp(): void
    {
        $appService = $this->createMock(ApplicationService::class);
        $appService->method('getPlatformVersion')->willReturn('test_version');

        $extension = new class extends ConfigurableService implements TestExtensionInterface {
            public function extend(AssessmentTest $test): void
            {
                $test->setTitle('changedTitle');
            }
        };

        $badExtension = new class extends ConfigurableService {
        };

        $this->serviceLocator = $this->getServiceLocatorMock(
            [
                ApplicationService::SERVICE_ID => $appService,
                'extension' => $extension,
                'badExtension' => $badExtension,
            ]
        );
    }

    /**
     * @dataProvider provideData
     *
     * @param array $options
     * @param array $expected
     *
     * @throws XmlStorageException
     * @throws common_exception_Error
     * @throws common_ext_ExtensionException
     */
    public function testBuild(array $options, array $expected): void
    {
        $builder = $this->createBuilder($options);

        $xml = $builder->create('testId', 'testLabel');

        $this->assertIsString($xml);

        $simpleXml = new SimpleXMLElement($xml);
        $this->assertSame('testId', (string)$simpleXml->attributes()['identifier']);
        $this->assertSame('testLabel', (string)$simpleXml->attributes()['title']);

        /** @noinspection PhpUndefinedFieldInspection */
        $testPartAttributes = $simpleXml->testPart->attributes();

        $this->assertSame(
            $expected[AssessmentTestXmlFactory::OPTION_TEST_PART_ID],
            (string)$testPartAttributes['identifier']
        );
        $this->assertSame(
            $expected[AssessmentTestXmlFactory::OPTION_TEST_PART_NAVIGATION_MODE],
            (string)$testPartAttributes['navigationMode']
        );
        $this->assertSame(
            $expected[AssessmentTestXmlFactory::OPTION_TEST_PART_SUBMISSION_MODE],
            (string)$testPartAttributes['submissionMode']
        );

        /** @noinspection PhpUndefinedFieldInspection */
        $itemSessionControl = $simpleXml->testPart->itemSessionControl->attributes();

        $this->assertSame(
            $expected[AssessmentTestXmlFactory::OPTION_TEST_MAX_ATTEMPTS],
            (int)$itemSessionControl['maxAttempts']
        );
    }

    /**
     * @throws XmlStorageException
     * @throws common_exception_Error
     * @throws common_ext_ExtensionException
     */
    public function testExtension(): void
    {
        $builder = $this->createBuilder(
            [
                AssessmentTestXmlFactory::OPTION_EXTENSIONS => ['extension']
            ]
        );

        $xml = $builder->create('identifier', 'title');

        $simpleXml = new SimpleXMLElement($xml);
        $this->assertSame('identifier', (string)$simpleXml->attributes()['identifier']);
        $this->assertSame('changedTitle', (string)$simpleXml->attributes()['title']);
    }

    /**
     * @throws XmlStorageException
     * @throws common_exception_Error
     * @throws common_ext_ExtensionException
     */
    public function testBadExtension(): void
    {
        $this->expectException(RuntimeException::class);

        $builder = $this->createBuilder(
            [
                AssessmentTestXmlFactory::OPTION_EXTENSIONS => ['badExtension']
            ]
        );

        $builder->create('identifier', 'title');
    }

    public function provideData(): array
    {
        return [
            [
                [],
                [
                    AssessmentTestXmlFactory::OPTION_TEST_PART_ID              => AssessmentTestXmlFactory::DEFAULT_TEST_PART_ID,
                    AssessmentTestXmlFactory::OPTION_TEST_PART_NAVIGATION_MODE => 'linear',
                    AssessmentTestXmlFactory::OPTION_TEST_PART_SUBMISSION_MODE => 'individual',
                    AssessmentTestXmlFactory::OPTION_ASSESSMENT_SECTION_TITLE  => AssessmentTestXmlFactory::DEFAULT_ASSESSMENT_SECTION_TITLE,
                    AssessmentTestXmlFactory::OPTION_ASSESSMENT_SECTION_ID     => AssessmentTestXmlFactory::DEFAULT_ASSESSMENT_SECTION_ID,
                    AssessmentTestXmlFactory::OPTION_TEST_MAX_ATTEMPTS         => AssessmentTestXmlFactory::DEFAULT_TEST_MAX_ATTEMPTS,
                ]
            ],
            [
                [
                    AssessmentTestXmlFactory::OPTION_TEST_PART_ID              => 'customTestPartId',
                    AssessmentTestXmlFactory::OPTION_TEST_PART_NAVIGATION_MODE => NavigationMode::NONLINEAR,
                    AssessmentTestXmlFactory::OPTION_TEST_PART_SUBMISSION_MODE => SubmissionMode::SIMULTANEOUS,
                    AssessmentTestXmlFactory::OPTION_ASSESSMENT_SECTION_TITLE  => 'customSectionTitle',
                    AssessmentTestXmlFactory::OPTION_ASSESSMENT_SECTION_ID     => 'customSectionId',
                    AssessmentTestXmlFactory::OPTION_TEST_MAX_ATTEMPTS         => 10,
                ],
                [
                    AssessmentTestXmlFactory::OPTION_TEST_PART_ID              => 'customTestPartId',
                    AssessmentTestXmlFactory::OPTION_TEST_PART_NAVIGATION_MODE => 'nonlinear',
                    AssessmentTestXmlFactory::OPTION_TEST_PART_SUBMISSION_MODE => 'simultaneous',
                    AssessmentTestXmlFactory::OPTION_ASSESSMENT_SECTION_TITLE  => 'customSectionTitle',
                    AssessmentTestXmlFactory::OPTION_ASSESSMENT_SECTION_ID     => 'customSectionId',
                    AssessmentTestXmlFactory::OPTION_TEST_MAX_ATTEMPTS         => 10,
                ]
            ]
        ];
    }

    private function createBuilder(array $params = []): AssessmentTestXmlFactory
    {
        $builder = new AssessmentTestXmlFactory($params);

        $builder->setServiceLocator($this->serviceLocator);

        return $builder;
    }
}
