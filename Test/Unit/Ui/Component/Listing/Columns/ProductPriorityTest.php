<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\Test\Unit\Ui\Component\Listing\Columns;

use PHPUnit\Framework\TestCase;
use Magento\Framework\View\Element\UiComponent\Processor;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use RunAsRoot\ProductPriorities\ConfigProvider\General as ConfigProvider;
use RunAsRoot\ProductPriorities\DataProvider\Priority as DataProvider;
use RunAsRoot\ProductPriorities\Ui\Component\Listing\Columns\ProductPriority;

class ProductPriorityTest extends TestCase
{
    private ConfigProvider $configProvider;

    private DataProvider $dataProvider;

    private Processor $processorMock;

    private ContextInterface $contextMock;

    private ProductPriority $sut;

    protected function setUp(): void
    {
        $this->contextMock = $this->createMock(ContextInterface::class);
        $uiComponentFactory = $this->createMock(UiComponentFactory::class);
        $this->configProvider = $this->createMock(ConfigProvider::class);
        $this->dataProvider = $this->createMock(DataProvider::class);

        $this->sut = new ProductPriority(
            $this->contextMock,
            $uiComponentFactory,
            $this->configProvider,
            $this->dataProvider
        );
    }

    public function testPrepareModuleEnabled(): void
    {
        $this->prepareSetUp();
        $this->configProvider->method('isModuleEnabled')
            ->willReturn(true);

        $this->sut->prepare();
        $config = $this->sut->getDataByKey('config');

        self::assertEmpty($config);
    }

    public function testPrepareModuleDisabled(): void
    {
        $this->prepareSetUp();

        $this->configProvider->method('isModuleEnabled')
            ->willReturn(false);

        $this->sut->prepare();
        $config = $this->sut->getDataByKey('config');

        self::assertNotEmpty($config);
        self::assertArrayHasKey('componentDisabled', $config);
        self::assertTrue($config['componentDisabled']);
    }

    private function prepareSetUp(): void
    {
        $this->processorMock = $this->getMockBuilder(Processor::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->contextMock->expects($this->atLeastOnce())
            ->method('getProcessor')
            ->willReturn($this->processorMock);

        $this->processorMock->expects($this->atLeastOnce())
            ->method('register');
    }

    /**
     * @dataProvider prepareDataSourceDataProvider
     */
    public function testPrepareDataSourceIsModuleDisabled($dataSource, $expectedResult): void
    {
        $this->configProvider->method('isModuleEnabled')->willReturn(false);
        self::assertNotEquals($expectedResult, $this->sut->prepareDataSource($dataSource));
    }

    /**
     * @dataProvider prepareDataSourceDataProvider
     */
    public function testPrepareDataSourceIsModuleEnabled($dataSource, $expectedResult): void
    {
        $this->configProvider->method('isModuleEnabled')->willReturn(true);
        $this->dataProvider->method('getData')->willReturn([2 => [
            'name' => 'A',
            'cell_color' => 'red'
        ]]);
        self::assertEquals($expectedResult, $this->sut->prepareDataSource($dataSource));
    }

    public function prepareDataSourceDataProvider(): array
    {
        return [
            'excludedPriority' => [
                'dataSource' => [
                    'data' => [
                        'items' => [
                            [
                                'entity_id' => 1,
                            ]
                        ]
                    ]
                ],
                'expectedResult' => [
                    'data' => [
                        'items' => [
                            [
                                'entity_id' => 1,
                                ProductPriority::FIELD_NAME => ProductPriority::DEFAULT_VALUE
                            ]
                        ]
                    ]
                ]
            ],
            'includedPriority' => [
                'dataSource' => [
                    'data' => [
                        'items' => [
                            [
                                'entity_id' => 2
                            ]
                        ]
                    ]
                ],
                'expectedResult' => [
                    'data' => [
                        'items' => [
                            [
                                'entity_id' => 2,
                                ProductPriority::FIELD_NAME => [
                                    'name' => 'A',
                                    'cell_color' => 'red'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
