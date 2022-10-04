<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\Test\Unit\Model\ProductPriority\DataProvider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use RunAsRoot\ProductPriorities\Model\ProductPriority\DataProvider;
use RunAsRoot\ProductPriorities\Model\ResourceModel\ProductPriority\Collection;
use RunAsRoot\ProductPriorities\Model\ResourceModel\ProductPriority\CollectionFactory;
use RunAsRoot\ProductPriorities\Api\Data\ProductPriorityInterface;

class DataProviderTest extends TestCase
{
    private $collection;

    private DataPersistorInterface $dataPersistor;

    private DataProvider $sut;

    protected function setUp(): void
    {
        $name = 'product_priority_form_data_source';
        $primaryFieldName = 'entity_id';
        $requestFieldName = 'entity_id';
        $collectionFactory = $this->createMock(CollectionFactory::class);
        $this->dataPersistor = $this->createMock(DataPersistorInterface::class);
        $meta = [];
        $data = [];
        $pool = $this->createMock(PoolInterface::class);
        $this->collection = $this->createMock(Collection::class);
        $collectionFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->collection);

        $this->sut = new DataProvider(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $collectionFactory,
            $this->dataPersistor,
            $meta,
            $data,
            $pool
        );
    }

    public function testGetData(): void
    {
        $priorityId1 = 1;
        $priorityId2 = 2;
        $priorityId = 3;
        $data = ['entity_id' => $priorityId, 'name' => 'New'];
        $data1 = ['entity_id' => $priorityId1, 'name' => 'A'];
        $data2 = ['entity_id' => $priorityId2, 'name' => 'B'];
        $productPriority1 = $this->getMockBuilder(ProductPriorityInterface::class)
            ->setMethods(['getEntityId', 'getData'])
            ->getMockForAbstractClass();
        $productPriority2 = $this->getMockBuilder(ProductPriorityInterface::class)
            ->setMethods(['getEntityId', 'getData'])
            ->getMockForAbstractClass();

        $this->collection->expects($this->once())
            ->method('getItems')
            ->willReturn([$productPriority1, $productPriority2]);

        $productPriority1->method('getEntityId')
            ->willReturn($priorityId1);
        $productPriority1->method('getData')
            ->willReturn($data1);

        $productPriority2->method('getEntityId')
            ->willReturn($priorityId2);
        $productPriority2->method('getData')
            ->willReturn($data2);

        $this->dataPersistor->method('get')
            ->with('product_priority')
            ->willReturn($data);

        $productPriorityNew = $this->getMockBuilder(ProductPriorityInterface::class)
            ->setMethods(['getEntityId', 'getData', 'setData'])
            ->getMockForAbstractClass();
        $this->collection->method('getNewEmptyItem')
            ->willReturn($productPriorityNew);

        $productPriorityNew->method('setData')
            ->with($data);
        $productPriorityNew->method('getEntityId')
            ->willReturn($priorityId);
        $productPriorityNew->method('getData')
            ->willReturn($data);

        $this->dataPersistor->method('clear')
            ->with('product_priority');

        $result = $this->sut->getData();

        self::assertNotEmpty($result);
        self::assertArrayHasKey('name', $result[$priorityId]);
    }
}
