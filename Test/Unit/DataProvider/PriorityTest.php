<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\Test\Unit\DataProvider;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Reports\Model\Item as ReportItem;
use PHPUnit\Framework\TestCase;
use RunAsRoot\ProductPriorities\Api\Data\ProductPriorityInterface;
use RunAsRoot\ProductPriorities\ConfigProvider\General as ConfigProvider;
use RunAsRoot\ProductPriorities\DataProvider\Priority;
use RunAsRoot\ProductPriorities\Model\ResourceModel\ProductPriority\Collection as PriorityCollection;
use RunAsRoot\ProductPriorities\Model\ResourceModel\ProductPriority\CollectionFactory as PriorityCollectionFactory;
use RunAsRoot\ProductPriorities\Model\ResourceModel\Report\Bestsellers\Collection as BestSellersCollection;
use RunAsRoot\ProductPriorities\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestSellersCollectionFactory;

class PriorityTest extends TestCase
{
    private StoreManagerInterface $storeManager;

    private BestSellersCollectionFactory $bestSellersCollectionFactory;

    private ConfigProvider $configProvider;

    private PriorityCollectionFactory $priorityCollectionFactory;

    private Priority $sut;

    private string $fromDate = '2022-10-01';

    private array $storeIds = [0, 1];

    protected function setUp(): void
    {
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->bestSellersCollectionFactory = $this->createMock(BestSellersCollectionFactory::class);
        $this->configProvider = $this->createMock(ConfigProvider::class);
        $this->priorityCollectionFactory = $this->createMock(PriorityCollectionFactory::class);

        $this->sut = new Priority(
            $this->storeManager,
            $this->bestSellersCollectionFactory,
            $this->configProvider,
            $this->priorityCollectionFactory
        );
    }

    public function testGetDataEmptyBestSellersCollection(): void
    {
        $collectionMock = $this->getCollection();

        $bestSeller = $this->createMock(ReportItem::class);
        $collectionMock->method('getFirstItem')
            ->willReturn($bestSeller);

        $bestSeller->method('getData')
            ->with('qty_ordered')
            ->willReturn(null);

        self::assertEmpty($this->sut->getData());
    }

    public function testGetData(): void
    {
        $maxQtyOrdered = 100;
        $collectionMock = $this->getCollection();

        $bestSeller = $this->createMock(ReportItem::class);
        $bestSeller1 = $this->createMock(ReportItem::class);
        $bestSeller2 = $this->createMock(ReportItem::class);
        $collectionMock->method('getFirstItem')
            ->willReturn($bestSeller);

        $bestSeller
            ->expects($this->atLeastOnce())
            ->method('getData')
            ->willReturnMap([
                ['qty_ordered', null, $maxQtyOrdered],
                ['product_id', null, 1]
            ]);

        $bestSeller1->method('getData')
            ->willReturnMap([
                ['product_id', null, 2],
                ['qty_ordered', null, 82]
            ]);

        $bestSeller2->method('getData')
            ->willReturnMap([
                ['product_id', null, 3],
                ['qty_ordered', null, 40]
            ]);

        $collectionMock->method('getItems')
            ->willReturn([$bestSeller, $bestSeller1, $bestSeller2]);

        $priorityCollection = $this->createMock(PriorityCollection::class);
        $this->priorityCollectionFactory->expects($this->once())
            ->method('create')
            ->willReturn($priorityCollection);
        $priorityCollection->method('setOrder')
            ->with('proportion_value', 'DESC')
            ->willReturnSelf();

        $priorityMock1 = $this->getMockBuilder(ProductPriorityInterface::class)
            ->setMethods(['getName', 'toArray'])
            ->getMockForAbstractClass();
        $priorityMock2 = $this->getMockBuilder(ProductPriorityInterface::class)
            ->setMethods(['getName', 'toArray'])
            ->getMockForAbstractClass();

        $priorityCollection
            ->expects($this->once())
            ->method('getItems')
            ->willReturn([$priorityMock1, $priorityMock2]);

        $priorityMock1->method('getName')
            ->willReturn('A');
        $priorityMock1->method('toArray')
            ->willReturn(['name' => 'A', 'proportion_value' => 90]);

        $priorityMock2->method('getName')
            ->willReturn('B');
        $priorityMock2->method('toArray')
            ->willReturn(['name' => 'B', 'proportion_value' => 80]);

        $result = $this->sut->getData();

        self::assertIsArray($result);
        self::assertArrayHasKey(1, $result);
        self::assertArrayHasKey(2, $result);
        self::assertArrayHasKey(3, $result);
        self::assertArrayHasKey('name', $result[2]);
        self::assertNull($result[3]);
    }

    private function getCollection(): BestSellersCollection
    {
        $collectionMock = $this->createMock(BestSellersCollection::class);
        $this->bestSellersCollectionFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);

        $this->configProvider
            ->expects($this->once())
            ->method('getFromDate')
            ->willReturn($this->fromDate);

        $collectionMock->method('setDateRange')
            ->with($this->fromDate)
            ->willReturnSelf();

        $this->storeManager->method('getStores')
            ->with(true)
            ->willReturn($this->storeIds);

        $collectionMock->method('addStoreFilter')
            ->with($this->storeIds)
            ->willReturnSelf();

        return $collectionMock;
    }
}
