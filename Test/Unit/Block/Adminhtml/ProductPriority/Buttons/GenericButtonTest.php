<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\Test\Unit\Block\Adminhtml\ProductPriority\Buttons;

use PHPUnit\Framework\TestCase;
use Magento\Framework\Phrase;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Backend\Block\Widget\Context;
use RunAsRoot\ProductPriorities\Api\Data\ProductPriorityInterface;
use RunAsRoot\ProductPriorities\Block\Adminhtml\ProductPriority\Buttons\GenericButton;
use RunAsRoot\ProductPriorities\Api\ProductPriorityRepositoryInterface;

class GenericButtonTest extends TestCase
{
    private GenericButton $sut;

    protected Context $contextMock;

    protected ProductPriorityRepositoryInterface $productPriorityRepositoryMock;

    protected function setUp(): void
    {
        $this->contextMock = $this->createMock(Context::class);
        $this->productPriorityRepositoryMock = $this->createMock(
            ProductPriorityRepositoryInterface::class
        );

        $this->sut = new GenericButton($this->contextMock, $this->productPriorityRepositoryMock);
    }

    public function testGetEntityIdReturnsNull(): void
    {
        $entityId = null;
        $requestMock = $this->createMock(\Magento\Framework\App\RequestInterface::class);
        $this->contextMock
            ->method('getRequest')
            ->willReturn($requestMock);

        $requestMock
            ->method('getParam')
            ->with('entity_id')
            ->willReturn($entityId);

        self::assertNull($this->sut->getEntityId());
    }

    public function testGetEntityIdReturnsException(): void
    {
        $entityId = 5;
        $requestMock = $this->createMock(\Magento\Framework\App\RequestInterface::class);
        $this->contextMock
            ->method('getRequest')
            ->willReturn($requestMock);

        $requestMock
            ->method('getParam')
            ->with('entity_id')
            ->willReturn($entityId);

        $this->productPriorityRepositoryMock
            ->method('get')
            ->with($entityId)
            ->willThrowException(new NoSuchEntityException($this->createMock(Phrase::class)));

        self::assertNull($this->sut->getEntityId());
    }

    public function testGetEntityIdReturnsId(): void
    {
        $entityId = 10;
        $requestMock = $this->createMock(\Magento\Framework\App\RequestInterface::class);
        $this->contextMock
            ->method('getRequest')
            ->willReturn($requestMock);

        $requestMock
            ->method('getParam')
            ->with('entity_id')
            ->willReturn($entityId);

        $productPriorityMock = $this->createMock(ProductPriorityInterface::class);

        $this->productPriorityRepositoryMock
            ->method('get')
            ->with($entityId)
            ->willReturn($productPriorityMock);

        $productPriorityMock
            ->method('getEntityId')
            ->willReturn($entityId);

        self::assertEquals($entityId, $this->sut->getEntityId());
    }

    public function testGetUrl(): void
    {
        $urlBuilderMock = $this->createMock(\Magento\Framework\UrlInterface::class);
        $this->contextMock
            ->method('getUrlBuilder')
            ->willReturn($urlBuilderMock);

        $map = [
            ['*/*/priority_delete', ['entity_id' => 5], 'run_as_root/product/priority_delete/entity_id/5'],
            ['*/*/priorities', [], 'run_as_root/product/priorities'],
        ];

        $urlBuilderMock
            ->method('getUrl')
            ->willReturnMap($map);

        $result1 = $this->sut->getUrl('*/*/priority_delete', ['entity_id' => 5]);
        self::assertStringContainsString('priority_delete/entity_id', $result1);

        $result2 = $this->sut->getUrl('*/*/priorities');
        self::assertStringContainsString('priorities', $result2);
    }
}
