<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\Test\Unit\Block\Adminhtml\ProductPriority\Buttons;

use Magento\Backend\Block\Widget\Context;
use RunAsRoot\ProductPriorities\Api\Data\ProductPriorityInterface;
use RunAsRoot\ProductPriorities\Api\ProductPriorityRepositoryInterface;
use RunAsRoot\ProductPriorities\Block\Adminhtml\ProductPriority\Buttons\DeleteButton;
use PHPUnit\Framework\TestCase;

class DeleteButtonTest extends TestCase
{
    private DeleteButton $sut;

    protected Context $contextMock;

    protected ProductPriorityRepositoryInterface $productPriorityRepositoryMock;

    protected function setUp(): void
    {
        $this->contextMock = $this->createMock(Context::class);
        $this->productPriorityRepositoryMock = $this->createMock(
            ProductPriorityRepositoryInterface::class
        );

        $this->sut = new DeleteButton($this->contextMock, $this->productPriorityRepositoryMock);
    }


    public function testGetButtonDataReturnsEmptyArray(): void
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

        self::assertEquals([], $this->sut->getButtonData());
    }

    public function testGetButtonDataReturnsArray(): void
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

        $urlBuilderMock = $this->createMock(\Magento\Framework\UrlInterface::class);
        $this->contextMock
            ->method('getUrlBuilder')
            ->willReturn($urlBuilderMock);

        $map = [
            ['*/*/priority_delete', ['entity_id' => $entityId], 'run_as_root/product/priority_delete/entity_id/10'],
        ];

        $urlBuilderMock
            ->method('getUrl')
            ->willReturnMap($map);

        self::assertArrayHasKey('on_click', $this->sut->getButtonData());
    }
}
