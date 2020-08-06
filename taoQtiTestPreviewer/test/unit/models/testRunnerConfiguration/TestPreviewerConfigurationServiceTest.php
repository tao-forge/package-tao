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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA
 */

declare(strict_types=1);

namespace unit\models\testRunnerConfiguration;

use oat\generis\test\TestCase;
use oat\tao\model\providers\ProviderModule;
use oat\taoQtiTest\models\runner\config\QtiRunnerConfig;
use oat\taoQtiTestPreviewer\models\testRunnerConfiguration\TestPreviewerConfigObject;
use oat\taoQtiTestPreviewer\models\testRunnerConfiguration\TestPreviewerConfigurationService;
use oat\taoTests\models\runner\plugins\TestPlugin;
use oat\taoTests\models\runner\plugins\TestPluginService;
use oat\taoTests\models\runner\providers\TestProviderService;

class TestPreviewerConfigurationServiceTest extends TestCase
{

    /**
     * @var TestPreviewerConfigurationService
     */
    private $subject;

    private const OPTIONS = ['c' => 'd'];
    /**
     * @var TestPluginService|\PHPUnit\Framework\MockObject\MockObject
     */
    private $testPluginService;
    /**
     * @var QtiRunnerConfig|\PHPUnit\Framework\MockObject\MockObject
     */
    private $qtiRunnerConfig;
    /**
     * @var TestProviderService|\PHPUnit\Framework\MockObject\MockObject
     */
    private $testProviderService;


    public function setUp(): void
    {
        $this->testProviderService = $this->createMock(TestProviderService::class);
        $this->testPluginService = $this->createMock(TestPluginService::class);
        $this->qtiRunnerConfig = $this->createMock(QtiRunnerConfig::class);

        $this->subject = new TestPreviewerConfigurationService();

        $slm = $this->getServiceLocatorMock(
            [
                TestPluginService::SERVICE_ID => $this->testPluginService,
                TestProviderService::SERVICE_ID => $this->testProviderService,
                QtiRunnerConfig::SERVICE_ID => $this->qtiRunnerConfig,
            ]
        );

        $this->subject->setServiceLocator($slm);
    }

    public function testGetTestRunnerConfiguration(): void
    {
        $this->qtiRunnerConfig->method('getConfig')->willReturn(self::OPTIONS);

        $plugins = $this->getPlugins();
        $providers = $this->getProviders();

        $this->testPluginService->method('getAllPlugins')->willReturn($plugins);
        $this->testProviderService->method('getAllProviders')->willReturn($providers);

        $configurationSample = new TestPreviewerConfigObject(
            ['category' => $providers, 'plugins' => $plugins,],
            self::OPTIONS
        );
        $this->assertEquals(
            $configurationSample,
            $this->subject->getTestRunnerConfiguration()
        );
    }

    private function getPlugins(): array
    {
        return [
            new TestPlugin('id1', 'module', 'category', ['active' => true]),
            new TestPlugin('id2', 'module', 'category', ['active' => true]),
        ];
    }

    private function getProviders(): array
    {
        return [
            new ProviderModule('idp1', 'module', 'category', ['active' => true]),
            new ProviderModule('idp2', 'module', 'category', ['active' => true]),
        ];
    }
}
